# 🚀 Fresh Mart - Quick Project Setup Guide

**Get your e-commerce grocery website running in 15 minutes!**

---

## ⚡ Prerequisites Checklist

Before you begin, ensure you have:

- [ ] MAMP/XAMPP/WAMP installed
- [ ] MySQL running
- [ ] Apache web server running
- [ ] Project files downloaded
- [ ] Terminal/Command prompt access

---

## 📦 Step 1: Install Web Server (5 minutes)

### For macOS - MAMP

1. **Download MAMP:**
   - Visit: https://www.mamp.info/en/downloads/
   - Download MAMP (free version)

2. **Install MAMP:**
   - Open downloaded DMG file
   - Drag MAMP to Applications folder
   - Launch MAMP

3. **Start Servers:**
   - Click "Start" button
   - Wait for green indicators
   - Note the ports:
     - Web Server: 8888
     - MySQL: 8889

4. **Verify:**
   - Open browser: `http://localhost:8888`
   - Should see MAMP start page

### For Windows - XAMPP

1. **Download XAMPP:**
   - Visit: https://www.apachefriends.org
   - Download XAMPP for Windows

2. **Install XAMPP:**
   - Run installer
   - Choose components: Apache, MySQL, PHP
   - Install to `C:\xampp\`

3. **Start Servers:**
   - Open XAMPP Control Panel
   - Click "Start" for Apache
   - Click "Start" for MySQL
   - Both should show green

4. **Verify:**
   - Open browser: `http://localhost`
   - Should see XAMPP dashboard

---

## 📁 Step 2: Deploy Project Files (2 minutes)

### Copy Files to Web Directory

**For MAMP (macOS):**
```bash
# Copy entire project
cp -r ECOMMERCE-WEBSITE /Applications/MAMP/htdocs/onlinesale/

# Verify
ls /Applications/MAMP/htdocs/onlinesale/ECOMMERCE-WEBSITE/
```

**For XAMPP (Windows):**
```bash
# Copy entire project
xcopy ECOMMERCE-WEBSITE C:\xampp\htdocs\onlinesale\ECOMMERCE-WEBSITE\ /E /I

# Verify
dir C:\xampp\htdocs\onlinesale\ECOMMERCE-WEBSITE\
```

### Verify Folder Structure

Your directory should look like:
```
ECOMMERCE-WEBSITE/
├── admin/                  (Admin panel files)
├── images/                 (Product images)
├── includes/               (Common PHP files)
├── Documentation/          (This folder!)
├── index.php              (Homepage)
├── products.php           (Product listing)
├── cart.php               (Shopping cart)
├── success.php            (Order confirmation)
├── onlinesale.sql         (Main database)
├── create_orders_tables.sql
├── admin_setup.sql
└── style.css
```

---

## 🗄️ Step 3: Setup Database (5 minutes)

### Create Database

**Option A: Using phpMyAdmin (Recommended)**

1. **Access phpMyAdmin:**
   - MAMP: `http://localhost:8888/phpMyAdmin`
   - XAMPP: `http://localhost/phpmyadmin`

2. **Create Database:**
   - Click "New" in left sidebar
   - Database name: `onlinesale`
   - Collation: `utf8_general_ci`
   - Click "Create"

3. **Import SQL Files:**
   - Select `onlinesale` database
   - Click "Import" tab
   - Import files **in this order:**
     
     **a) Main Schema:**
     - Click "Choose File"
     - Select `onlinesale.sql`
     - Click "Go"
     - Wait for success message
     
     **b) Orders System:**
     - Click "Choose File"
     - Select `create_orders_tables.sql`
     - Click "Go"
     - Wait for success message
     
     **c) Admin Panel:**
     - Click "Choose File"
     - Select `admin_setup.sql`
     - Click "Go"
     - Wait for success message

4. **Verify Tables:**
   - Click `onlinesale` database
   - Should see tables:
     - ✓ products
     - ✓ users
     - ✓ users_products
     - ✓ orders
     - ✓ order_items
     - ✓ admins

**Option B: Using Command Line**

```bash
# Navigate to project directory
cd /Applications/MAMP/htdocs/onlinesale/ECOMMERCE-WEBSITE/

# Create database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS onlinesale;"

# Import files in order
mysql -u root -p onlinesale < onlinesale.sql
mysql -u root -p onlinesale < create_orders_tables.sql
mysql -u root -p onlinesale < admin_setup.sql

# Verify
mysql -u root -p onlinesale -e "SHOW TABLES;"
```

**Password:**
- MAMP: `root`
- XAMPP: (press Enter, no password)

---

## ⚙️ Step 4: Configure Database Connection (2 minutes)

### Update Connection Settings

**File 1: Customer Site Connection**

Edit: `includes/common.php` (line 5)

**For MAMP:**
```php
$con = mysqli_connect("localhost:8889", "root", "root", "onlinesale");
```

**For XAMPP:**
```php
$con = mysqli_connect("localhost:3306", "root", "", "onlinesale");
```

**File 2: Admin Panel Connection**

Edit: `admin/includes/admin_common.php` (line 5)

**For MAMP:**
```php
$con = mysqli_connect("localhost:8889", "root", "root", "onlinesale");
```

**For XAMPP:**
```php
$con = mysqli_connect("localhost:3306", "root", "", "onlinesale");
```

### Connection Parameters Explained

| Parameter | MAMP | XAMPP | Purpose |
|-----------|------|-------|---------|
| Host | localhost:8889 | localhost:3306 | MySQL server address |
| Username | root | root | Database user |
| Password | root | (empty) | Database password |
| Database | onlinesale | onlinesale | Database name |

---

## 🔐 Step 5: Set Permissions (1 minute)

### For macOS/Linux

```bash
# Navigate to project
cd /Applications/MAMP/htdocs/onlinesale/ECOMMERCE-WEBSITE/

# Set folder permissions
chmod 755 images/
chmod 755 admin/

# Set file permissions
chmod 644 images/*
chmod 644 *.php
```

### For Windows

```bash
# Right-click images folder
# Properties → Security
# Ensure "Users" have Write permissions
```

---

## ✅ Step 6: Verify Installation (5 minutes)

### Test Customer Site

1. **Access Homepage:**
   ```
   http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/
   ```

2. **Verify:**
   - [ ] Homepage loads
   - [ ] Product categories visible
   - [ ] Images display correctly
   - [ ] "Sign Up" and "Login" buttons work

3. **Test Registration:**
   - Click "Sign Up"
   - Fill form:
     - First Name: Test
     - Last Name: User
     - Email: test@test.com
     - Password: test123
     - Phone: 1234567890
   - Click "Sign Up"
   - [ ] Should auto-login and redirect to products

4. **Test Shopping:**
   - [ ] Products display with prices
   - [ ] Stock status shows correctly
   - [ ] Click "Add to Cart" on any product
   - [ ] Cart icon updates with count
   - [ ] Click cart icon
   - [ ] Cart page shows added item

5. **Test Checkout:**
   - In cart, click "Confirm Order"
   - Enter delivery address
   - Click "Confirm Order"
   - [ ] Success page appears
   - [ ] Order number displayed
   - [ ] "View Invoice" button works

### Test Admin Panel

1. **Access Admin Login:**
   ```
   http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/admin/
   ```

2. **Login:**
   - Username: `admin`
   - Password: `admin123`
   - Click "Login"
   - [ ] Dashboard loads

3. **Verify Dashboard:**
   - [ ] Total Products shows count
   - [ ] Total Orders shows count
   - [ ] Low Stock alerts visible
   - [ ] Recent products table displays

4. **Test Product Management:**
   - Click "Add Product"
   - [ ] Form loads
   - Fill test product details
   - Upload test image
   - Click "Add Product"
   - [ ] Product added successfully

5. **Test Order Management:**
   - Click "Orders" in sidebar
   - [ ] Orders list displays
   - Click "View Details" on any order
   - [ ] Order details show correctly
   - Change status to "Processing"
   - [ ] Status updates successfully

---

## 🧪 Step 7: Test Concurrency Control (2 minutes)

### Run Automated Test

1. **Access Test Script:**
   ```
   http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/test_concurrency.php
   ```

2. **Expected Results:**
   - ✓ Test product created with stock = 1
   - ✓ Two test users created
   - ✓ User A: Order placed successfully
   - ✓ User B: Out of Stock error
   - ✓ Final stock: 0 (NOT -1)
   - ✓ PASS: Concurrency control working!

3. **If Test Fails:**
   - Check database engine: `SHOW TABLE STATUS WHERE Name = 'products';`
   - Should be InnoDB (not MyISAM)
   - If MyISAM: `ALTER TABLE products ENGINE=InnoDB;`

---

## 🎉 Setup Complete!

### ✅ Verification Checklist

- [ ] Web server running (Apache + MySQL)
- [ ] Project files in htdocs
- [ ] Database created and imported (3 SQL files)
- [ ] Database connection configured (2 files)
- [ ] Folder permissions set
- [ ] Customer site accessible
- [ ] Admin panel accessible
- [ ] Test registration works
- [ ] Test order placement works
- [ ] Concurrency test passes

### 🎯 You Can Now:

✅ Browse products  
✅ Register customers  
✅ Add items to cart  
✅ Place orders  
✅ Generate invoices  
✅ Manage products (admin)  
✅ Process orders (admin)  
✅ Monitor stock (admin)  

---

## 📍 Important URLs

### Customer Site
```
Homepage:     http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/
Products:     http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/products.php
Cart:         http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/cart.php
```

### Admin Panel
```
Login:        http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/admin/
Dashboard:    http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/admin/dashboard.php
Products:     http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/admin/products.php
Orders:       http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/admin/orders.php
```

### Testing
```
Concurrency:  http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/test_concurrency.php
Demo:         http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/concurrency_demo.html
Documentation: http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/documentation.html
```

---

## 🔑 Default Credentials

### Admin Panel
- **Username:** `admin`
- **Password:** `admin123`

### Test Customer (if created)
- **Email:** `test@test.com`
- **Password:** `test123`

**⚠️ IMPORTANT:** Change admin password after first login!

```sql
-- Change admin password
UPDATE admins SET password = MD5('your_new_password') WHERE username = 'admin';
```

---

## 🛠️ Troubleshooting Quick Fixes

### Issue: "Connection failed"

**Fix database connection:**

1. Check MySQL is running
2. Verify port number:
   - MAMP: 8889
   - XAMPP: 3306
3. Update `includes/common.php` and `admin/includes/admin_common.php`

### Issue: "Table doesn't exist"

**Re-import database:**
```bash
mysql -u root -p onlinesale < onlinesale.sql
mysql -u root -p onlinesale < create_orders_tables.sql
mysql -u root -p onlinesale < admin_setup.sql
```

### Issue: Images not displaying

**Fix permissions:**
```bash
chmod 755 images/
chmod 644 images/*
```

### Issue: Can't upload images

**Check folder permissions:**
```bash
# macOS/Linux
chmod 755 images/

# Windows
# Right-click images folder → Properties → Security
# Grant "Write" permission to Users group
```

### Issue: Admin login fails

**Reset admin account:**
```sql
-- Access MySQL
mysql -u root -p onlinesale

-- Check admin exists
SELECT * FROM admins;

-- If missing, create admin
INSERT INTO admins (username, password, email) 
VALUES ('admin', MD5('admin123'), 'admin@freshmart.com');
```

---

## 🎯 Post-Setup Tasks

### Immediate (Do Now)

1. **Change Admin Password:**
   ```sql
   UPDATE admins SET password = MD5('YourSecurePassword123') WHERE username = 'admin';
   ```

2. **Test Core Features:**
   - [ ] Register a customer
   - [ ] Add product to cart
   - [ ] Place an order
   - [ ] View invoice
   - [ ] Login to admin
   - [ ] View the order in admin panel

3. **Run Concurrency Test:**
   - Visit: `test_concurrency.php`
   - Verify: Stock doesn't go negative

### Within 24 Hours

1. **Customize Content:**
   - Update company name (if not "Fresh Mart")
   - Update contact number
   - Update about page content

2. **Add Real Products:**
   - Login to admin panel
   - Add your actual products
   - Upload real product images
   - Set correct prices and stock

3. **Configure Email (Optional):**
   - Edit `email_invoice.php`
   - Add SMTP settings
   - Test email delivery

### Within 1 Week

1. **Setup Backup System:**
   ```bash
   # Create backup script
   #!/bin/bash
   mysqldump -u root -p onlinesale > backup_$(date +%Y%m%d).sql
   tar -czf images_backup_$(date +%Y%m%d).tar.gz images/
   ```

2. **Monitor System:**
   - Check error logs daily
   - Monitor stock levels
   - Process orders promptly

3. **Train Staff:**
   - Admin staff: Read `USER_MANUAL_SOP.md` - Section 4
   - Follow test flows in `ADMIN_TEST_FLOWS.md`

---

## 📊 Database Schema Overview

### Tables Created

| Table | Purpose | Key Fields |
|-------|---------|------------|
| **products** | Product catalog | id, name, price, stock_quantity, category |
| **users** | Customer accounts | id, email_id, password, first_name, phone |
| **users_products** | Shopping cart | user_id, item_id, quantity, status |
| **orders** | Order headers | id, user_id, order_number, final_amount |
| **order_items** | Order details | order_id, product_id, quantity, final_price |
| **admins** | Admin accounts | id, username, password |

### Sample Data Included

**16 Products:**
- 4 Fruits (Apples, Bananas, Oranges, Grapes)
- 4 Vegetables (Tomatoes, Onions, Potatoes, Carrots)
- 4 Dairy (Milk, Yogurt, Cheese, Butter)
- 4 Beverages (Orange Juice, Apple Juice, Water, Soft Drink)

**1 Admin Account:**
- Username: admin
- Password: admin123

---

## 🔧 Configuration Reference

### Database Connection

**File Locations:**
- Customer site: `includes/common.php` (line 5)
- Admin panel: `admin/includes/admin_common.php` (line 5)

**MAMP Configuration:**
```php
$con = mysqli_connect("localhost:8889", "root", "root", "onlinesale");
```

**XAMPP Configuration:**
```php
$con = mysqli_connect("localhost:3306", "root", "", "onlinesale");
```

### PHP Settings (if needed)

Edit `php.ini`:
```ini
upload_max_filesize = 5M
post_max_size = 10M
max_execution_time = 300
memory_limit = 128M
```

**Find php.ini:**
- MAMP: `/Applications/MAMP/bin/php/php7.x.x/conf/php.ini`
- XAMPP: `C:\xampp\php\php.ini`

---

## 🎨 Customization Guide

### Update Company Information

**1. Company Name:**
- Edit: `includes/header_menu.php`
- Find: "Fresh Mart"
- Replace with your company name

**2. Contact Number:**
- Edit: `includes/footer.php`
- Find: "+91 9360509515"
- Replace with your number

**3. About Page:**
- Edit: `about.php`
- Update company description
- Add your story

### Add Your Logo

1. Create logo image (recommended: 200x60px)
2. Save as: `images/logo.png`
3. Edit: `includes/header_menu.php`
4. Update logo image reference

### Customize Colors

Edit: `style.css`

**Primary Color (Green):**
```css
/* Find and replace */
#10b981 → Your primary color
#059669 → Your darker shade
```

**Accent Colors:**
```css
/* Buttons, links, highlights */
.btn-primary { background: #your-color; }
```

---

## 📱 Mobile Responsiveness

**Already Included:**
- Bootstrap 4 responsive grid
- Mobile-friendly navigation
- Touch-optimized buttons
- Responsive images

**Test On:**
- Desktop browsers
- Tablets
- Mobile phones
- Different screen sizes

---

## 🔒 Security Checklist

### Immediate Security Tasks

- [ ] Change admin password from default
- [ ] Update database credentials
- [ ] Set proper folder permissions
- [ ] Enable HTTPS (for production)
- [ ] Disable directory listing
- [ ] Remove test files (in production)

### Production Security

**Before Going Live:**

1. **Change All Passwords:**
   ```sql
   UPDATE admins SET password = MD5('SecurePassword123!') WHERE username = 'admin';
   ```

2. **Disable Test Files:**
   - Delete or restrict access to:
     - `test_concurrency.php`
     - `invoice_demo.php`
     - `concurrency_demo.html`

3. **Enable HTTPS:**
   - Get SSL certificate
   - Configure Apache for HTTPS
   - Force HTTPS redirect

4. **Secure Database:**
   - Create dedicated database user (not root)
   - Grant only necessary permissions
   - Use strong password

---

## 🚀 Going Live Checklist

### Pre-Launch

- [ ] All setup steps completed
- [ ] Test order placed successfully
- [ ] Admin panel accessible
- [ ] Concurrency test passes
- [ ] Images uploading correctly
- [ ] Invoice generation works
- [ ] Email delivery configured (optional)
- [ ] Backup system setup
- [ ] Security measures implemented
- [ ] Admin password changed
- [ ] Staff trained

### Launch Day

- [ ] Final database backup
- [ ] Monitor error logs
- [ ] Test all features
- [ ] Customer support ready
- [ ] Admin monitoring dashboard

### Post-Launch

- [ ] Monitor orders daily
- [ ] Check stock levels
- [ ] Process orders promptly
- [ ] Respond to customer inquiries
- [ ] Daily backups
- [ ] Weekly system review

---

## 📞 Support & Resources

### Documentation

**In This Folder:**
- `USER_MANUAL_SOP.md` - Complete procedures
- `QUICK_START_GUIDE.md` - Fast reference
- `ONE_PAGE_REFERENCE.md` - Daily reference

**In Parent Folder:**
- `documentation.html` - Interactive portal
- `CONCURRENCY_CONTROL.md` - Technical details
- `ADMIN_SETUP_GUIDE.md` - Admin guide

### Get Help

**Technical Issues:**
1. Check troubleshooting section above
2. Review `USER_MANUAL_SOP.md` - Section 8
3. Check error logs
4. Contact support: +91 9360509515

**Training:**
1. Follow `QUICK_START_GUIDE.md`
2. Complete `ADMIN_TEST_FLOWS.md`
3. Practice with test data

---

## ⏱️ Setup Time Summary

| Step | Task | Time |
|------|------|------|
| 1 | Install web server | 5 min |
| 2 | Deploy project files | 2 min |
| 3 | Setup database | 5 min |
| 4 | Configure connection | 2 min |
| 5 | Set permissions | 1 min |
| 6 | Verify installation | 5 min |
| **Total** | **Complete Setup** | **20 min** |

---

## 🎊 Next Steps

### Now That Setup is Complete

1. **Read Documentation:**
   - `QUICK_START_GUIDE.md` for common tasks
   - `USER_MANUAL_SOP.md` for detailed procedures

2. **Customize System:**
   - Add your products
   - Update company information
   - Configure email settings

3. **Train Your Team:**
   - Share documentation
   - Practice with test data
   - Review procedures

4. **Go Live:**
   - Final testing
   - Security review
   - Launch!

---

## 💡 Pro Tips

1. **Bookmark These URLs:**
   - Customer site
   - Admin panel
   - Documentation portal

2. **Print ONE_PAGE_REFERENCE.md:**
   - Keep at your desk
   - Quick command reference
   - Daily operations guide

3. **Setup Daily Backup:**
   - Automate database backup
   - Store securely
   - Test restore procedure

4. **Monitor Stock Daily:**
   - Check low stock alerts
   - Reorder inventory
   - Update stock quantities

5. **Process Orders Promptly:**
   - Check pending orders daily
   - Update statuses
   - Maintain customer satisfaction

---

## 📋 Quick Command Reference

### Database Backup
```bash
mysqldump -u root -p onlinesale > backup_$(date +%Y%m%d).sql
```

### Database Restore
```bash
mysql -u root -p onlinesale < backup_20260321.sql
```

### Check Tables
```bash
mysql -u root -p onlinesale -e "SHOW TABLES;"
```

### View Products
```bash
mysql -u root -p onlinesale -e "SELECT id, name, stock_quantity FROM products;"
```

### View Orders
```bash
mysql -u root -p onlinesale -e "SELECT order_number, final_amount, order_status FROM orders ORDER BY created_at DESC LIMIT 10;"
```

---

## 🎯 Success Criteria

**Setup is successful when:**

✅ Homepage loads without errors  
✅ Products display with images  
✅ Customer can register and login  
✅ Items can be added to cart  
✅ Orders can be placed  
✅ Invoices generate correctly  
✅ Admin can login  
✅ Admin can manage products  
✅ Admin can view orders  
✅ Concurrency test passes  

**All Green? You're ready to go! 🎉**

---

## 📖 Further Reading

**After Setup:**
- Read `USER_MANUAL_SOP.md` for complete operations guide
- Review `CONCURRENCY_CONTROL.md` to understand transaction logic
- Complete `ADMIN_TEST_FLOWS.md` for thorough testing

**For Daily Use:**
- Keep `ONE_PAGE_REFERENCE.md` handy
- Bookmark admin panel
- Monitor dashboard alerts

---

## 🆘 Common Setup Issues

### Port Already in Use

**MAMP:**
- Preferences → Ports
- Change Apache port to 8888
- Change MySQL port to 8889

**XAMPP:**
- Config → Service Settings
- Change Apache port to 8080
- Change MySQL port to 3307

### Database Import Fails

**Check:**
1. Database exists: `SHOW DATABASES;`
2. User has permissions: `GRANT ALL ON onlinesale.* TO 'root'@'localhost';`
3. File path is correct
4. No syntax errors in SQL file

### Page Shows Blank

**Enable error reporting:**

Add to top of `index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

Check for:
- PHP syntax errors
- Database connection errors
- Missing files

---

## 🎊 Setup Complete!

**Your Fresh Mart e-commerce system is now ready to use!**

**Next:** Read `QUICK_START_GUIDE.md` for common operations

**Support:** +91 9360509515

---

**🛒 Welcome to Fresh Mart!**

*Professional E-Commerce Platform - Setup Complete*
