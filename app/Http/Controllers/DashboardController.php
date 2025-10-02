<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Bill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Customer Dashboard
     */
    public function customerDashboard()
    {
        $user = Auth::user();

        $recentOrders = $user->orders()
            ->with(['items.product', 'bill'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_orders' => $user->orders()->count(),
            'pending_orders' => $user->orders()->where('status', 'pending')->count(),
            'completed_orders' => $user->orders()->where('status', 'delivered')->count(),
            'total_spent' => $user->orders()->where('status', 'delivered')->sum('total'),
        ];

        return Inertia::render('Dashboard/Customer', [
            'recentOrders' => $recentOrders,
            'stats' => $stats,
        ]);
    }

    /**
     * Manager Dashboard
     */
    public function managerDashboard()
    {
        // Sales statistics
        $todaySales = Order::whereDate('created_at', today())->sum('total');
        $monthSales = Order::whereMonth('created_at', now()->month)->sum('total');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();

        // Low stock alerts
        $lowStockItems = Inventory::with('product')
            ->whereRaw('quantity <= minimum_stock')
            ->take(5)
            ->get();

        // Recent orders
        $recentOrders = Order::with(['customer', 'bill'])
            ->latest()
            ->take(10)
            ->get();

        // Top selling products
        $topProducts = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard/Manager', [
            'todaySales' => $todaySales,
            'monthSales' => $monthSales,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'lowStockItems' => $lowStockItems,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
        ]);
    }

    /**
     * Kitchen Dashboard
     */
    public function kitchenDashboard()
    {
        $orders = Order::with(['items.product', 'customer'])
            ->whereIn('status', ['confirmed', 'preparing', 'ready'])
            ->latest()
            ->get();

        $stats = [
            'pending' => Order::where('status', 'confirmed')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'ready' => Order::where('status', 'ready')->count(),
            'completed_today' => Order::whereDate('created_at', today())
                ->where('status', 'delivered')
                ->count(),
        ];

        return Inertia::render('Dashboard/Kitchen', [
            'orders' => $orders,
            'stats' => $stats,
        ]);
    }

    /**
     * Supplier Dashboard
     */
    public function supplierDashboard()
    {
        $user = Auth::user();
        
        // Get inventory orders for this supplier
        $inventoryOrders = \App\Models\InventoryOrder::with(['manager', 'items.product'])
            ->where('supplier_id', $user->id)
            ->latest()
            ->get();

        $stats = [
            'total_orders' => $inventoryOrders->count(),
            'pending_orders' => $inventoryOrders->where('status', 'pending')->count(),
            'sent_orders' => $inventoryOrders->where('status', 'sent')->count(),
            'received_orders' => $inventoryOrders->where('status', 'received')->count(),
            'total_value' => $inventoryOrders->sum('total_amount'),
        ];

        return Inertia::render('Dashboard/Supplier', [
            'inventoryOrders' => $inventoryOrders,
            'stats' => $stats,
        ]);
    }

    /**
     * Generate sales report
     */
    public function salesReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $orders = Order::with(['items.product', 'customer', 'bill'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->get();

        $stats = [
            'total_revenue' => $orders->sum('total'),
            'total_orders' => $orders->count(),
            'average_order_value' => $orders->avg('total'),
            'total_tax' => $orders->sum('tax'),
        ];

        // Group by date
        $dailySales = $orders->groupBy(function ($order) {
            return $order->created_at->format('Y-m-d');
        });

        return view('reports.sales', compact('orders', 'stats', 'dailySales', 'startDate', 'endDate'));
    }

    /**
     * Generate inventory report
     */
    public function inventoryReport()
    {
        $inventory = Inventory::with('product.category')->get();

        $stats = [
            'total_products' => $inventory->count(),
            'low_stock_items' => $inventory->filter(fn($item) => $item->isLowStock())->count(),
            'out_of_stock' => $inventory->filter(fn($item) => $item->quantity == 0)->count(),
            'total_value' => $inventory->sum(function ($item) {
                return $item->quantity * $item->product->price;
            }),
        ];

        return view('reports.inventory', compact('inventory', 'stats'));
    }

    /**
     * Get comprehensive dashboard statistics
     */
    public function getDashboardStats(Request $request)
    {
        $period = $request->input('period', '30'); // days
        
        $stats = [
            'sales' => [
                'today' => Order::whereDate('created_at', today())->sum('total'),
                'this_week' => Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total'),
                'this_month' => Order::whereMonth('created_at', now()->month)->sum('total'),
                'last_30_days' => Order::where('created_at', '>=', now()->subDays(30))->sum('total'),
            ],
            'orders' => [
                'total' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'confirmed' => Order::where('status', 'confirmed')->count(),
                'preparing' => Order::where('status', 'preparing')->count(),
                'ready' => Order::where('status', 'ready')->count(),
                'delivered' => Order::where('status', 'delivered')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
            ],
            'customers' => [
                'total' => User::where('role', 'customer')->count(),
                'active_today' => User::where('role', 'customer')
                    ->whereHas('orders', function ($query) {
                        $query->whereDate('created_at', today());
                    })->count(),
                'new_this_month' => User::where('role', 'customer')
                    ->whereMonth('created_at', now()->month)->count(),
            ],
            'inventory' => [
                'total_products' => Inventory::count(),
                'low_stock' => Inventory::lowStock()->count(),
                'out_of_stock' => Inventory::outOfStock()->count(),
                'total_value' => Inventory::with('product')->get()->sum(function ($item) {
                    return $item->quantity * $item->product->price;
                }),
            ],
            'payments' => [
                'total_revenue' => Bill::paid()->sum('amount'),
                'pending_payments' => Bill::unpaid()->sum('amount'),
                'refunded_amount' => Bill::refunded()->sum('amount'),
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Get sales analytics
     */
    public function getSalesAnalytics(Request $request)
    {
        $days = $request->input('days', 30);
        $startDate = now()->subDays($days);

        // Daily sales data
        $dailySales = Order::where('created_at', '>=', $startDate)
            ->where('status', 'delivered')
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top selling products
        $topProducts = Product::withCount(['orderItems as total_quantity' => function ($query) use ($startDate) {
                $query->whereHas('order', function ($q) use ($startDate) {
                    $q->where('created_at', '>=', $startDate)
                      ->where('status', 'delivered');
                });
            }])
            ->orderBy('total_quantity', 'desc')
            ->take(10)
            ->get();

        // Sales by category
        $salesByCategory = Product::with('category')
            ->whereHas('orderItems.order', function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate)
                      ->where('status', 'delivered');
            })
            ->withSum(['orderItems as total_revenue' => function ($query) use ($startDate) {
                $query->whereHas('order', function ($q) use ($startDate) {
                    $q->where('created_at', '>=', $startDate)
                      ->where('status', 'delivered');
                });
            }], 'subtotal')
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function ($products) {
                return [
                    'category' => $products->first()->category->name,
                    'revenue' => $products->sum('total_revenue'),
                    'products_count' => $products->count(),
                ];
            })
            ->values();

        return response()->json([
            'daily_sales' => $dailySales,
            'top_products' => $topProducts,
            'sales_by_category' => $salesByCategory,
        ]);
    }

    /**
     * Get order status analytics
     */
    public function getOrderStatusAnalytics()
    {
        $analytics = [
            'status_distribution' => Order::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status'),
            
            'average_processing_time' => [
                'pending_to_confirmed' => $this->getAverageProcessingTime('pending', 'confirmed'),
                'confirmed_to_delivered' => $this->getAverageProcessingTime('confirmed', 'delivered'),
                'total_processing_time' => $this->getAverageProcessingTime('pending', 'delivered'),
            ],
            
            'hourly_distribution' => Order::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get(),
                
            'daily_distribution' => Order::selectRaw('DAYOFWEEK(created_at) as day, COUNT(*) as count')
                ->groupBy('day')
                ->get(),
        ];

        return response()->json($analytics);
    }

    /**
     * Calculate average processing time between statuses
     */
    private function getAverageProcessingTime($fromStatus, $toStatus)
    {
        $orders = Order::whereNotNull('confirmed_at')
            ->whereNotNull('delivered_at')
            ->get();

        if ($orders->isEmpty()) {
            return 0;
        }

        $totalMinutes = 0;
        $count = 0;

        foreach ($orders as $order) {
            if ($fromStatus === 'pending' && $toStatus === 'confirmed') {
                if ($order->confirmed_at) {
                    $totalMinutes += $order->created_at->diffInMinutes($order->confirmed_at);
                    $count++;
                }
            } elseif ($fromStatus === 'confirmed' && $toStatus === 'delivered') {
                if ($order->delivered_at && $order->confirmed_at) {
                    $totalMinutes += $order->confirmed_at->diffInMinutes($order->delivered_at);
                    $count++;
                }
            } elseif ($fromStatus === 'pending' && $toStatus === 'delivered') {
                if ($order->delivered_at) {
                    $totalMinutes += $order->created_at->diffInMinutes($order->delivered_at);
                    $count++;
                }
            }
        }

        return $count > 0 ? round($totalMinutes / $count, 2) : 0;
    }
}
