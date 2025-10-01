<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'special_instructions',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relationships

    /**
     * Get the order that owns this item
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for this order item
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helper methods

    /**
     * Calculate subtotal
     */
    public function calculateSubtotal()
    {
        $this->subtotal = $this->quantity * $this->unit_price;
        return $this->subtotal;
    }
}
