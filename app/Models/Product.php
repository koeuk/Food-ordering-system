<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HasUuidTrait;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    protected $appends = ['image_url'];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relationships

    /**
     * Get the category that owns this product
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the inventory record for this product
     */
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    /**
     * Get all order items for this product
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get all inventory order items for this product
     */
    public function inventoryOrderItems()
    {
        return $this->hasMany(InventoryOrderItem::class);
    }

    // Accessors

    /**
     * Get the full URL for the product image
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    // Helper methods

    /**
     * Check if product is in stock
     */
    public function isInStock()
    {
        return $this->inventory && $this->inventory->quantity > 0;
    }

    /**
     * Check if product stock is low
     */
    public function isLowStock()
    {
        return $this->inventory && $this->inventory->isLowStock();
    }

    /**
     * Get current stock quantity
     */
    public function getStockQuantity()
    {
        return $this->inventory ? $this->inventory->quantity : 0;
    }
}
