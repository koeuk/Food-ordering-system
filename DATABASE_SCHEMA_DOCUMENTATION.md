# Food Ordering System - Complete Database Schema Documentation

## 📋 Overview

This document provides the complete database schema for the Food Ordering System using dbdiagram.io format. The schema includes 10 core tables with proper relationships, indexes, and constraints.

---

## 🗄️ Database Schema Structure

### **Technology Stack**
- **Backend**: Laravel 11+ (PHP)
- **Database**: MySQL 8.0
- **Frontend**: Vue.js 3 + Vuetify 3
- **Authentication**: Laravel Sanctum
- **ORM**: Eloquent

---

## 📊 Table Definitions

### **1. USER MANAGEMENT**

#### **users**
```sql
Table users {
  id bigint [primary key, increment]
  name varchar(255) [not null]
  email varchar(255) [unique, not null]
  password varchar(255) [not null]
  role varchar(50) [not null, note: 'customer, manager, kitchen, supplier']
  phone varchar(20)
  address text
  created_at timestamp
  updated_at timestamp
  
  indexes {
    email [unique]
    role
  }
}
```

**Purpose**: Stores all system users with role-based access control
**Roles**: customer, manager, kitchen, supplier

---

### **2. PRODUCT MANAGEMENT**

#### **categories**
```sql
Table categories {
  id bigint [primary key, increment]
  name varchar(255) [not null]
  description text
  created_at timestamp
  updated_at timestamp
  
  indexes {
    name
  }
}
```

**Purpose**: Product categorization (Appetizers, Main Course, Desserts, etc.)

#### **products**
```sql
Table products {
  id bigint [primary key, increment]
  category_id bigint [not null]
  name varchar(255) [not null]
  description text
  price decimal(10,2) [not null]
  image varchar(255)
  is_available boolean [default: true]
  created_at timestamp
  updated_at timestamp
  
  indexes {
    category_id
    name
    is_available
  }
}
```

**Purpose**: Food items available for ordering

#### **inventory**
```sql
Table inventory {
  id bigint [primary key, increment]
  product_id bigint [unique, not null]
  quantity integer [not null, default: 0]
  minimum_stock integer [not null, default: 10]
  last_restocked_at timestamp
  created_at timestamp
  updated_at timestamp
  
  indexes {
    product_id [unique]
    quantity
  }
}
```

**Purpose**: Tracks stock levels for each product

---

### **3. ORDER MANAGEMENT**

#### **orders**
```sql
Table orders {
  id bigint [primary key, increment]
  customer_id bigint [not null]
  order_number varchar(50) [unique, not null]
  status varchar(50) [not null, default: 'pending', note: 'pending, confirmed, preparing, ready, delivered, cancelled']
  subtotal decimal(10,2) [not null]
  tax decimal(10,2) [not null, default: 0]
  total decimal(10,2) [not null]
  delivery_address text
  notes text
  confirmed_at timestamp
  delivered_at timestamp
  created_at timestamp
  updated_at timestamp
  
  indexes {
    customer_id
    order_number [unique]
    status
    created_at
  }
}
```

**Purpose**: Customer food orders with lifecycle tracking
**Status Values**: pending, confirmed, preparing, ready, delivered, cancelled

#### **order_items**
```sql
Table order_items {
  id bigint [primary key, increment]
  order_id bigint [not null]
  product_id bigint [not null]
  quantity integer [not null]
  unit_price decimal(10,2) [not null]
  subtotal decimal(10,2) [not null]
  special_instructions text
  created_at timestamp
  updated_at timestamp
  
  indexes {
    order_id
    product_id
  }
}
```

**Purpose**: Junction table between orders and products (individual items in an order)

#### **bills**
```sql
Table bills {
  id bigint [primary key, increment]
  order_id bigint [unique, not null]
  bill_number varchar(50) [unique, not null]
  amount decimal(10,2) [not null]
  payment_status varchar(50) [not null, default: 'unpaid', note: 'unpaid, paid, refunded']
  payment_method varchar(50) [note: 'cash, card, online']
  paid_at timestamp
  created_at timestamp
  updated_at timestamp
  
  indexes {
    order_id [unique]
    bill_number [unique]
    payment_status
  }
}
```

**Purpose**: Payment information and tracking for orders
**Payment Status**: unpaid, paid, refunded
**Payment Methods**: cash, card, online

---

### **4. SUPPLIER & INVENTORY MANAGEMENT**

#### **suppliers**
```sql
Table suppliers {
  id bigint [primary key, increment]
  name varchar(255) [not null]
  email varchar(255) [unique, not null]
  phone varchar(20) [not null]
  address text [not null]
  contact_person varchar(255)
  created_at timestamp
  updated_at timestamp
  
  indexes {
    email [unique]
    name
  }
}
```

**Purpose**: External vendors who supply ingredients/products

#### **inventory_orders**
```sql
Table inventory_orders {
  id bigint [primary key, increment]
  supplier_id bigint [not null]
  manager_id bigint [not null]
  order_number varchar(50) [unique, not null]
  status varchar(50) [not null, default: 'pending', note: 'pending, sent, received, cancelled']
  total_amount decimal(10,2) [not null]
  sent_at timestamp
  received_at timestamp
  created_at timestamp
  updated_at timestamp
  
  indexes {
    supplier_id
    manager_id
    order_number [unique]
    status
  }
}
```

**Purpose**: Orders placed to suppliers for restocking (managed by managers)
**Status Values**: pending, sent, received, cancelled

#### **inventory_order_items**
```sql
Table inventory_order_items {
  id bigint [primary key, increment]
  inventory_order_id bigint [not null]
  product_id bigint [not null]
  quantity integer [not null]
  unit_cost decimal(10,2) [not null]
  subtotal decimal(10,2) [not null]
  created_at timestamp
  updated_at timestamp
  
  indexes {
    inventory_order_id
    product_id
  }
}
```

**Purpose**: Junction table between inventory orders and products

---

## 🔗 Relationships

### **Relationship Symbols**
- `>` : many-to-one
- `<` : one-to-many  
- `-` : one-to-one

### **Delete Actions**
- `cascade`: Delete related records
- `restrict`: Prevent deletion if related records exist

### **User Relationships**
```sql
Ref: orders.customer_id > users.id [delete: cascade]
Ref: inventory_orders.manager_id > users.id [delete: cascade]
```

### **Product & Category**
```sql
Ref: products.category_id > categories.id [delete: cascade]
```

### **Inventory**
```sql
Ref: inventory.product_id - products.id [delete: cascade]
```

### **Orders & Related Tables**
```sql
Ref: order_items.order_id > orders.id [delete: cascade]
Ref: order_items.product_id > products.id [delete: restrict]
Ref: bills.order_id - orders.id [delete: cascade]
```

### **Supplier & Inventory Orders**
```sql
Ref: inventory_orders.supplier_id > suppliers.id [delete: cascade]
Ref: inventory_order_items.inventory_order_id > inventory_orders.id [delete: cascade]
Ref: inventory_order_items.product_id > products.id [delete: restrict]
```

---

## 📈 Business Rules

### **Order Management**
- Minimum order amount: $10
- Tax rate: 10% (configurable)
- Order cancellation: Only if status is 'pending'
- Refunds: Within 24 hours of delivery

### **Inventory Management**
- Low stock threshold: 10 units (configurable)
- Auto-alert when `quantity <= minimum_stock`
- Update inventory after order confirmation

### **Role-Based Access**
- **Customer**: Place orders, view own orders, make payments
- **Manager**: Generate reports, manage inventory, create supplier orders
- **Kitchen**: View pending orders, update order status
- **Supplier**: View inventory orders, update delivery status

---

## 🛡️ Security Considerations

### **Password Security**
- Hash all passwords using bcrypt
- Minimum password length: 8 characters

### **API Security**
- Implement CSRF protection
- Use Laravel Sanctum for API authentication
- Rate limiting on all endpoints

### **Input Validation**
- Validate and sanitize all user inputs
- Use Laravel Form Requests for validation
- Prevent SQL injection with prepared statements

---

## 🚀 Implementation Status

### **✅ Completed**
- Database migrations
- Seeders with fake data
- Categories CRUD
- Suppliers CRUD
- Basic routing structure

### **🔄 In Progress**
- Product management CRUD
- Order management CRUD
- Inventory management CRUD
- User management CRUD

### **📋 Pending**
- Bill management CRUD
- Inventory order management CRUD
- Reports and analytics
- Payment integration
- Real-time notifications

---

## 📚 Usage Instructions

### **For dbdiagram.io**
1. Copy the schema code
2. Paste into https://dbdiagram.io/
3. Generate visual ER diagram
4. Export as PNG/PDF

### **For Laravel Implementation**
1. Use provided migrations
2. Follow relationship definitions
3. Implement CRUD controllers
4. Create Vue.js components with Vuetify

---

## 📝 Document Information

**Document Version:** 1.0  
**Last Updated:** October 2025  
**Prepared By:** System Analyst  
**Project:** Food Ordering System - System Analysis & Design Assignment

**References:**
- Laravel Schema: `food_ordering_laravel.php`
- Requirements Document: `SAD_ASS.pdf`
- Technology Stack: Laravel 11+, MySQL 8.0, Vue.js 3, Vuetify 3

---

**End of Database Schema Documentation**
