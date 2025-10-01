<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'product_id',
        'quantity',
        'minimum_stock',
        'last_restocked_at',
    ];

    protected $casts = [
        'last_restocked_at' => 'datetime',
    ];

    // Relationships

    /**
     * Get the product that owns this inventory
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helper methods

    /**
     * Check if inventory is low
     */
    public function isLowStock()
    {
        return $this->quantity <= $this->minimum_stock;
    }

    /**
     * Decrease inventory quantity
     */
    public function decreaseQuantity($amount)
    {
        $this->quantity -= $amount;
        return $this->save();
    }

    /**
     * Increase inventory quantity
     */
    public function increaseQuantity($amount)
    {
        $this->quantity += $amount;
        $this->last_restocked_at = now();
        return $this->save();
    }

    /**
     * Check if has enough stock
     */
    public function hasEnoughStock($requiredQuantity)
    {
        return $this->quantity >= $requiredQuantity;
    }
}
