# Fresh Mart - Quick Start Guide

**Get up and running in 10 minutes!**

---

## 1. Setup (5 minutes)

### Install & Configure

```bash
# 1. Copy files to MAMP
cp -r ECOMMERCE-WEBSITE /Applications/MAMP/htdocs/onlinesale/

# 2. Import database
cd /Applications/MAMP/htdocs/onlinesale/ECOMMERCE-WEBSITE/
mysql -u root -p onlinesale < onlinesale.sql
mysql -u root -p onlinesale < create_orders_tables.sql
mysql -u root -p onlinesale < admin_setup.sql

# 3. Set permissions
chmod 755 images/
```

### Update Database Connection

Edit `includes/common.php` (line 5):
```php
$con = mysqli_connect("localhost:8889", "root", "root", "onlinesale");
```

Edit `admin/includes/admin_common.php` (line 5):
```php
$con = mysqli_connect("localhost:8889", "root", "root", "onlinesale");
```

---

## 2. Access the System

### Customer Site
```
http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/
```

### Admin Panel
```
http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/admin/
Username: admin
Password: admin123
```

---

## 3. Customer Flow (2 minutes)

### Register & Shop

1. **Sign Up:**
   - Click "Sign Up" → Fill form → Submit
   - Auto-logged in after registration

2. **Browse Products:**
   - Click category or "Shop Now"
   - View products with prices and stock

3. **Add to Cart:**
   - Click "Add to Cart" on desired products
   - Cart icon shows item count

4. **Checkout:**
   - Click "Cart" icon
   - Review items and quantities
   - Click "Confirm Order"
   - Enter delivery address and phone
   - Submit

5. **Order Confirmed:**
   - View invoice
   - Download PDF
   - Continue shopping

---

## 4. Admin Flow (3 minutes)

### Manage Products & Orders

1. **Login:**
   - Go to `/admin/`
   - Username: `admin`, Password: `admin123`

2. **Dashboard:**
   - View statistics
   - Check stock alerts
   - See recent products

3. **Add Product:**
   - Click "Add Product"
   - Fill: Name, Category, Price, Stock, Unit
   - Upload image
   - Submit

4. **Manage Orders:**
   - Click "Orders"
   - View all orders
   - Click "View Details" for any order
   - Update status: Pending → Processing → Shipped → Delivered

5. **Monitor Stock:**
   - Check "Low Stock" filter
   - Update stock quantities as needed
   - Reorder inventory

---

## 5. Key Features

### Concurrency Control ✓
- Multiple users can shop simultaneously
- No overselling
- Stock never goes negative
- Last item goes to first buyer

### Stock Management ✓
- Real-time stock tracking
- Low stock alerts (< 5 units)
- Out of stock badges
- Automatic stock reduction

### Invoice System ✓
- Auto-generated invoices
- PDF download
- Email delivery
- Professional format

### Admin Panel ✓
- Product management (Add/Edit/Delete)
- Order tracking
- Stock monitoring
- Sales dashboard

---

## 6. Common Tasks

### Task: Add New Product

```
Admin Panel → Add Product → Fill Form → Upload Image → Submit
```

**Required Fields:**
- Name, Category, Price, Stock, Unit, Image

### Task: Process Order

```
Admin Panel → Orders → View Details → Update Status
```

**Status Flow:**
Pending → Processing → Shipped → Delivered

### Task: Update Stock

```
Admin Panel → All Products → Edit Icon → Update Stock Quantity → Submit
```

### Task: Check Low Stock

```
Admin Panel → Dashboard → Low Stock Alert → View Products
```

---

## 7. Testing

### Test Concurrency Control

```
http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/test_concurrency.php
```

**Expected:**
- User A: Order success ✓
- User B: Out of stock ✗
- Final stock: 0 (not -1)

### Test Order Flow

1. Register new user
2. Add 2-3 products to cart
3. Update quantities
4. Confirm order
5. View invoice
6. Download PDF

**Expected:** All steps work smoothly

---

## 8. Troubleshooting

### Issue: Can't Login

**Fix:**
```bash
# Re-import database
mysql -u root -p onlinesale < admin_setup.sql
```

### Issue: Products Not Showing

**Fix:**
1. Check database connection
2. Verify images exist in `images/` folder
3. Check `products` table has data

### Issue: Image Upload Fails

**Fix:**
```bash
chmod 755 images/
```

### Issue: Order Fails

**Check:**
1. Stock availability
2. Database connection
3. Transaction support (InnoDB)

---

## 9. URLs Quick Reference

| Page | URL |
|------|-----|
| Homepage | `/` |
| Products | `/products.php` |
| Cart | `/cart.php` |
| Admin Login | `/admin/` |
| Admin Dashboard | `/admin/dashboard.php` |
| Admin Products | `/admin/products.php` |
| Admin Orders | `/admin/orders.php` |
| Concurrency Test | `/test_concurrency.php` |

---

## 10. Default Credentials

### Admin
- Username: `admin`
- Password: `admin123`

### Test Customer (if created)
- Email: `test@test.com`
- Password: `test123`

**⚠️ Change default passwords in production!**

---

## 11. Support

**Documentation:**
- Full SOP: `USER_MANUAL_SOP.md`
- Admin Guide: `ADMIN_SETUP_GUIDE.md`
- Concurrency: `CONCURRENCY_CONTROL.md`

**Contact:**
- Phone: +91 9360509515

---

## 12. Checklist

### Initial Setup Checklist

- [ ] MAMP/XAMPP installed and running
- [ ] Project files copied to htdocs
- [ ] Database imported (3 SQL files)
- [ ] Database connection configured
- [ ] Images folder permissions set
- [ ] Homepage loads successfully
- [ ] Admin panel accessible
- [ ] Test order placed successfully

### Daily Operations Checklist

**Admin:**
- [ ] Check pending orders
- [ ] Update order statuses
- [ ] Monitor stock alerts
- [ ] Process new orders
- [ ] Respond to customer issues

**Maintenance:**
- [ ] Backup database
- [ ] Check error logs
- [ ] Monitor system performance
- [ ] Update stock levels

---

**Quick Start Complete! For detailed procedures, see `USER_MANUAL_SOP.md`**
