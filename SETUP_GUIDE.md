# Food Ordering System - Setup Guide

## 🚀 Quick Start Guide

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL 8.0
- Node.js & NPM
- Git

### Installation Steps

#### 1. Environment Configuration

Copy the environment file:
```bash
cp .env.example .env
```

Update the `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=food_ordering_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### 2. Install Dependencies

Install PHP dependencies:
```bash
composer install
```

Install Node dependencies:
```bash
npm install
```

#### 3. Generate Application Key

```bash
php artisan key:generate
```

#### 4. Run Database Migrations

Create the database:
```bash
mysql -u your_username -p
CREATE DATABASE food_ordering_system;
EXIT;
```

Run migrations:
```bash
php artisan migrate
```

#### 5. Create Storage Link

```bash
php artisan storage:link
```

#### 6. Seed Database (Optional)

Create a database seeder or manually add test data:
```bash
php artisan db:seed
```

#### 7. Build Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

#### 8. Start Development Server

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

---

## 📋 Database Schema Overview

### Tables Created:
1. **users** - System users (customers, managers, kitchen staff, suppliers)
2. **categories** - Product categories
3. **products** - Food items
4. **inventory** - Stock management
5. **orders** - Customer orders
6. **order_items** - Order line items
7. **bills** - Payment tracking
8. **suppliers** - Supplier information
9. **inventory_orders** - Restock orders
10. **inventory_order_items** - Restock order items

---

## 🎯 User Roles & Access

### Customer
- Browse products/menu
- Place orders
- View order history
- Make payments
- Cancel pending orders

### Manager
- All customer features
- Manage products (CRUD)
- Manage inventory
- Create supplier orders
- Generate reports (sales, inventory)
- Process refunds

### Kitchen
- View pending orders
- Update order status (preparing, ready)
- View order details

### Supplier
- View inventory orders
- Update delivery status

---

## 🔐 Creating Test Users

Use Laravel Tinker to create users:

```bash
php artisan tinker
```

Then run:

```php
// Create Customer
\App\Models\User::create([
    'name' => 'John Customer',
    'email' => 'customer@test.com',
    'password' => bcrypt('password'),
    'role' => 'customer',
    'phone' => '1234567890',
    'address' => '123 Main St'
]);

// Create Manager
\App\Models\User::create([
    'name' => 'Jane Manager',
    'email' => 'manager@test.com',
    'password' => bcrypt('password'),
    'role' => 'manager',
    'phone' => '0987654321',
    'address' => '456 Admin Ave'
]);

// Create Kitchen Staff
\App\Models\User::create([
    'name' => 'Bob Kitchen',
    'email' => 'kitchen@test.com',
    'password' => bcrypt('password'),
    'role' => 'kitchen',
    'phone' => '5555555555',
]);
```

---

## 📦 Sample Data Creation

### Create Categories

```php
$categories = [
    ['name' => 'Appetizers', 'description' => 'Start your meal right'],
    ['name' => 'Main Course', 'description' => 'Our signature dishes'],
    ['name' => 'Desserts', 'description' => 'Sweet endings'],
    ['name' => 'Beverages', 'description' => 'Refreshing drinks'],
];

foreach ($categories as $cat) {
    \App\Models\Category::create($cat);
}
```

### Create Products

```php
$product = \App\Models\Product::create([
    'category_id' => 1,
    'name' => 'Spring Rolls',
    'description' => 'Crispy vegetable spring rolls served with sweet chili sauce',
    'price' => 8.99,
    'is_available' => true,
]);

// Create inventory for the product
$product->inventory()->create([
    'quantity' => 50,
    'minimum_stock' => 10,
]);
```

### Create Supplier

```php
\App\Models\Supplier::create([
    'name' => 'Fresh Foods Ltd',
    'email' => 'supplier@freshfoods.com',
    'phone' => '1112223333',
    'address' => '789 Supplier Blvd',
    'contact_person' => 'Mike Supplier',
]);
```

---

## 🌐 Routes Overview

### Public Routes
- `GET /` - Welcome page
- `GET /products` - Browse menu
- `GET /products/{product}` - Product details

### Customer Routes (Auth Required)
- `GET /dashboard` - Customer dashboard
- `GET /orders` - Order history
- `POST /orders` - Place new order
- `GET /orders/{order}` - Order details
- `POST /bills/{bill}/payment` - Process payment

### Manager Routes
- `GET /dashboard/manager` - Manager dashboard
- `GET /manager/inventory` - Inventory management
- `GET /manager/inventory-orders` - Supplier orders
- `GET /manager/reports/sales` - Sales report
- `GET /manager/reports/inventory` - Inventory report

### Kitchen Routes
- `GET /dashboard/kitchen` - Kitchen dashboard
- `GET /kitchen/orders` - Pending orders
- `PATCH /kitchen/orders/{order}/status` - Update order status

### API Routes
- `GET /api/v1/products` - Get available products
- `POST /api/v1/orders` - Create order (auth required)
- `GET /api/v1/inventory/low-stock` - Get low stock items

---

## 🔧 Configuration

### Business Rules (configurable in code)

- **Minimum Order Amount**: $10
- **Tax Rate**: 10%
- **Low Stock Threshold**: 10 units (per product)
- **Refund Period**: 24 hours after delivery

### Email Configuration

Update `.env` for email notifications:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@foodordering.com
MAIL_FROM_NAME="Food Ordering System"
```

---

## 📊 Testing

Run tests:
```bash
php artisan test
```

---

## 🐛 Troubleshooting

### Migration Errors
```bash
php artisan migrate:fresh
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Permission Issues (Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
```

---

## 📁 Project Structure

```
food-ordering-system/
├── app/
│   ├── Http/Controllers/
│   │   ├── ProductController.php
│   │   ├── OrderController.php
│   │   ├── BillController.php
│   │   ├── InventoryController.php
│   │   ├── InventoryOrderController.php
│   │   └── DashboardController.php
│   └── Models/
│       ├── User.php
│       ├── Category.php
│       ├── Product.php
│       ├── Inventory.php
│       ├── Order.php
│       ├── OrderItem.php
│       ├── Bill.php
│       ├── Supplier.php
│       ├── InventoryOrder.php
│       └── InventoryOrderItem.php
├── database/
│   └── migrations/
│       └── 2024_01_01_* (10 migration files)
├── resources/
│   └── views/
│       ├── layouts/app.blade.php
│       ├── dashboard/customer.blade.php
│       ├── products/index.blade.php
│       ├── orders/show.blade.php
│       └── inventory/index.blade.php
└── routes/
    ├── web.php
    └── api.php
```

---

## 🚀 Deployment

### Production Checklist

1. Set environment to production:
```env
APP_ENV=production
APP_DEBUG=false
```

2. Optimize application:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

3. Build assets:
```bash
npm run build
```

4. Set up queue worker (for background jobs):
```bash
php artisan queue:work --daemon
```

5. Configure web server (Apache/Nginx)

---

## 📝 License

This project is for educational purposes - System Analysis & Design Assignment

## 👥 Contributors

- System Analyst - Beltie University Year 3 SE

---

**Document Version:** 1.0
**Last Updated:** 2025
**For Support:** Contact your instructor
