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
}
