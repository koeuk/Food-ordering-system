<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'contact_person',
    ];

    // Relationships

    /**
     * Get all inventory orders for this supplier
     */
    public function inventoryOrders()
    {
        return $this->hasMany(InventoryOrder::class);
    }

    /**
     * Get pending inventory orders
     */
    public function pendingOrders()
    {
        return $this->hasMany(InventoryOrder::class)->where('status', 'pending');
    }
}
