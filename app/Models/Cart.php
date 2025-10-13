<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory, HasUuidTrait;

    protected $fillable = [
        'user_id',
        'session_id',
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
     * Get the user that owns the cart
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cart items
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the total amount of the cart
     */
    public function getTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return floatval($item->price) * $item->quantity;
        });
    }

    /**
     * Get the total number of items in the cart
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }
}
