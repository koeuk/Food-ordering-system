<?php

namespace App\Http\Controllers;

use App\Models\InventoryOrder;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InventoryOrderController extends Controller
{
    /**
     * Display inventory orders
     */
    public function index()
    {
        $inventoryOrders = InventoryOrder::with(['supplier', 'manager', 'items.product'])
            ->latest()
            ->paginate(10);

        return Inertia::render('InventoryOrders/Index', [
            'inventoryOrders' => $inventoryOrders,
        ]);
    }

    /**
     * Show the form for creating a new inventory order
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::with('inventory')->get();

        return Inertia::render('InventoryOrders/Create', [
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created inventory order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $orderItems = [];

            // Calculate total amount
            foreach ($validated['items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_cost'];
                $totalAmount += $itemSubtotal;

                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Create inventory order
            $order = InventoryOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'manager_id' => Auth::id(),
                'order_number' => InventoryOrder::generateOrderNumber(),
                'status' => 'pending',
                'total_amount' => $totalAmount,
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            return redirect()->route('inventory-orders.index')
                ->with('success', 'Inventory order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified inventory order
     */
    public function show(InventoryOrder $inventoryOrder)
    {
        $inventoryOrder->load(['supplier', 'manager', 'items.product']);

        return Inertia::render('InventoryOrders/Show', [
            'inventoryOrder' => $inventoryOrder,
        ]);
    }

    /**
     * Mark order as sent
     */
    public function markAsSent(InventoryOrder $inventoryOrder)
    {
        if ($inventoryOrder->status !== 'pending') {
            return back()->withErrors(['error' => 'Order cannot be sent']);
        }

        $inventoryOrder->markAsSent();

        // Here you would send email/notification to supplier

        return back()->with('success', 'Order marked as sent!');
    }

    /**
     * Mark order as received (updates inventory)
     */
    public function markAsReceived(InventoryOrder $inventoryOrder)
    {
        if ($inventoryOrder->status !== 'sent') {
            return back()->withErrors(['error' => 'Order is not in sent status']);
        }

        DB::beginTransaction();

        try {
            $inventoryOrder->markAsReceived();

            DB::commit();

            return back()->with('success', 'Order received and inventory updated!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Cancel inventory order
     */
    public function cancel(InventoryOrder $inventoryOrder)
    {
        if (!in_array($inventoryOrder->status, ['pending', 'sent'])) {
            return back()->withErrors(['error' => 'Order cannot be cancelled']);
        }

        $inventoryOrder->cancel();

        return back()->with('success', 'Inventory order cancelled!');
    }
}
