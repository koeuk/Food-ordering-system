# Food Ordering System - Database Tables Reference

This document contains a complete list of all database tables with their column structures for CRUD implementation reference.

---

## 📋 Table of Contents

1. [users](#1-users)
2. [categories](#2-categories)
3. [products](#3-products)
4. [inventory](#4-inventory)
5. [orders](#5-orders)
6. [order_items](#6-order_items)
7. [bills](#7-bills)
8. [suppliers](#8-suppliers)
9. [inventory_orders](#9-inventory_orders)
10. [inventory_order_items](#10-inventory_order_items)
11. [roles](#11-roles)
12. [user_roles](#12-user_roles)

---

## 1. users

**Table Name:** `users`

**Columns:**
```
[
  id,
  uuid (unique),
  name,
  email (unique),
  password,
  role (enum: 'admin', 'user', default: 'user'),
  phone (nullable),
  address (nullable),
  remember_token,
  created_at,
  updated_at
]
```

**Relationships:**
- Has Many: orders (as customer)
- Has Many: inventory_orders (as manager)
- Has Many: user_roles

**Validation Rules:**
```javascript
name: yup.string().required(__("Name is required")),
email: yup.string().email().required(__("Email is required")),
password: yup.string().min(8).required(__("Password is required")),
role: yup.string().oneOf(['admin', 'user']).required(__("Role is required")),
phone: yup.string(),
address: yup.string()
```

---

## 2. categories

**Table Name:** `categories`

**Columns:**
```
[
  id,
  uuid (unique),
  name,
  description (nullable),
  created_at,
  updated_at
]
```

**Relationships:**
- Has Many: products

**Validation Rules:**
```javascript
name: yup.string().required(__("Category name is required")),
description: yup.string()
```

---

## 3. products

**Table Name:** `products`

**Columns:**
```
[
  id,
  uuid (unique),
  category_id (foreign key -> categories),
  name,
  description (nullable),
  price (decimal 10,2),
  image (nullable),
  is_available (boolean, default: true),
  created_at,
  updated_at
]
```

**Relationships:**
- Belongs To: category
- Has One: inventory
- Has Many: order_items
- Has Many: inventory_order_items

**Validation Rules:**
```javascript
category_id: yup.number().required(__("Category is required")),
name: yup.string().required(__("Product name is required")),
description: yup.string(),
price: yup.number().min(0).required(__("Price is required")),
image: yup.string(),
is_available: yup.boolean()
```

---

## 4. inventory

**Table Name:** `inventory`

**Columns:**
```
[
  id,
  uuid (unique),
  product_id (foreign key -> products, unique),
  quantity (default: 0),
  minimum_stock (default: 10),
  last_restocked_at (nullable),
  created_at,
  updated_at
]
```

**Relationships:**
- Belongs To: product

**Validation Rules:**
```javascript
product_id: yup.number().required(__("Product is required")),
quantity: yup.number().min(0).required(__("Quantity is required")),
minimum_stock: yup.number().min(0).required(__("Minimum stock is required")),
last_restocked_at: yup.date()
```

---

## 5. orders

**Table Name:** `orders`

**Columns:**
```
[
  id,
  uuid (unique),
  customer_id (foreign key -> users),
  order_number (unique),
  status (enum: 'pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled', default: 'pending'),
  subtotal (decimal 10,2),
  tax (decimal 10,2, default: 0),
  total (decimal 10,2),
  delivery_address (nullable),
  notes (nullable),
  confirmed_at (nullable),
  delivered_at (nullable),
  created_at,
  updated_at
]
```

**Relationships:**
- Belongs To: customer (users)
- Has Many: order_items
- Has One: bill

**Validation Rules:**
```javascript
customer_id: yup.number().required(__("Customer is required")),
order_number: yup.string().required(__("Order number is required")),
status: yup.string().oneOf(['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled']).required(__("Status is required")),
subtotal: yup.number().min(0).required(__("Subtotal is required")),
tax: yup.number().min(0),
total: yup.number().min(0).required(__("Total is required")),
delivery_address: yup.string(),
notes: yup.string()
```

---

## 6. order_items

**Table Name:** `order_items`

**Columns:**
```
[
  id,
  uuid (unique),
  order_id (foreign key -> orders),
  product_id (foreign key -> products),
  quantity,
  unit_price (decimal 10,2),
  subtotal (decimal 10,2),
  special_instructions (nullable),
  created_at,
  updated_at
]
```

**Relationships:**
- Belongs To: order
- Belongs To: product

**Validation Rules:**
```javascript
order_id: yup.number().required(__("Order is required")),
product_id: yup.number().required(__("Product is required")),
quantity: yup.number().min(1).required(__("Quantity is required")),
unit_price: yup.number().min(0).required(__("Unit price is required")),
subtotal: yup.number().min(0).required(__("Subtotal is required")),
special_instructions: yup.string()
```

---

## 7. bills

**Table Name:** `bills`

**Columns:**
```
[
  id,
  uuid (unique),
  order_id (foreign key -> orders, unique),
  bill_number (unique),
  amount (decimal 10,2),
  payment_status (enum: 'unpaid', 'paid', 'refunded', default: 'unpaid'),
  payment_method (enum: 'cash', 'card', 'online', nullable),
  paid_at (nullable),
  created_at,
  updated_at
]
```

**Relationships:**
- Belongs To: order

**Validation Rules:**
```javascript
order_id: yup.number().required(__("Order is required")),
bill_number: yup.string().required(__("Bill number is required")),
amount: yup.number().min(0).required(__("Amount is required")),
payment_status: yup.string().oneOf(['unpaid', 'paid', 'refunded']).required(__("Payment status is required")),
payment_method: yup.string().oneOf(['cash', 'card', 'online']),
paid_at: yup.date()
```

---

## 8. suppliers

**Table Name:** `suppliers`

**Columns:**
```
[
  id,
  uuid (unique),
  name,
  email (unique),
  phone,
  address,
  contact_person (nullable),
  created_at,
  updated_at
]
```

**Relationships:**
- Has Many: inventory_orders

**Validation Rules:**
```javascript
name: yup.string().required(__("Supplier name is required")),
email: yup.string().email().required(__("Email is required")),
phone: yup.string().required(__("Phone is required")),
address: yup.string().required(__("Address is required")),
contact_person: yup.string()
```

---

## 9. inventory_orders

**Table Name:** `inventory_orders`

**Columns:**
```
[
  id,
  uuid (unique),
  supplier_id (foreign key -> suppliers),
  manager_id (foreign key -> users),
  order_number (unique),
  status (enum: 'pending', 'sent', 'received', 'cancelled', default: 'pending'),
  total_amount (decimal 10,2),
  sent_at (nullable),
  received_at (nullable),
  created_at,
  updated_at
]
```

**Relationships:**
- Belongs To: supplier
- Belongs To: manager (users)
- Has Many: inventory_order_items

**Validation Rules:**
```javascript
supplier_id: yup.number().required(__("Supplier is required")),
manager_id: yup.number().required(__("Manager is required")),
order_number: yup.string().required(__("Order number is required")),
status: yup.string().oneOf(['pending', 'sent', 'received', 'cancelled']).required(__("Status is required")),
total_amount: yup.number().min(0).required(__("Total amount is required")),
sent_at: yup.date(),
received_at: yup.date()
```

---

## 10. inventory_order_items

**Table Name:** `inventory_order_items`

**Columns:**
```
[
  id,
  uuid (unique),
  inventory_order_id (foreign key -> inventory_orders),
  product_id (foreign key -> products),
  quantity,
  unit_cost (decimal 10,2),
  subtotal (decimal 10,2),
  created_at,
  updated_at
]
```

**Relationships:**
- Belongs To: inventory_order
- Belongs To: product

**Validation Rules:**
```javascript
inventory_order_id: yup.number().required(__("Inventory order is required")),
product_id: yup.number().required(__("Product is required")),
quantity: yup.number().min(1).required(__("Quantity is required")),
unit_cost: yup.number().min(0).required(__("Unit cost is required")),
subtotal: yup.number().min(0).required(__("Subtotal is required"))
```

---

## 11. roles

**Table Name:** `roles`

**Columns:**
```
[
  id,
  uuid (unique),
  name (unique),
  display_name,
  description (nullable),
  permissions (json, nullable),
  is_active (boolean, default: true),
  is_system (boolean, default: false),
  sort_order (default: 0),
  created_at,
  updated_at
]
```

**Relationships:**
- Has Many: user_roles

**Validation Rules:**
```javascript
name: yup.string().required(__("Role name is required")),
display_name: yup.string().required(__("Display name is required")),
description: yup.string(),
permissions: yup.array(),
is_active: yup.boolean(),
is_system: yup.boolean(),
sort_order: yup.number().min(0)
```

---

## 12. user_roles

**Table Name:** `user_roles`

**Columns:**
```
[
  id,
  uuid (unique),
  user_id (foreign key -> users),
  role_id (foreign key -> roles),
  created_at,
  updated_at
]
```

**Relationships:**
- Belongs To: user
- Belongs To: role

**Validation Rules:**
```javascript
user_id: yup.number().required(__("User is required")),
role_id: yup.number().required(__("Role is required"))
```

---

## 📊 Database Statistics

- **Total Tables:** 12
- **Tables with Foreign Keys:** 9
- **Many-to-Many Relations:** 1 (user_roles)
- **One-to-One Relations:** 2 (inventory, bills)
- **One-to-Many Relations:** 10+

---

## 🔗 Relationship Diagram (Text)

```
users (1) ────< (N) orders
users (1) ────< (N) inventory_orders
users (N) >────< (N) roles (via user_roles)

categories (1) ────< (N) products

products (1) ────< (1) inventory
products (1) ────< (N) order_items
products (1) ────< (N) inventory_order_items

orders (1) ────< (N) order_items
orders (1) ────< (1) bills

suppliers (1) ────< (N) inventory_orders

inventory_orders (1) ────< (N) inventory_order_items
```

---

## 📝 CRUD Implementation Notes

### Required for All Tables:
1. **UUID field** - Always use for public-facing operations
2. **Timestamps** - created_at, updated_at (Laravel handles automatically)
3. **Soft Deletes** - Consider implementing where needed

### Standard CRUD Routes Pattern:
```
GET    /items           - index (list)
GET    /items/create    - create form
POST   /items           - store
GET    /items/{uuid}    - show
GET    /items/{uuid}/edit - edit form
PUT    /items/{uuid}    - update
DELETE /items/{uuid}    - destroy
```

### Permission Pattern for Each Table:
```
view {table_name}
create {table_name}
update {table_name}
delete {table_name}
```

---

## 🎯 Implementation Priority

### Phase 1 - Core Tables (Already Implemented):
- ✅ users
- ✅ categories
- ✅ products
- ✅ roles
- ✅ user_roles

### Phase 2 - Inventory Management:
- ⏳ inventory
- ⏳ suppliers
- ⏳ inventory_orders
- ⏳ inventory_order_items

### Phase 3 - Order Management:
- ⏳ orders
- ⏳ order_items
- ⏳ bills

---

## 📚 Related Documentation

- [Form Validation Guide](./FORM_VALIDATION_SHADCN.md)
- [Creating Index Pages](./creating-index-pages.md)
- [Batch Refactor Script](./BATCH_REFACTOR_SCRIPT.md)
- [Restaurant Refactor Status](./RESTAURANT_REFACTOR_STATUS.md)
- [README](./README.md)

---

**Last Updated:** October 9, 2025  
**Maintained By:** Development Team

