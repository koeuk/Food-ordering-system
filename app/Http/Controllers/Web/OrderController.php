<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display customer's orders
     */
    public function index()
    {
        $orders = Auth::user()->orders()
            ->with(['items.product', 'bill'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $products = Product::with(['inventory', 'category'])
            ->where('is_available', true)
            ->whereHas('inventory', function ($query) {
                $query->where('quantity', '>', 0);
            })
            ->get();

        $categories = \App\Models\Category::all();

        return Inertia::render('Orders/Create', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.special_instructions' => 'nullable|string',
            'delivery_address' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $orderItems = [];

            // Validate stock and calculate subtotal
            foreach ($validated['items'] as $item) {
                $product = Product::with('inventory')->findOrFail($item['product_id']);

                // Check stock
                if (!$product->inventory->hasEnoughStock($item['quantity'])) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $itemSubtotal,
                    'special_instructions' => $item['special_instructions'] ?? null,
                ];
            }

            // Calculate tax (10%)
            $tax = $subtotal * 0.10;
            $total = $subtotal + $tax;

            // Check minimum order amount ($10)
            if ($total < 10) {
                throw new \Exception("Minimum order amount is $10");
            }

            // Create order
            $order = Order::create([
                'customer_id' => Auth::id(),
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'delivery_address' => $validated['delivery_address'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            // Create bill
            Bill::create([
                'order_id' => $order->id,
                'bill_number' => Bill::generateBillNumber(),
                'amount' => $total,
                'payment_status' => 'unpaid',
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->customer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $order->load(['items.product', 'bill', 'customer']);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Confirm order (update inventory)
     */
    public function confirm(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->withErrors(['error' => 'Order cannot be confirmed']);
        }

        DB::beginTransaction();

        try {
            // Update inventory
            foreach ($order->items as $item) {
                $inventory = $item->product->inventory;
                $inventory->decreaseQuantity($item->quantity);
            }

            $order->confirm();

            DB::commit();

            return back()->with('success', 'Order confirmed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        if ($validated['status'] === 'delivered') {
            $order->markAsDelivered();
        }

        return back()->with('success', 'Order status updated successfully!');
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order)
    {
        if (!$order->canBeCancelled()) {
            return back()->withErrors(['error' => 'Order cannot be cancelled']);
        }

        $order->cancel();

        return back()->with('success', 'Order cancelled successfully!');
    }

    /**
     * Kitchen dashboard - pending orders
     */
    public function kitchenOrders()
    {
        $orders = Order::with(['items.product', 'customer'])
            ->whereIn('status', ['confirmed', 'preparing'])
            ->latest()
            ->get();

        return Inertia::render('Kitchen/Orders', [
            'orders' => $orders,
        ]);
    }
}
