<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::query()
            ->withCount('users')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('display_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->type, function ($query, $type) {
                if ($type === 'system') {
                    $query->where('is_system', true);
                } elseif ($type === 'custom') {
                    $query->where('is_system', false);
                }
            })
            ->when($request->status, function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        // Add computed fields
        $roles->getCollection()->transform(function ($role) {
            $role->can_be_deleted = $role->canBeDeleted();
            return $role;
        });

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search', 'type', 'status']),
            'availablePermissions' => Role::getAvailablePermissions(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Roles/Create', [
            'availablePermissions' => Role::getAvailablePermissions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $role = Role::create($validated);

        return redirect()->route('manager.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load(['users' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return Inertia::render('Roles/Show', [
            'role' => $role,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'availablePermissions' => Role::getAvailablePermissions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        // Prevent editing system roles
        if ($role->is_system) {
            return redirect()->route('manager.roles.index')
                ->with('error', 'System roles cannot be modified.');
        }

        $role->update($validated);

        return redirect()->route('manager.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Check if role can be deleted
        if (!$role->canBeDeleted()) {
            $reason = $role->is_system ? 'System roles cannot be deleted.' : 'Role has assigned users and cannot be deleted.';
            return redirect()->route('manager.roles.index')
                ->with('error', $reason);
        }

        $role->delete();

        return redirect()->route('manager.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Assign role to user
     */
    public function assignToUser(Request $request, Role $role)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        
        if (!$user->hasRole($role->name)) {
            $user->assignRole($role->name);
            
            return redirect()->back()
                ->with('success', "Role '{$role->display_name}' assigned to user successfully.");
        }

        return redirect()->back()
            ->with('error', 'User already has this role.');
    }

    /**
     * Remove role from user
     */
    public function removeFromUser(Request $request, Role $role)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->removeRole($role->name);

        return redirect()->back()
            ->with('success', "Role '{$role->display_name}' removed from user successfully.");
    }

    /**
     * Toggle role active status
     */
    public function toggleStatus(Role $role)
    {
        // Prevent deactivating system roles
        if ($role->is_system && !$role->is_active) {
            return redirect()->back()
                ->with('error', 'System roles cannot be deactivated.');
        }

        $role->update(['is_active' => !$role->is_active]);

        $status = $role->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Role '{$role->display_name}' {$status} successfully.");
    }

    /**
     * User role management page
     */
    public function userRoleManagement(Request $request)
    {
        $users = User::query()
            ->with(['roles'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->role, function ($query, $roleId) {
                $query->whereHas('roles', function ($q) use ($roleId) {
                    $q->where('role_id', $roleId);
                });
            })
            ->when($request->status, function ($query, $status) {
                if ($status === 'verified') {
                    $query->whereNotNull('email_verified_at');
                } elseif ($status === 'pending') {
                    $query->whereNull('email_verified_at');
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $roles = Role::active()->orderBy('sort_order')->get();

        // Calculate stats
        $stats = [
            'total_users' => User::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'users_with_roles' => User::whereHas('roles')->count(),
            'total_roles' => Role::active()->count(),
        ];

        return Inertia::render('Roles/UserManagement', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $request->only(['search', 'role', 'status']),
            'stats' => $stats,
        ]);
    }
}
