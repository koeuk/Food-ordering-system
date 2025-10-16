<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of orders (Admin view)
     */
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['customer', 'items.product', 'bill'])
            ->when($request->search, function ($query, $search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15);

        // Calculate order statistics
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'ready' => Order::where('status', 'ready')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return Inertia::render('Dashboard/Orders/Index', [
            'orders' => $orders,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        // Orders are typically created by customers through web interface
        // This is just an informational page for admins
        return Inertia::render('Dashboard/Orders/Create');
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'items.product.category', 'bill']);

        return Inertia::render('Dashboard/Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Show the form for editing order status
     */
    public function edit(Order $order)
    {
        $order->load(['customer', 'items.product', 'bill']);

        return Inertia::render('Dashboard/Orders/Edit', [
            'order' => $order,
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled',
        ]);

        // If changing to confirmed from pending, use the confirm method to update inventory
        if ($validated['status'] === 'confirmed' && $order->status === 'pending') {
            $order->confirm();
        } else {
            $order->update(['status' => $validated['status']]);
        }

        if ($validated['status'] === 'delivered') {
            $order->markAsDelivered();
        }

        return redirect()->route('dashboard.orders.index')
            ->with('success', 'Order status updated successfully.');
    }

    /**
     * Confirm order
     */
    public function confirm(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->withErrors(['error' => 'Order cannot be confirmed']);
        }

        $order->confirm();

        return back()->with('success', 'Order confirmed successfully!');
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
     * Show the form for deleting/cancelling the order
     */
    public function delete(Order $order)
    {
        $order->load(['customer', 'items.product']);

        return Inertia::render('Dashboard/Orders/Delete', [
            'order' => $order,
        ]);
    }

    /**
     * Remove the specified order (not typically used - use cancel instead)
     */
    public function destroy(Order $order)
    {
        // For audit purposes, we typically don't delete orders
        // Instead, we cancel them
        if ($order->status !== 'pending') {
            return back()->withErrors(['error' => 'Cannot delete order that is not pending']);
        }

        $order->cancel();

        return redirect()->route('dashboard.orders.index')
            ->with('success', 'Order cancelled successfully.');
    }
}

