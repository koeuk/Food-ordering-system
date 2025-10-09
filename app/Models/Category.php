<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HasUuidTrait;

    protected $fillable = [
        'name',
        'description',
    ];

    // Relationships

    /**
     * Get all products in this category
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get available products in this category
     */
    public function availableProducts()
    {
        return $this->hasMany(Product::class)->where('is_available', true);
    }
}
