# Admin Panel Test Flows - Quick Reference

## Setup Verification (Run First!)

```bash
./test_admin_setup.sh
```

This verifies all 25 admin files are in place.

---

## Critical Test Flows

### Flow 1: Initial Setup & Login (5 min)

1. **Setup Database**
   ```bash
   mysql -u root -p onlinesale < admin_setup.sql
   ```
   - ✓ No errors during import
   - ✓ `admins` table created
   - ✓ `products` table updated with `category` and `image_path`

2. **First Login**
   - Navigate to: `http://localhost:8888/admin/`
   - Username: `admin`
   - Password: `admin123`
   - ✓ Dashboard loads
   - ✓ Statistics display (products, orders, stock alerts)
   - ✓ Admin name shows in header

3. **Verify Dashboard**
   - ✓ Total Products count matches database
   - ✓ Recent products table shows 5 products
   - ✓ All navigation links work

---

### Flow 2: Add Product Complete Workflow (5 min)

1. **Navigate to Add Product**
   - Click "Add Product" in sidebar
   - ✓ Form loads with all fields

2. **Fill Form**
   - Product Name: `Test Strawberry`
   - Category: `Fruits`
   - Price: `180`
   - Discount: `15`
   - Stock Quantity: `40`
   - Unit: `kg`
   - Upload image: Select any JPG/PNG (< 5MB)
   - ✓ Image preview appears

3. **Submit & Verify**
   - Click "Add Product"
   - ✓ Success message appears
   - ✓ Product appears in products list
   - ✓ All data correct in table
   - ✓ Image displays as thumbnail

4. **Check Frontend**
   - Open: `http://localhost:8888/products.php`
   - ✓ New product appears in Fruits section
   - ✓ Image displays correctly
   - ✓ Price and discount shown
   - ✓ Stock badge shows "In Stock"

---

### Flow 3: Edit Product Complete Workflow (5 min)

1. **Select Product to Edit**
   - Go to "All Products"
   - Click blue edit icon for "Test Strawberry"
   - ✓ Edit form loads with current data

2. **Update Fields**
   - Change name to: `Fresh Strawberries`
   - Change price to: `200`
   - Change stock to: `3` (to test low stock)
   - Optionally upload new image
   - Click "Update Product"

3. **Verify Changes**
   - ✓ Success message: "Product updated successfully"
   - ✓ Updated name shows in list
   - ✓ New price displayed
   - ✓ Status badge shows "Low Stock" (yellow)
   - ✓ New image displays if uploaded

4. **Check Frontend**
   - Refresh products page
   - ✓ Updated information displays
   - ✓ Low stock badge appears (yellow)

---

### Flow 4: Delete Product Workflow (3 min)

1. **Delete Product**
   - Go to "All Products"
   - Click red delete icon for "Fresh Strawberries"
   - ✓ Confirmation dialog appears
   - Click "OK"

2. **Verify Deletion**
   - ✓ Success message: "Product deleted successfully"
   - ✓ Product removed from list
   - ✓ Total products count decreased by 1

3. **Check Database**
   ```sql
   SELECT * FROM products WHERE name LIKE '%Strawberr%';
   ```
   - ✓ Product not found in database

4. **Check Filesystem**
   - ✓ Image file deleted from `images/` folder

5. **Check Frontend**
   - ✓ Product no longer appears on products page

---

### Flow 5: Stock Management Validation (5 min)

1. **Test Low Stock Alert**
   - Edit any product, set stock to `2`
   - ✓ Dashboard "Low Stock Items" counter increases
   - ✓ Product shows yellow badge in list
   - ✓ Frontend shows "Only 2 left!" badge

2. **Test Out of Stock**
   - Edit same product, set stock to `0`
   - ✓ Dashboard "Out of Stock" counter increases
   - ✓ Product shows red badge in list
   - ✓ Frontend shows "Out of Stock" badge
   - ✓ "Add to Cart" button disabled on frontend

3. **Test Stock Filters**
   - Click "Low Stock" filter
   - ✓ Only low stock products shown
   - Click "Out of Stock" filter
   - ✓ Only out of stock products shown
   - Click "All" filter
   - ✓ All products shown

---

### Flow 6: Security & Validation Testing (5 min)

1. **Test Unauthorized Access**
   - Logout from admin
   - Try to access: `http://localhost:8888/admin/dashboard.php`
   - ✓ Redirects to login page

2. **Test Invalid Login**
   - Username: `admin`
   - Password: `wrongpassword`
   - ✓ Error message: "Invalid username or password"
   - ✓ No access granted

3. **Test Form Validation**
   - Go to "Add Product"
   - Leave Product Name empty
   - Try to submit
   - ✓ Browser validation prevents submission

4. **Test Invalid Price**
   - Fill form with negative price: `-50`
   - Submit
   - ✓ Error message: "Invalid price"

5. **Test Invalid Discount**
   - Fill form with discount: `150`
   - Submit
   - ✓ Error message: "Discount must be between 0 and 100"

6. **Test Invalid Image**
   - Try to upload .txt file
   - ✓ Error message: "Invalid file type"

---

### Flow 7: Image Upload Testing (5 min)

1. **Test Valid Image Formats**
   - Add product with JPG image → ✓ Works
   - Add product with PNG image → ✓ Works
   - Add product with WEBP image → ✓ Works

2. **Test Image Preview**
   - Select image in add/edit form
   - ✓ Preview appears immediately
   - ✓ Preview shows correct image

3. **Test Image Storage**
   - Add product with image
   - Check `images/` folder
   - ✓ Image saved with unique filename (format: `[uniqid]_[timestamp].[ext]`)
   - ✓ No filename conflicts

4. **Test Image Display**
   - ✓ Thumbnail in admin product list (50x50px)
   - ✓ Preview in edit form (200x200px max)
   - ✓ Full size on frontend

---

### Flow 8: Complete Product Lifecycle (10 min)

**End-to-end test of a product from creation to deletion:**

1. **Create Product**
   - Add "Test Watermelon"
   - Category: Fruits, Price: 80, Stock: 20
   - Upload image
   - ✓ Product created successfully

2. **View on Frontend**
   - Open products page
   - ✓ Product appears in Fruits section
   - ✓ All details correct

3. **Customer Interaction** (Optional)
   - Login as customer
   - Add product to cart
   - ✓ Can add to cart
   - ✓ Stock validation works

4. **Edit Product**
   - Change price to 90
   - Update stock to 2
   - ✓ Changes saved
   - ✓ Low stock badge appears

5. **Delete Product**
   - Delete the product
   - ✓ Removed from admin
   - ✓ Removed from frontend
   - ✓ Image deleted

---

## Quick Smoke Test (2 min)

Fastest way to verify everything works:

```bash
# 1. Verify files
./test_admin_setup.sh

# 2. Setup database (if not done)
mysql -u root -p onlinesale < admin_setup.sql
```

**Browser Tests:**
1. Login → ✓ Dashboard loads
2. Add Product → ✓ Form works, product created
3. View Products → ✓ List displays with edit/delete buttons
4. Edit Product → ✓ Can update fields
5. Delete Product → ✓ Confirmation works, product removed
6. Logout → ✓ Session cleared

---

## Automated Database Checks

Run these SQL queries to verify data integrity:

```sql
-- Check admin table
SELECT * FROM admins;
-- Expected: 1 row with username 'admin'

-- Check products schema
DESCRIBE products;
-- Expected: Columns include category, image_path, stock_quantity

-- Check products have categories
SELECT id, name, category, image_path FROM products LIMIT 5;
-- Expected: All products have category and image_path values

-- Check product count
SELECT COUNT(*) as total FROM products;
-- Expected: 16 products (original data)

-- Check low stock products
SELECT id, name, stock_quantity FROM products WHERE stock_quantity < 5 AND stock_quantity > 0;
-- Expected: Products with stock 1-4

-- Check out of stock products
SELECT id, name, stock_quantity FROM products WHERE stock_quantity = 0;
-- Expected: Products with stock 0
```

---

## Test Scenarios by Priority

### P0 - Critical (Must Pass)
- [ ] Database setup completes without errors
- [ ] Admin login works with correct credentials
- [ ] Can add new product with all fields
- [ ] Can view product list
- [ ] Can edit existing product
- [ ] Can delete product
- [ ] Session protection works (no unauthorized access)

### P1 - High Priority
- [ ] Image upload works for all formats (JPG, PNG, WEBP)
- [ ] Form validation catches invalid inputs
- [ ] Dashboard statistics are accurate
- [ ] Products appear on frontend after adding
- [ ] Stock filters work correctly
- [ ] Logout works properly

### P2 - Medium Priority
- [ ] Image preview works
- [ ] Low stock alerts display correctly
- [ ] Out of stock products handled properly
- [ ] Edit without changing image works
- [ ] Special characters in product names handled
- [ ] Responsive design works on mobile

### P3 - Nice to Have
- [ ] Alert messages dismissible
- [ ] UI looks good in all browsers
- [ ] Navigation smooth and intuitive
- [ ] Error messages are helpful

---

## Common Issues & Solutions

### Issue: "Connection failed" error
**Solution:** Update database credentials in `admin/includes/admin_common.php`:
```php
$con = mysqli_connect("localhost:8889", "root", "root", "onlinesale");
```

### Issue: Cannot upload images
**Solution:** Set folder permissions:
```bash
chmod 755 images/
```

### Issue: "Table 'admins' doesn't exist"
**Solution:** Run the setup script:
```bash
mysql -u root -p onlinesale < admin_setup.sql
```

### Issue: Images not displaying
**Solution:** Check that image_path in database starts with `images/` and file exists

### Issue: Products not showing on frontend
**Solution:** Clear browser cache and refresh

---

## Test Execution Order

For first-time testing, follow this order:

1. **Setup** (5 min)
   - Run `test_admin_setup.sh`
   - Run `admin_setup.sql`

2. **Authentication** (5 min)
   - Test Flow 1 (Login/Logout)

3. **Core Features** (15 min)
   - Test Flow 2 (Add Product)
   - Test Flow 3 (Edit Product)
   - Test Flow 4 (Delete Product)

4. **Integration** (10 min)
   - Test Flow 5 (Stock Management)
   - Verify frontend displays

5. **Security** (5 min)
   - Test Flow 6 (Security & Validation)

6. **Advanced** (10 min)
   - Test Flow 7 (Image Upload)
   - Test Flow 8 (Complete Lifecycle)

**Total Time: ~50 minutes for complete testing**

---

## Test Report Template

```
Test Date: _______________
Tester: _______________
Browser: _______________
PHP Version: _______________
MySQL Version: _______________

Critical Tests (P0):
[ ] Database Setup: PASS / FAIL
[ ] Admin Login: PASS / FAIL
[ ] Add Product: PASS / FAIL
[ ] Edit Product: PASS / FAIL
[ ] Delete Product: PASS / FAIL
[ ] Session Security: PASS / FAIL

Issues Found:
1. _______________
2. _______________

Overall Status: READY / NEEDS FIXES
```

---

## Quick Validation Commands

```bash
# Verify all files exist
./test_admin_setup.sh

# Check database tables
mysql -u root -p onlinesale -e "SHOW TABLES;"

# Check admin user
mysql -u root -p onlinesale -e "SELECT username FROM admins;"

# Check products schema
mysql -u root -p onlinesale -e "DESCRIBE products;"

# Count products
mysql -u root -p onlinesale -e "SELECT COUNT(*) FROM products;"

# Check images folder
ls -lh images/ | head -10
```

---

## Success Criteria

The admin panel is ready for use when:

✓ All 25 files verified by test script
✓ Database setup completed without errors
✓ Can login with default credentials
✓ Can add a new product with image
✓ Can edit existing product
✓ Can delete product
✓ Products appear correctly on frontend
✓ Stock management works
✓ No security vulnerabilities in basic tests
✓ Image uploads work for all formats

---

## Next Steps After Testing

1. **Change Default Password** (Critical!)
   - Login to admin
   - Update admin password in database or create new admin user

2. **Production Considerations**
   - Use prepared statements instead of mysqli_real_escape_string
   - Implement bcrypt/password_hash() instead of MD5
   - Add CSRF protection
   - Implement rate limiting for login
   - Add audit logging
   - Set up regular database backups

3. **Optional Enhancements**
   - Add product search in admin
   - Bulk product operations
   - Export products to CSV
   - Product categories management
   - Admin user management
   - Order management interface

---

For detailed test cases, see `ADMIN_TESTING.md` (18 comprehensive test flows with 60+ test cases).
