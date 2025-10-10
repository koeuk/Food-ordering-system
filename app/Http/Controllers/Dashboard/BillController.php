<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BillController extends Controller
{
    /**
     * Display a listing of bills
     */
    public function index(Request $request)
    {
        $bills = Bill::query()
            ->with(['order.customer'])
            ->when($request->search, function ($query, $search) {
                $query->where('bill_number', 'like', "%{$search}%")
                    ->orWhereHas('order.customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('payment_status', $status);
            })
            ->latest()
            ->paginate(15);

        return Inertia::render('Dashboard/Bills/Index', [
            'bills' => $bills,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Display the specified bill
     */
    public function show(Bill $bill)
    {
        $bill->load(['order.customer', 'order.items.product']);

        return Inertia::render('Dashboard/Bills/Show', [
            'bill' => $bill,
        ]);
    }

    /**
     * Process refund for a bill
     */
    public function refund(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0|max:' . $bill->amount,
        ]);

        $bill->update([
            'payment_status' => 'refunded',
            'refund_amount' => $validated['amount'],
            'refund_reason' => $validated['reason'],
            'refunded_at' => now(),
        ]);

        return redirect()->route('dashboard.bills.index')
            ->with('success', 'Refund processed successfully.');
    }
}
