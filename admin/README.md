# Fresh Mart Admin Panel

Complete admin panel for managing the Fresh Mart ecommerce website.

## Features

- **Admin Authentication**: Secure login system with session management
- **Dashboard**: Overview with key metrics (total products, orders, low stock alerts)
- **Product Management**: 
  - Add new products with image upload
  - Edit existing products
  - Delete products
  - View all products with filtering options
- **Stock Management**: Visual indicators for low stock and out-of-stock items
- **Image Upload**: Automatic image handling and storage

## Installation

1. **Run the database setup script**:
   ```bash
   mysql -u root -p onlinesale < admin_setup.sql
   ```
   Or import `admin_setup.sql` via phpMyAdmin.

2. **Ensure the images folder is writable**:
   ```bash
   chmod 755 ../images/
   ```

3. **Access the admin panel**:
   Navigate to: `http://localhost/admin/login.php`

## Default Login Credentials

- **Username**: `admin`
- **Password**: `admin123`

**Important**: Change the default password after first login for security!

## File Structure

```
admin/
├── login.php                  # Admin login page
├── login_process.php          # Login authentication handler
├── logout.php                 # Logout handler
├── dashboard.php              # Admin dashboard with statistics
├── products.php               # Product listing with edit/delete
├── add_product.php            # Add new product form
├── add_product_process.php    # Add product handler
├── edit_product.php           # Edit product form
├── edit_product_process.php   # Edit product handler
├── delete_product.php         # Delete product handler
├── includes/
│   ├── admin_common.php       # Database connection and helper functions
│   ├── admin_header.php       # Admin navigation header
│   └── admin_sidebar.php      # Admin sidebar menu
└── style/
    └── admin.css              # Admin panel styles
```

## Security Features

- Session-based authentication
- SQL injection prevention with mysqli_real_escape_string
- File upload validation (type and size checks)
- Admin-only access checks on all pages
- Password hashing with MD5 (matches existing user system)

## Usage

### Adding a Product

1. Navigate to "Add Product" from the sidebar
2. Fill in all required fields:
   - Product Name
   - Category (Fruits, Vegetables, Dairy, Beverages)
   - Price (in Rs)
   - Discount percentage (0-100)
   - Stock Quantity
   - Unit (kg, liter, piece, pack, dozen)
   - Product Image
3. Click "Add Product"

### Editing a Product

1. Go to "All Products"
2. Click the edit icon (pencil) next to the product
3. Update the fields as needed
4. Optionally upload a new image
5. Click "Update Product"

### Deleting a Product

1. Go to "All Products"
2. Click the delete icon (trash) next to the product
3. Confirm the deletion

## Database Schema

### admins table
- `id`: Auto-increment primary key
- `username`: Unique admin username
- `password`: MD5 hashed password
- `full_name`: Admin's full name
- `created_at`: Timestamp of account creation

### products table (updated)
- Added `category` field (varchar)
- Added `image_path` field (varchar)

## Notes

- Images are uploaded to the `../images/` directory
- Image filenames are automatically generated with unique IDs
- Old images are deleted when a product is removed
- The system validates file types and sizes before upload
