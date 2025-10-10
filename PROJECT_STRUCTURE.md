# 📁 Food Ordering System - Project Structure Documentation

This comprehensive guide outlines the organized structure of the Food Ordering System, clearly separating **Dashboard (Admin)** and **Web (Public)** sections.

## 📖 Table of Contents

1. [Complete Visual Directory Structure](#complete-visual-directory-structure)
2. [Structure Principles](#structure-principles)
3. [Structure Statistics](#structure-statistics)
4. [Route to File Mapping](#route-to-file-mapping)
5. [Component & Data Flow](#component--data-flow)
6. [Creating New Features](#creating-new-features)
7. [Naming Conventions](#naming-conventions)
8. [Import Aliases & Examples](#import-aliases--examples)
9. [Layout Usage](#layout-usage)
10. [Best Practices](#best-practices)
11. [Quick Navigation Guide](#quick-navigation-guide)
12. [Troubleshooting](#troubleshooting)
13. [Additional Resources](#additional-resources)

---

## Complete Visual Directory Structure

```
food-ordering-system/
│
├── 📁 app/
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/
│   │   │   ├── 📁 Auth/                    # 🔐 Authentication
│   │   │   │   ├── AuthenticatedSessionController.php
│   │   │   │   ├── RegisteredUserController.php
│   │   │   │   └── ... (9 files total)
│   │   │   │
│   │   │   ├── 📁 Dashboard/               # 👨‍💼 Admin Controllers
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── InventoryController.php
│   │   │   │   ├── InventoryOrderController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── RoleController.php
│   │   │   │   ├── SupplierController.php
│   │   │   │   └── UserController.php
│   │   │   │
│   │   │   └── 📁 Web/                     # 🌐 Public Controllers
│   │   │       ├── BillController.php
│   │   │       ├── OrderController.php
│   │   │       ├── ProductController.php
│   │   │       └── ProfileController.php
│   │   │
│   │   ├── 📁 Middleware/
│   │   └── 📁 Requests/
│   │
│   ├── 📁 Models/                          # 🗃️ Database Models
│   │   ├── Bill.php
│   │   ├── Category.php
│   │   ├── Inventory.php
│   │   ├── Order.php
│   │   ├── Product.php
│   │   ├── Supplier.php
│   │   └── User.php
│   │
│   └── 📁 Providers/
│
├── 📁 resources/
│   ├── 📁 js/
│   │   │
│   │   ├── 📁 Components/                  # 🧩 Reusable Components
│   │   │   │
│   │   │   ├── 📁 Dashboard/               # 👨‍💼 Admin Components
│   │   │   │   ├── StatsCard.vue           ✨ Statistics display card
│   │   │   │   ├── OrderStatusChip.vue     ✨ Order status indicator
│   │   │   │   └── QuickAction.vue         ✨ Quick action button
│   │   │   │
│   │   │   ├── 📁 Web/                     # 🌐 Public Components
│   │   │   │   ├── ProductCard.vue         ✨ Product display card
│   │   │   │   └── CartItem.vue            ✨ Shopping cart item
│   │   │   │
│   │   │   └── ComingSoon.vue              ✨ Shared placeholder
│   │   │
│   │   ├── 📁 Layouts/                     # 📐 Layout Templates
│   │   │   ├── DashboardLayout.vue         # Admin layout (purple sidebar)
│   │   │   └── AppLayout.vue               # Public layout (top nav)
│   │   │
│   │   ├── 📁 Pages/                       # 📄 Application Pages
│   │   │   │
│   │   │   ├── 📁 Dashboard/               # 👨‍💼 Admin Pages
│   │   │   │   ├── Admin.vue               # Main dashboard
│   │   │   │   ├── User.vue                # User dashboard
│   │   │   │   │
│   │   │   │   ├── 📁 Auth/
│   │   │   │   │   └── Login.vue
│   │   │   │   │
│   │   │   │   ├── 📁 Categories/
│   │   │   │   │   ├── Index.vue           # List categories
│   │   │   │   │   ├── Create.vue          # Add category
│   │   │   │   │   ├── Edit.vue            # Edit category
│   │   │   │   │   └── Show.vue            # View category
│   │   │   │   │
│   │   │   │   ├── 📁 Products/
│   │   │   │   │   ├── Index.vue           # List products
│   │   │   │   │   ├── Create.vue          # Add product
│   │   │   │   │   ├── Edit.vue            # Edit product
│   │   │   │   │   ├── Show.vue            # View product
│   │   │   │   │   └── Delete.vue          # Delete product
│   │   │   │   │
│   │   │   │   ├── 📁 Inventory/
│   │   │   │   │   ├── Index.vue           # List inventory
│   │   │   │   │   ├── Create.vue          # Add inventory
│   │   │   │   │   └── Edit.vue            # Edit inventory
│   │   │   │   │
│   │   │   │   ├── 📁 Orders/
│   │   │   │   │   ├── Index.vue           # List orders
│   │   │   │   │   ├── Show.vue            # View order
│   │   │   │   │   └── Edit.vue            # Edit order
│   │   │   │   │
│   │   │   │   ├── 📁 Suppliers/
│   │   │   │   │   ├── Index.vue           # List suppliers
│   │   │   │   │   ├── Create.vue          # Add supplier
│   │   │   │   │   └── Edit.vue            # Edit supplier
│   │   │   │   │
│   │   │   │   └── 📁 Reports/
│   │   │   │       ├── Sales.vue           # Sales reports
│   │   │   │       ├── Inventory.vue       # Inventory reports
│   │   │   │       └── Analytics.vue       # Analytics dashboard
│   │   │   │
│   │   │   ├── 📁 Web/                     # 🌐 Public Pages
│   │   │   │   │
│   │   │   │   ├── 📁 Auth/
│   │   │   │   │   ├── Login.vue           # Customer login
│   │   │   │   │   └── Register.vue        # Customer registration
│   │   │   │   │
│   │   │   │   ├── 📁 Products/
│   │   │   │   │   ├── Index.vue           # Product catalog
│   │   │   │   │   └── Show.vue            # Product details
│   │   │   │   │
│   │   │   │   ├── 📁 Cart/
│   │   │   │   │   └── Index.vue           # Shopping cart
│   │   │   │   │
│   │   │   │   ├── 📁 Orders/
│   │   │   │   │   ├── Index.vue           # Order history
│   │   │   │   │   └── Show.vue            # Order details
│   │   │   │   │
│   │   │   │   └── 📁 Profile/
│   │   │   │       └── Index.vue           # User profile
│   │   │   │
│   │   │   └── Welcome.vue                 # Landing page
│   │   │
│   │   ├── app.js                          # Main Vue app entry
│   │   └── bootstrap.js                    # Bootstrap file
│   │
│   ├── 📁 css/
│   │   └── app.css                         # Global styles
│   │
│   └── 📁 views/
│       └── app.blade.php                   # Main Blade template
│
├── 📁 routes/
│   ├── web.php                             # 🛣️ Web routes (main)
│   ├── api.php                             # 🔌 API routes
│   ├── auth.php                            # 🔐 Auth routes
│   └── console.php                         # 🖥️ Console commands
│
├── 📁 database/
│   ├── 📁 migrations/                      # Database migrations
│   ├── 📁 seeders/                         # Database seeders
│   └── database.sqlite                     # SQLite database
│
├── 📁 config/
│   ├── debugbar.php                        # ⚙️ Debugbar config (modified)
│   ├── app.php
│   ├── auth.php
│   └── ... (other config files)
│
├── 📁 storage/
│   ├── 📁 app/
│   ├── 📁 debugbar/                        # ⚠️ Cleared (was causing timeout)
│   ├── 📁 framework/
│   └── 📁 logs/
│
├── 📁 public/
│   ├── 📁 build/                           # Vite build output
│   │   ├── 📁 assets/
│   │   └── manifest.json
│   └── index.php
│
├── 📁 vendor/                              # Composer dependencies
├── 📁 node_modules/                        # NPM dependencies
│
├── 📄 PROJECT_STRUCTURE.md                 # 📚 This documentation
├── 📄 STRUCTURE_REORGANIZATION_SUMMARY.md  # 📝 Reorganization summary
├── 📄 QUICK_REFERENCE.md                   # 🚀 Quick reference guide
├── 📄 composer.json                        # PHP dependencies
├── 📄 package.json                         # Node dependencies
├── 📄 vite.config.js                       # Vite configuration
└── 📄 .env                                 # Environment variables
```

### 🎯 Icon Legend

- 📁 **Folder**
- 📄 **File**
- 🔐 **Authentication Related**
- 👨‍💼 **Admin/Dashboard Related**
- 🌐 **Public/Web Related**
- 🧩 **Components**
- 📐 **Layouts**
- 🗃️ **Models/Data**
- 🛣️ **Routes**
- ✨ **New Components Created**
- ⚙️ **Configuration**
- ⚠️ **Modified/Fixed**

---

## Structure Principles

### 1. **Separation of Concerns**
- **Dashboard**: Admin/management interface
- **Web**: Public-facing interface
- Each section has its own components, pages, and controllers

### 2. **Route Organization** (`routes/web.php`)

```php
// Public Web Routes
Route::prefix('web')->name('web.')->group(function () {
    Route::get('/products', [WebProductController::class, 'index'])->name('products.index');
    // ... more web routes
});

// Dashboard Routes (Admin)
Route::middleware(['auth', 'role:admin'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
        Route::resource('products', DashboardProductController::class);
        Route::resource('categories', CategoryController::class);
        // ... more dashboard routes
    });
```

### 3. **Controller Organization**

#### **Dashboard Controllers** (`app/Http/Controllers/Dashboard/`)
Handle admin operations:
- Product management (CRUD)
- Category management
- Inventory tracking
- Order management
- User management
- Reports & analytics

#### **Web Controllers** (`app/Http/Controllers/Web/`)
Handle public operations:
- Product browsing
- Order placement
- Profile management
- Bill viewing

### 4. **Component Organization**

#### **Dashboard Components** (`resources/js/Components/Dashboard/`)
Reusable admin components:
- **StatsCard.vue** - Statistics display card
- **OrderStatusChip.vue** - Order status indicator
- **QuickAction.vue** - Quick action button
- Data tables with sorting/filtering
- Admin-specific forms

#### **Web Components** (`resources/js/Components/Web/`)
Reusable public components:
- **ProductCard.vue** - Product display card
- **CartItem.vue** - Shopping cart item
- Navigation menus
- Customer-facing forms

### 5. **Page Organization**

#### **Dashboard Pages** (`resources/js/Pages/Dashboard/`)
Admin interface pages using `DashboardLayout.vue`

#### **Web Pages** (`resources/js/Pages/Web/`)
Public interface pages using `AppLayout.vue`

---

## Structure Statistics

### Backend (PHP/Laravel)
- **Controllers**: 21 files
  - Auth: 9 files
  - Dashboard: 8 files
  - Web: 4 files
- **Models**: 11 files
- **Migrations**: 15 files

### Frontend (Vue.js)
- **Components**: 6 files (5 new + 1 existing)
  - Dashboard: 3 components
  - Web: 2 components
  - Shared: 1 component
- **Layouts**: 2 files
- **Pages**: 25+ files
  - Dashboard: 15+ pages
  - Web: 10+ pages

### Routes
- **Web Routes**: ~40 routes
  - Dashboard: ~25 routes
  - Web: ~10 routes
  - Auth: ~5 routes

---

## Route to File Mapping

### Dashboard Routes → Controllers → Pages

| Route | Controller | Page |
|-------|-----------|------|
| `/dashboard/admin` | `Dashboard\DashboardController@adminDashboard` | `Dashboard/Admin.vue` |
| `/dashboard/products` | `Dashboard\ProductController@index` | `Dashboard/Products/Index.vue` |
| `/dashboard/categories` | `Dashboard\CategoryController@index` | `Dashboard/Categories/Index.vue` |
| `/dashboard/inventory` | `Dashboard\InventoryController@index` | `Dashboard/Inventory/Index.vue` |
| `/dashboard/orders` | `Dashboard\OrderController@index` | `Dashboard/Orders/Index.vue` |
| `/dashboard/suppliers` | `Dashboard\SupplierController@index` | `Dashboard/Suppliers/Index.vue` |
| `/dashboard/reports/sales` | `Dashboard\DashboardController@salesReport` | `Dashboard/Reports/Sales.vue` |

### Web Routes → Controllers → Pages

| Route | Controller | Page |
|-------|-----------|------|
| `/web/products` | `Web\ProductController@index` | `Web/Products/Index.vue` |
| `/web/products/{id}` | `Web\ProductController@show` | `Web/Products/Show.vue` |
| `/web/cart` | `Web\CartController@index` | `Web/Cart/Index.vue` |
| `/web/orders` | `Web\OrderController@index` | `Web/Orders/Index.vue` |
| `/web/orders/{id}` | `Web\OrderController@show` | `Web/Orders/Show.vue` |

---

## Component & Data Flow

### Dashboard Component Flow
```
Admin.vue
    ↓
DashboardLayout.vue
    ↓
├─ StatsCard.vue (for statistics)
├─ OrderStatusChip.vue (for order statuses)
└─ QuickAction.vue (for quick actions)
```

### Web Component Flow
```
Products/Index.vue
    ↓
AppLayout.vue
    ↓
└─ ProductCard.vue (for each product)
        ↓ (add to cart)
        ↓
Cart/Index.vue
    ↓
└─ CartItem.vue (for each cart item)
```

### Data Flow Diagram
```
User Request
    ↓
Route (web.php)
    ↓
Controller (Dashboard/ or Web/)
    ↓
Model (Eloquent)
    ↓
Database
    ↓
Controller (returns Inertia response)
    ↓
Page Component (Dashboard/ or Web/)
    ↓
Uses: Components (Dashboard/ or Web/)
    ↓
Rendered in: Layout (DashboardLayout or AppLayout)
    ↓
User sees result
```

---

## Creating New Features

### Adding a Dashboard Feature

1. **Create Controller**:
   ```bash
   php artisan make:controller Dashboard/FeatureController --resource
   ```

2. **Create Pages**:
   ```
   resources/js/Pages/Dashboard/Feature/
   ├── Index.vue
   ├── Create.vue
   ├── Edit.vue
   └── Show.vue
   ```

3. **Add Routes**:
   ```php
   Route::middleware(['auth', 'role:admin'])
       ->prefix('dashboard')
       ->name('dashboard.')
       ->group(function () {
           Route::resource('feature', FeatureController::class);
       });
   ```

4. **Update Navigation**:
   Add to `resources/js/Layouts/DashboardLayout.vue`

### Adding a Web Feature

1. **Create Controller**:
   ```bash
   php artisan make:controller Web/FeatureController
   ```

2. **Create Pages**:
   ```
   resources/js/Pages/Web/Feature/
   ├── Index.vue
   └── Show.vue
   ```

3. **Add Routes**:
   ```php
   Route::prefix('web')->name('web.')->group(function () {
       Route::get('/feature', [FeatureController::class, 'index'])->name('feature.index');
   });
   ```

4. **Update Navigation**:
   Add to `resources/js/Layouts/AppLayout.vue`

---

## Naming Conventions

### Routes
- **Dashboard**: `dashboard.resource.action` (e.g., `dashboard.products.index`)
- **Web**: `web.resource.action` (e.g., `web.products.index`)

### URLs
- **Dashboard**: `/dashboard/resource` (e.g., `/dashboard/products`)
- **Web**: `/web/resource` (e.g., `/web/products`)

### Controllers
- **Dashboard**: `Dashboard\ResourceController`
- **Web**: `Web\ResourceController`

### Components
- **Dashboard**: PascalCase (e.g., `StatsCard.vue`, `OrderStatusChip.vue`)
- **Web**: PascalCase (e.g., `ProductCard.vue`, `CartItem.vue`)

---

## Import Aliases & Examples

The project uses `@` alias for the `resources/js` directory:

```javascript
// Layouts
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

// Dashboard Components
import StatsCard from '@/Components/Dashboard/StatsCard.vue';
import OrderStatusChip from '@/Components/Dashboard/OrderStatusChip.vue';
import QuickAction from '@/Components/Dashboard/QuickAction.vue';

// Web Components
import ProductCard from '@/Components/Web/ProductCard.vue';
import CartItem from '@/Components/Web/CartItem.vue';

// Shared Components
import ComingSoon from '@/Components/ComingSoon.vue';

// Inertia
import { Head, Link, router, usePage } from '@inertiajs/vue3';
```

---

## Layout Usage

### Dashboard Pages
```vue
<template>
  <DashboardLayout>
    <Head title="Page Title" />
    <!-- Your content -->
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
</script>
```

### Web Pages
```vue
<template>
  <AppLayout>
    <Head title="Page Title" />
    <!-- Your content -->
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
</script>
```

---

## Best Practices

1. **Component Reusability**: Place shared components in the root `Components/` directory
2. **Feature-Specific Components**: Place in `Dashboard/` or `Web/` subdirectories
3. **Consistent Naming**: Follow the naming conventions outlined above
4. **Route Grouping**: Always use route groups for better organization
5. **Middleware**: Apply appropriate middleware (`auth`, `role:admin`, etc.)
6. **Import Paths**: Use the `@` alias for cleaner imports
7. **Small Components**: Keep components focused and single-purpose
8. **Props Validation**: Always validate props in components
9. **Emit Events**: Use events for parent-child communication
10. **Composition API**: Use `<script setup>` for new components

---

## Quick Navigation Guide

### Section Reference Table

| Section | Layout | Route Prefix | Controller Namespace | Component Path |
|---------|--------|--------------|---------------------|----------------|
| Dashboard | `DashboardLayout.vue` | `/dashboard` | `Dashboard\` | `Components/Dashboard/` |
| Web | `AppLayout.vue` | `/web` | `Web\` | `Components/Web/` |
| Shared | N/A | N/A | N/A | `Components/` |

### For Admin Tasks
1. Start at: `/dashboard/admin` → `Dashboard/Admin.vue`
2. Manage products: `/dashboard/products` → `Dashboard/Products/Index.vue`
3. View reports: `/dashboard/reports/sales` → `Dashboard/Reports/Sales.vue`

### For Public Tasks
1. Start at: `/` → `Welcome.vue`
2. Browse products: `/web/products` → `Web/Products/Index.vue`
3. View cart: `/web/cart` → `Web/Cart/Index.vue`

### For Development
1. Components: `resources/js/Components/[Dashboard|Web]/`
2. Pages: `resources/js/Pages/[Dashboard|Web]/`
3. Controllers: `app/Http/Controllers/[Dashboard|Web]/`
4. Routes: `routes/web.php`

---

## Troubleshooting

### Debugbar Timeout Issues
If you experience timeout errors with Laravel Debugbar:
1. Clear debugbar storage: `Remove-Item -Path "storage/debugbar/*" -Recurse -Force`
2. Disable storage in `config/debugbar.php`: Set `'enabled' => false` in storage array
3. Clear config cache: `php artisan config:clear`

### Route Not Found
1. Verify route name matches the format: `dashboard.*` or `web.*`
2. Check route list: `php artisan route:list`
3. Clear route cache: `php artisan route:clear`

### Component Not Found
1. Verify import path uses `@` alias
2. Check component exists in correct directory
3. Restart Vite dev server: `npm run dev`

### Vite Manifest Error
1. Ensure Vite dev server is running: `npm run dev`
2. Check `vite.config.js` configuration
3. Clear browser cache

### Cache Issues
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Vuetify Documentation](https://vuetifyjs.com/)
- [Material Design Icons](https://pictogrammers.com/library/mdi/)

---

**Last Updated**: October 10, 2025  
**Version**: 2.0.0 (Combined Documentation)  
**Legend**: ✨ = Newly created | ⚠️ = Modified | 📚 = Documentation
