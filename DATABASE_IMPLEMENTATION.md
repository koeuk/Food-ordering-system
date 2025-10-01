# Food Ordering System - Database Implementation & Relationships

## Table of Contents
1. [Database Schema Overview](#database-schema-overview)
2. [Table Definitions](#table-definitions)
3. [Relationship Mapping](#relationship-mapping)
4. [ER Diagram Summary](#er-diagram-summary)
5. [Implementation Guidelines](#implementation-guidelines)

---

## Database Schema Overview

The Food Ordering System consists of **10 core tables** that manage users, products, orders, inventory, suppliers, and billing. The database follows **Third Normal Form (3NF)** principles for optimal data integrity.

### Technology Stack
- **Backend**: Laravel 10+ (PHP)
- **Database**: MySQL 8.0
- **Authentication**: Laravel Sanctum/Passport
- **Deployment**: Docker, AWS/DigitalOcean

---

## Table Definitions

### 1. USERS Table
**Purpose**: Stores all system users with role-based access control

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT (PK) | AUTO_INCREMENT | Primary key |
| name | VARCHAR(255) | NOT NULL | User full name |
| email | VARCHAR(255) | UNIQUE, NOT NULL | User email (login) |
| password | VARCHAR(255) | NOT NULL | Hashed password (bcrypt) |
| role | ENUM | NOT NULL | 'customer', 'manager', 'kitchen', 'supplier' |
| phone | VARCHAR(20) | NULLABLE | Contact number |
| address | TEXT | NULLABLE | Physical address |
| created_at | TIMESTAMP | | Record creation time |
| updated_at | TIMESTAMP | | Last update time |

**Relationships:**
- One-to-Many: `users(customer) → orders`
- One-to-Many: `users(manager) → inventory_orders`

---

### 2. CATEGORIES Table
**Purpose**: Product categorization (Appetizers, Main Course, Desserts, etc.)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT (PK) | AUTO_INCREMENT | Primary key |
| name | VARCHAR(255) | NOT NULL | Category name |
| description | TEXT | NULLABLE | Category description |
| created_at | TIMESTAMP | | Record creation time |
| updated_at | TIMESTAMP | | Last update time |

**Relationships:**
- One-to-Many: `categories → products`

---

### 3. PRODUCTS Table
**Purpose**: Food items available for ordering

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT (PK) | AUTO_INCREMENT | Primary key |
| category_id | INT (FK) | NOT NULL | References categories(id) |
| name | VARCHAR(255) | NOT NULL | Product name |
| description | TEXT | NULLABLE | Product description |
| price | DECIMAL(10,2) | NOT NULL | Product price |
| image | VARCHAR(255) | NULLABLE | Image file path |
| is_available | BOOLEAN | DEFAULT true | Availability status |
| created_at | TIMESTAMP | | Record creation time |
| updated_at | TIMESTAMP | | Last update time |

**Foreign Keys:**
- `category_id` → `categories(id)` ON DELETE CASCADE

**Relationships:**
- Many-to-One: `products → categories`
- One-to-One: `products ↔ inventory`
- One-to-Many: `products → order_items`
- One-to-Many: `products → inventory_order_items`

---

### 4. INVENTORY Table
**Purpose**: Tracks stock levels for each product

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT (PK) | AUTO_INCREMENT | Primary key |
| product_id | INT (FK) | UNIQUE, NOT NULL | References products(id) |
| quantity | INT | NOT NULL | Current stock quantity |
| minimum_stock | INT | DEFAULT 10 | Low stock threshold |
| last_restocked_at | TIMESTAMP | NULLABLE | Last restock timestamp |
| created_at | TIMESTAMP | | Record creation time |
| updated_at | TIMESTAMP | | Last update time |

**Foreign Keys:**
- `product_id` → `products(id)` ON DELETE CASCADE, UNIQUE

**Relationships:**
- One-to-One: `inventory ↔ products`

---

### 5. ORDERS Table
**Purpose**: Customer food orders with lifecycle tracking

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT (PK) | AUTO_INCREMENT | Primary key |
| customer_id | INT (FK) | NOT NULL | References users(id) |
| order_number | VARCHAR(50) | UNIQUE, NOT NULL | Unique order identifier |
| status | ENUM | DEFAULT 'pending' | Order status lifecycle |
| subtotal | DECIMAL(10,2) | NOT NULL | Order subtotal |
| tax | DECIMAL(10,2) | DEFAULT 0 | Tax amount (10%) |
| total | DECIMAL(10,2) | NOT NULL | Total amount |
| delivery_address | TEXT | NULLABLE | Delivery address |
| notes | TEXT | NULLABLE | Special instructions |
| confirmed_at | TIMESTAMP | NULLABLE | Confirmation timestamp |
| delivered_at | TIMESTAMP | NULLABLE | Delivery timestamp |
| created_at | TIMESTAMP | | Record creation time |
| updated_at | TIMESTAMP | | Last update time |

**Status Values:** 'pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'

**Foreign Keys:**
- `customer_id` → `users(id)` ON DELETE CASCADE

**Relationships:**
- Many-to-One: `orders → users(customer)`
- One-to-Many: `orders → order_items`
- One-to-One: `orders ↔ bills`

---

### 6. ORDER_ITEMS Table
**Purpose**: Junction table between orders and products (individual items in an order)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT (PK) | AUTO_INCREMENT | Primary key |
| order_id | INT (FK) | NOT NULL | References orders(id) |
| product_id | INT (FK) | NOT NULL | References products(id) |
| quantity | INT | NOT NULL | Item quantity |
| unit_price | DECIMAL(10,2) | NOT NULL | Price per unit |
| subtotal | DECIMAL(10,2) | NOT NULL | Line item total |
| special_instructions | TEXT | NULLABLE | Item-specific notes |
| created_at | TIMESTAMP | | Record creation time |
| updated_at | TIMESTAMP | | Last update time |

**Foreign Keys:**
- `order_id` → `orders(id)` ON DELETE CASCADE
- `product_id` → `products(id)` ON DELETE CASCADE

**Relationships:**
- Many-to-One: `order_items → orders`
- Many-to-One: `order_items → products`

---

### 7. BILLS Table
**Purpose**: Payment information and tracking for orders

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT (PK) | AUTO_INCREMENT | Primary key |
| order_id | INT (FK) | UNIQUE, NOT NULL | References orders(id) |
| bill_number | VARCHAR(50) | UNIQUE, NOT NULL | Unique bill identifier |
| amount | DECIMAL(10,2) | NOT NULL | Bill amount |
| payment_status | ENUM | DEFAULT 'unpaid' | Payment status |
| payment_method | ENUM | NULLABLE | Payment method used |
| paid_at | TIMESTAMP | NULLABLE | Payment timestamp |
| created_at | TIMESTAMP | | Record creation time |
| updated_at | TIMESTAMP | | Last update time |

**Payment Status:** 'unpaid', 'paid', 'refunded'
**Payment Methods:** 'cash', 'card', 'online'

**Foreign Keys:**
- `order_id` → `orders(id)` ON DELETE CASCADE, UNIQUE

**Relationships:**
- One-to-One: `bills ↔ orders`

---

### 8. SUPPLIERS Table
**Purpose**: External vendors who supply ingredients/products

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT (PK) | AUTO_INCREMENT | Primary key |
| name | VARCHAR(255) | NOT NULL | Supplier name |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Supplier email |
| phone | VARCHAR(20) | NOT NULL | Contact number |
| address | TEXT | NOT NULL | Supplier address |
| contact_person | VARCHAR(255) | NULLABLE | Contact person name |
| created_at | TIMESTAMP | | Record creation time |
| updated_at | TIMESTAMP | | Last update time |

**Relationships:**
- One-to-Many: `suppliers → inventory_orders`

---

### 9. INVENTORY_ORDERS Table
**Purpose**: Orders placed to suppliers for restocking (managed by managers)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT (PK) | AUTO_INCREMENT | Primary key |
| supplier_id | INT (FK) | NOT NULL | References suppliers(id) |
| manager_id | INT (FK) | NOT NULL | References users(id) |
| order_number | VARCHAR(50) | UNIQUE, NOT NULL | Unique order identifier |
| status | ENUM | DEFAULT 'pending' | Order status |
| total_amount | DECIMAL(10,2) | NOT NULL | Total order cost |
| sent_at | TIMESTAMP | NULLABLE | Sent to supplier timestamp |
| received_at | TIMESTAMP | NULLABLE | Received timestamp |
| created_at | TIMESTAMP | | Record creation time |
| updated_at | TIMESTAMP | | Last update time |

**Status Values:** 'pending', 'sent', 'received', 'cancelled'

**Foreign Keys:**
- `supplier_id` → `suppliers(id)` ON DELETE CASCADE
- `manager_id` → `users(id)` ON DELETE CASCADE

**Relationships:**
- Many-to-One: `inventory_orders → suppliers`
- Many-to-One: `inventory_orders → users(manager)`
- One-to-Many: `inventory_orders → inventory_order_items`

---

### 10. INVENTORY_ORDER_ITEMS Table
**Purpose**: Junction table between inventory orders and products

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT (PK) | AUTO_INCREMENT | Primary key |
| inventory_order_id | INT (FK) | NOT NULL | References inventory_orders(id) |
| product_id | INT (FK) | NOT NULL | References products(id) |
| quantity | INT | NOT NULL | Quantity ordered |
| unit_cost | DECIMAL(10,2) | NOT NULL | Cost per unit |
| subtotal | DECIMAL(10,2) | NOT NULL | Line item total |
| created_at | TIMESTAMP | | Record creation time |
| updated_at | TIMESTAMP | | Last update time |

**Foreign Keys:**
- `inventory_order_id` → `inventory_orders(id)` ON DELETE CASCADE
- `product_id` → `products(id)` ON DELETE CASCADE

**Relationships:**
- Many-to-One: `inventory_order_items → inventory_orders`
- Many-to-One: `inventory_order_items → products`

---

## Relationship Mapping

### One-to-Many Relationships

1. **users(customer) → orders**
   - One customer can place multiple orders
   - FK: `orders.customer_id` → `users.id`

2. **users(manager) → inventory_orders**
   - One manager can create multiple inventory orders
   - FK: `inventory_orders.manager_id` → `users.id`

3. **categories → products**
   - One category contains multiple products
   - FK: `products.category_id` → `categories.id`

4. **orders → order_items**
   - One order contains multiple items
   - FK: `order_items.order_id` → `orders.id`

5. **suppliers → inventory_orders**
   - One supplier receives multiple inventory orders
   - FK: `inventory_orders.supplier_id` → `suppliers.id`

6. **inventory_orders → inventory_order_items**
   - One inventory order contains multiple items
   - FK: `inventory_order_items.inventory_order_id` → `inventory_orders.id`

7. **products → order_items**
   - One product appears in multiple order items
   - FK: `order_items.product_id` → `products.id`

8. **products → inventory_order_items**
   - One product appears in multiple inventory order items
   - FK: `inventory_order_items.product_id` → `products.id`

### One-to-One Relationships

1. **products ↔ inventory**
   - Each product has exactly one inventory record
   - FK: `inventory.product_id` → `products.id` (UNIQUE)

2. **orders ↔ bills**
   - Each order has exactly one bill
   - FK: `bills.order_id` → `orders.id` (UNIQUE)

---

## ER Diagram Summary

### Entity Participation
- **USERS**: Central entity for authentication and role management
- **CATEGORIES**: Classification entity for products
- **PRODUCTS**: Core menu items entity
- **INVENTORY**: Stock management entity (1:1 with products)
- **ORDERS**: Customer order transaction entity
- **ORDER_ITEMS**: Order-Product junction entity (many-to-many bridge)
- **BILLS**: Payment tracking entity (1:1 with orders)
- **SUPPLIERS**: External vendor entity
- **INVENTORY_ORDERS**: Supplier order entity
- **INVENTORY_ORDER_ITEMS**: Inventory Order-Product junction entity

### Cardinality Summary
```
users(customer) --(1:N)-- orders --(1:1)-- bills
users(manager) --(1:N)-- inventory_orders
categories --(1:N)-- products --(1:1)-- inventory
products --(1:N)-- order_items --(N:1)-- orders
products --(1:N)-- inventory_order_items --(N:1)-- inventory_orders
suppliers --(1:N)-- inventory_orders
```

---

## Implementation Guidelines

### Database Indexes (Performance Optimization)

```sql
-- Performance indexes
CREATE INDEX idx_orders_customer ON orders(customer_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_inventory_product ON inventory(product_id);
CREATE INDEX idx_bills_order ON bills(order_id);
CREATE INDEX idx_inv_orders_supplier ON inventory_orders(supplier_id);
CREATE INDEX idx_inv_orders_manager ON inventory_orders(manager_id);
```

### Business Rules Implementation

1. **Order Validation**
   - Minimum order amount: $10
   - Tax rate: 10% (configurable)
   - Order cancellation: Only if status is 'pending'
   - Refunds: Within 24 hours of delivery

2. **Inventory Management**
   - Low stock threshold: 10 units (configurable)
   - Auto-alert when `quantity <= minimum_stock`
   - Update inventory after order confirmation

3. **Role-Based Access**
   - **Customer**: Place orders, view own orders, make payments
   - **Manager**: Generate reports, manage inventory, create supplier orders
   - **Kitchen**: View pending orders, update order status
   - **Supplier**: View inventory orders, update delivery status

### Security Considerations

1. **Password Security**
   - Hash all passwords using bcrypt
   - Minimum password length: 8 characters

2. **API Security**
   - Implement CSRF protection
   - Use Laravel Sanctum for API authentication
   - Rate limiting on all endpoints

3. **Input Validation**
   - Validate and sanitize all user inputs
   - Use Laravel Form Requests for validation
   - Prevent SQL injection with prepared statements

4. **Production Security**
   - Use HTTPS only
   - Implement secure session management
   - Enable database encryption at rest

### Data Flow Summary

**Customer Order Flow:**
```
Customer → Browse Products → Add to Cart → Checkout
→ Create Order → Process Payment → Create Bill
→ Update Inventory → Notify Kitchen → Prepare Order
→ Update Status → Deliver → Complete
```

**Manager Inventory Flow:**
```
Manager → View Low Stock Alerts → Create Inventory Order
→ Send to Supplier → Supplier Confirms → Receive Delivery
→ Update Inventory Levels → Complete Order
```

---

## Laravel Migration Order

Execute migrations in this order to respect foreign key constraints:

1. `create_users_table`
2. `create_categories_table`
3. `create_products_table`
4. `create_inventory_table`
5. `create_orders_table`
6. `create_order_items_table`
7. `create_bills_table`
8. `create_suppliers_table`
9. `create_inventory_orders_table`
10. `create_inventory_order_items_table`

---

## Document Information

**Document Version:** 1.0
**Last Updated:** October 2025
**Prepared By:** System Analyst
**Project:** Food Ordering System - System Analysis & Design Assignment

**References:**
- Laravel Schema: `food_ordering_laravel.php`
- Requirements Document: `SAD_ASS.pdf`
- Technology Stack: Laravel 10+, MySQL 8.0, Vue.js/React

---

**End of Database Implementation Document**
