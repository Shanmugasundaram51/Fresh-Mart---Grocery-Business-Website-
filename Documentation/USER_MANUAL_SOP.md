# Fresh Mart E-Commerce Website - Standard Operating Procedure (SOP)

**Version:** 2.1  
**Last Updated:** March 29, 2026  
**System:** Online Grocery Shopping Platform

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Initial Setup](#initial-setup)
3. [Customer Operations](#customer-operations)
4. [Admin Operations](#admin-operations)
5. [Order Management](#order-management)
6. [Stock Management](#stock-management)
7. [Invoice System](#invoice-system)
8. [Troubleshooting](#troubleshooting)
9. [Security Guidelines](#security-guidelines)
10. [Maintenance Procedures](#maintenance-procedures)

---

## 1. System Overview

### 1.1 System Description

Fresh Mart is a PHP-based e-commerce platform for online grocery shopping with the following capabilities:
- Customer registration and authentication
- Product browsing with real-time stock information
- Shopping cart with quantity management
- Secure order placement with concurrency control
- Admin panel for product and order management
- Invoice generation and email delivery
- Stock tracking and alerts

### 1.2 System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    FRESH MART SYSTEM                     │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ┌──────────────┐         ┌──────────────┐             │
│  │   CUSTOMER   │         │    ADMIN     │             │
│  │   FRONTEND   │         │    PANEL     │             │
│  └──────┬───────┘         └──────┬───────┘             │
│         │                        │                      │
│         └────────┬───────────────┘                      │
│                  │                                      │
│         ┌────────▼────────┐                            │
│         │  PHP BACKEND    │                            │
│         │  (Business Logic)│                           │
│         └────────┬────────┘                            │
│                  │                                      │
│         ┌────────▼────────┐                            │
│         │  MySQL DATABASE │                            │
│         │  (InnoDB Engine)│                            │
│         └─────────────────┘                            │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

### 1.3 Technology Stack

- **Backend:** PHP 7.0+
- **Database:** MySQL 5.7+ (InnoDB)
- **Frontend:** HTML5, CSS3, Bootstrap 4
- **JavaScript:** jQuery 3.3.1
- **PDF Generation:** FPDF Library
- **Server:** Apache (MAMP/XAMPP/WAMP)

### 1.4 System Requirements

**Server Requirements:**
- PHP 7.0 or higher
- MySQL 5.7 or higher
- Apache Web Server
- 100MB disk space minimum
- PHP extensions: mysqli, gd, mbstring

**Client Requirements:**
- Modern web browser (Chrome, Firefox, Safari, Edge)
- JavaScript enabled
- Cookies enabled

---

## 2. Initial Setup

### 2.1 Installation Procedure

#### Step 1: Install Web Server

**For macOS (MAMP):**
1. Download MAMP from https://www.mamp.info
2. Install MAMP
3. Start Apache and MySQL servers
4. Note the port numbers (default: 8888 for web, 8889 for MySQL)

**For Windows (XAMPP):**
1. Download XAMPP from https://www.apachefriends.org
2. Install XAMPP
3. Start Apache and MySQL from control panel

#### Step 2: Deploy Project Files

1. Copy all project files to web server directory:
   - **MAMP:** `/Applications/MAMP/htdocs/onlinesale/ECOMMERCE-WEBSITE/`
   - **XAMPP:** `C:\xampp\htdocs\onlinesale\ECOMMERCE-WEBSITE\`

2. Verify folder structure:
   ```
   ECOMMERCE-WEBSITE/
   ├── admin/
   ├── images/
   ├── includes/
   ├── Documentation/
   ├── SQL Setup files/
   ├── index.php
   ├── products.php
   ├── cart.php
   ├── success.php
   └── style.css
   ```

#### Step 3: Database Setup

**RECOMMENDED: Single File Setup (New in v2.1)**
```bash
# Navigate to SQL Setup files directory
cd /Applications/MAMP/htdocs/onlinesale/ECOMMERCE-WEBSITE/SQL\ Setup\ files/

# Import complete setup (ONE FILE - includes everything!)
/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root -proot < complete_setup.sql
```

This single file creates:
- Database: `onlinesale`
- All 7 tables (users, products, orders, order_items, users_products, admins, invoice_logs)
- 24 products across 4 categories
- Default admin account (admin/admin123)
- 2 test customer accounts

**Alternative: Using phpMyAdmin**
1. Open phpMyAdmin (http://localhost:8888/phpMyAdmin)
2. Click "Import" tab
3. Select `SQL Setup files/complete_setup.sql`
4. Click "Go"
5. Done! Database is ready

**Legacy Setup (Old Method - Not Recommended):**
If you need to run individual files:
```bash
mysql -u root -proot onlinesale < onlinesale.sql
mysql -u root -proot onlinesale < create_orders_tables.sql
mysql -u root -proot onlinesale < admin_setup.sql
```

#### Step 4: Configure Database Connection

Edit `includes/common.php` (line 5):
```php
$con = mysqli_connect("localhost:8889", "root", "root", "onlinesale");
```

Edit `admin/includes/admin_common.php` (line 5):
```php
$con = mysqli_connect("localhost:8889", "root", "root", "onlinesale");
```

**Connection Parameters:**
- **Host:** `localhost:8889` (MAMP) or `localhost:3306` (XAMPP)
- **Username:** `root`
- **Password:** `root` (MAMP) or empty (XAMPP)
- **Database:** `onlinesale`

#### Step 5: Set Folder Permissions

```bash
chmod 755 images/
chmod 644 images/*
```

#### Step 6: Verify Installation

1. Open browser: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/`
2. Homepage should load with product categories
3. Products should display with images
4. Registration and login should work

---

## 3. Customer Operations

### 3.1 Customer Registration

**Procedure:**

1. Navigate to homepage
2. Click "Sign Up" button in header
3. Fill registration form:
   - First Name (required)
   - Last Name (required)
   - Email Address (required, unique)
   - Password (required, min 6 characters)
   - Phone Number (required, 10 digits)
4. Click "Sign Up"
5. System validates:
   - Email uniqueness
   - Password strength
   - Phone number format
6. Success: User is logged in automatically
7. Failure: Error message displayed

**Validation Rules:**
- Email must be unique in system
- Password minimum 6 characters
- Phone must be 10 digits
- All fields are mandatory

### 3.2 Customer Login

**Procedure:**

1. Click "Login" button in header
2. Enter credentials:
   - Email Address
   - Password
3. Click "Login"
4. Success: Redirected to products page
5. Failure: "Invalid email or password" error

**Session Management:**
- Session expires on browser close
- User can logout using "Logout" button
- Session stores: user_id, email, first_name

### 3.3 Product Browsing

**Features:**

1. **Homepage Categories:**
   - Fruits
   - Vegetables
   - Dairy
   - Beverages
   - Click category to view products

2. **Product Listing:**
   - Product image
   - Product name
   - Price per unit
   - Discount badge (if applicable)
   - Stock status:
     - 🟢 "In Stock (X)" - Stock >= 5
     - 🟡 "Only X left!" - Stock 1-4
     - 🔴 "Out of Stock" - Stock = 0
   - Add to Cart button (disabled if out of stock)

3. **Product Information:**
   - Price displayed per unit (kg, liter, pack, dozen, piece)
   - Discount percentage shown
   - Final price calculated automatically

### 3.4 Shopping Cart Operations

#### 3.4.1 Add to Cart

**Procedure:**

1. Browse products
2. Click "Add to Cart" button
3. System checks:
   - User is logged in (redirects to login if not)
   - Product has stock available
   - Product not already in cart
4. Success: Product added with quantity 1
5. Cart icon updates with item count

**Business Rules:**
- Cannot add out-of-stock items
- Cannot add duplicate items (use quantity update instead)
- Must be logged in to add to cart

#### 3.4.2 View Cart

**Procedure:**

1. Click "Cart" icon in header
2. Cart displays:
   - Item number
   - Item name
   - Quantity (editable)
   - Price per unit
   - Subtotal
   - Stock status badge
   - Remove button
3. Total amount calculated at bottom

#### 3.4.3 Update Cart Quantity

**Procedure:**

1. In cart, change quantity in input field
2. System validates:
   - Quantity >= 1
   - Quantity <= available stock
3. Page reloads with updated quantity
4. Subtotal recalculated
5. Total updated

**Validation:**
- Minimum quantity: 1
- Maximum quantity: Available stock
- Invalid entries show alert

#### 3.4.4 Remove from Cart

**Procedure:**

1. Click "Remove" link next to item
2. Item removed from cart
3. Cart refreshes
4. Total recalculated

### 3.5 Order Placement (Checkout)

#### 3.5.1 Pre-Checkout Validation

**Automatic Checks:**

1. **Stock Validation:**
   - System checks current stock for all cart items
   - If any item out of stock → "Confirm Order" button disabled
   - Stock status badges show real-time availability

2. **Cart Status:**
   - Empty cart → "Add items to cart first" message
   - Stock issues → Warning message displayed

#### 3.5.2 Checkout Process

**Procedure:**

1. **Review Cart:**
   - Verify all items and quantities
   - Check stock status (all must be green)
   - Review total amount

2. **Click "Confirm Order":**
   - Modal popup appears

3. **Enter Delivery Information:**
   - Phone Number (pre-filled from profile)
   - Delivery Address (required)
     - Include: House/flat number, street, landmark, city, pincode

4. **Review Order Summary:**
   - Total Amount
   - Payment Method: Cash on Delivery

5. **Click "Confirm Order":**
   - System processes order with transaction control
   - Stock is locked and validated
   - Order created atomically

6. **Order Confirmation:**
   - Success page displays:
     - Order Number
     - Total Amount
     - View Invoice button
     - Download PDF button

**Important Notes:**
- Order placement uses database transactions
- Stock is locked during order processing
- If stock becomes unavailable during checkout, order fails gracefully
- User receives clear error message if order fails

#### 3.5.3 Concurrency Control (Behind the Scenes)

**What Happens:**

```
User A clicks "Confirm Order"
↓
START TRANSACTION
↓
LOCK product rows (SELECT FOR UPDATE)
↓
Check stock inside transaction
↓
If stock sufficient:
  → Reduce stock
  → Create order
  → Create order items
  → COMMIT (all succeed)
Else:
  → ROLLBACK (undo everything)
  → Show "Out of Stock" error
```

**Scenario: Two Users, One Item**

```
Product Stock = 1

User A: Clicks "Buy" → Transaction starts → Locks row
User B: Clicks "Buy" → Transaction starts → WAITS for lock

User A: Checks stock (1) ✓ → Reduces to 0 → Creates order → COMMITS
User B: Lock acquired → Checks stock (0) ✗ → ROLLBACK → Error shown

Result:
- User A: Order confirmed ✓
- User B: "Out of Stock" error ✓
- Final Stock: 0 (NOT -1)
```

### 3.6 Order Confirmation & Invoice

#### 3.6.1 View Invoice

**Procedure:**

1. After order confirmation, click "View Invoice"
2. Invoice displays:
   - Order details (number, date, status)
   - Customer information
   - Delivery address and phone
   - Itemized list with prices
   - Discount calculations
   - Total amount
3. Options:
   - Download PDF
   - Email invoice
   - Print invoice

#### 3.6.2 Download PDF Invoice

**Procedure:**

1. Click "Download PDF" button
2. PDF generates automatically
3. Browser downloads file: `Invoice_ORD-XXXX.pdf`
4. PDF contains complete order details

#### 3.6.3 Email Invoice

**Procedure:**

1. Click "Email Invoice" button
2. System sends email to registered email address
3. Email contains:
   - Order summary
   - PDF attachment
   - Contact information
4. Success message displayed

---

## 4. Admin Operations

### 4.1 Admin Login

**Procedure:**

1. Navigate to: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/admin/`
2. Enter credentials:
   - **Username:** `admin`
   - **Password:** `admin123`
3. Click "Login"
4. Dashboard loads

**Default Credentials:**
- Username: `admin`
- Password: `admin123`

**⚠️ SECURITY:** Change default password after first login!

### 4.2 Admin Dashboard

**Dashboard Displays:**

1. **Statistics Cards:**
   - Total Products
   - Total Orders
   - Pending Orders
   - Total Revenue
   - Low Stock Alerts (1-4 units)
   - Out of Stock Items (0 units)

2. **Recent Products Table:**
   - Last 5 added products
   - Quick view of stock status

3. **Quick Actions:**
   - Add New Product
   - View All Products
   - View Orders
   - Manage Stock

### 4.3 Product Management

#### 4.3.1 Add New Product

**Procedure:**

1. Click "Add Product" in sidebar
2. Fill product form:
   - **Product Name** (required, max 100 chars)
   - **Category** (required): Fruits, Vegetables, Dairy, Beverages
   - **Price** (required, in Rupees)
   - **Discount** (0-100%)
   - **Stock Quantity** (required, integer)
   - **Unit** (required): kg, liter, piece, pack, dozen
   - **Product Image** (required)
     - Formats: JPG, PNG, GIF, WEBP
     - Max size: 5MB
3. Click "Add Product"
4. Success: Redirected to products list
5. Failure: Error message displayed

**Validation Rules:**
- Product name: 1-100 characters
- Price: Positive number
- Discount: 0-100
- Stock: Non-negative integer
- Image: Valid format, under 5MB

**Image Handling:**
- Uploaded to `images/` folder
- Filename sanitized and made unique
- Original filename preserved with timestamp

#### 4.3.2 Edit Product

**Procedure:**

1. Go to "All Products"
2. Click blue edit icon (pencil) next to product
3. Edit form loads with current values
4. Modify any fields:
   - Name, category, price, discount, stock, unit
   - Optionally upload new image
5. Click "Update Product"
6. Success: Product updated, redirected to list
7. Failure: Error message displayed

**Notes:**
- If new image uploaded, old image is deleted
- If no new image, existing image retained
- All fields can be modified

#### 4.3.3 Delete Product

**Procedure:**

1. Go to "All Products"
2. Click red delete icon (trash) next to product
3. Confirmation prompt appears
4. Click "OK" to confirm
5. Product deleted from database
6. Associated image file deleted from server
7. Page refreshes

**⚠️ Warning:** Deletion is permanent and cannot be undone!

**Business Rules:**
- Cannot delete products with pending orders
- Image file automatically removed
- Database cascade deletes related records

#### 4.3.4 Filter Products

**Available Filters:**

1. **All Products:** Shows complete inventory
2. **Low Stock:** Products with 1-4 units
3. **Out of Stock:** Products with 0 units

**Procedure:**

1. Click filter button in products page
2. Table updates to show filtered results
3. Stock status color-coded:
   - 🟢 Green: Stock >= 5
   - 🟡 Yellow: Stock 1-4
   - 🔴 Red: Stock = 0

### 4.4 Order Management

#### 4.4.1 View All Orders

**Procedure:**

1. Click "Orders" in sidebar
2. Orders table displays:
   - Order Number
   - Customer Name
   - Order Date
   - Total Amount
   - Payment Status
   - Order Status
   - Actions (View Details, Update Status)

3. Sort by:
   - Date (newest first by default)
   - Status
   - Amount

#### 4.4.2 View Order Details

**Procedure:**

1. In orders list, click "View Details"
2. Order details page shows:
   - **Order Information:**
     - Order Number
     - Order Date
     - Order Status
     - Payment Status
   - **Customer Information:**
     - Name
     - Email
     - Phone
     - Delivery Address
   - **Order Items:**
     - Product name
     - Quantity
     - Price
     - Discount
     - Subtotal
   - **Order Summary:**
     - Subtotal
     - Discount
     - Final Amount

3. Available actions:
   - Update order status
   - View invoice
   - Download PDF
   - Email invoice

#### 4.4.3 Update Order Status

**Procedure:**

1. In order details, find "Update Status" section
2. Select new status:
   - Pending (initial)
   - Processing (being prepared)
   - Shipped (out for delivery)
   - Delivered (completed)
   - Cancelled (order cancelled)
3. Click "Update Status"
4. Status updated in database
5. Customer can see updated status

**Status Workflow:**
```
Pending → Processing → Shipped → Delivered
   ↓
Cancelled (can cancel from any status)
```

#### 4.4.4 Filter Orders

**Available Filters:**

1. **All Orders:** Complete order history
2. **Pending:** New orders awaiting processing
3. **Processing:** Orders being prepared
4. **Shipped:** Orders out for delivery
5. **Delivered:** Completed orders
6. **Cancelled:** Cancelled orders

**Procedure:**

1. Click filter button in orders page
2. Table updates with filtered results
3. Count displayed for each status

### 4.5 Stock Management

#### 4.5.1 Monitor Stock Levels

**Dashboard Alerts:**

1. **Low Stock Alert:**
   - Shows count of products with 1-4 units
   - Click to view list
   - Reorder recommended

2. **Out of Stock Alert:**
   - Shows count of products with 0 units
   - Click to view list
   - Immediate action required

#### 4.5.2 Update Stock Quantity

**Procedure:**

1. Go to "All Products"
2. Click edit icon for product
3. Update "Stock Quantity" field
4. Click "Update Product"
5. Stock updated immediately

**Best Practices:**
- Check stock daily
- Reorder when stock < 10
- Update stock after receiving inventory
- Monitor low stock alerts

#### 4.5.3 Stock Deduction (Automatic)

**When Stock Reduces:**

1. Customer places order
2. System uses transaction:
   - Locks product row
   - Validates stock
   - Reduces stock atomically
3. Stock updated in real-time
4. Other customers see updated stock

**Concurrency Protection:**
- Multiple orders processed safely
- No overselling possible
- Stock never goes negative

---

## 5. Order Management

### 5.1 Order Lifecycle

```
┌──────────────┐
│   Customer   │
│  Places Order│
└──────┬───────┘
       │
       ▼
┌──────────────┐
│   PENDING    │ ← Initial status
│              │   Admin notified
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  PROCESSING  │ ← Admin preparing order
│              │   Items being packed
└──────┬───────┘
       │
       ▼
┌──────────────┐
│   SHIPPED    │ ← Out for delivery
│              │   Tracking available
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  DELIVERED   │ ← Order completed
│              │   Customer received
└──────────────┘

       OR
       
┌──────────────┐
│  CANCELLED   │ ← Order cancelled
│              │   Stock restored
└──────────────┘
```

### 5.2 Order Processing Workflow

**Step-by-Step:**

1. **New Order Received:**
   - Status: Pending
   - Payment: Paid (COD)
   - Admin receives notification (dashboard)

2. **Admin Reviews Order:**
   - Check order details
   - Verify delivery address
   - Confirm stock availability
   - Update status to "Processing"

3. **Prepare Order:**
   - Pick items from inventory
   - Pack items securely
   - Attach invoice
   - Update status to "Shipped"

4. **Dispatch Order:**
   - Hand over to delivery partner
   - Note tracking information
   - Customer can track status

5. **Delivery Confirmation:**
   - Delivery partner confirms delivery
   - Update status to "Delivered"
   - Order complete

### 5.3 Order Cancellation

**Procedure:**

1. Go to order details
2. Select "Cancelled" status
3. Click "Update Status"
4. **Manual Action Required:** Restore stock
   - Go to products
   - Edit each product in cancelled order
   - Add back the ordered quantity

**⚠️ Important:** Stock is NOT automatically restored on cancellation. Admin must manually update stock.

---

## 6. Stock Management

### 6.1 Stock Tracking System

**Real-Time Tracking:**

1. **Product Page:**
   - Shows current stock
   - Updates after each order
   - Color-coded status

2. **Cart Page:**
   - Validates stock before checkout
   - Shows stock status for each item
   - Prevents checkout if insufficient stock

3. **Admin Dashboard:**
   - Low stock alerts
   - Out of stock alerts
   - Stock level monitoring

### 6.2 Stock Validation Rules

**Multi-Level Validation:**

1. **Add to Cart:**
   - Check: stock > 0
   - Prevent: Adding out-of-stock items

2. **Cart Display:**
   - Check: stock >= cart quantity
   - Show: Stock status badge

3. **Checkout:**
   - Check: stock >= requested quantity
   - Block: Order if insufficient stock

4. **Order Placement (Transaction):**
   - Lock: Product row
   - Check: stock >= quantity (inside transaction)
   - Reduce: stock atomically
   - Commit: If successful
   - Rollback: If insufficient

### 6.3 Concurrency Control

**How It Works:**

1. **Transaction Begins:**
   ```sql
   START TRANSACTION;
   ```

2. **Lock Product Row:**
   ```sql
   SELECT stock_quantity FROM products WHERE id=? FOR UPDATE;
   ```
   - Acquires exclusive lock
   - Other transactions wait

3. **Validate Stock:**
   - Check stock >= requested quantity
   - Inside transaction (sees locked value)

4. **Execute or Rollback:**
   - **If sufficient:** Reduce stock, create order, COMMIT
   - **If insufficient:** ROLLBACK, show error

5. **Release Lock:**
   - COMMIT or ROLLBACK releases lock
   - Next transaction can proceed

**Benefits:**
- ✓ Prevents overselling
- ✓ No negative stock
- ✓ Race condition free
- ✓ Data integrity guaranteed

### 6.4 Stock Replenishment

**Procedure:**

1. **Identify Low Stock:**
   - Check dashboard alerts
   - Review "Low Stock" filter

2. **Order Inventory:**
   - Contact suppliers
   - Place replenishment orders

3. **Update Stock:**
   - Go to "All Products"
   - Click edit for product
   - Update "Stock Quantity"
   - Add received quantity to current stock
   - Click "Update Product"

4. **Verify:**
   - Check product page shows updated stock
   - Verify alert cleared from dashboard

**Recommended Stock Levels:**
- Minimum: 10 units
- Reorder point: 5 units
- Maximum: Based on storage capacity

---

## 7. Invoice System

### 7.1 Invoice Generation

**Automatic Generation:**

1. Order placed successfully
2. Invoice data stored in database
3. Invoice accessible via:
   - Order confirmation page
   - Admin order details
   - Customer email

### 7.2 Invoice Components

**Invoice Includes:**

1. **Header:**
   - Company name: Fresh Mart
   - Contact: +91 9360509515
   - Invoice number
   - Invoice date

2. **Customer Details:**
   - Name
   - Email
   - Phone
   - Delivery address

3. **Order Information:**
   - Order number
   - Order date
   - Payment method: COD
   - Payment status

4. **Itemized List:**
   - Product name
   - Quantity with unit
   - Price per unit
   - Discount percentage
   - Subtotal
   - Discount amount
   - Final price

5. **Summary:**
   - Subtotal
   - Total discount
   - Final amount
   - Payment status

6. **Footer:**
   - Thank you message
   - Terms and conditions
   - Contact information

### 7.3 Invoice Operations

#### 7.3.1 View Invoice (Web)

**URL Format:**
```
http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/view_invoice.php?order_id=123
```

**Access Points:**
- Order confirmation page
- Admin order details
- Direct URL with order_id

#### 7.3.2 Download PDF

**URL Format:**
```
http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/generate_invoice.php?order_id=123
```

**Features:**
- Professional PDF layout
- Printable format
- Includes all order details
- Filename: `Invoice_ORD-XXXX.pdf`

#### 7.3.3 Email Invoice

**URL Format:**
```
http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/email_invoice.php?order_id=123
```

**Email Contains:**
- Subject: "Your Fresh Mart Invoice - Order #ORD-XXXX"
- Body: Order summary
- Attachment: PDF invoice
- Sent to customer's registered email

**Configuration Required:**
- SMTP settings in `email_invoice.php`
- Valid email server credentials

---

## 8. Troubleshooting

### 8.1 Common Customer Issues

#### Issue: Cannot Add to Cart

**Symptoms:**
- "Add to Cart" button doesn't work
- No items appear in cart

**Solutions:**
1. **Check Login Status:**
   - Ensure user is logged in
   - Try logging out and logging back in

2. **Check Stock:**
   - Verify product has stock > 0
   - Out of stock items cannot be added

3. **Check Browser:**
   - Enable JavaScript
   - Clear browser cache
   - Try different browser

#### Issue: "Out of Stock" Error During Checkout

**Symptoms:**
- Cart shows items available
- Checkout fails with stock error

**Cause:**
- Another customer purchased the item
- Stock changed between cart view and checkout

**Solution:**
1. Return to cart
2. Review stock status
3. Remove out-of-stock items or reduce quantity
4. Try checkout again

#### Issue: Order Not Confirmed

**Symptoms:**
- Clicked "Confirm Order"
- No success page
- Order not in system

**Solutions:**
1. **Check Stock:**
   - Verify all items have sufficient stock
   - Stock may have changed during checkout

2. **Check Delivery Info:**
   - Ensure phone number is 10 digits
   - Ensure address is filled

3. **Check Database:**
   - Verify database connection
   - Check error logs

### 8.2 Common Admin Issues

#### Issue: Cannot Login to Admin Panel

**Solutions:**

1. **Verify Database Setup:**
   ```bash
   mysql -u root -p onlinesale < admin_setup.sql
   ```

2. **Check Credentials:**
   - Username: `admin`
   - Password: `admin123`
   - Case-sensitive

3. **Verify Table Exists:**
   ```sql
   SELECT * FROM admins;
   ```

#### Issue: Image Upload Fails

**Solutions:**

1. **Check Folder Permissions:**
   ```bash
   chmod 755 images/
   ```

2. **Check File Size:**
   - Maximum: 5MB
   - Compress large images

3. **Check File Format:**
   - Allowed: JPG, PNG, GIF, WEBP
   - Convert unsupported formats

4. **Check PHP Settings:**
   ```php
   // In php.ini
   upload_max_filesize = 5M
   post_max_size = 10M
   ```

#### Issue: Products Not Displaying

**Solutions:**

1. **Check Database:**
   ```sql
   SELECT * FROM products;
   ```

2. **Verify Image Paths:**
   - Check `image_path` column in database
   - Verify images exist in `images/` folder

3. **Check Category:**
   - Ensure products have valid category
   - Categories: Fruits, Vegetables, Dairy, Beverages

### 8.3 Database Issues

#### Issue: Connection Failed

**Error Message:**
```
Connection failed: Access denied for user 'root'@'localhost'
```

**Solutions:**

1. **Update Connection Settings:**
   - Edit `includes/common.php` (line 5)
   - Edit `admin/includes/admin_common.php` (line 5)

2. **MAMP Settings:**
   ```php
   $con = mysqli_connect("localhost:8889", "root", "root", "onlinesale");
   ```

3. **XAMPP Settings:**
   ```php
   $con = mysqli_connect("localhost:3306", "root", "", "onlinesale");
   ```

#### Issue: Table Doesn't Exist

**Error Message:**
```
Table 'onlinesale.orders' doesn't exist
```

**Solution:**
```bash
mysql -u root -p onlinesale < create_orders_tables.sql
```

#### Issue: Negative Stock

**This should NEVER happen with the new concurrency control!**

**If it does occur:**

1. **Immediate Action:**
   ```sql
   UPDATE products SET stock_quantity = 0 WHERE stock_quantity < 0;
   ```

2. **Investigation:**
   - Check if `create_order_with_transaction()` is being used
   - Verify InnoDB engine:
     ```sql
     SHOW TABLE STATUS WHERE Name = 'products';
     ```
   - Review error logs

3. **Prevention:**
   - Ensure all order placement uses transaction function
   - Never use old `create_order()` function
   - Always use `create_order_with_transaction()`

---

## 9. Security Guidelines

### 9.1 Admin Security

**Password Management:**

1. **Change Default Password:**
   ```sql
   UPDATE admins SET password = MD5('your_new_password') WHERE username = 'admin';
   ```

2. **Password Requirements:**
   - Minimum 8 characters
   - Mix of letters and numbers
   - Change every 90 days

3. **Session Security:**
   - Logout after use
   - Don't share credentials
   - Use private browsing for public computers

### 9.2 Customer Security

**Account Protection:**

1. **Password Guidelines:**
   - Minimum 6 characters
   - Unique per user
   - Not shared with others

2. **Session Management:**
   - Auto-logout on browser close
   - Manual logout available
   - Session timeout: Browser session

### 9.3 Data Protection

**SQL Injection Prevention:**
- All inputs sanitized with `mysqli_real_escape_string()`
- Prepared statements for critical queries
- Input validation on all forms

**File Upload Security:**
- File type validation
- File size limits
- Unique filename generation
- No executable files allowed

### 9.4 Backup Procedures

**Daily Backup:**

```bash
# Backup database
mysqldump -u root -p onlinesale > backup_$(date +%Y%m%d).sql

# Backup images
tar -czf images_backup_$(date +%Y%m%d).tar.gz images/
```

**Weekly Backup:**
- Full system backup
- Store offsite
- Test restore procedure

---

## 10. Maintenance Procedures

### 10.1 Daily Tasks

**Admin Checklist:**

- [ ] Check pending orders
- [ ] Review low stock alerts
- [ ] Process new orders (Pending → Processing)
- [ ] Update shipped orders status
- [ ] Monitor customer inquiries
- [ ] Verify payment confirmations

**Estimated Time:** 15-30 minutes

### 10.2 Weekly Tasks

**Admin Checklist:**

- [ ] Review inventory levels
- [ ] Place replenishment orders
- [ ] Update product prices (if needed)
- [ ] Check for expired products
- [ ] Review sales reports
- [ ] Backup database
- [ ] Clean up old images

**Estimated Time:** 1-2 hours

### 10.3 Monthly Tasks

**Admin Checklist:**

- [ ] Analyze sales trends
- [ ] Update product catalog
- [ ] Add seasonal products
- [ ] Remove slow-moving items
- [ ] Review customer feedback
- [ ] Update promotional discounts
- [ ] System performance check
- [ ] Security audit

**Estimated Time:** 2-4 hours

### 10.4 Database Maintenance

**Monthly Optimization:**

```sql
-- Optimize tables
OPTIMIZE TABLE products;
OPTIMIZE TABLE orders;
OPTIMIZE TABLE order_items;
OPTIMIZE TABLE users;

-- Check table integrity
CHECK TABLE products;
CHECK TABLE orders;

-- Analyze tables
ANALYZE TABLE products;
ANALYZE TABLE orders;
```

### 10.5 Log Management

**Error Logs:**

1. **PHP Error Log:**
   - Location: `/Applications/MAMP/logs/php_error.log`
   - Review weekly
   - Address recurring errors

2. **MySQL Error Log:**
   - Location: `/Applications/MAMP/logs/mysql_error.log`
   - Monitor for connection issues

3. **Application Logs:**
   - Order failures logged in database
   - Stock issues logged
   - Review regularly

---

## 11. Testing Procedures

### 11.1 Functional Testing

#### Test 1: Customer Registration

**Steps:**
1. Click "Sign Up"
2. Enter valid details
3. Submit form
4. Verify: User logged in, redirected to products

**Expected Result:** ✓ User created, auto-logged in

#### Test 2: Product Browsing

**Steps:**
1. Navigate to products page
2. Verify all products display
3. Check stock badges
4. Verify prices and discounts

**Expected Result:** ✓ All products visible with correct info

#### Test 3: Add to Cart

**Steps:**
1. Click "Add to Cart" on in-stock product
2. Verify cart icon updates
3. Go to cart
4. Verify item appears

**Expected Result:** ✓ Item in cart with quantity 1

#### Test 4: Order Placement

**Steps:**
1. Add items to cart
2. Go to cart
3. Click "Confirm Order"
4. Enter delivery info
5. Submit

**Expected Result:** ✓ Order confirmed, invoice available

#### Test 5: Concurrency Control

**Steps:**
1. Set product stock to 1
2. Open two browsers (different users)
3. Add product to both carts
4. Click "Confirm Order" simultaneously

**Expected Result:** ✓ One succeeds, one gets "Out of Stock", stock = 0

### 11.2 Admin Testing

#### Test 1: Add Product

**Steps:**
1. Login to admin
2. Click "Add Product"
3. Fill all fields
4. Upload image
5. Submit

**Expected Result:** ✓ Product added, appears in list

#### Test 2: Edit Product

**Steps:**
1. Go to "All Products"
2. Click edit icon
3. Modify fields
4. Submit

**Expected Result:** ✓ Product updated with new values

#### Test 3: Order Management

**Steps:**
1. Place test order (customer side)
2. Login to admin
3. View orders
4. Click order details
5. Update status

**Expected Result:** ✓ Status updated successfully

### 11.3 Automated Testing

**Concurrency Test:**

```bash
# Access test script
http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/test_concurrency.php
```

**Expected Output:**
- User A: Order placed ✓
- User B: Out of Stock ✗
- Final Stock: 0 (not -1)

---

## 12. Performance Optimization

### 12.1 Database Optimization

**Indexes:**

```sql
-- Ensure indexes exist
ALTER TABLE products ADD INDEX idx_category (category);
ALTER TABLE products ADD INDEX idx_stock (stock_quantity);
ALTER TABLE orders ADD INDEX idx_user (user_id);
ALTER TABLE orders ADD INDEX idx_status (order_status);
ALTER TABLE order_items ADD INDEX idx_order (order_id);
```

**Query Optimization:**
- Use prepared statements
- Limit result sets
- Avoid SELECT *
- Use appropriate indexes

### 12.2 Image Optimization

**Best Practices:**

1. **Image Size:**
   - Maximum: 500KB per image
   - Compress before upload
   - Use WebP format for better compression

2. **Image Dimensions:**
   - Product images: 800x800px
   - Category images: 1200x600px
   - Thumbnails: 200x200px

3. **Tools:**
   - TinyPNG for compression
   - ImageMagick for batch processing

### 12.3 Caching

**Browser Caching:**

Add to `.htaccess`:
```apache
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType image/jpg "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/png "access plus 1 year"
  ExpiresByType image/webp "access plus 1 year"
  ExpiresByType text/css "access plus 1 month"
  ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

---

## 13. Business Rules

### 13.1 Pricing Rules

1. **Price Display:**
   - Always show price per unit
   - Show discount percentage if > 0
   - Calculate final price: `price - (price × discount / 100)`

2. **Discount Rules:**
   - Range: 0-100%
   - Applied at item level
   - Shown on product card

3. **Total Calculation:**
   - Subtotal = Price × Quantity
   - Discount = Subtotal × (Discount% / 100)
   - Final = Subtotal - Discount

### 13.2 Order Rules

1. **Minimum Order:**
   - No minimum order value
   - At least 1 item required

2. **Payment:**
   - Method: Cash on Delivery (COD)
   - Status: Marked as "Paid" on order creation

3. **Delivery:**
   - Address required
   - Phone number required (10 digits)
   - Delivery within service area

### 13.3 Stock Rules

1. **Stock Availability:**
   - Must be > 0 to add to cart
   - Validated at checkout
   - Locked during order placement

2. **Stock Reduction:**
   - Automatic on order confirmation
   - Atomic transaction
   - Cannot go negative

3. **Stock Alerts:**
   - Low stock: 1-4 units
   - Out of stock: 0 units
   - Admin notified on dashboard

---

## 14. System URLs Reference

### 14.1 Customer URLs

**Main Pages:**
- Homepage: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/`
- Products: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/products.php`
- Cart: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/cart.php`
- About: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/about.php`

**Order Pages:**
- Order Success: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/success.php`
- View Invoice: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/view_invoice.php?order_id=X`
- Download PDF: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/generate_invoice.php?order_id=X`

### 14.2 Admin URLs

**Admin Panel:**
- Login: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/admin/`
- Dashboard: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/admin/dashboard.php`
- Products: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/admin/products.php`
- Orders: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/admin/orders.php`
- Add Product: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/admin/add_product.php`

### 14.3 Testing URLs

**Test Scripts:**
- Concurrency Test: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/test_concurrency.php`
- Concurrency Demo: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/concurrency_demo.html`
- Invoice Demo: `http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/invoice_demo.php`

---

## 15. Quick Reference Commands

### 15.1 Database Commands

**Import Database:**
```bash
mysql -u root -p onlinesale < onlinesale.sql
mysql -u root -p onlinesale < create_orders_tables.sql
mysql -u root -p onlinesale < admin_setup.sql
```

**Backup Database:**
```bash
mysqldump -u root -p onlinesale > backup_$(date +%Y%m%d).sql
```

**Reset Database:**
```bash
mysql -u root -p -e "DROP DATABASE IF EXISTS onlinesale; CREATE DATABASE onlinesale;"
mysql -u root -p onlinesale < onlinesale.sql
mysql -u root -p onlinesale < create_orders_tables.sql
mysql -u root -p onlinesale < admin_setup.sql
```

### 15.2 Useful SQL Queries

**Check Stock Levels:**
```sql
SELECT name, stock_quantity, category 
FROM products 
WHERE stock_quantity < 5 
ORDER BY stock_quantity ASC;
```

**View Recent Orders:**
```sql
SELECT o.order_number, u.first_name, u.last_name, o.final_amount, o.order_status, o.created_at
FROM orders o
JOIN users u ON o.user_id = u.id
ORDER BY o.created_at DESC
LIMIT 10;
```

**Sales Summary:**
```sql
SELECT 
    DATE(created_at) as date,
    COUNT(*) as orders,
    SUM(final_amount) as revenue
FROM orders
WHERE order_status != 'Cancelled'
GROUP BY DATE(created_at)
ORDER BY date DESC;
```

**Top Selling Products:**
```sql
SELECT 
    oi.product_name,
    SUM(oi.quantity) as total_sold,
    SUM(oi.final_price) as revenue
FROM order_items oi
JOIN orders o ON oi.order_id = o.id
WHERE o.order_status != 'Cancelled'
GROUP BY oi.product_id, oi.product_name
ORDER BY total_sold DESC
LIMIT 10;
```

**Customer Order History:**
```sql
SELECT 
    o.order_number,
    o.order_status,
    o.final_amount,
    o.created_at
FROM orders o
WHERE o.user_id = ?
ORDER BY o.created_at DESC;
```

---

## 16. Workflow Diagrams

### 16.1 Customer Purchase Flow

```
START
  ↓
[Browse Products]
  ↓
[Select Product] → Check Stock → Out of Stock? → [Show Error]
  ↓ In Stock
[Add to Cart]
  ↓
[View Cart] → Update Quantity? → [Update Cart]
  ↓
[Confirm Order]
  ↓
[Enter Delivery Info]
  ↓
[Submit Order]
  ↓
[Transaction Processing]
  ├─ Lock product rows
  ├─ Validate stock
  ├─ Reduce stock
  ├─ Create order
  └─ Commit/Rollback
  ↓
Success? ─┬─ YES → [Order Confirmed] → [View Invoice] → END
          │
          └─ NO → [Show Error] → [Back to Cart]
```

### 16.2 Admin Order Processing Flow

```
START
  ↓
[New Order Received]
  ↓ Status: PENDING
[Review Order Details]
  ↓
[Verify Stock Available]
  ↓
[Update Status: PROCESSING]
  ↓
[Pick Items from Inventory]
  ↓
[Pack Order]
  ↓
[Generate Invoice]
  ↓
[Update Status: SHIPPED]
  ↓
[Hand to Delivery Partner]
  ↓
[Delivery Completed]
  ↓
[Update Status: DELIVERED]
  ↓
END

(Can cancel at any stage → Status: CANCELLED)
```

### 16.3 Stock Management Flow

```
START
  ↓
[Monitor Dashboard]
  ↓
Low Stock Alert? ─┬─ YES → [Identify Products]
                  │          ↓
                  │        [Contact Supplier]
                  │          ↓
                  │        [Place Order]
                  │          ↓
                  │        [Receive Inventory]
                  │          ↓
                  │        [Update Stock in System]
                  │          ↓
                  └────────[Alert Cleared]
  ↓ NO
[Continue Monitoring]
  ↓
END
```

---

## 17. Reporting

### 17.1 Sales Reports

**Daily Sales:**
```sql
SELECT 
    COUNT(*) as total_orders,
    SUM(final_amount) as total_revenue,
    AVG(final_amount) as avg_order_value
FROM orders
WHERE DATE(created_at) = CURDATE()
AND order_status != 'Cancelled';
```

**Monthly Sales:**
```sql
SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as total_orders,
    SUM(final_amount) as total_revenue
FROM orders
WHERE order_status != 'Cancelled'
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month DESC;
```

### 17.2 Inventory Reports

**Current Stock Status:**
```sql
SELECT 
    category,
    COUNT(*) as products,
    SUM(stock_quantity) as total_stock,
    SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
    SUM(CASE WHEN stock_quantity BETWEEN 1 AND 4 THEN 1 ELSE 0 END) as low_stock
FROM products
GROUP BY category;
```

**Stock Movement:**
```sql
SELECT 
    p.name,
    p.stock_quantity as current_stock,
    COALESCE(SUM(oi.quantity), 0) as total_sold
FROM products p
LEFT JOIN order_items oi ON p.id = oi.product_id
LEFT JOIN orders o ON oi.order_id = o.id
WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
AND o.order_status != 'Cancelled'
GROUP BY p.id, p.name, p.stock_quantity
ORDER BY total_sold DESC;
```

### 17.3 Customer Reports

**New Customers:**
```sql
SELECT 
    DATE(reg_date) as date,
    COUNT(*) as new_customers
FROM users
WHERE reg_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(reg_date)
ORDER BY date DESC;
```

**Customer Orders:**
```sql
SELECT 
    u.first_name,
    u.last_name,
    u.email_id,
    COUNT(o.id) as total_orders,
    SUM(o.final_amount) as total_spent
FROM users u
LEFT JOIN orders o ON u.id = o.user_id
WHERE o.order_status != 'Cancelled'
GROUP BY u.id
ORDER BY total_spent DESC
LIMIT 20;
```

---

## 18. Contact & Support

### 18.1 Technical Support

**For Technical Issues:**
- Review this SOP document
- Check troubleshooting section
- Review error logs
- Contact: +91 9360509515

### 18.2 Documentation

**Available Documentation:**
- `USER_MANUAL_SOP.md` - This document
- `README.md` - Project overview
- `ADMIN_SETUP_GUIDE.md` - Admin panel setup
- `CONCURRENCY_CONTROL.md` - Transaction implementation
- `INVOICE_FEATURES.md` - Invoice system details
- `STOCK_TESTING.md` - Stock validation testing

### 18.3 Training Resources

**For New Admins:**
1. Read this SOP completely
2. Review `ADMIN_SETUP_GUIDE.md`
3. Complete test flows in `ADMIN_TEST_FLOWS.md`
4. Practice in test environment
5. Shadow experienced admin

**For Developers:**
1. Review codebase structure
2. Study `CONCURRENCY_CONTROL.md`
3. Understand transaction flow
4. Review security practices
5. Run test scripts

---

## 19. Glossary

**Terms:**

- **COD:** Cash on Delivery - Payment collected at delivery
- **SKU:** Stock Keeping Unit - Unique product identifier
- **Transaction:** Atomic database operation (all-or-nothing)
- **Concurrency:** Multiple users accessing system simultaneously
- **Race Condition:** Bug when timing affects outcome
- **SELECT FOR UPDATE:** SQL locking mechanism
- **ROLLBACK:** Undo transaction changes
- **COMMIT:** Save transaction changes permanently
- **InnoDB:** MySQL storage engine supporting transactions

---

## 20. Appendix

### 20.1 Database Schema

**Tables:**
- `products` - Product catalog
- `users` - Customer accounts
- `users_products` - Shopping cart items
- `orders` - Order headers
- `order_items` - Order line items
- `admins` - Admin accounts

### 20.2 File Permissions

**Required Permissions:**
```
images/          → 755 (rwxr-xr-x)
images/*.jpg     → 644 (rw-r--r--)
includes/        → 755 (rwxr-xr-x)
*.php            → 644 (rw-r--r--)
```

### 20.3 Configuration Files

**Database Config:**
- `includes/common.php` (line 5)
- `admin/includes/admin_common.php` (line 5)

**Email Config:**
- `email_invoice.php` (SMTP settings)

### 20.4 Version History

**Version 2.0 (Current):**
- ✓ Concurrency control implemented
- ✓ Transaction-based order placement
- ✓ Invoice system with PDF generation
- ✓ Admin panel with order management
- ✓ Stock tracking and alerts
- ✓ Enhanced error handling

**Version 1.0:**
- Basic e-commerce functionality
- Product catalog
- Shopping cart
- Simple order placement

---

## Emergency Procedures

### System Down

1. **Check Server Status:**
   - MAMP: Verify Apache and MySQL running
   - Check port availability

2. **Check Database:**
   - Verify MySQL service running
   - Test connection: `mysql -u root -p`

3. **Check Error Logs:**
   - PHP errors: `/Applications/MAMP/logs/php_error.log`
   - MySQL errors: `/Applications/MAMP/logs/mysql_error.log`

### Data Corruption

1. **Stop accepting orders** (maintenance mode)
2. **Restore from latest backup:**
   ```bash
   mysql -u root -p onlinesale < backup_YYYYMMDD.sql
   ```
3. **Verify data integrity:**
   - Check stock levels
   - Verify order completeness
4. **Resume operations**

### Security Breach

1. **Immediate Actions:**
   - Change all admin passwords
   - Review access logs
   - Check for unauthorized changes

2. **Investigation:**
   - Review recent database changes
   - Check file modifications
   - Analyze access patterns

3. **Recovery:**
   - Restore from clean backup if needed
   - Update security measures
   - Document incident

---

## Contact Information

**Technical Support:**
- Phone: +91 9360509515
- Email: support@freshmart.com (if configured)

**Business Hours:**
- Monday - Saturday: 9:00 AM - 6:00 PM
- Sunday: Closed

---

**END OF SOP**

*This document should be reviewed and updated quarterly or when significant system changes are made.*
