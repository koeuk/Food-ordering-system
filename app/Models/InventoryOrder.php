<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'manager_id',
        'order_number',
        'status',
        'total_amount',
        'sent_at',
        'received_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    // Relationships

    /**
     * Get the supplier for this order
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the manager who created this order
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get all items in this inventory order
     */
    public function items()
    {
        return $this->hasMany(InventoryOrderItem::class);
    }

    // Helper methods

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber()
    {
        return 'INV-' . strtoupper(uniqid());
    }

    /**
     * Mark order as sent
     */
    public function markAsSent()
    {
        $this->status = 'sent';
        $this->sent_at = now();
        return $this->save();
    }

    /**
     * Mark order as received and update inventory
     */
    public function markAsReceived()
    {
        $this->status = 'received';
        $this->received_at = now();

        // Update inventory for all items
        foreach ($this->items as $item) {
            $inventory = $item->product->inventory;
            if ($inventory) {
                $inventory->increaseQuantity($item->quantity);
            }
        }

        return $this->save();
    }

    /**
     * Cancel the order
     */
    public function cancel()
    {
        $this->status = 'cancelled';
        return $this->save();
    }
}
