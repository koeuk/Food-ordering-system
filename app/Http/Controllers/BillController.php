<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BillController extends Controller
{
    /**
     * Display the specified bill
     */
    public function show(Bill $bill)
    {
        // Ensure user can only view their own bills
        if ($bill->order->customer_id !== Auth::id() && !Auth::user()->isManager()) {
            abort(403);
        }

        $bill->load(['order.items.product', 'order.customer']);

        return Inertia::render('Bills/Show', [
            'bill' => $bill,
        ]);
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,online',
        ]);

        if ($bill->isPaid()) {
            return back()->withErrors(['error' => 'Bill is already paid']);
        }

        // Here you would integrate with payment gateway (Stripe/PayPal)
        // For now, we'll just mark as paid

        $bill->markAsPaid($validated['payment_method']);

        return redirect()->route('orders.show', $bill->order)
            ->with('success', 'Payment processed successfully!');
    }

    /**
     * Refund payment
     */
    public function refund(Bill $bill)
    {
        if (!$bill->isPaid()) {
            return back()->withErrors(['error' => 'Bill is not paid yet']);
        }

        // Check if order was delivered within 24 hours
        $deliveredAt = $bill->order->delivered_at;
        if ($deliveredAt && now()->diffInHours($deliveredAt) > 24) {
            return back()->withErrors(['error' => 'Refund period has expired (24 hours)']);
        }

        // Process refund (integrate with payment gateway)
        $bill->refund();

        return back()->with('success', 'Payment refunded successfully!');
    }

    /**
     * Download bill as PDF
     */
    public function download(Bill $bill)
    {
        // Ensure user can only download their own bills
        if ($bill->order->customer_id !== Auth::id() && !Auth::user()->isManager()) {
            abort(403);
        }

        $bill->load(['order.items.product', 'order.customer']);

        // Here you would generate PDF using library like DomPDF
        // For now, return the view
        return view('bills.pdf', compact('bill'));
    }
}
