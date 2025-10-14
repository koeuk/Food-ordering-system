<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
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
     * User Dashboard
     */
    public function userDashboard()
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

        return Inertia::render('Dashboard/User', [
            'recentOrders' => $recentOrders,
            'stats' => $stats,
        ]);
    }

    /**
     * Admin Dashboard
     */
    public function adminDashboard()
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

        // Recent orders with location data
        $recentOrders = Order::with(['customer', 'bill'])
            ->latest()
            ->take(10)
            ->get();

        // New orders (last 24 hours) for notifications
        $newOrders = Order::where('created_at', '>=', now()->subDay())
            ->with(['customer', 'bill'])
            ->latest()
            ->get();

        // Top selling products with category information
        $topProducts = Product::with(['category', 'inventory'])
            ->withCount(['orderItems as total_sales' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('status', 'delivered');
                });
            }])
            ->where('is_available', true)
            ->orderBy('total_sales', 'desc')
            ->take(10)
            ->get();

        return Inertia::render('Dashboard/Admin', [
            'user' => Auth::user(),
            'stats' => [
                'total_revenue' => $todaySales,
                'orders_today' => Order::whereDate('created_at', today())->count(),
                'active_products' => Product::where('is_available', true)->count(),
                'low_stock_count' => Inventory::whereRaw('quantity <= minimum_stock')->count(),
                'new_orders_count' => $newOrders->count(),
            ],
            'todaySales' => $todaySales,
            'monthSales' => $monthSales,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'lowStockItems' => $lowStockItems,
            'recentOrders' => $recentOrders,
            'newOrders' => $newOrders,
            'topProducts' => $topProducts,
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
                'total' => User::where('role', 'user')->count(),
                'active_today' => User::where('role', 'user')
                    ->whereHas('orders', function ($query) {
                        $query->whereDate('created_at', today());
                    })->count(),
                'new_this_month' => User::where('role', 'user')
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
        $period = $request->input('period', 'monthly'); // daily, weekly, monthly
        $months = $request->input('months', 12); // number of months to show

        if ($period === 'monthly') {
            // Monthly sales data for the last 12 months
            $startDate = now()->subMonths($months - 1)->startOfMonth();
            
            $monthlySales = Order::where('created_at', '>=', $startDate)
                ->where('status', 'delivered')
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total) as revenue, COUNT(*) as orders')
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();

            // Fill in missing months with zero values
            $salesData = collect();
            $currentDate = $startDate;
            
            while ($currentDate->lte(now())) {
                $existingData = $monthlySales->first(function ($item) use ($currentDate) {
                    return $item->year == $currentDate->year && $item->month == $currentDate->month;
                });
                
                $salesData->push([
                    'year' => $currentDate->year,
                    'month' => $currentDate->month,
                    'month_name' => $currentDate->format('M'),
                    'revenue' => $existingData ? $existingData->revenue : 0,
                    'orders' => $existingData ? $existingData->orders : 0,
                ]);
                
                $currentDate->addMonth();
            }
            
            $dailySales = $salesData;
        } else {
            // Daily sales data
            $days = $request->input('days', 30);
            $startDate = now()->subDays($days);

            $dailySales = Order::where('created_at', '>=', $startDate)
                ->where('status', 'delivered')
                ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        // Top selling products with category information
        $topProducts = Product::with(['category', 'inventory'])
            ->withCount(['orderItems as order_items_count' => function ($query) use ($startDate) {
                $query->whereHas('order', function ($q) use ($startDate) {
                    $q->where('created_at', '>=', $startDate)
                      ->where('status', 'delivered');
                });
            }])
            ->where('is_available', true)
            ->orderBy('order_items_count', 'desc')
            ->take(15)
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
     * Get user order history for favorite products
     */
    public function getUserOrderHistory(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get products that the user has ordered, with order count
        $userOrderedProducts = Product::with(['category', 'inventory'])
            ->whereHas('orderItems.order', function ($query) use ($user) {
                $query->where('customer_id', $user->id)
                      ->where('status', 'delivered');
            })
            ->withCount(['orderItems as user_order_count' => function ($query) use ($user) {
                $query->whereHas('order', function ($q) use ($user) {
                    $q->where('customer_id', $user->id)
                      ->where('status', 'delivered');
                });
            }])
            ->where('is_available', true)
            ->orderBy('user_order_count', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'user_ordered_products' => $userOrderedProducts,
            'total_products_ordered' => $userOrderedProducts->count(),
            'total_user_orders' => $userOrderedProducts->sum('user_order_count'),
            'total_user_spent' => $userOrderedProducts->sum(function ($product) {
                return $product->price * $product->user_order_count;
            }),
        ]);
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
