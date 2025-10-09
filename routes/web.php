<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Role-based Dashboards
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return redirect()->route('dashboard.admin');
        } else {
            return redirect()->route('dashboard.user');
        }
    })->name('dashboard');

    Route::get('/dashboard/user', [DashboardController::class, 'userDashboard'])->name('dashboard.user');
    Route::get('/dashboard/admin', [DashboardController::class, 'adminDashboard'])->name('dashboard.admin');
});

// Public Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Authenticated Customer Routes
Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Bills
    Route::get('/bills/{bill}', [BillController::class, 'show'])->name('bills.show');
    Route::post('/bills/{bill}/payment', [BillController::class, 'processPayment'])->name('bills.payment');
    Route::get('/bills/{bill}/download', [BillController::class, 'download'])->name('bills.download');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Categories Management
    Route::resource('categories', CategoryController::class);
    
    // Products Management
    Route::resource('products', ProductController::class)->except(['index', 'show']);

    // Inventory Management
    Route::resource('inventory', InventoryController::class);
    Route::post('/inventory/{inventory}/restock', [InventoryController::class, 'restock'])->name('inventory.restock');
    Route::get('/inventory/alerts', [InventoryController::class, 'alerts'])->name('inventory.alerts');

    // Suppliers Management
    Route::resource('suppliers', SupplierController::class);

    // Inventory Orders
    Route::resource('inventory-orders', InventoryOrderController::class);
    Route::post('/inventory-orders/{inventoryOrder}/sent', [InventoryOrderController::class, 'markAsSent'])->name('inventory-orders.sent');
    Route::post('/inventory-orders/{inventoryOrder}/received', [InventoryOrderController::class, 'markAsReceived'])->name('inventory-orders.received');
    Route::post('/inventory-orders/{inventoryOrder}/cancel', [InventoryOrderController::class, 'cancel'])->name('inventory-orders.cancel');

    // Orders Management
    Route::resource('orders', OrderController::class);
    Route::post('/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    // Bills Management
    Route::resource('bills', BillController::class);
    Route::post('/bills/{bill}/refund', [BillController::class, 'refund'])->name('bills.refund');

    // Users Management
    Route::resource('users', UserController::class);

    // Roles Management
    Route::resource('roles', RoleController::class);
    Route::get('/user-roles', [RoleController::class, 'userRoleManagement'])->name('user-roles.index');
    Route::post('/roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
    Route::post('/roles/{role}/assign-user', [RoleController::class, 'assignToUser'])->name('roles.assign-user');
    Route::post('/roles/{role}/remove-user', [RoleController::class, 'removeFromUser'])->name('roles.remove-user');

    // Reports
    Route::get('/reports/sales', [DashboardController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/inventory', [DashboardController::class, 'inventoryReport'])->name('reports.inventory');
    Route::get('/reports/analytics', [DashboardController::class, 'analytics'])->name('reports.analytics');
});

require __DIR__.'/auth.php';
