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
        $user = Auth::user();
        
        // Get orders with relationships
        $orders = $user->orders()
            ->with(['items.product', 'bill'])
            ->latest()
            ->get();

        // Calculate statistics
        $stats = [
            'total_orders' => $orders->count(),
            'pending_orders' => $orders->where('status', 'pending')->count(),
            'completed_orders' => $orders->where('status', 'delivered')->count(),
            'total_spent' => $orders->sum('total'),
        ];

        return Inertia::render('Web/Orders/Index', [
            'orders' => $orders,
            'stats' => $stats,
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
        // Debug: Log incoming request data
        \Log::info('Order submission request data:', $request->all());
        
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'delivery_location' => 'required|string',
            'delivery_latitude' => 'nullable|numeric|between:-90,90',
            'delivery_longitude' => 'nullable|numeric|between:-180,180',
            'order_notes' => 'nullable|string|max:500',
            'total_amount' => 'required|numeric|min:0',
        ]);

        \Log::info('Validated order data:', $validated);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $orderItems = [];

            // Validate stock and calculate subtotal
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Find or create inventory for this product
                $inventory = \App\Models\Inventory::firstOrCreate(
                    ['product_id' => $product->id],
                    [
                        'quantity' => 100, // Default stock
                        'minimum_stock' => 10,
                    ]
                );

                // Check stock
                if (!$inventory->hasEnoughStock($item['quantity'])) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $itemSubtotal = $item['price'] * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Calculate tax (10%)
            $tax = $subtotal * 0.10;
            $total = $subtotal + $tax;

            // Check minimum order amount ($1 for testing)
            if ($total < 1) {
                throw new \Exception("Minimum order amount is $1. Your order total is $" . number_format($total, 2));
            }

            // Create order
            $order = Order::create([
                'customer_id' => Auth::id(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'delivery_address' => $validated['delivery_location'],
                'delivery_latitude' => $validated['delivery_latitude'] ?? null,
                'delivery_longitude' => $validated['delivery_longitude'] ?? null,
                'notes' => $validated['order_notes'] ?? null,
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

            // Clear the cart after successful order
            if (Auth::check()) {
                $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
                if ($cart) {
                    $cart->items()->delete();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'redirect_url' => route('web.orders.show', $order),
                'payment_url' => route('web.payment.show', $bill)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
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

        return Inertia::render('Web/Orders/Show', [
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
