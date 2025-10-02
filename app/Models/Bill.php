<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'bill_number',
        'amount',
        'payment_status',
        'payment_method',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Relationships

    /**
     * Get the order for this bill
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Helper methods

    /**
     * Generate unique bill number
     */
    public static function generateBillNumber()
    {
        return 'BILL-' . strtoupper(uniqid());
    }

    /**
     * Mark bill as paid
     */
    public function markAsPaid($paymentMethod)
    {
        $this->payment_status = 'paid';
        $this->payment_method = $paymentMethod;
        $this->paid_at = now();
        return $this->save();
    }

    /**
     * Check if bill is paid
     */
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Refund the payment
     */
    public function refund()
    {
        $this->payment_status = 'refunded';
        return $this->save();
    }

    /**
     * Check if refund is allowed (within 24 hours of delivery)
     */
    public function canRefund()
    {
        if (!$this->isPaid()) {
            return false;
        }

        // Check if order was delivered within 24 hours
        if ($this->order && $this->order->delivered_at) {
            return $this->order->delivered_at->diffInHours(now()) <= 24;
        }

        return false;
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmount()
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Scope for paid bills
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope for unpaid bills
     */
    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    /**
     * Scope for refunded bills
     */
    public function scopeRefunded($query)
    {
        return $query->where('payment_status', 'refunded');
    }

    /**
     * Check if bill is overdue (unpaid for more than 24 hours)
     */
    public function isOverdue()
    {
        if ($this->isPaid()) {
            return false;
        }

        return $this->created_at->diffInHours(now()) > 24;
    }
}
