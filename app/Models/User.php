<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Keep for backward compatibility
        'phone',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships

    /**
     * Get orders placed by this customer
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Get inventory orders created by this manager
     */
    public function inventoryOrders()
    {
        return $this->hasMany(InventoryOrder::class, 'manager_id');
    }

    /**
     * Get roles assigned to this user
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    // Helper methods

    public function isCustomer()
    {
        return $this->role === 'customer' || $this->hasRole('customer');
    }

    public function isManager()
    {
        return $this->role === 'manager' || $this->hasRole('manager');
    }

    public function isKitchen()
    {
        return $this->role === 'kitchen' || $this->hasRole('kitchen');
    }

    public function isSupplier()
    {
        return $this->role === 'supplier' || $this->hasRole('supplier');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        // First check the role field for backward compatibility
        if ($this->role === $roleName) {
            return true;
        }
        
        // Then check the dynamic roles
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Assign a role to the user
     */
    public function assignRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role && !$this->hasRole($roleName)) {
            $this->roles()->attach($role->id);
        }
    }

    /**
     * Remove a role from the user
     */
    public function removeRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role && $this->hasRole($roleName)) {
            $this->roles()->detach($role->id);
        }
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return $this->roles()->whereJsonContains('permissions', $permission)->exists();
    }

    /**
     * Get all permissions for the user
     */
    public function getAllPermissions(): array
    {
        return $this->roles()
            ->where('is_active', true)
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Get primary role (for backward compatibility)
     */
    public function getPrimaryRoleAttribute()
    {
        if ($this->role) {
            return $this->role;
        }
        
        $primaryRole = $this->roles()->where('is_active', true)->orderBy('sort_order')->first();
        return $primaryRole ? $primaryRole->name : 'customer';
    }
}
