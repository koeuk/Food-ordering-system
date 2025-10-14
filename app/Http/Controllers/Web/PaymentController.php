<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PaymentController extends Controller
{
    /**
     * Display the payment page
     */
    public function show(Bill $bill)
    {
        // Ensure user can only view their own bills
        if ($bill->order->customer_id !== Auth::id() && !Auth::user()->role === 'admin') {
            abort(403);
        }

        // Check if bill is already paid
        if ($bill->isPaid()) {
            return redirect()->route('web.orders.show', $bill->order)
                ->with('info', 'This bill has already been paid.');
        }

        // Load relationships
        $bill->load([
            'order.items.product.category',
            'order.customer'
        ]);

        return Inertia::render('Web/Payment/PayNow', [
            'bill' => $bill,
            'order' => $bill->order,
        ]);
    }

    /**
     * Show payment success page
     */
    public function success(Bill $bill)
    {
        // Ensure user can only view their own bills
        if ($bill->order->customer_id !== Auth::id() && !Auth::user()->role === 'admin') {
            abort(403);
        }

        // Check if bill is paid
        if (!$bill->isPaid()) {
            return redirect()->route('web.payment.show', $bill)
                ->with('error', 'Payment not found or not completed.');
        }

        $bill->load([
            'order.items.product.category',
            'order.customer'
        ]);

        return Inertia::render('Web/Payment/Success', [
            'bill' => $bill,
            'order' => $bill->order,
        ]);
    }

    /**
     * Show payment failed page
     */
    public function failed(Bill $bill)
    {
        // Ensure user can only view their own bills
        if ($bill->order->customer_id !== Auth::id() && !Auth::user()->role === 'admin') {
            abort(403);
        }

        $bill->load([
            'order.items.product.category',
            'order.customer'
        ]);

        return Inertia::render('Web/Payment/Failed', [
            'bill' => $bill,
            'order' => $bill->order,
        ]);
    }

    /**
     * Get payment methods available
     */
    public function getPaymentMethods()
    {
        $paymentMethods = [
            'card' => [
                'name' => 'Credit/Debit Card',
                'icon' => 'mdi-credit-card',
                'description' => 'Visa, MasterCard, American Express',
                'enabled' => true,
                'processing_time' => 'Instant',
                'fees' => 'No additional fees'
            ],
            'online' => [
                'name' => 'Online Banking',
                'icon' => 'mdi-bank',
                'description' => 'Direct bank transfer',
                'enabled' => true,
                'processing_time' => '1-3 business days',
                'fees' => 'No additional fees'
            ],
            'cash' => [
                'name' => 'Cash on Delivery',
                'icon' => 'mdi-cash',
                'description' => 'Pay when your order arrives',
                'enabled' => true,
                'processing_time' => 'On delivery',
                'fees' => 'No additional fees'
            ]
        ];

        return response()->json($paymentMethods);
    }

    /**
     * Validate payment data
     */
    public function validatePayment(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:card,online,cash',
            'card_details' => 'nullable|array',
            'card_details.cardNumber' => 'nullable|string|regex:/^\d{4}\s\d{4}\s\d{4}\s\d{4}$/',
            'card_details.expiryDate' => 'nullable|string|regex:/^(0[1-9]|1[0-2])\/\d{2}$/',
            'card_details.cvv' => 'nullable|string|regex:/^\d{3,4}$/',
            'card_details.cardholderName' => 'nullable|string|max:255',
            'bank_details' => 'nullable|array',
            'bank_details.bank' => 'nullable|string|max:255',
            'bank_details.accountNumber' => 'nullable|string|regex:/^\d{8,20}$/',
            'billing_address' => 'required|array',
            'billing_address.firstName' => 'required|string|max:255',
            'billing_address.lastName' => 'required|string|max:255',
            'billing_address.street' => 'required|string|max:255',
            'billing_address.city' => 'required|string|max:255',
            'billing_address.postalCode' => 'required|string|max:20',
        ]);

        // Additional validation based on payment method
        if ($validated['payment_method'] === 'card') {
            if (!$validated['card_details'] || 
                !$validated['card_details']['cardNumber'] || 
                !$validated['card_details']['expiryDate'] || 
                !$validated['card_details']['cvv'] || 
                !$validated['card_details']['cardholderName']) {
                return response()->json([
                    'valid' => false,
                    'errors' => ['cardNumber' => 'All card details are required']
                ], 422);
            }
        }

        if ($validated['payment_method'] === 'online') {
            if (!$validated['bank_details'] || 
                !$validated['bank_details']['bank'] || 
                !$validated['bank_details']['accountNumber']) {
                return response()->json([
                    'valid' => false,
                    'errors' => ['bank' => 'All bank details are required']
                ], 422);
            }
        }

        return response()->json([
            'valid' => true,
            'message' => 'Payment data is valid'
        ]);
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(Bill $bill)
    {
        // Ensure user can only view their own bills
        if ($bill->order->customer_id !== Auth::id() && !Auth::user()->role === 'admin') {
            abort(403);
        }

        $status = [
            'bill_id' => $bill->uuid,
            'order_id' => $bill->order->uuid,
            'payment_status' => $bill->payment_status,
            'payment_method' => $bill->payment_method,
            'amount' => $bill->amount,
            'paid_at' => $bill->paid_at,
            'can_refund' => $bill->canRefund(),
            'is_overdue' => $bill->isOverdue() ?? false,
        ];

        return response()->json($status);
    }

    /**
     * Get payment history for user
     */
    public function getPaymentHistory()
    {
        $user = Auth::user();
        
        $bills = Bill::with(['order.items.product'])
            ->whereHas('order', function ($query) use ($user) {
                $query->where('customer_id', $user->id);
            })
            ->latest()
            ->paginate(10);

        $summary = [
            'total_payments' => $bills->total(),
            'total_amount' => $bills->sum('amount'),
            'paid_amount' => $bills->where('payment_status', 'paid')->sum('amount'),
            'unpaid_amount' => $bills->where('payment_status', 'unpaid')->sum('amount'),
            'refunded_amount' => $bills->where('payment_status', 'refunded')->sum('amount'),
        ];

        return response()->json([
            'bills' => $bills,
            'summary' => $summary
        ]);
    }

    /**
     * Cancel payment (if not processed)
     */
    public function cancelPayment(Bill $bill)
    {
        // Ensure user can only cancel their own bills
        if ($bill->order->customer_id !== Auth::id()) {
            abort(403);
        }

        // Check if payment can be cancelled
        if ($bill->isPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel a completed payment'
            ], 400);
        }

        // Here you would cancel any pending payment processing
        // For now, just return success
        return response()->json([
            'success' => true,
            'message' => 'Payment cancelled successfully'
        ]);
    }
}
