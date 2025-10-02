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

    /**
     * Check if out of stock
     */
    public function isOutOfStock()
    {
        return $this->quantity === 0;
    }

    /**
     * Get stock status
     */
    public function getStockStatus()
    {
        if ($this->isOutOfStock()) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Scope for low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= minimum_stock');
    }

    /**
     * Scope for out of stock items
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', 0);
    }

    /**
     * Restock inventory
     */
    public function restock($amount, $notes = null)
    {
        $this->quantity += $amount;
        $this->last_restocked_at = now();
        
        // Log the restock action if needed
        if ($notes) {
            // Could implement activity logging here
        }
        
        return $this->save();
    }
}
