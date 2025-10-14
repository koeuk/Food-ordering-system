<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SalesReportsController extends Controller
{
    /**
     * Display sales reports page
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        // Get top products with detailed statistics
        $topProducts = $this->getTopProducts($startDate, $endDate);

        // Get sales summary statistics
        $summary = $this->getSalesSummary($startDate, $endDate);

        // Get daily sales data for the chart
        $salesData = $this->getDailySalesData($startDate, $endDate);

        return Inertia::render('Dashboard/Reports/Sales', [
            'topProducts' => $topProducts,
            'summary' => $summary,
            'salesData' => $salesData,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        ]);
    }

    /**
     * Get top products with sales statistics
     */
    private function getTopProducts($startDate, $endDate)
    {
        return Product::with(['category'])
            ->whereHas('orderItems.order', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'delivered');
            })
            ->withCount(['orderItems as quantity_sold' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('order', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'delivered');
                });
            }])
            ->withSum(['orderItems as revenue' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('order', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'delivered');
                });
            }], 'subtotal')
            ->where('is_available', true)
            ->orderBy('quantity_sold', 'desc')
            ->orderBy('revenue', 'desc')
            ->take(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'quantity_sold' => $product->quantity_sold,
                    'revenue' => $product->revenue ?? 0,
                    'price' => $product->price,
                ];
            });
    }

    /**
     * Get sales summary statistics
     */
    private function getSalesSummary($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->get();

        $totalRevenue = $orders->sum('total');
        $totalOrders = $orders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Get top product sales count
        $topProductSales = $this->getTopProducts($startDate, $endDate)->first()['quantity_sold'] ?? 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'avg_order_value' => $avgOrderValue,
            'top_product_sales' => $topProductSales,
        ];
    }

    /**
     * Get daily sales data for charts
     */
    private function getDailySalesData($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders_count')
            ->selectRaw('AVG(total) as avg_order_value')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'revenue' => $item->revenue,
                    'orders_count' => $item->orders_count,
                    'avg_order_value' => $item->avg_order_value,
                ];
            });
    }

    /**
     * Get top products API endpoint
     */
    public function getTopProductsApi(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());
        $limit = $request->input('limit', 10);

        $topProducts = Product::with(['category'])
            ->whereHas('orderItems.order', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'delivered');
            })
            ->withCount(['orderItems as quantity_sold' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('order', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'delivered');
                });
            }])
            ->withSum(['orderItems as revenue' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('order', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'delivered');
                });
            }], 'subtotal')
            ->where('is_available', true)
            ->orderBy('quantity_sold', 'desc')
            ->orderBy('revenue', 'desc')
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topProducts,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        ]);
    }

    /**
     * Get sales analytics data
     */
    public function getSalesAnalytics(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $analytics = [
            'top_products' => $this->getTopProducts($startDate, $endDate),
            'sales_summary' => $this->getSalesSummary($startDate, $endDate),
            'daily_sales' => $this->getDailySalesData($startDate, $endDate),
            'sales_by_category' => $this->getSalesByCategory($startDate, $endDate),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * Get sales breakdown by category
     */
    private function getSalesByCategory($startDate, $endDate)
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'delivered')
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_items.product_id) as products_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();
    }
}