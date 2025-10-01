<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_order_id',
        'product_id',
        'quantity',
        'unit_cost',
        'subtotal',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relationships

    /**
     * Get the inventory order that owns this item
     */
    public function inventoryOrder()
    {
        return $this->belongsTo(InventoryOrder::class);
    }

    /**
     * Get the product for this item
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
        $this->subtotal = $this->quantity * $this->unit_cost;
        return $this->subtotal;
    }
}
