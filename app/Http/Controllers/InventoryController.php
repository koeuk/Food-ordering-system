<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

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

        return view('inventory.index', compact('inventory', 'lowStockCount'));
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
        $alerts = Inventory::with('product')
            ->whereRaw('quantity <= minimum_stock')
            ->orderBy('quantity', 'asc')
            ->get();

        return view('inventory.alerts', compact('alerts'));
    }
}
