# Admin Panel Quick Start

## 1. Database Setup (Required First!)

Run this command in your terminal:
```bash
mysql -u root -p onlinesale < admin_setup.sql
```

Or import `admin_setup.sql` via phpMyAdmin.

## 2. Login

Navigate to: `http://localhost:8888/admin/`

**Default Credentials:**
- Username: `admin`
- Password: `admin123`

## 3. Quick Tour

### Dashboard
- View key metrics (products, orders, stock alerts)
- Quick action buttons for common tasks

### Add Product
1. Click "Add Product" in sidebar
2. Fill in all fields (name, category, price, discount, stock, unit)
3. Upload product image
4. Click "Add Product"

### Manage Products
1. Click "All Products" in sidebar
2. Use filters: All / Low Stock / Out of Stock
3. Edit: Click blue pencil icon
4. Delete: Click red trash icon (confirms before deleting)

## File Locations

- **Admin Panel**: `/admin/`
- **Uploaded Images**: `/images/`
- **Database Config**: `/admin/includes/admin_common.php`

## Security Notes

- Change default password immediately
- All pages require admin login
- File uploads are validated (type & size)
- SQL inputs are sanitized

## Need Help?

See `ADMIN_SETUP_GUIDE.md` for detailed documentation.
