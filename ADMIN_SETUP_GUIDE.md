# Admin Panel Setup Guide

This guide will help you set up the admin panel for the Fresh Mart ecommerce website.

## Prerequisites

- PHP 7.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/MAMP/XAMPP)
- Existing Fresh Mart database (`onlinesale`)

## Installation Steps

### Step 1: Run Database Setup

You need to run the `admin_setup.sql` script to create the admin table and add new fields to the products table.

**Option A: Using Command Line**
```bash
mysql -u root -p onlinesale < admin_setup.sql
```

**Option B: Using phpMyAdmin**
1. Open phpMyAdmin
2. Select the `onlinesale` database
3. Click on "Import" tab
4. Choose the `admin_setup.sql` file
5. Click "Go"

### Step 2: Verify Database Changes

After running the script, verify that:
1. A new `admins` table exists with a default admin user
2. The `products` table has two new columns: `category` and `image_path`
3. Existing products have been updated with categories and image paths

### Step 3: Set Folder Permissions

Ensure the images folder has write permissions for PHP to upload files:

```bash
chmod 755 images/
```

### Step 4: Access Admin Panel

Navigate to the admin login page in your browser:
```
http://localhost:8888/admin/login.php
```

(Adjust the port number based on your MAMP/XAMPP configuration)

## Default Login Credentials

- **Username**: `admin`
- **Password**: `admin123`

**IMPORTANT**: For security reasons, you should change this password after your first login!

## Admin Panel Features

### 1. Dashboard
- View total products count
- View total orders
- See low stock alerts
- Monitor out-of-stock items
- Quick action buttons
- Recent products overview

### 2. Product Management

#### Add New Product
- Navigate to "Add Product" from sidebar
- Fill in all required fields:
  - Product Name
  - Category (Fruits, Vegetables, Dairy, Beverages)
  - Price (in Rupees)
  - Discount (0-100%)
  - Stock Quantity
  - Unit (kg, liter, piece, pack, dozen)
  - Product Image (JPG, PNG, GIF, WEBP - max 5MB)
- Click "Add Product"

#### Edit Product
- Go to "All Products"
- Click the edit icon (blue pencil button)
- Update any fields
- Optionally upload a new image
- Click "Update Product"

#### Delete Product
- Go to "All Products"
- Click the delete icon (red trash button)
- Confirm deletion
- Product and its image will be removed

### 3. Filtering Options
- **All Products**: View all products
- **Low Stock**: Products with 1-4 units remaining
- **Out of Stock**: Products with 0 units

## File Structure

```
admin/
├── login.php                  # Admin login page
├── login_process.php          # Handles login authentication
├── logout.php                 # Handles logout
├── dashboard.php              # Main dashboard with statistics
├── products.php               # Product listing with filters
├── add_product.php            # Add new product form
├── add_product_process.php    # Processes new product submission
├── edit_product.php           # Edit product form
├── edit_product_process.php   # Processes product updates
├── delete_product.php         # Handles product deletion
├── includes/
│   ├── admin_common.php       # Database connection & helper functions
│   ├── admin_header.php       # Navigation header component
│   └── admin_sidebar.php      # Sidebar menu component
└── style/
    └── admin.css              # Admin panel styles
```

## Security Features

1. **Session-based Authentication**: All admin pages check for valid session
2. **SQL Injection Prevention**: All inputs are sanitized using `mysqli_real_escape_string`
3. **File Upload Validation**: 
   - File type checking (only images allowed)
   - File size limit (5MB max)
   - Unique filename generation
4. **Input Validation**: All form inputs are validated before processing
5. **Password Hashing**: Passwords stored as MD5 hashes (consistent with existing user system)

## Troubleshooting

### Cannot login
- Verify the `admin_setup.sql` script ran successfully
- Check that the `admins` table exists in the database
- Ensure credentials are correct (username: `admin`, password: `admin123`)

### Image upload fails
- Check that the `images/` folder has write permissions
- Verify file size is under 5MB
- Ensure file format is supported (JPG, PNG, GIF, WEBP)

### Products not displaying
- Verify database connection settings in `admin/includes/admin_common.php`
- Check that the connection details match your MySQL setup
- Ensure the `category` and `image_path` columns exist in the `products` table

### Database connection error
- Update the connection details in `admin/includes/admin_common.php`:
  ```php
  $con = mysqli_connect("localhost:8889", "root", "root", "onlinesale");
  ```
- Adjust host, port, username, and password as needed

## Updating Products Display on Frontend

The frontend product display has been updated to use the `image_path` from the database. After running the admin setup, all products will automatically use their database-stored image paths.

## Next Steps

1. Run the database setup script
2. Login to the admin panel
3. Verify existing products are displayed correctly
4. Try adding a new product
5. Test editing and deleting products
6. Change the default admin password for security

## Support

For issues or questions, refer to the main README.md in the project root directory.
