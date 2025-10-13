<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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
        if ($bill->order->customer_id !== Auth::id() && !Auth::user()->isAdmin()) {
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
            'card_details' => 'nullable|array',
            'card_details.cardNumber' => 'nullable|string|regex:/^\d{4}\s\d{4}\s\d{4}\s\d{4}$/',
            'card_details.expiryDate' => 'nullable|string|regex:/^(0[1-9]|1[0-2])\/\d{2}$/',
            'card_details.cvv' => 'nullable|string|regex:/^\d{3,4}$/',
            'card_details.cardholderName' => 'nullable|string|max:255',
            'bank_details' => 'nullable|array',
            'bank_details.bank' => 'nullable|string|max:255',
            'bank_details.accountNumber' => 'nullable|string|regex:/^\d{8,20}$/',
        ]);

        if ($bill->isPaid()) {
            return back()->withErrors(['error' => 'Bill is already paid']);
        }

        // Validate card details if payment method is card
        if ($validated['payment_method'] === 'card') {
            if (!$validated['card_details'] || 
                !$validated['card_details']['cardNumber'] || 
                !$validated['card_details']['expiryDate'] || 
                !$validated['card_details']['cvv'] || 
                !$validated['card_details']['cardholderName']) {
                return back()->withErrors(['cardNumber' => 'All card details are required']);
            }
        }

        // Validate bank details if payment method is online
        if ($validated['payment_method'] === 'online') {
            if (!$validated['bank_details'] || 
                !$validated['bank_details']['bank'] || 
                !$validated['bank_details']['accountNumber']) {
                return back()->withErrors(['bank' => 'All bank details are required']);
            }
        }

        // Here you would integrate with payment gateway (Stripe/PayPal)
        // For demo purposes, we'll simulate payment processing
        try {
            // Simulate payment processing delay
            sleep(1);
            
            // For card payments, you would validate with payment processor
            if ($validated['payment_method'] === 'card') {
                // Simulate card validation
                $cardNumber = str_replace(' ', '', $validated['card_details']['cardNumber']);
                if (!preg_match('/^\d{16}$/', $cardNumber)) {
                    return back()->withErrors(['cardNumber' => 'Invalid card number']);
                }
            }

            // Mark bill as paid
            $bill->markAsPaid($validated['payment_method']);

            return redirect()->route('my-orders.show', $bill->order)
                ->with('success', 'Payment processed successfully! Your order is now confirmed.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Payment processing failed. Please try again.']);
        }
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
        if ($bill->order->customer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $bill->load(['order.items.product', 'order.customer']);

        // Here you would generate PDF using library like DomPDF
        // For now, return the view
        return view('bills.pdf', compact('bill'));
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats()
    {
        $stats = [
            'total_bills' => Bill::count(),
            'paid_bills' => Bill::paid()->count(),
            'unpaid_bills' => Bill::unpaid()->count(),
            'refunded_bills' => Bill::refunded()->count(),
            'total_revenue' => Bill::paid()->sum('amount'),
            'pending_revenue' => Bill::unpaid()->sum('amount'),
            'refunded_amount' => Bill::refunded()->sum('amount'),
        ];

        return response()->json($stats);
    }

    /**
     * Get overdue bills
     */
    public function getOverdueBills()
    {
        $overdueBills = Bill::with('order.customer')
            ->unpaid()
            ->whereHas('order', function ($query) {
                $query->where('created_at', '<', now()->subHours(24));
            })
            ->get()
            ->filter(function ($bill) {
                return $bill->isOverdue();
            });

        return response()->json($overdueBills);
    }

    /**
     * Send payment reminder
     */
    public function sendPaymentReminder(Bill $bill)
    {
        if ($bill->isPaid()) {
            return back()->withErrors(['error' => 'Bill is already paid']);
        }

        // Here you would send email notification
        // For now, just return success
        return back()->with('success', 'Payment reminder sent successfully!');
    }

    /**
     * Get bills by date range
     */
    public function getBillsByDateRange(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $bills = Bill::with(['order.customer', 'order.items.product'])
            ->whereBetween('created_at', [$validated['start_date'], $validated['end_date']])
            ->get();

        $summary = [
            'total_bills' => $bills->count(),
            'total_amount' => $bills->sum('amount'),
            'paid_amount' => $bills->where('payment_status', 'paid')->sum('amount'),
            'unpaid_amount' => $bills->where('payment_status', 'unpaid')->sum('amount'),
        ];

        return response()->json([
            'bills' => $bills,
            'summary' => $summary,
        ]);
    }

    /**
     * Get payment method statistics
     */
    public function getPaymentMethodStats()
    {
        $stats = Bill::paid()
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        return response()->json($stats);
    }
}
