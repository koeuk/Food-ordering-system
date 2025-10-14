<?php

use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\ProductController as WebProductController;
use App\Http\Controllers\Web\OrderController as WebOrderController;
use App\Http\Controllers\Dashboard\ProductController as DashboardProductController;
use App\Http\Controllers\Web\BillController;
use App\Http\Controllers\Dashboard\BillController as DashboardBillController;
use App\Http\Controllers\Dashboard\InventoryController;
use App\Http\Controllers\Dashboard\InventoryOrderController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\SupplierController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\OrderController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // If user is authenticated, show the home page with navigation
    if (\Illuminate\Support\Facades\Auth::check()) {
        return Inertia::render('Welcome');
    }
    
    // If not authenticated, show the welcome page
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Home page for authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        return Inertia::render('Welcome');
    })->name('home');
});

// Role-based Dashboards
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        if ($user && $user->role === 'admin') {
            return redirect()->route('dashboard.admin');
        } else {
            return redirect()->route('dashboard.user');
        }
    })->name('dashboard');

    Route::get('/dashboard/user', [DashboardController::class, 'userDashboard'])->name('dashboard.user');
    Route::get('/dashboard/admin', [DashboardController::class, 'adminDashboard'])->name('dashboard.admin');
});

// Public Web Routes (Web Interface)
Route::prefix('web')->name('web.')->group(function () {
    // Products (Public Menu)
    Route::get('/products', [WebProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product:uuid}', [WebProductController::class, 'show'])->name('products.show');
    Route::get('/products/random', [WebProductController::class, 'random'])->name('products.random');
    Route::get('/products/related/{product:uuid}', [WebProductController::class, 'related'])->name('products.related');
    
    // Cart
    Route::get('/cart', [\App\Http\Controllers\Web\CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/add', [\App\Http\Controllers\Web\CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/items/{cartItem:uuid}', [\App\Http\Controllers\Web\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/items/{cartItem:uuid}', [\App\Http\Controllers\Web\CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [\App\Http\Controllers\Web\CartController::class, 'clear'])->name('cart.clear');
    
    // Orders (User Orders)
    Route::get('/orders', [WebOrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [WebOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [WebOrderController::class, 'show'])->name('orders.show');
});

// Legacy public products route (redirect to web)
Route::get('/products', function () {
    return redirect()->route('web.products.index');
});

// Blog Routes
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\BlogController::class, 'index'])->name('index');
    Route::get('/about', [\App\Http\Controllers\Web\BlogController::class, 'about'])->name('about');
    Route::get('/vision', [\App\Http\Controllers\Web\BlogController::class, 'vision'])->name('vision');
    Route::get('/policy', [\App\Http\Controllers\Web\BlogController::class, 'policy'])->name('policy');
});

// Public API Routes (no authentication required)
Route::prefix('api/public')->name('api.public.')->group(function () {
    Route::get('/products/featured', [\App\Http\Controllers\Web\ProductController::class, 'getFeaturedProducts'])->name('products.featured');
});

// Authenticated Customer Routes
Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Orders (Customer Orders)
    Route::get('/my-orders', [WebOrderController::class, 'index'])->name('my-orders.index');
    Route::get('/my-orders/{order:uuid}', [WebOrderController::class, 'show'])->name('my-orders.show');
    Route::post('/orders/{order:uuid}/cancel', [WebOrderController::class, 'cancel'])->name('orders.cancel');

    // Bills
    Route::get('/bills/{bill}', [BillController::class, 'show'])->name('bills.show');
    Route::post('/bills/{bill}/payment', [BillController::class, 'processPayment'])->name('bills.payment');
    Route::get('/bills/{bill}/download', [BillController::class, 'download'])->name('bills.download');
});

// Dashboard Routes (Admin Interface)
Route::middleware(['auth', 'role:admin'])->prefix('dashboard')->name('dashboard.')->group(function () {
    // Categories Management
    Route::resource('categories', CategoryController::class)->parameters([
        'categories' => 'category:uuid'
    ]);
    
    // Products Management
    Route::resource('products', DashboardProductController::class)->parameters([
        'products' => 'product:uuid'
    ]);

    // Inventory Management
    Route::resource('inventory', InventoryController::class)->parameters([
        'inventory' => 'inventory:uuid'
    ]);
    Route::post('/inventory/{inventory}/restock', [InventoryController::class, 'restock'])->name('inventory.restock');
    Route::get('/inventory/alerts', [InventoryController::class, 'alerts'])->name('inventory.alerts');

    // Suppliers Management
    Route::resource('suppliers', SupplierController::class)->parameters([
        'suppliers' => 'supplier:uuid'
    ]);

    // Inventory Orders
    Route::resource('inventory-orders', InventoryOrderController::class)->parameters([
        'inventory-orders' => 'inventoryOrder:uuid'
    ]);
    Route::post('/inventory-orders/{inventoryOrder}/sent', [InventoryOrderController::class, 'markAsSent'])->name('inventory-orders.sent');
    Route::post('/inventory-orders/{inventoryOrder}/received', [InventoryOrderController::class, 'markAsReceived'])->name('inventory-orders.received');
    Route::post('/inventory-orders/{inventoryOrder}/cancel', [InventoryOrderController::class, 'cancel'])->name('inventory-orders.cancel');

    // Orders Management
    Route::resource('orders', OrderController::class)->parameters([
        'orders' => 'order:uuid'
    ]);
    Route::post('/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    // Bills Management
    Route::resource('bills', DashboardBillController::class)->parameters([
        'bills' => 'bill:uuid'
    ]);
    Route::post('/bills/{bill}/refund', [DashboardBillController::class, 'refund'])->name('bills.refund');

    // Users Management
    Route::resource('users', UserController::class)->parameters([
        'users' => 'user:uuid'
    ]);

    // Roles Management
    Route::resource('roles', RoleController::class)->parameters([
        'roles' => 'role:uuid'
    ]);
    Route::get('/user-roles', [RoleController::class, 'userRoleManagement'])->name('user-roles.index');
    Route::post('/roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
    Route::post('/roles/{role}/assign-user', [RoleController::class, 'assignToUser'])->name('roles.assign-user');
    Route::post('/roles/{role}/remove-user', [RoleController::class, 'removeFromUser'])->name('roles.remove-user');

    // Reports
    Route::get('/reports/sales', [DashboardController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/inventory', [DashboardController::class, 'inventoryReport'])->name('reports.inventory');
    Route::get('/reports/analytics', [DashboardController::class, 'analytics'])->name('reports.analytics');
    
    // API Routes for Dashboard Data
    Route::get('/api/sales-analytics', [DashboardController::class, 'getSalesAnalytics'])->name('api.sales-analytics');
    Route::get('/api/dashboard-stats', [DashboardController::class, 'getDashboardStats'])->name('api.dashboard-stats');
    Route::get('/api/order-status-analytics', [DashboardController::class, 'getOrderStatusAnalytics'])->name('api.order-status-analytics');
});

require __DIR__.'/auth.php';
