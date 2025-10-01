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
}
