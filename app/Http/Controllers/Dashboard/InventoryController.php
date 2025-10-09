<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryController extends Controller
{
    /**
     * Display inventory dashboard
     */
    public function index(Request $request)
    {
        $query = Inventory::with('product.category');

        // Filter by low stock
        if ($request->has('low_stock')) {
            $query->whereRaw('quantity <= minimum_stock');
        }

        // Search by product name
        if ($request->has('search')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $inventory = $query->paginate(15);

        // Get low stock count for alert
        $lowStockCount = Inventory::whereRaw('quantity <= minimum_stock')->count();

        return Inertia::render('Inventory/Index', [
            'inventory' => $inventory,
            'lowStockCount' => $lowStockCount,
            'filters' => [
                'search' => $request->search,
                'low_stock' => $request->low_stock,
            ],
        ]);
    }

    /**
     * Update inventory quantity
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
        ]);

        $inventory->update($validated);

        return back()->with('success', 'Inventory updated successfully!');
    }

    /**
     * Restock inventory
     */
    public function restock(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $inventory->increaseQuantity($validated['quantity']);

        return back()->with('success', 'Inventory restocked successfully!');
    }

    /**
     * Get low stock items (API)
     */
    public function getLowStock()
    {
        $lowStockItems = Inventory::with('product')
            ->whereRaw('quantity <= minimum_stock')
            ->get();

        return response()->json($lowStockItems);
    }

    /**
     * Get low stock alerts
     */
    public function alerts()
    {
        $lowStockItems = Inventory::with('product.category')
            ->whereRaw('quantity <= minimum_stock')
            ->orderBy('quantity', 'asc')
            ->get();

        return Inertia::render('Inventory/Alerts', [
            'lowStockItems' => $lowStockItems,
        ]);
    }

    /**
     * Bulk restock multiple items
     */
    public function bulkRestock(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.inventory_id' => 'required|exists:inventory,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        foreach ($validated['items'] as $item) {
            $inventory = Inventory::find($item['inventory_id']);
            $inventory->restock($item['quantity']);
        }

        return back()->with('success', 'Bulk restock completed successfully!');
    }

    /**
     * Generate inventory report
     */
    public function report(Request $request)
    {
        $query = Inventory::with('product.category');

        // Apply filters
        if ($request->has('category_id')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->has('stock_status')) {
            switch ($request->stock_status) {
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
                case 'in_stock':
                    $query->whereRaw('quantity > minimum_stock');
                    break;
            }
        }

        $inventory = $query->get();

        // Generate summary statistics
        $summary = [
            'total_products' => $inventory->count(),
            'in_stock' => $inventory->where('quantity', '>', 0)->count(),
            'low_stock' => $inventory->filter(function ($item) {
                return $item->isLowStock() && !$item->isOutOfStock();
            })->count(),
            'out_of_stock' => $inventory->where('quantity', 0)->count(),
            'total_value' => $inventory->sum(function ($item) {
                return $item->quantity * $item->product->price;
            }),
        ];

        return Inertia::render('Inventory/Report', [
            'inventory' => $inventory,
            'summary' => $summary,
            'filters' => $request->only(['category_id', 'stock_status']),
        ]);
    }

    /**
     * Set minimum stock levels
     */
    public function setMinimumStock(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'minimum_stock' => 'required|integer|min:0',
        ]);

        $inventory = Inventory::find($validated['inventory_id']);
        $inventory->minimum_stock = $validated['minimum_stock'];
        $inventory->save();

        return back()->with('success', 'Minimum stock level updated successfully!');
    }

    /**
     * Get inventory statistics for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total_products' => Inventory::count(),
            'low_stock_count' => Inventory::lowStock()->count(),
            'out_of_stock_count' => Inventory::outOfStock()->count(),
            'total_inventory_value' => Inventory::with('product')
                ->get()
                ->sum(function ($item) {
                    return $item->quantity * $item->product->price;
                }),
            'average_stock_level' => Inventory::avg('quantity'),
        ];

        return response()->json($stats);
    }
}
