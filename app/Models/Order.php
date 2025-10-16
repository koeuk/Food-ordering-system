<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasUuidTrait;

    protected $fillable = [
        'customer_id',
        'customer_name',
        'customer_phone',
        'order_number',
        'status',
        'subtotal',
        'tax',
        'total',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'notes',
        'confirmed_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relationships

    /**
     * Get the customer who placed this order
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get all items in this order
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the bill for this order
     */
    public function bill()
    {
        return $this->hasOne(Bill::class);
    }

    // Helper methods

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber()
    {
        return 'ORD-' . strtoupper(uniqid());
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled()
    {
        return $this->status === 'pending';
    }

    /**
     * Confirm the order
     */
    public function confirm()
    {
        // Update inventory quantities
        foreach ($this->items as $item) {
            $inventory = $item->product->inventory;
            if ($inventory) {
                $oldQuantity = $inventory->quantity;
                $inventory->decreaseQuantity($item->quantity);
                
                // Log inventory update
                \Log::info("Inventory updated for order {$this->order_number}: {$item->product->name} - {$oldQuantity} -> {$inventory->quantity} units (decreased by {$item->quantity})");
            }
        }

        $this->status = 'confirmed';
        $this->confirmed_at = now();
        return $this->save();
    }

    /**
     * Mark order as delivered
     */
    public function markAsDelivered()
    {
        $this->status = 'delivered';
        $this->delivered_at = now();
        return $this->save();
    }

    /**
     * Cancel the order
     */
    public function cancel()
    {
        if ($this->canBeCancelled()) {
            $this->status = 'cancelled';
            return $this->save();
        }
        return false;
    }

    /**
     * Cancel confirmed order and restore inventory
     */
    public function cancelConfirmed()
    {
        if ($this->status === 'confirmed') {
            // Restore inventory
            $this->restoreInventory();
            $this->status = 'cancelled';
            return $this->save();
        }
        return false;
    }

    /**
     * Restore inventory when order is cancelled (if it was confirmed)
     */
    public function restoreInventory()
    {
        if ($this->confirmed_at) {
            foreach ($this->items as $item) {
                $inventory = $item->product->inventory;
                if ($inventory) {
                    $oldQuantity = $inventory->quantity;
                    $inventory->increaseQuantity($item->quantity);
                    
                    // Log inventory restoration
                    \Log::info("Inventory restored for cancelled order {$this->order_number}: {$item->product->name} - {$oldQuantity} -> {$inventory->quantity} units (increased by {$item->quantity})");
                }
            }
        }
    }
}
