# Technical Developer Guide

**Project:** Fresh Mart E-commerce Platform  
**Version:** 1.0  
**Last Updated:** March 23, 2026  
**Target Audience:** Backend/Full-Stack Developers

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [Technology Stack](#technology-stack)
3. [System Architecture](#system-architecture)
4. [Database Schema](#database-schema)
5. [Time & Space Complexity Analysis](#time--space-complexity-analysis)
6. [Customer Flow Code Paths](#customer-flow-code-paths)
7. [Admin Flow Code Paths](#admin-flow-code-paths)



---

## Project Overview

Fresh Mart is a PHP-based e-commerce platform for online grocery shopping with real-time inventory management, transaction-safe order processing, and comprehensive admin controls.

### Core Features
- Multi-category product catalog (Fruits, Vegetables, Dairy, Beverages)
- Session-based authentication
- Real-time stock validation
- Transaction-safe order processing with pessimistic locking
- PDF invoice generation
- Admin dashboard with inventory management

---

## Technology Stack

### Backend
| Component | Technology | Version |
|-----------|-----------|---------|
| **Language** | PHP | 7.x+ |
| **Database** | MySQL/MariaDB | 5.7+ |
| **Session Management** | PHP Sessions | Native |
| **PDF Generation** | FPDF | 1.84 |

### Frontend
| Component | Technology | Version |
|-----------|-----------|---------|
| **CSS Framework** | Bootstrap | 4.1.3 |
| **Icons** | Font Awesome | 4.7.0 |
| **JavaScript** | jQuery | 3.3.1 |
| **Fonts** | Google Fonts | Poppins, Inter |

### Database Connection
```php
// includes/common.php
$con = mysqli_connect("localhost:8889", "root", "root", "onlinesale");
```

### File Structure
```
ECOMMERCE-WEBSITE/
├── index.php                 # Landing page
├── products.php              # Product catalog
├── cart.php                  # Shopping cart
├── addtocart.php            # Add to cart handler
├── update-cart-quantity.php # Cart quantity updater
├── cart-remove.php          # Remove from cart
├── success.php              # Order confirmation
├── login_script.php         # User login handler
├── signup_script.php        # User registration handler
├── logout_script.php        # Logout handler
├── about.php                # About page
├── generate_invoice.php     # PDF invoice generator
├── view_invoice.php         # HTML invoice viewer
├── email_invoice.php        # Email invoice sender
├── includes/
│   ├── common.php           # Database & core functions
│   ├── header_menu.php      # Navigation & auth modals
│   ├── footer.php           # Footer component
│   ├── check-if-added.php   # Cart check utility
│   ├── product_card.php     # Product card renderer
│   ├── invoice_helpers.php  # Invoice utilities
│   └── fpdf.php            # PDF library
├── admin/
│   ├── login.php           # Admin login page
│   ├── dashboard.php       # Admin dashboard
│   ├── products.php        # Product management
│   ├── orders.php          # Order management
│   ├── order_details.php   # Order detail view
│   ├── add_product.php     # Add product form
│   ├── edit_product.php    # Edit product form
│   └── includes/
│       ├── admin_common.php      # Admin functions
│       ├── order_functions.php   # Order utilities
│       ├── admin_header.php      # Admin header
│       └── admin_sidebar.php     # Admin sidebar
└── images/                  # Product images
```

---

## System Architecture

### Request Flow
```
Client Browser
    ↓
Apache/Nginx Web Server
    ↓
PHP Interpreter
    ↓
Session Manager (PHP Sessions)
    ↓
Business Logic (includes/common.php)
    ↓
MySQL Database (onlinesale)
    ↓
Response (HTML/Redirect)
```

### Session Architecture
- **Storage**: Server-side PHP sessions
- **Identifier**: PHPSESSID cookie
- **Data Stored**: `user_id`, `email`, `admin_id`, `last_order_id`
- **Lifetime**: Until browser close or explicit logout

---

## Database Schema

### Tables Overview

#### **1. products**
```sql
CREATE TABLE products (
  id INT(11) PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(30),
  price INT(20) NOT NULL,
  unit VARCHAR(20) DEFAULT 'piece',
  discount INT(3) DEFAULT 0,
  stock_quantity INT(11) DEFAULT 100,
  category VARCHAR(50),
  image_path VARCHAR(255)
);
```
**Indexes**: PRIMARY KEY on `id`

#### **2. users**
```sql
CREATE TABLE users (
  id INT(11) PRIMARY KEY AUTO_INCREMENT,
  email_id VARCHAR(255) NOT NULL,
  first_name VARCHAR(20) NOT NULL,
  last_name VARCHAR(20),
  phone VARCHAR(15),
  registration_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  password VARCHAR(255) NOT NULL
);
```
**Indexes**: PRIMARY KEY on `id`

#### **3. users_products** (Cart)
```sql
CREATE TABLE users_products (
  id INT(11) PRIMARY KEY AUTO_INCREMENT,
  user_id INT(11),
  item_id INT(11),
  status ENUM('Added To Cart','Confirmed'),
  quantity INT(11) DEFAULT 1,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (item_id) REFERENCES products(id)
);
```
**Indexes**: PRIMARY KEY on `id`, INDEX on `user_id`, INDEX on `item_id`

#### **4. orders**
```sql
CREATE TABLE orders (
  id INT(11) PRIMARY KEY AUTO_INCREMENT,
  user_id INT(11),
  order_number VARCHAR(50) UNIQUE,
  total_amount DECIMAL(10,2),
  discount_amount DECIMAL(10,2),
  final_amount DECIMAL(10,2),
  order_status ENUM('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  payment_status ENUM('Pending','Paid','Failed') DEFAULT 'Pending',
  delivery_address TEXT,
  delivery_phone VARCHAR(15),
  order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### **5. order_items**
```sql
CREATE TABLE order_items (
  id INT(11) PRIMARY KEY AUTO_INCREMENT,
  order_id INT(11),
  product_id INT(11),
  product_name VARCHAR(100),
  product_price DECIMAL(10,2),
  product_unit VARCHAR(20),
  discount_percent INT(3),
  quantity INT(11),
  subtotal DECIMAL(10,2),
  discount_amount DECIMAL(10,2),
  final_price DECIMAL(10,2)
);
```

#### **6. admins**
```sql
CREATE TABLE admins (
  id INT(11) PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) UNIQUE,
  password VARCHAR(255),
  email VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## Time & Space Complexity Analysis

### Customer Operations

#### **1. Product Display (`products.php`)**

**Code Flow:**
```php
// Line 69: Fetch all products
$query = "SELECT * FROM products ORDER BY category, id";
$result = mysqli_query($con, $query);

// Lines 74-80: Group by category
while ($row = mysqli_fetch_assoc($result)) {
    $products_by_category[$category][] = $row;
}

// Lines 98-100: Render cards (FIXED - no longer N+1)
foreach ($products_by_category[$category] as $product) {
    render_product_card($product); // Pass full array, not ID
}
```

| Metric | Complexity | Explanation |
|--------|-----------|-------------|
| **Time** | `O(P)` | P = total products. Single query + linear iteration |
| **Space** | `O(P)` | Stores all products in `$products_by_category` array |
| **DB Queries** | `1` | Single SELECT (after N+1 fix) |

**Previous N+1 Problem (FIXED):**
- ❌ **Before**: `O(P²)` - 1 query + P queries in `render_product_card()`
- ✅ **After**: `O(P)` - Single query, data passed to renderer

---

#### **2. Add to Cart (`addtocart.php`)**

**Code Flow:**
```php
// Line 14: Check current cart quantity
$current_cart_qty = get_cart_item_quantity($user_id, $item_id);
  └─> SELECT SUM(quantity) WHERE user_id AND item_id

// Line 17: Validate stock
validate_stock($item_id, $total_requested)
  └─> SELECT stock_quantity WHERE id='$product_id'

// Line 27: Check if already in cart
SELECT id FROM users_products WHERE user_id AND item_id LIMIT 1

// Line 31 or 33: Update or Insert
UPDATE users_products SET quantity = quantity + $quantity
  OR
INSERT INTO users_products VALUES(...)
```

| Metric | Complexity | Explanation |
|--------|-----------|-------------|
| **Time** | `O(1)` | Fixed number of indexed queries |
| **Space** | `O(1)` | Constant memory for query results |
| **DB Queries** | `4` | All indexed, constant time |

**Indexes Used:**
- `users_products(user_id, item_id)` - Composite index recommended
- `products(id)` - Primary key

---

#### **3. View Cart (`cart.php`)**

**Code Flow:**
```php
// Lines 67-70: Fetch cart items with JOIN
$query = "SELECT products.price, products.id, products.name, products.stock_quantity,
          users_products.quantity, products.unit, users_products.id AS cart_id
          FROM users_products 
          JOIN products ON users_products.item_id = products.id 
          WHERE users_products.user_id='$user_id' AND status='Added To Cart'";

// Lines 89-124: Iterate and display
while ($row = mysqli_fetch_array($result)) {
    // Calculate subtotal
    // Check stock status
    // Render row
}
```

| Metric | Complexity | Explanation |
|--------|-----------|-------------|
| **Time** | `O(C)` | C = items in user's cart |
| **Space** | `O(C)` | Stores cart items in memory |
| **DB Queries** | `2` | Cart query + user phone query |

**Typical Cart Size:** 1-20 items  
**Worst Case:** Unbounded (no limit enforced)

---

#### **4. Update Cart Quantity (`update-cart-quantity.php`)**

**Code Flow:**
```php
// Line 20: Verify cart ownership
SELECT item_id FROM users_products WHERE id='$cart_id' AND user_id='$user_id'

// Line 31: Validate stock
validate_stock($product_id, $new_quantity)
  └─> SELECT stock_quantity FROM products WHERE id='$product_id'

// Line 37: Update quantity
UPDATE users_products SET quantity = $new_quantity WHERE id='$cart_id'
```

| Metric | Complexity | Explanation |
|--------|-----------|-------------|
| **Time** | `O(1)` | Three indexed queries |
| **Space** | `O(1)` | Constant memory |
| **DB Queries** | `3` | All indexed |

---

#### **5. Order Placement (`success.php` → `create_order_with_transaction()`)**

**Code Flow:**
```php
// includes/common.php: Lines 151-306

mysqli_begin_transaction($con, MYSQLI_TRANS_START_READ_WRITE);

// Step 1: Fetch cart items (Line 160-174)
SELECT up.item_id, up.quantity, p.name, p.price, p.unit, p.discount 
FROM users_products up 
INNER JOIN products p ON up.item_id = p.id 
WHERE up.user_id='$user_id' AND up.status='Added to cart'
// Time: O(C), Space: O(C)

// Step 2: Lock products and validate stock (Lines 179-202)
foreach ($cart_items as $item) {
    SELECT id, stock_quantity, name FROM products 
    WHERE id='$product_id' FOR UPDATE;  // Pessimistic lock
    
    if (available_stock < requested_qty) {
        $insufficient_stock[] = item;
    }
}
// Time: O(C), Space: O(C)

// Step 3: Update stock (Lines 210-220)
foreach ($cart_items as $item) {
    UPDATE products SET stock_quantity = stock_quantity - $requested_qty 
    WHERE id='$product_id';
}
// Time: O(C)

// Step 4: Create order record (Lines 257-264)
INSERT INTO orders (user_id, order_number, total_amount, ...) VALUES (...)
// Time: O(1)

// Step 5: Create order items (Lines 268-287)
foreach ($order_items as $item) {
    INSERT INTO order_items (order_id, product_id, ...) VALUES (...)
}
// Time: O(C)

// Step 6: Update cart status (Line 289)
UPDATE users_products SET status='Confirmed' 
WHERE user_id='$user_id' AND status='Added to cart'
// Time: O(1)

mysqli_commit($con);
```

| Metric | Complexity | Explanation |
|--------|-----------|-------------|
| **Time** | `O(C)` | C = cart items. Linear with cart size |
| **Space** | `O(C)` | Stores cart items and order items arrays |
| **DB Queries** | `3 + 3C` | 3 fixed + 3 per cart item (SELECT FOR UPDATE, UPDATE, INSERT) |
| **Transaction** | ACID-compliant | Uses pessimistic locking (FOR UPDATE) |

**Transaction Isolation:**
- Uses `FOR UPDATE` for row-level locking
- Prevents race conditions in concurrent orders
- Rollback on any failure

---

#### **6. User Authentication**

##### **Signup (`signup_script.php`)**
```php
// Line 21: Check existing email
SELECT * FROM users WHERE email_id='$email'
// Time: O(1) - indexed on email_id

// Line 30: Insert new user
INSERT INTO users(email_id, first_name, last_name, phone, password) 
VALUES('$email', '$first', '$last', '$phone', '$pass')
// Time: O(1)
```

| Metric | Complexity |
|--------|-----------|
| **Time** | `O(1)` |
| **Space** | `O(1)` |
| **DB Queries** | `2` |

##### **Login (`login_script.php`)**
```php
// Line 12: Verify credentials
SELECT id, email_id, password FROM users 
WHERE email_id='$email' AND password='$password'
// Time: O(1) - indexed lookup
```

| Metric | Complexity |
|--------|-----------|
| **Time** | `O(1)` |
| **Space** | `O(1)` |
| **DB Queries** | `1` |

**Security Note:** Uses MD5 hashing (⚠️ deprecated, see Security section)

---

### Admin Operations

#### **1. Dashboard (`admin/dashboard.php`)**

**Code Flow:**
```php
// Lines 7-12: Aggregate queries
$total_products = get_total_products();
  └─> SELECT COUNT(*) FROM products

$total_orders = get_total_orders_count();
  └─> SELECT COUNT(*) FROM orders

$low_stock = get_low_stock_products();
  └─> SELECT COUNT(*) FROM products WHERE stock_quantity < 5 AND stock_quantity > 0

$out_of_stock = get_out_of_stock_products();
  └─> SELECT COUNT(*) FROM products WHERE stock_quantity = 0

$pending_orders = get_pending_orders_count();
  └─> SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'

$total_revenue = get_total_revenue();
  └─> SELECT SUM(final_amount) FROM orders WHERE order_status != 'Cancelled'

// Line 221: Recent products
$products = get_all_products();
  └─> SELECT * FROM products ORDER BY id DESC
```

| Metric | Complexity | Explanation |
|--------|-----------|-------------|
| **Time** | `O(P)` | P = products. Bottleneck: `get_all_products()` |
| **Space** | `O(P)` | Stores all products (only displays 5) |
| **DB Queries** | `7` | 6 aggregates + 1 full table scan |

**Optimization Opportunity:** Only fetch 5 products with `LIMIT 5`

---

#### **2. Manage Products (`admin/products.php`)**

**Code Flow:**
```php
// Line 6: Fetch all products
$products = get_all_products();
  └─> SELECT * FROM products ORDER BY id DESC
// Time: O(P), Space: O(P)

// Lines 9-16: Filter in PHP (⚠️ inefficient)
if ($filter == 'low_stock') {
    $products = array_filter($products, function($p) {
        return $p['stock_quantity'] > 0 && $p['stock_quantity'] < 5;
    });
}
// Time: O(P), Space: O(P)

// Lines 91-125: Display loop
foreach ($products as $product) {
    // Render table row
}
// Time: O(P)
```

| Metric | Complexity | Explanation |
|--------|-----------|-------------|
| **Time** | `O(P)` | Fetches all, filters in PHP |
| **Space** | `O(P)` | Full product array in memory |
| **DB Queries** | `1` | Single query, no pagination |

**Issues:**
- ⚠️ **No pagination** - loads all products
- ⚠️ **PHP filtering** - should use SQL WHERE clause
- ⚠️ **Memory inefficient** for large catalogs (1000+ products)

---

#### **3. Manage Orders (`admin/orders.php`)**

**Code Flow:**
```php
// Line 8: Fetch orders with filter
$orders = get_all_orders($filter);
  └─> SELECT o.*, u.first_name, u.last_name, u.email_id 
      FROM orders o 
      INNER JOIN users u ON o.user_id = u.id 
      WHERE o.order_status = '$filter'  -- if filter set
      ORDER BY o.order_date DESC
// Time: O(O), Space: O(O)

// Lines 70-107: Display loop with N+1 problem
foreach ($orders as $order) {
    // Line 83: Count items for EACH order
    $items_query = "SELECT COUNT(*) FROM order_items WHERE order_id='" . $order['id'] . "'";
    // ⚠️ N+1 Query Problem
}
// Time: O(O²), Space: O(O)
```

| Metric | Complexity | Explanation |
|--------|-----------|-------------|
| **Time** | `O(O²)` | **N+1 problem**: O queries for item counts |
| **Space** | `O(O)` | Stores all orders |
| **DB Queries** | `1 + O` | 1 order query + O item count queries |

**Critical Issue:** N+1 query problem on order item counts

---

#### **4. Order Details (`admin/order_details.php`)**

**Code Flow:**
```php
// get_order_with_items() in order_functions.php

// Lines 35-39: Fetch order
SELECT o.*, u.first_name, u.last_name, u.email_id, u.phone 
FROM orders o 
INNER JOIN users u ON o.user_id = u.id 
WHERE o.id='$order_id'
// Time: O(1)

// Lines 47-54: Fetch order items
SELECT * FROM order_items WHERE order_id='$order_id'
// Time: O(I), I = items in this order
```

| Metric | Complexity | Explanation |
|--------|-----------|-------------|
| **Time** | `O(I)` | I = items in order (typically 1-10) |
| **Space** | `O(I)` | Stores order items |
| **DB Queries** | `2` | Order + items |

---

## Customer Flow Code Paths

### Flow 1: User Registration

```
┌─────────────────────────────────────────────────────────────┐
│ 1. User clicks "Sign Up" in header_menu.php                │
│    → Opens Bootstrap modal (#signup)                        │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. User fills form and submits                             │
│    → POST to signup_script.php                             │
│    → Fields: eMail, password, firstName, lastName, phone   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. signup_script.php (Lines 5-19)                          │
│    ├─ Sanitize inputs with mysqli_real_escape_string()     │
│    ├─ Hash password with MD5 (⚠️ weak)                     │
│    ├─ Check if email exists:                               │
│    │  SELECT * FROM users WHERE email_id='$email'          │
│    │  Time: O(1), Space: O(1)                              │
│    └─ If exists: redirect to index.php?error=...           │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Insert new user (Line 30)                               │
│    INSERT INTO users(...) VALUES(...)                      │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. Create session (Lines 35-36)                            │
│    $_SESSION['email'] = $email                             │
│    $_SESSION['user_id'] = mysqli_insert_id($con)           │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. Redirect to products.php                                │
│    header('location:products.php')                         │
└─────────────────────────────────────────────────────────────┘

Total Time: O(1)
Total Space: O(1)
DB Queries: 2
```

---

### Flow 2: User Login

```
┌─────────────────────────────────────────────────────────────┐
│ 1. User clicks "Login" in header_menu.php                  │
│    → Opens Bootstrap modal (#login)                         │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. User submits credentials                                │
│    → POST to login_script.php                              │
│    → Fields: lemail, lpassword                             │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. login_script.php (Lines 5-14)                           │
│    ├─ Sanitize email and password                          │
│    ├─ Hash password with MD5                               │
│    └─ Verify credentials:                                  │
│       SELECT id, email_id, password FROM users             │
│       WHERE email_id='$email' AND password='$password'     │
│       Time: O(1), Space: O(1)                              │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Check result (Lines 15-23)                              │
│    ├─ If no match: redirect to index.php?errorl=...        │
│    └─ If match:                                            │
│       ├─ Set $_SESSION['email']                            │
│       ├─ Set $_SESSION['user_id']                          │
│       └─ Redirect to products.php                          │
└─────────────────────────────────────────────────────────────┘

Total Time: O(1)
Total Space: O(1)
DB Queries: 1
```

---

### Flow 3: Browse Products

```
┌─────────────────────────────────────────────────────────────┐
│ 1. User navigates to products.php                          │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. products.php: Fetch all products (Line 69)              │
│    SELECT * FROM products ORDER BY category, id            │
│    Time: O(P), Space: O(P)                                 │
│    Result: ~16 products in typical setup                   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Group products by category (Lines 74-80)                │
│    while ($row = mysqli_fetch_assoc($result)) {            │
│        $products_by_category[$category][] = $row;          │
│    }                                                        │
│    Time: O(P), Space: O(P)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Render categories (Lines 90-104)                        │
│    foreach ($category_config as $category => $config) {    │
│        foreach ($products_by_category[$category] as $p) {  │
│            render_product_card($p); // Pass full array     │
│        }                                                    │
│    }                                                        │
│    Time: O(P), Space: O(1) per iteration                   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. render_product_card() in includes/product_card.php      │
│    ├─ Extract product data from array (no DB query!)       │
│    ├─ Check if added to cart (Line 12):                    │
│    │  check_if_added_to_cart($product_id)                  │
│    │  └─> SELECT * FROM users_products                     │
│    │      WHERE item_id='$id' AND user_id='$uid'           │
│    │      AND status='Added to cart'                       │
│    │      Time: O(1) per product, O(P) total               │
│    └─ Render HTML card with stock status                   │
└─────────────────────────────────────────────────────────────┘

Total Time: O(P) - Linear with product count
Total Space: O(P) - Stores all products
DB Queries: 1 + P (1 for products, P for cart checks)

⚠️ Remaining Optimization: Batch cart checks with single query
```

---

### Flow 4: Add to Cart

```
┌─────────────────────────────────────────────────────────────┐
│ 1. User selects quantity and clicks "Add to Cart"          │
│    → JavaScript: addToCart(productId, maxStock)            │
│    → Client-side validation (Lines 122-136 in products.php)│
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Redirect to addtocart.php?id=X&qty=Y                    │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. addtocart.php: Validate and sanitize (Lines 5-12)       │
│    ├─ Check if id is numeric                               │
│    ├─ Sanitize with mysqli_real_escape_string()            │
│    └─ Validate quantity >= 1                               │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Check current cart quantity (Line 14)                   │
│    get_cart_item_quantity($user_id, $item_id)              │
│    └─> SELECT SUM(quantity) FROM users_products            │
│        WHERE user_id='$uid' AND item_id='$iid'             │
│        AND status='Added To Cart'                          │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. Validate total stock (Line 17)                          │
│    validate_stock($item_id, $current_cart_qty + $quantity) │
│    └─> SELECT stock_quantity FROM products WHERE id='$id'  │
│    Time: O(1), Space: O(1)                                 │
│                                                             │
│    If insufficient:                                         │
│    → Redirect to products.php?error=insufficient_stock     │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. Check if item already in cart (Line 27)                 │
│    SELECT id FROM users_products                           │
│    WHERE user_id='$uid' AND item_id='$iid'                 │
│    AND status='Added to cart' LIMIT 1                      │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 7. Update or Insert (Lines 30-34)                          │
│    If exists:                                              │
│      UPDATE users_products SET quantity = quantity + $qty  │
│      WHERE user_id='$uid' AND item_id='$iid'               │
│    Else:                                                   │
│      INSERT INTO users_products(...) VALUES(...)           │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 8. Redirect to products.php?success=added                  │
└─────────────────────────────────────────────────────────────┘

Total Time: O(1) - Constant time operation
Total Space: O(1) - Constant memory
DB Queries: 4 (all indexed)
```

---

### Flow 5: View Cart

```
┌─────────────────────────────────────────────────────────────┐
│ 1. User clicks "Cart" in navigation                        │
│    → Navigate to cart.php                                  │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. cart.php: Session check (Lines 4-6)                     │
│    if (!isset($_SESSION['email'])) {                       │
│        header('location: index.php');                      │
│    }                                                        │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Fetch cart items with JOIN (Lines 67-70)                │
│    SELECT products.price, products.id, products.name,      │
│           products.stock_quantity, users_products.quantity,│
│           products.unit, users_products.id AS cart_id      │
│    FROM users_products                                     │
│    JOIN products ON users_products.item_id = products.id   │
│    WHERE users_products.user_id='$user_id'                 │
│    AND status='Added To Cart'                              │
│    Time: O(C), Space: O(C)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Fetch user phone (Lines 168-173)                        │
│    SELECT phone FROM users WHERE id='$user_id'             │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. Process cart items (Lines 89-124)                       │
│    while ($row = mysqli_fetch_array($result)) {            │
│        $quantity = $row["Quantity"];                       │
│        $item_total = $row["Price"] * $quantity;            │
│        $sum += $item_total;                                │
│                                                             │
│        // Check stock status                               │
│        if ($row["Stock"] == 0) {                           │
│            $stock_status = "Out of Stock";                 │
│            $has_stock_issue = true;                        │
│        } elseif ($row["Stock"] < $quantity) {              │
│            $stock_status = "Only X available";             │
│            $has_stock_issue = true;                        │
│        }                                                    │
│                                                             │
│        // Render table row with:                           │
│        // - Item details                                   │
│        // - Quantity input (with updateQuantity JS)        │
│        // - Stock status badge                             │
│        // - Remove button                                  │
│    }                                                        │
│    Time: O(C), Space: O(1) per iteration                   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. Display total and action button (Lines 127-132)         │
│    if ($has_stock_issue) {                                 │
│        → Show warning, disable "Confirm Order"             │
│    } else {                                                │
│        → Show "Confirm Order" button (opens modal)         │
│    }                                                        │
└─────────────────────────────────────────────────────────────┘

Total Time: O(C) - Linear with cart size
Total Space: O(C) - Stores cart items
DB Queries: 2 (cart items + user phone)
```

---

### Flow 6: Update Cart Quantity

```
┌─────────────────────────────────────────────────────────────┐
│ 1. User changes quantity in cart.php                       │
│    → JavaScript: updateQuantity(cartId, productId, stock)  │
│    → Client-side validation (Lines 240-256)                │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Redirect to update-cart-quantity.php?cart_id=X&qty=Y    │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. update-cart-quantity.php: Validate (Lines 10-18)        │
│    ├─ Check session                                        │
│    ├─ Sanitize cart_id and qty                             │
│    └─ Validate qty >= 1                                    │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Verify cart ownership (Line 20)                         │
│    SELECT item_id FROM users_products                      │
│    WHERE id='$cart_id' AND user_id='$user_id'              │
│    AND status='Added to cart'                              │
│    Time: O(1), Space: O(1)                                 │
│                                                             │
│    If not found: redirect with error                       │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. Validate stock availability (Line 31)                   │
│    validate_stock($product_id, $new_quantity)              │
│    └─> SELECT stock_quantity FROM products                 │
│        WHERE id='$product_id'                              │
│    Time: O(1), Space: O(1)                                 │
│                                                             │
│    If insufficient: redirect with available count          │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. Update quantity (Line 37)                               │
│    UPDATE users_products SET quantity = $new_quantity      │
│    WHERE id='$cart_id' AND user_id='$user_id'              │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 7. Redirect to cart.php?success=quantity_updated           │
└─────────────────────────────────────────────────────────────┘

Total Time: O(1) - All indexed queries
Total Space: O(1) - Constant memory
DB Queries: 3
```

---

### Flow 7: Remove from Cart

```
┌─────────────────────────────────────────────────────────────┐
│ 1. User clicks "Remove" link in cart.php                   │
│    → Navigate to cart-remove.php?id=X                      │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. cart-remove.php: Validate and delete (Lines 5-12)       │
│    if (isset($_GET['id']) && is_numeric($_GET['id'])) {    │
│        DELETE FROM users_products                          │
│        WHERE item_id='$item_id' AND user_id='$user_id'     │
│    }                                                        │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Redirect to cart.php                                    │
└─────────────────────────────────────────────────────────────┘

Total Time: O(1)
Total Space: O(1)
DB Queries: 1
```

---

### Flow 8: Place Order (Transaction Flow)

```
┌─────────────────────────────────────────────────────────────┐
│ 1. User clicks "Confirm Order" in cart.php                 │
│    → Opens address modal (#addressModal)                   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. User fills delivery info and submits                    │
│    → POST to success.php                                   │
│    → Fields: delivery_address, delivery_phone              │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. success.php: Call transaction function (Line 10)        │
│    $result = create_order_with_transaction(                │
│        $user_id, $delivery_address, $delivery_phone        │
│    );                                                       │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. BEGIN TRANSACTION (Line 158)                            │
│    mysqli_begin_transaction($con, MYSQLI_TRANS_START_READ_WRITE)│
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. Fetch cart items (Lines 160-175)                        │
│    SELECT up.item_id, up.quantity, p.name, p.price,        │
│           p.unit, p.discount                               │
│    FROM users_products up                                  │
│    INNER JOIN products p ON up.item_id = p.id              │
│    WHERE up.user_id='$user_id' AND up.status='Added to cart'│
│    Time: O(C), Space: O(C)                                 │
│                                                             │
│    Store in $cart_items array                              │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. Lock products and validate stock (Lines 179-202)        │
│    foreach ($cart_items as $item) {                        │
│        // PESSIMISTIC LOCK                                 │
│        SELECT id, stock_quantity, name FROM products       │
│        WHERE id='$product_id' FOR UPDATE;                  │
│        // Locks row until transaction ends                 │
│                                                             │
│        if (available_stock < requested_qty) {              │
│            $insufficient_stock[] = item;                   │
│        }                                                    │
│    }                                                        │
│    Time: O(C), Space: O(C)                                 │
│                                                             │
│    If any stock issue:                                     │
│    → ROLLBACK and redirect with error details              │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 7. Deduct stock (Lines 210-220)                            │
│    foreach ($cart_items as $item) {                        │
│        UPDATE products                                     │
│        SET stock_quantity = stock_quantity - $requested_qty│
│        WHERE id='$product_id'                              │
│    }                                                        │
│    Time: O(C), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 8. Generate order number (Line 222)                        │
│    $order_number = 'ORD-' . str_pad($user_id, 4, '0')     │
│                  . '-' . date('Ymd')                       │
│                  . '-' . str_pad(rand(1,9999), 4, '0')    │
│    Example: ORD-0067-20260323-1234                         │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 9. Calculate totals (Lines 228-247)                        │
│    foreach ($cart_items as $item) {                        │
│        $subtotal = price * quantity                        │
│        $item_discount = (price * discount% / 100) * qty    │
│        $final_price = subtotal - item_discount             │
│        $total_amount += subtotal                           │
│        $discount_amount += item_discount                   │
│    }                                                        │
│    Time: O(C), Space: O(C)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 10. Create order record (Lines 257-264)                    │
│     INSERT INTO orders (user_id, order_number,             │
│         total_amount, discount_amount, final_amount,       │
│         payment_status, delivery_address, delivery_phone)  │
│     VALUES (...)                                           │
│     Time: O(1), Space: O(1)                                │
│     $order_id = mysqli_insert_id($con)                     │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 11. Create order items (Lines 268-287)                     │
│     foreach ($order_items as $item) {                      │
│         INSERT INTO order_items (order_id, product_id,     │
│             product_name, product_price, quantity, ...)    │
│         VALUES (...)                                       │
│     }                                                       │
│     Time: O(C), Space: O(1)                                │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 12. Update cart status (Line 289)                          │
│     UPDATE users_products SET status='Confirmed'           │
│     WHERE user_id='$user_id' AND status='Added to cart'    │
│     Time: O(1), Space: O(1)                                │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 13. COMMIT TRANSACTION (Line 296)                          │
│     mysqli_commit($con)                                    │
│     → All changes persisted atomically                     │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 14. Store order_id in session (success.php Line 26)        │
│     $_SESSION['last_order_id'] = $result['order_id']       │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 15. Display success page with:                             │
│     ├─ Order confirmation message                          │
│     ├─ Order number and total                              │
│     ├─ View Invoice button                                 │
│     └─ Download PDF button                                 │
└─────────────────────────────────────────────────────────────┘

Total Time: O(C) - Linear with cart size
Total Space: O(C) - Stores cart items and order items
DB Queries: 3 + 3C (3 fixed + 3 per cart item)
Transaction: ACID-compliant with pessimistic locking
```

**Transaction Properties:**
- **Atomicity**: All operations succeed or all fail
- **Consistency**: Stock never goes negative
- **Isolation**: `FOR UPDATE` prevents concurrent modifications
- **Durability**: Changes persisted after commit

---

### Flow 9: View/Download Invoice

```
┌─────────────────────────────────────────────────────────────┐
│ 1. User clicks "View Invoice" or "Download PDF"            │
│    → view_invoice.php?order_id=X (HTML)                    │
│    → generate_invoice.php?order_id=X (PDF)                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Fetch order details                                     │
│    get_order_details($order_id)                            │
│    └─> SELECT o.*, u.first_name, u.last_name, u.email_id  │
│        FROM orders o JOIN users u ON o.user_id = u.id      │
│        WHERE o.id='$order_id'                              │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Fetch order items                                       │
│    get_order_items($order_id)                              │
│    └─> SELECT * FROM order_items WHERE order_id='$oid'     │
│    Time: O(I), Space: O(I)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Generate output                                         │
│    HTML (view_invoice.php):                                │
│    ├─ Render invoice template                              │
│    └─ Time: O(I), Space: O(1)                              │
│                                                             │
│    PDF (generate_invoice.php):                             │
│    ├─ Initialize FPDF                                      │
│    ├─ Add header, customer info, items table               │
│    ├─ Output PDF to browser                                │
│    └─ Time: O(I), Space: O(I)                              │
└─────────────────────────────────────────────────────────────┘

Total Time: O(I) - Linear with order items
Total Space: O(I) - Stores order items
DB Queries: 2
```

---

### Flow 10: Logout

```
┌─────────────────────────────────────────────────────────────┐
│ 1. User clicks "Logout" in navigation                      │
│    → Navigate to logout_script.php                         │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. logout_script.php (Lines 2-4)                           │
│    session_unset();   // Clear session variables           │
│    session_destroy(); // Destroy session                   │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Redirect to index.php                                   │
└─────────────────────────────────────────────────────────────┘

Total Time: O(1)
Total Space: O(1)
DB Queries: 0
```

---

## Admin Flow Code Paths

### Flow 1: Admin Login

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Admin navigates to admin/login.php                      │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Submit credentials to admin/login_process.php           │
│    → POST: username, password                              │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. verify_admin_credentials() in admin_common.php          │
│    SELECT * FROM admins                                    │
│    WHERE username='$username' AND password='$password'     │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Set session and redirect                                │
│    $_SESSION['admin_id'] = $admin['id']                    │
│    header('location: dashboard.php')                       │
└─────────────────────────────────────────────────────────────┘

Total Time: O(1)
Total Space: O(1)
DB Queries: 1
```

---

### Flow 2: View Dashboard

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Admin navigates to admin/dashboard.php                  │
│    → check_admin_login() verifies session                  │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Fetch dashboard metrics (Lines 7-12)                    │
│    ├─ get_total_products()                                 │
│    │  └─> SELECT COUNT(*) FROM products                    │
│    │      Time: O(1), Space: O(1)                          │
│    │                                                        │
│    ├─ get_total_orders_count()                             │
│    │  └─> SELECT COUNT(*) FROM orders                      │
│    │      Time: O(1), Space: O(1)                          │
│    │                                                        │
│    ├─ get_low_stock_products()                             │
│    │  └─> SELECT COUNT(*) FROM products                    │
│    │      WHERE stock_quantity < 5 AND stock_quantity > 0  │
│    │      Time: O(1), Space: O(1)                          │
│    │                                                        │
│    ├─ get_out_of_stock_products()                          │
│    │  └─> SELECT COUNT(*) FROM products                    │
│    │      WHERE stock_quantity = 0                         │
│    │      Time: O(1), Space: O(1)                          │
│    │                                                        │
│    ├─ get_pending_orders_count()                           │
│    │  └─> SELECT COUNT(*) FROM orders                      │
│    │      WHERE order_status = 'Pending'                   │
│    │      Time: O(1), Space: O(1)                          │
│    │                                                        │
│    └─ get_total_revenue()                                  │
│       └─> SELECT SUM(final_amount) FROM orders             │
│           WHERE order_status != 'Cancelled'                │
│           Time: O(1), Space: O(1)                          │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Fetch recent products (Line 221)                        │
│    $products = get_all_products();                         │
│    └─> SELECT * FROM products ORDER BY id DESC             │
│    Time: O(P), Space: O(P)                                 │
│    ⚠️ Fetches ALL products but only displays 5             │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Render dashboard with metrics and recent products       │
│    Time: O(P), Space: O(1) for rendering                   │
└─────────────────────────────────────────────────────────────┘

Total Time: O(P) - Bottleneck: fetching all products
Total Space: O(P) - Stores full product array
DB Queries: 7 (6 aggregates + 1 full scan)

⚠️ Optimization: Change to SELECT * FROM products LIMIT 5
```

---

### Flow 3: Manage Products

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Admin navigates to admin/products.php                   │
│    → Optional filter: ?filter=low_stock or out_of_stock    │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Fetch all products (Line 6)                             │
│    $products = get_all_products();                         │
│    └─> SELECT * FROM products ORDER BY id DESC             │
│    Time: O(P), Space: O(P)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Filter in PHP (Lines 9-16) ⚠️ INEFFICIENT               │
│    if ($filter == 'low_stock') {                           │
│        $products = array_filter($products, function($p) {  │
│            return $p['stock_quantity'] > 0                 │
│                && $p['stock_quantity'] < 5;                │
│        });                                                 │
│    }                                                        │
│    Time: O(P), Space: O(P)                                 │
│    ⚠️ Should use SQL WHERE clause instead                  │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Display products table (Lines 91-125)                   │
│    foreach ($products as $product) {                       │
│        // Render table row with:                           │
│        // - Product details                                │
│        // - Stock status badge                             │
│        // - Edit/Delete buttons                            │
│    }                                                        │
│    Time: O(P), Space: O(1) per iteration                   │
└─────────────────────────────────────────────────────────────┘

Total Time: O(P) - Linear with product count
Total Space: O(P) - Full product array
DB Queries: 1 (no pagination)

⚠️ Issues:
- No pagination (loads all products)
- PHP filtering instead of SQL WHERE
- Memory inefficient for large catalogs
```

---

### Flow 4: Add Product

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Admin clicks "Add New Product"                          │
│    → Navigate to admin/add_product.php                     │
│    → Display form                                          │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Admin fills form and submits                            │
│    → POST to admin/add_product_process.php                 │
│    → Fields: name, category, price, discount, stock, unit  │
│    → File: product image                                   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Upload image (upload_product_image() in admin_common.php)│
│    ├─ Validate file type (jpg, png, gif, webp)             │
│    ├─ Check file size (max 5MB)                            │
│    ├─ Generate unique filename: uniqid() + timestamp       │
│    └─ move_uploaded_file() to images/ directory            │
│    Time: O(F), F = file size                               │
│    Space: O(F)                                             │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Insert product (add_product() in admin_common.php)      │
│    INSERT INTO products (name, category, price, unit,      │
│        discount, stock_quantity, image_path)               │
│    VALUES (...)                                            │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. Redirect to products.php?success=...                    │
└─────────────────────────────────────────────────────────────┘

Total Time: O(F) - Dominated by file upload
Total Space: O(F) - File in memory during upload
DB Queries: 1
```

---

### Flow 5: Edit Product

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Admin clicks "Edit" button                              │
│    → Navigate to admin/edit_product.php?id=X               │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Fetch product details                                   │
│    get_product_by_id($product_id)                          │
│    └─> SELECT * FROM products WHERE id='$product_id'       │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Display pre-filled form                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Admin updates and submits                               │
│    → POST to admin/edit_product_process.php                │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. Update product (update_product() in admin_common.php)   │
│    UPDATE products SET name='$name', category='$category', │
│        price=$price, unit='$unit', discount=$discount,     │
│        stock_quantity=$stock, image_path='$path'           │
│    WHERE id=$id                                            │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. Redirect to products.php?success=...                    │
└─────────────────────────────────────────────────────────────┘

Total Time: O(1) or O(F) if image uploaded
Total Space: O(1) or O(F) if image uploaded
DB Queries: 2 (fetch + update)
```

---

### Flow 6: Manage Orders

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Admin navigates to admin/orders.php                     │
│    → Optional filter: ?filter=pending/processing/etc       │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Fetch orders (get_all_orders() in order_functions.php)  │
│    SELECT o.*, u.first_name, u.last_name, u.email_id       │
│    FROM orders o                                           │
│    INNER JOIN users u ON o.user_id = u.id                  │
│    WHERE o.order_status = '$filter'  -- if filter set      │
│    ORDER BY o.order_date DESC                              │
│    Time: O(O), Space: O(O)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Display orders with N+1 problem (Lines 70-107)          │
│    foreach ($orders as $order) {                           │
│        // Line 83: Count items for EACH order              │
│        SELECT COUNT(*) FROM order_items                    │
│        WHERE order_id='" . $order['id'] . "'               │
│        // ⚠️ O queries for O orders                        │
│                                                             │
│        // Render order row                                 │
│    }                                                        │
│    Time: O(O²), Space: O(1) per iteration                  │
└─────────────────────────────────────────────────────────────┘

Total Time: O(O²) - N+1 query problem
Total Space: O(O) - Stores all orders
DB Queries: 1 + O (1 for orders + O for item counts)

⚠️ Critical Issue: N+1 query problem
Fix: Use LEFT JOIN with COUNT() or subquery
```

**Recommended Fix:**
```php
SELECT o.*, u.first_name, u.last_name, u.email_id,
       COUNT(oi.id) as items_count
FROM orders o
INNER JOIN users u ON o.user_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id
ORDER BY o.order_date DESC
```

---

### Flow 7: View Order Details

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Admin clicks "View Details" for an order                │
│    → Navigate to admin/order_details.php?id=X              │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Fetch order with items                                  │
│    get_order_with_items($order_id)                         │
│    ├─ Query 1: Fetch order details                         │
│    │  SELECT o.*, u.first_name, u.last_name, u.email_id   │
│    │  FROM orders o JOIN users u ON o.user_id = u.id       │
│    │  WHERE o.id='$order_id'                               │
│    │  Time: O(1), Space: O(1)                              │
│    │                                                        │
│    └─ Query 2: Fetch order items                           │
│       SELECT * FROM order_items WHERE order_id='$order_id' │
│       Time: O(I), Space: O(I)                              │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Display order details                                   │
│    ├─ Customer information                                 │
│    ├─ Order status and payment info                        │
│    ├─ Items table (foreach loop)                           │
│    └─ Total calculations                                   │
│    Time: O(I), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘

Total Time: O(I) - Linear with order items
Total Space: O(I) - Stores order items
DB Queries: 2
```

---

### Flow 8: Update Order Status

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Admin selects new status in order_details.php           │
│    → POST to admin/update_order_status.php                 │
│    → Fields: order_id, status                              │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Update order status                                     │
│    update_order_status($order_id, $status)                 │
│    └─> UPDATE orders SET order_status='$status'            │
│        WHERE id='$order_id'                                │
│    Time: O(1), Space: O(1)                                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Redirect back to order_details.php?success=...          │
└─────────────────────────────────────────────────────────────┘

Total Time: O(1)
Total Space: O(1)
DB Queries: 1
```

---

### Flow 9: Delete Product

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Admin clicks "Delete" with confirmation                 │
│    → Navigate to admin/delete_product.php?id=X             │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Delete product                                          │
│    delete_product($product_id)                             │
│    └─> DELETE FROM products WHERE id=$product_id           │
│    Time: O(1), Space: O(1)                                 │
│    ⚠️ May violate foreign key constraints if in orders     │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Redirect to products.php?success=deleted                │
└─────────────────────────────────────────────────────────────┘

Total Time: O(1)
Total Space: O(1)
DB Queries: 1
```

---

## Time & Space Complexity Summary

### Customer Operations

| Operation | Time Complexity | Space Complexity | DB Queries | Optimization Status |
|-----------|----------------|------------------|------------|-------------------|
| **Product Display** | O(P) | O(P) | 1 + P | ✅ Fixed N+1 for products, ⚠️ cart checks remain |
| **Add to Cart** | O(1) | O(1) | 4 | ✅ Optimized |
| **View Cart** | O(C) | O(C) | 2 | ✅ Optimized |
| **Update Cart Qty** | O(1) | O(1) | 3 | ✅ Optimized |
| **Remove from Cart** | O(1) | O(1) | 1 | ✅ Optimized |
| **Place Order** | O(C) | O(C) | 3 + 3C | ✅ Transaction-safe |
| **View Invoice** | O(I) | O(I) | 2 | ✅ Optimized |
| **User Signup** | O(1) | O(1) | 2 | ✅ Optimized |
| **User Login** | O(1) | O(1) | 1 | ✅ Optimized |
| **User Logout** | O(1) | O(1) | 0 | ✅ Optimized |

### Admin Operations

| Operation | Time Complexity | Space Complexity | DB Queries | Optimization Status |
|-----------|----------------|------------------|------------|-------------------|
| **Dashboard** | O(P) | O(P) | 7 | ⚠️ Fetches all products, displays 5 |
| **Manage Products** | O(P) | O(P) | 1 | ⚠️ No pagination, PHP filtering |
| **Manage Orders** | O(O²) | O(O) | 1 + O | ❌ N+1 query problem |
| **Order Details** | O(I) | O(I) | 2 | ✅ Optimized |
| **Add Product** | O(F) | O(F) | 1 | ✅ Optimized |
| **Edit Product** | O(F) | O(F) | 2 | ✅ Optimized |
| **Delete Product** | O(1) | O(1) | 1 | ✅ Optimized |
| **Update Order Status** | O(1) | O(1) | 1 | ✅ Optimized |

**Annotations:**
- P = Total products in database
- U = Total users
- C = Items in user's cart (typically 1-20)
- O = Total orders
- I = Items in a specific order (typically 1-10)
- F = File size for image uploads

---
