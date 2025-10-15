# Food Ordering System - Database Schema Documentation

## Overview
This document provides a comprehensive overview of all database tables and their fields in the Food Ordering System. The system uses Laravel's migration system with UUIDs for primary keys and proper foreign key relationships.

---

## Core Business Tables

### 1. Users Table (`users`)
**Purpose**: Stores user information including customers and administrators

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `uuid` | uuid | Unique | Public identifier |
| `name` | varchar(255) | Required | User's full name |
| `email` | varchar(255) | Unique, Required | User's email address |
| `email_verified_at` | timestamp | Nullable | Email verification timestamp |
| `password` | varchar(255) | Required | Hashed password |
| `role` | enum | Default: 'user' | User role: 'admin' or 'user' |
| `phone` | varchar(20) | Nullable | Phone number |
| `address` | text | Nullable | User's address |
| `profile_image` | varchar(255) | Nullable | Profile image path |
| `last_login_at` | timestamp | Nullable | Last login timestamp |
| `remember_token` | varchar(100) | Nullable | Remember me token |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

**Indexes**: `email` (unique), `uuid` (unique)

---

### 2. Categories Table (`categories`)
**Purpose**: Product categorization system

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `uuid` | uuid | Unique | Public identifier |
| `name` | varchar(255) | Required | Category name |
| `description` | text | Nullable | Category description |
| `is_active` | boolean | Default: true | Category status |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

**Indexes**: `uuid` (unique)

---

### 3. Products Table (`products`)
**Purpose**: Menu items and food products

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `uuid` | uuid | Unique | Public identifier |
| `category_id` | bigint | Foreign Key → categories.id | Product category |
| `name` | varchar(255) | Required | Product name |
| `description` | text | Nullable | Product description |
| `price` | decimal(10,2) | Required | Product price |
| `image` | varchar(255) | Nullable | Product image path |
| `is_available` | boolean | Default: true | Product availability |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

**Foreign Keys**: `category_id` → `categories(id)` ON DELETE CASCADE
**Indexes**: `category_id`, `uuid` (unique)

---

### 4. Inventory Table (`inventory`)
**Purpose**: Stock management for products

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `uuid` | uuid | Unique | Public identifier |
| `product_id` | bigint | Foreign Key → products.id, Unique | Product reference |
| `quantity` | integer | Default: 0 | Current stock quantity |
| `minimum_stock` | integer | Default: 10 | Minimum stock threshold |
| `unit` | varchar(255) | Nullable | Stock unit (kg, pieces, etc.) |
| `location` | varchar(255) | Nullable | Storage location |
| `expiry_date` | date | Nullable | Product expiry date |
| `notes` | text | Nullable | Additional notes |
| `last_restocked_at` | timestamp | Nullable | Last restock timestamp |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

**Foreign Keys**: `product_id` → `products(id)` ON DELETE CASCADE
**Indexes**: `product_id` (unique), `uuid` (unique)

---

### 5. Orders Table (`orders`)
**Purpose**: Customer order management

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `uuid` | uuid | Unique | Public identifier |
| `customer_id` | bigint | Foreign Key → users.id | Customer reference |
| `customer_name` | varchar(255) | Nullable | Customer name (for guest orders) |
| `customer_phone` | varchar(255) | Nullable | Customer phone (for guest orders) |
| `order_number` | varchar(50) | Unique | Order number |
| `status` | enum | Default: 'pending' | Order status: 'pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled' |
| `subtotal` | decimal(10,2) | Required | Order subtotal |
| `tax` | decimal(10,2) | Default: 0 | Tax amount |
| `total` | decimal(10,2) | Required | Total amount |
| `delivery_address` | text | Nullable | Delivery address |
| `delivery_latitude` | decimal(10,8) | Nullable | Delivery latitude |
| `delivery_longitude` | decimal(11,8) | Nullable | Delivery longitude |
| `notes` | text | Nullable | Order notes |
| `confirmed_at` | timestamp | Nullable | Confirmation timestamp |
| `delivered_at` | timestamp | Nullable | Delivery timestamp |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

**Foreign Keys**: `customer_id` → `users(id)` ON DELETE CASCADE
**Indexes**: `customer_id`, `status`, `uuid` (unique), `order_number` (unique)

---

### 6. Order Items Table (`order_items`)
**Purpose**: Individual items within orders

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `uuid` | uuid | Unique | Public identifier |
| `order_id` | bigint | Foreign Key → orders.id | Order reference |
| `product_id` | bigint | Foreign Key → products.id | Product reference |
| `quantity` | integer | Required | Item quantity |
| `unit_price` | decimal(10,2) | Required | Price per unit |
| `subtotal` | decimal(10,2) | Required | Item subtotal |
| `special_instructions` | text | Nullable | Special preparation notes |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

**Foreign Keys**: 
- `order_id` → `orders(id)` ON DELETE CASCADE
- `product_id` → `products(id)` ON DELETE CASCADE

**Indexes**: `order_id`, `product_id`, `uuid` (unique)

---

### 7. Bills Table (`bills`)
**Purpose**: Payment and billing information

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `uuid` | uuid | Unique | Public identifier |
| `order_id` | bigint | Foreign Key → orders.id, Unique | Order reference |
| `bill_number` | varchar(50) | Unique | Bill number |
| `amount` | decimal(10,2) | Required | Bill amount |
| `payment_status` | enum | Default: 'unpaid' | Payment status: 'unpaid', 'paid', 'refunded' |
| `payment_method` | enum | Nullable | Payment method: 'cash', 'card', 'online' |
| `paid_at` | timestamp | Nullable | Payment timestamp |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

**Foreign Keys**: `order_id` → `orders(id)` ON DELETE CASCADE
**Indexes**: `order_id` (unique), `uuid` (unique), `bill_number` (unique)

---

### 8. Suppliers Table (`suppliers`)
**Purpose**: Vendor/supplier information

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `uuid` | uuid | Unique | Public identifier |
| `name` | varchar(255) | Required | Supplier name |
| `email` | varchar(255) | Unique | Supplier email |
| `phone` | varchar(20) | Required | Supplier phone |
| `address` | text | Required | Supplier address |
| `contact_person` | varchar(255) | Nullable | Contact person name |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

**Indexes**: `email` (unique), `uuid` (unique)

---

### 9. Inventory Orders Table (`inventory_orders`)
**Purpose**: Purchase orders from suppliers

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `uuid` | uuid | Unique | Public identifier |
| `supplier_id` | bigint | Foreign Key → suppliers.id | Supplier reference |
| `manager_id` | bigint | Foreign Key → users.id | Manager who created order |
| `order_number` | varchar(50) | Unique | Order number |
| `status` | enum | Default: 'pending' | Order status: 'pending', 'sent', 'received', 'cancelled' |
| `total_amount` | decimal(10,2) | Required | Total order amount |
| `sent_at` | timestamp | Nullable | Order sent timestamp |
| `received_at` | timestamp | Nullable | Order received timestamp |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

**Foreign Keys**: 
- `supplier_id` → `suppliers(id)` ON DELETE CASCADE
- `manager_id` → `users(id)` ON DELETE CASCADE

**Indexes**: `supplier_id`, `manager_id`, `uuid` (unique), `order_number` (unique)

---

### 10. Inventory Order Items Table (`inventory_order_items`)
**Purpose**: Individual items in inventory orders

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `uuid` | uuid | Unique | Public identifier |
| `inventory_order_id` | bigint | Foreign Key → inventory_orders.id | Inventory order reference |
| `product_id` | bigint | Foreign Key → products.id | Product reference |
| `quantity` | integer | Required | Ordered quantity |
| `unit_cost` | decimal(10,2) | Required | Cost per unit |
| `subtotal` | decimal(10,2) | Required | Item subtotal |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

**Foreign Keys**: 
- `inventory_order_id` → `inventory_orders(id)` ON DELETE CASCADE
- `product_id` → `products(id)` ON DELETE CASCADE

**Indexes**: `inventory_order_id`, `product_id`, `uuid` (unique)

---

## Shopping Cart Tables

### 11. Carts Table (`carts`)
**Purpose**: Shopping cart management

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `uuid` | uuid | Unique | Public identifier |
| `user_id` | bigint | Foreign Key → users.id, Nullable | User reference (for logged-in users) |
| `session_id` | varchar(255) | Nullable | Session ID (for guest users) |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

**Foreign Keys**: `user_id` → `users(id)` ON DELETE CASCADE
**Indexes**: `uuid` (unique)

---

### 12. Cart Items Table (`cart_items`)
**Purpose**: Items in shopping carts

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `uuid` | uuid | Unique | Public identifier |
| `cart_id` | bigint | Foreign Key → carts.id | Cart reference |
| `product_id` | bigint | Foreign Key → products.id | Product reference |
| `quantity` | integer | Default: 1 | Item quantity |
| `price` | decimal(10,2) | Required | Price at time of adding |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

**Foreign Keys**: 
- `cart_id` → `carts(id)` ON DELETE CASCADE
- `product_id` → `products(id)` ON DELETE CASCADE

**Indexes**: `uuid` (unique), Unique constraint on (`cart_id`, `product_id`)

---

## Role Management Tables

### 13. Roles Table (`roles`)
**Purpose**: Role-based access control

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `uuid` | uuid | Unique | Public identifier |
| `name` | varchar(255) | Unique | Role name |
| `display_name` | varchar(255) | Required | Display name |
| `description` | text | Nullable | Role description |
| `permissions` | json | Nullable | Permissions as JSON |
| `is_active` | boolean | Default: true | Role status |
| `is_system` | boolean | Default: false | System role flag |
| `sort_order` | integer | Default: 0 | Display order |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

**Indexes**: `name` (unique), `uuid` (unique), (`is_active`, `sort_order`)

---

### 14. User Roles Table (`user_roles`)
**Purpose**: Many-to-many relationship between users and roles

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `uuid` | uuid | Unique | Public identifier |
| `user_id` | bigint | Foreign Key → users.id | User reference |
| `role_id` | bigint | Foreign Key → roles.id | Role reference |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

**Foreign Keys**: 
- `user_id` → `users(id)` ON DELETE CASCADE
- `role_id` → `roles(id)` ON DELETE CASCADE

**Indexes**: `uuid` (unique), Unique constraint on (`user_id`, `role_id`)

---

## Content Management Tables

### 15. Slider Images Table (`slider_images`)
**Purpose**: Homepage slider management

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Internal ID |
| `title` | varchar(255) | Required | Slider title |
| `description` | text | Nullable | Slider description |
| `image_url` | varchar(255) | Required | Image URL/path |
| `button_text` | varchar(255) | Nullable | Call-to-action button text |
| `button_url` | varchar(255) | Nullable | Call-to-action button URL |
| `order` | integer | Default: 0 | Display order |
| `is_active` | boolean | Default: true | Slider status |
| `created_at` | timestamp | Required | Record creation time |
| `updated_at` | timestamp | Required | Record update time |

---

## System Tables (Laravel Framework)

### 16. Cache Table (`cache`)
**Purpose**: Application caching

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `key` | varchar(255) | Primary Key | Cache key |
| `value` | mediumtext | Required | Cached value |
| `expiration` | integer | Required | Expiration timestamp |

---

### 17. Cache Locks Table (`cache_locks`)
**Purpose**: Cache locking mechanism

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `key` | varchar(255) | Primary Key | Lock key |
| `owner` | varchar(255) | Required | Lock owner |
| `expiration` | integer | Required | Lock expiration |

---

### 18. Jobs Table (`jobs`)
**Purpose**: Background job queue

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Job ID |
| `queue` | varchar(255) | Required | Queue name |
| `payload` | longtext | Required | Job payload |
| `attempts` | tinyint unsigned | Required | Attempt count |
| `reserved_at` | int unsigned | Nullable | Reservation timestamp |
| `available_at` | int unsigned | Required | Availability timestamp |
| `created_at` | int unsigned | Required | Creation timestamp |

**Indexes**: `queue`

---

### 19. Job Batches Table (`job_batches`)
**Purpose**: Batch job management

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | varchar(255) | Primary Key | Batch ID |
| `name` | varchar(255) | Required | Batch name |
| `total_jobs` | integer | Required | Total job count |
| `pending_jobs` | integer | Required | Pending job count |
| `failed_jobs` | integer | Required | Failed job count |
| `failed_job_ids` | longtext | Required | Failed job IDs |
| `options` | mediumtext | Nullable | Batch options |
| `cancelled_at` | integer | Nullable | Cancellation timestamp |
| `created_at` | integer | Required | Creation timestamp |
| `finished_at` | integer | Nullable | Completion timestamp |

---

### 20. Failed Jobs Table (`failed_jobs`)
**Purpose**: Failed job tracking

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Failed job ID |
| `uuid` | varchar(255) | Unique | Job UUID |
| `connection` | text | Required | Connection name |
| `queue` | text | Required | Queue name |
| `payload` | longtext | Required | Job payload |
| `exception` | longtext | Required | Exception details |
| `failed_at` | timestamp | Required | Failure timestamp |

**Indexes**: `uuid` (unique)

---

### 21. Sessions Table (`sessions`)
**Purpose**: User session management

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | varchar(255) | Primary Key | Session ID |
| `user_id` | bigint | Foreign Key → users.id, Nullable | User reference |
| `ip_address` | varchar(45) | Nullable | Client IP address |
| `user_agent` | text | Nullable | Client user agent |
| `payload` | longtext | Required | Session data |
| `last_activity` | integer | Required | Last activity timestamp |

**Foreign Keys**: `user_id` → `users(id)`
**Indexes**: `user_id`, `last_activity`

---

## Database Relationships Summary

### Primary Relationships:
- **Users** → **Orders** (One-to-Many)
- **Orders** → **Order Items** (One-to-Many)
- **Products** → **Order Items** (One-to-Many)
- **Products** → **Inventory** (One-to-One)
- **Categories** → **Products** (One-to-Many)
- **Suppliers** → **Inventory Orders** (One-to-Many)
- **Users** → **Inventory Orders** (One-to-Many) [Manager relationship]
- **Users** → **Carts** (One-to-Many)
- **Orders** → **Bills** (One-to-One)

### Many-to-Many Relationships:
- **Users** ↔ **Roles** (via `user_roles` table)

### Key Features:
- All tables use UUIDs for public identification
- Comprehensive foreign key constraints with CASCADE deletes
- Proper indexing for performance
- Support for guest users (nullable user_id in carts and sessions)
- Role-based access control system
- Inventory management with stock tracking
- Order management with status tracking
- Shopping cart functionality
- Payment and billing system

---

*This documentation reflects the current state of the database schema as of the latest migrations.*
