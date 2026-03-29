# ⚡ EC2 Quick Go-Live Guide

**Get your website live in 30 minutes after EC2 instance is created!**

---

## 🎯 Assumption

You have:
- ✅ EC2 instance running (Ubuntu 22.04)
- ✅ SSH access working
- ✅ Public IP address noted
- ✅ Security group allows ports 22, 80, 443

**Now let's make it live!**

---

## 🚀 Quick Steps (30 Minutes)

### Step 1: Connect to EC2 (1 minute)

```bash
# Replace YOUR_KEY_FILE and YOUR_EC2_IP with your values
ssh -i ~/.ssh/fresh-mart-key.pem ubuntu@YOUR_EC2_IP

# Example:
ssh -i ~/.ssh/fresh-mart-key.pem ubuntu@13.126.45.67
```

**✅ You should see Ubuntu welcome message**

---

### Step 2: Install LAMP Stack (10 minutes)

**Copy and paste this entire block:**

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Apache
sudo apt install -y apache2

# Install MySQL
sudo apt install -y mysql-server

# Install PHP and extensions
sudo apt install -y php libapache2-mod-php php-mysql php-cli php-curl php-gd php-mbstring php-xml php-zip

# Start services
sudo systemctl start apache2
sudo systemctl start mysql
sudo systemctl enable apache2
sudo systemctl enable mysql

# Verify installation
php -v
apache2 -v
mysql --version
```

**✅ Test:** Open browser → `http://YOUR_EC2_IP` → Should see Apache default page

---

### Step 3: Secure MySQL (2 minutes)

```bash
# Set root password
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'YourPassword123!';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Test login
mysql -u root -p
# Enter password: YourPassword123!
# Type: EXIT
```

**✅ MySQL is now secured**

---

### Step 4: Upload Project Files (5 minutes)

**Option A: Using SCP (From Your Local Machine)**

Open **NEW terminal window** on your local machine:

```bash
# Navigate to your project
cd "/Users/shanmugasundaram.g/PROJECT/Ecommerce Project/ECOMMERCE-WEBSITE"

# Upload all files (this may take 2-3 minutes)
scp -i ~/.ssh/fresh-mart-key.pem -r * ubuntu@YOUR_EC2_IP:/home/ubuntu/fresh-mart/
```

**Back on EC2 terminal:**

```bash
# Move files to web directory
sudo rm -rf /var/www/html/*
sudo mv /home/ubuntu/fresh-mart/* /var/www/html/
sudo chown -R www-data:www-data /var/www/html/
sudo chmod -R 755 /var/www/html/
sudo chmod 775 /var/www/html/images/
```

**Option B: Using Git (If Your Code is on GitHub)**

```bash
# On EC2
cd /var/www/html
sudo rm -rf *
sudo git clone https://github.com/yourusername/fresh-mart.git .
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod 775 images/
```

**✅ Files are now on server**

---

### Step 5: Setup Database (5 minutes)

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE onlinesale;"

# Import schema files
cd /var/www/html
mysql -u root -p onlinesale < onlinesale.sql
mysql -u root -p onlinesale < create_orders_tables.sql
mysql -u root -p onlinesale < admin_setup.sql

# Verify tables
mysql -u root -p onlinesale -e "SHOW TABLES;"
```

**✅ Expected output:**
```
+----------------------+
| Tables_in_onlinesale |
+----------------------+
| admins               |
| order_items          |
| orders               |
| products             |
| users                |
| users_products       |
+----------------------+
```

---

### Step 6: Configure Database Connection (2 minutes)

**Edit customer site connection:**

```bash
sudo nano /var/www/html/includes/common.php
```

**Change line 5 to:**
```php
$con = mysqli_connect("localhost", "root", "YourPassword123!", "onlinesale");
```

**Save:** Ctrl+X, Y, Enter

**Edit admin panel connection:**

```bash
sudo nano /var/www/html/admin/includes/admin_common.php
```

**Change line 5 to:**
```php
$con = mysqli_connect("localhost", "root", "YourPassword123!", "onlinesale");
```

**Save:** Ctrl+X, Y, Enter

**✅ Database connection configured**

---

### Step 7: Configure Apache (3 minutes)

```bash
# Enable mod_rewrite
sudo a2enmod rewrite

# Restart Apache
sudo systemctl restart apache2
```

**✅ Apache configured**

---

### Step 8: Open Firewall Ports (1 minute)

```bash
# Enable firewall
sudo ufw enable

# Allow SSH (IMPORTANT!)
sudo ufw allow 22/tcp

# Allow HTTP
sudo ufw allow 80/tcp

# Allow HTTPS
sudo ufw allow 443/tcp

# Check status
sudo ufw status
```

**✅ Firewall configured**

---

## 🌐 Step 9: VIEW YOUR WEBSITE LIVE! (1 minute)

### Open in Browser:

```
http://YOUR_EC2_PUBLIC_IP
```

**Example:**
```
http://13.126.45.67
```

### ✅ You Should See:

- 🏠 Fresh Mart homepage
- 🖼️ Product category images
- 🔘 Sign Up and Login buttons
- 🎨 Green theme with modern design

---

## 🎉 YOUR WEBSITE IS NOW LIVE!

### Quick Test (5 minutes)

**1. Test Customer Flow:**
```
http://YOUR_EC2_IP
↓
Click "Sign Up"
↓
Register: test@test.com / test123
↓
Browse Products
↓
Add to Cart
↓
Confirm Order
↓
Success! ✅
```

**2. Test Admin Panel:**
```
http://YOUR_EC2_IP/admin/
↓
Login: admin / admin123
↓
Dashboard loads ✅
↓
View Products ✅
↓
View Orders ✅
```

---

## 🔗 Your Live URLs

### Customer Site
```
Homepage:     http://YOUR_EC2_IP/
Products:     http://YOUR_EC2_IP/products.php
Cart:         http://YOUR_EC2_IP/cart.php
About:        http://YOUR_EC2_IP/about.php
```

### Admin Panel
```
Login:        http://YOUR_EC2_IP/admin/
Dashboard:    http://YOUR_EC2_IP/admin/dashboard.php
Products:     http://YOUR_EC2_IP/admin/products.php
Orders:       http://YOUR_EC2_IP/admin/orders.php
```

### Testing
```
Concurrency:  http://YOUR_EC2_IP/test_concurrency.php
```

---

## 🔑 Default Credentials

### Admin Panel
- **Username:** `admin`
- **Password:** `admin123`

### Test Customer (create during testing)
- **Email:** `test@test.com`
- **Password:** `test123`

**⚠️ IMPORTANT:** Change admin password immediately!

```bash
mysql -u root -p onlinesale -e "UPDATE admins SET password = MD5('YourNewSecurePassword') WHERE username = 'admin';"
```

---

## 🎯 Must-Do Tasks After Going Live

### Immediate (Next 10 minutes)

#### 1. Change Admin Password
```bash
mysql -u root -p onlinesale
```
```sql
UPDATE admins SET password = MD5('YourSecurePassword123!') WHERE username = 'admin';
EXIT;
```

#### 2. Remove Test Files
```bash
cd /var/www/html
sudo rm test_concurrency.php
sudo rm concurrency_demo.html
sudo rm invoice_demo.php
```

#### 3. Test Core Features
- [ ] Register new customer
- [ ] Add product to cart
- [ ] Place order
- [ ] View invoice
- [ ] Login to admin
- [ ] View order in admin panel

---

## 🔒 Essential Security (Next 20 minutes)

### 1. Update Security Group (AWS Console)

**Restrict SSH Access:**
1. AWS Console → EC2 → Security Groups
2. Select your security group
3. Edit inbound rules
4. SSH rule: Change source from `0.0.0.0/0` to `My IP`
5. Save rules

**✅ Now only YOUR IP can SSH to server**

### 2. Disable Root Login

```bash
sudo nano /etc/ssh/sshd_config

# Find and change:
PermitRootLogin no
PasswordAuthentication no

# Save and restart
sudo systemctl restart sshd
```

### 3. Setup Automatic Backups

```bash
# Create backup script
sudo nano /usr/local/bin/backup.sh
```

**Add this:**
```bash
#!/bin/bash
DATE=$(date +%Y%m%d)
mysqldump -u root -pYourPassword123! onlinesale | gzip > /home/ubuntu/backup_$DATE.sql.gz
tar -czf /home/ubuntu/images_$DATE.tar.gz /var/www/html/images
find /home/ubuntu/backup_*.sql.gz -mtime +7 -delete
find /home/ubuntu/images_*.tar.gz -mtime +7 -delete
```

**Make executable and schedule:**
```bash
sudo chmod +x /usr/local/bin/backup.sh

# Schedule daily at 2 AM
sudo crontab -e
# Add this line:
0 2 * * * /usr/local/bin/backup.sh
```

**Test backup:**
```bash
sudo /usr/local/bin/backup.sh
ls -lh /home/ubuntu/backup_*
```

**✅ Daily backups configured**

---

## 🌍 Add Domain Name (Optional - 30 minutes)

### If You Have a Domain

#### 1. Allocate Elastic IP (AWS Console)

1. EC2 → Elastic IPs → Allocate Elastic IP
2. Select new IP → Actions → Associate
3. Select your instance → Associate
4. **Note your Elastic IP:** `xx.xx.xx.xx`

#### 2. Configure DNS (At Your Domain Registrar)

**Add A Records:**

| Type | Name | Value | TTL |
|------|------|-------|-----|
| A | @ | YOUR_ELASTIC_IP | 3600 |
| A | www | YOUR_ELASTIC_IP | 3600 |

**Wait:** 5-60 minutes for DNS propagation

**Test:**
```bash
nslookup your-domain.com
# Should return your Elastic IP
```

#### 3. Install SSL Certificate (FREE)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-apache

# Get certificate
sudo certbot --apache -d your-domain.com -d www.your-domain.com

# Follow prompts:
# - Enter email
# - Agree to terms (A)
# - Redirect HTTP to HTTPS? → 2 (Yes)
```

**✅ Your site now has HTTPS!**

**Access:**
```
https://your-domain.com
```

---

## 📊 Quick Verification Checklist

### ✅ Website Live Checklist

- [ ] Can access: `http://YOUR_EC2_IP`
- [ ] Homepage loads with images
- [ ] Can click "Sign Up"
- [ ] Can register new user
- [ ] Can view products
- [ ] Can add to cart
- [ ] Can place order
- [ ] Can view invoice
- [ ] Admin panel accessible: `http://YOUR_EC2_IP/admin/`
- [ ] Can login to admin (admin/admin123)
- [ ] Dashboard shows statistics
- [ ] Can view orders in admin

**All checked?** ✅ **YOUR WEBSITE IS LIVE!**

---

## 🚨 Troubleshooting Quick Fixes

### Issue: Website Not Loading

**Check 1: Apache Running?**
```bash
sudo systemctl status apache2
# If not running:
sudo systemctl start apache2
```

**Check 2: Security Group?**
- AWS Console → EC2 → Security Groups
- Inbound rules must allow HTTP (port 80) from `0.0.0.0/0`

**Check 3: Firewall?**
```bash
sudo ufw status
# Should show: 80/tcp ALLOW Anywhere
```

### Issue: Database Connection Error

**Check connection:**
```bash
mysql -u root -p
# Enter password
# If fails, reset password:
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'YourPassword123!';"
```

**Check config files:**
```bash
# Verify password in both files
cat /var/www/html/includes/common.php | grep mysqli_connect
cat /var/www/html/admin/includes/admin_common.php | grep mysqli_connect
```

### Issue: Images Not Displaying

**Fix permissions:**
```bash
cd /var/www/html
sudo chown -R www-data:www-data images/
sudo chmod 775 images/
sudo chmod 644 images/*
```

### Issue: 403 Forbidden Error

**Fix permissions:**
```bash
sudo chown -R www-data:www-data /var/www/html/
sudo chmod -R 755 /var/www/html/
```

---

## 🎯 30-Minute Go-Live Procedure

### Timeline

| Time | Task | Command |
|------|------|---------|
| 0:00 | Connect to EC2 | `ssh -i key.pem ubuntu@IP` |
| 0:01 | Install Apache | `sudo apt install -y apache2` |
| 0:03 | Install MySQL | `sudo apt install -y mysql-server` |
| 0:05 | Install PHP | `sudo apt install -y php libapache2-mod-php php-mysql` |
| 0:08 | Secure MySQL | Set root password |
| 0:10 | Upload files | `scp -r * ubuntu@IP:/home/ubuntu/` |
| 0:15 | Move files | `sudo mv /home/ubuntu/* /var/www/html/` |
| 0:16 | Set permissions | `sudo chown -R www-data:www-data /var/www/html/` |
| 0:17 | Create database | `mysql -e "CREATE DATABASE onlinesale;"` |
| 0:18 | Import SQL | `mysql onlinesale < onlinesale.sql` |
| 0:20 | Import orders | `mysql onlinesale < create_orders_tables.sql` |
| 0:22 | Import admin | `mysql onlinesale < admin_setup.sql` |
| 0:23 | Update config | Edit `includes/common.php` |
| 0:25 | Update admin config | Edit `admin/includes/admin_common.php` |
| 0:27 | Open firewall | `sudo ufw allow 80/tcp` |
| 0:28 | Restart Apache | `sudo systemctl restart apache2` |
| 0:30 | **LIVE!** | Open `http://YOUR_EC2_IP` |

---

## 🌐 How to See Your Website Live

### Method 1: Using Public IP (Immediate)

**Get Your Public IP:**
1. AWS Console → EC2 → Instances
2. Select your instance
3. Copy "Public IPv4 address"
4. Example: `13.126.45.67`

**Open in Browser:**
```
http://13.126.45.67
```

**✅ Your website is LIVE!**

**Share with others:**
```
Send them: http://YOUR_EC2_IP
They can access from anywhere in the world!
```

### Method 2: Using Domain Name (30 min setup)

**If you have a domain (e.g., freshmart.com):**

1. **Get Elastic IP:**
   - AWS Console → EC2 → Elastic IPs
   - Allocate new address
   - Associate with your instance
   - Note the IP: `xx.xx.xx.xx`

2. **Configure DNS:**
   - Go to your domain registrar (GoDaddy, Namecheap, etc.)
   - Add A record:
     - Type: A
     - Name: @
     - Value: YOUR_ELASTIC_IP
     - TTL: 3600
   - Add A record for www:
     - Type: A
     - Name: www
     - Value: YOUR_ELASTIC_IP
     - TTL: 3600

3. **Wait for DNS propagation:** 5-60 minutes

4. **Test:**
   ```bash
   nslookup your-domain.com
   # Should return your Elastic IP
   ```

5. **Access:**
   ```
   http://your-domain.com
   ```

**✅ Your website is live with custom domain!**

---

## 📱 Share Your Live Website

### Get Your URL

**With Public IP:**
```
http://YOUR_EC2_PUBLIC_IP
```

**With Domain:**
```
http://your-domain.com
```

### Share With:

**Customers:**
```
🛒 Shop at Fresh Mart: http://YOUR_URL
📱 Mobile friendly
💳 Cash on Delivery available
```

**Team:**
```
🔧 Admin Panel: http://YOUR_URL/admin/
👤 Username: admin
🔑 Password: admin123 (change this!)
```

**Testers:**
```
🧪 Test Site: http://YOUR_URL
📝 Register and place test orders
🐛 Report any issues
```

---

## 🔍 Verify Everything is Working

### Quick Test Script

**Run this on EC2:**

```bash
# Test homepage
curl -I http://localhost | head -n 1
# Should show: HTTP/1.1 200 OK

# Test database
mysql -u root -p onlinesale -e "SELECT COUNT(*) FROM products;"
# Should show: 16 (or your product count)

# Test PHP
php -r "echo 'PHP is working!';"
# Should show: PHP is working!

# Test Apache
sudo systemctl status apache2 | grep Active
# Should show: Active: active (running)

# Test MySQL
sudo systemctl status mysql | grep Active
# Should show: Active: active (running)
```

**All tests pass?** ✅ **Everything is working!**

---

## 📸 What You Should See

### Homepage (http://YOUR_EC2_IP)

```
┌─────────────────────────────────────────────────┐
│  🏪 Fresh Mart - Online Grocery Shopping        │
│  [Sign Up] [Login] [Cart]                       │
├─────────────────────────────────────────────────┤
│                                                  │
│  🍎 Fruits    🥕 Vegetables                     │
│  🥛 Dairy     🥤 Beverages                      │
│                                                  │
│  [Shop Now]                                      │
│                                                  │
└─────────────────────────────────────────────────┘
```

### Products Page

```
┌─────────────────────────────────────────────────┐
│  Fresh Apples          Bananas                  │
│  Rs 120/kg             Rs 50/dozen              │
│  🟢 In Stock (50)      🟢 In Stock (100)        │
│  [Add to Cart]         [Add to Cart]            │
├─────────────────────────────────────────────────┤
│  Orange                Grapes                   │
│  Rs 80/kg              Rs 150/kg                │
│  🟡 Only 3 left!       🟢 In Stock (30)         │
│  [Add to Cart]         [Add to Cart]            │
└─────────────────────────────────────────────────┘
```

### Admin Dashboard

```
┌─────────────────────────────────────────────────┐
│  Admin Dashboard                    [Logout]    │
├─────────────────────────────────────────────────┤
│  📦 Total Products: 16                          │
│  🛒 Total Orders: 0                             │
│  ⚠️  Low Stock: 2                               │
│  🔴 Out of Stock: 2                             │
├─────────────────────────────────────────────────┤
│  Recent Products                                │
│  - Fresh Apples (Stock: 50)                     │
│  - Bananas (Stock: 100)                         │
└─────────────────────────────────────────────────┘
```

---

## 🎊 Congratulations!

### Your Website is Live on AWS EC2!

**What You've Accomplished:**

✅ EC2 instance configured  
✅ LAMP stack installed  
✅ Project deployed  
✅ Database setup complete  
✅ Website accessible worldwide  
✅ Admin panel working  
✅ All features functional  

### Access Your Live Site:

**Customer Site:**
```
http://YOUR_EC2_PUBLIC_IP
```

**Admin Panel:**
```
http://YOUR_EC2_PUBLIC_IP/admin/
```

**Share with anyone!** They can access from anywhere in the world.

---

## 📋 Post-Live Checklist

### Within 1 Hour

- [ ] Change admin password
- [ ] Remove test files
- [ ] Test all features
- [ ] Place test order
- [ ] Verify invoice generation
- [ ] Check error logs: `sudo tail -f /var/log/apache2/error.log`

### Within 24 Hours

- [ ] Setup automated backups (see Step 3 in Essential Security)
- [ ] Configure monitoring
- [ ] Add real products
- [ ] Update company information
- [ ] Test from different devices
- [ ] Share URL with team

### Within 1 Week

- [ ] Add domain name (optional)
- [ ] Install SSL certificate
- [ ] Setup email notifications
- [ ] Configure payment gateway (if not COD only)
- [ ] Train staff on admin panel
- [ ] Create backup/restore procedures
- [ ] Document custom configurations

---

## 🚀 Performance Tips

### Make Your Site Faster

**1. Enable Compression:**
```bash
sudo a2enmod deflate
sudo systemctl restart apache2
```

**2. Enable Caching:**
```bash
sudo a2enmod expires
sudo a2enmod headers
sudo systemctl restart apache2
```

**3. Optimize Images:**
```bash
# Install optimization tools
sudo apt install -y jpegoptim optipng

# Optimize images
cd /var/www/html/images
sudo jpegoptim --strip-all *.jpg
sudo optipng -o2 *.png
```

**4. Enable OPcache:**
```bash
sudo nano /etc/php/8.1/apache2/php.ini

# Find and enable:
opcache.enable=1
opcache.memory_consumption=128

# Restart
sudo systemctl restart apache2
```

---

## 📊 Monitor Your Live Site

### Check Server Status

```bash
# CPU and Memory
htop
# Install if needed: sudo apt install htop

# Disk space
df -h

# Active connections
sudo netstat -an | grep :80 | wc -l

# Apache status
sudo systemctl status apache2

# MySQL status
sudo systemctl status mysql
```

### Check Website Performance

**From Your Browser:**
1. Open Developer Tools (F12)
2. Network tab
3. Reload page
4. Check load time (should be < 2 seconds)

**From Command Line:**
```bash
# Test response time
curl -o /dev/null -s -w "Time: %{time_total}s\n" http://YOUR_EC2_IP

# Test from different location
curl -o /dev/null -s -w "Time: %{time_total}s\n" http://YOUR_EC2_IP
```

### Monitor Logs in Real-Time

```bash
# Watch access log (see visitors)
sudo tail -f /var/log/apache2/access.log

# Watch error log (see errors)
sudo tail -f /var/log/apache2/error.log

# Watch MySQL log
sudo tail -f /var/log/mysql/error.log
```

---

## 🎯 Quick Commands Reference

### Restart Services
```bash
sudo systemctl restart apache2
sudo systemctl restart mysql
```

### View Logs
```bash
sudo tail -100 /var/log/apache2/error.log
```

### Check Disk Space
```bash
df -h
```

### Check Memory
```bash
free -h
```

### Backup Now
```bash
sudo /usr/local/bin/backup.sh
```

### Update System
```bash
sudo apt update && sudo apt upgrade -y
```

---

## 🌟 Your Website is LIVE!

### Share Your Success

**Customer URL:**
```
🌐 http://YOUR_EC2_IP
or
🌐 https://your-domain.com
```

**Admin Access:**
```
🔐 http://YOUR_EC2_IP/admin/
👤 admin / YourNewPassword
```

### Next Steps

1. **Customize:** Add your products, update branding
2. **Secure:** Complete all security tasks
3. **Monitor:** Watch logs and performance
4. **Promote:** Share with customers
5. **Maintain:** Daily checks and backups

---

## 📞 Support

**Technical Issues:**
- Check troubleshooting section above
- Review logs: `sudo tail -f /var/log/apache2/error.log`
- Contact: +91 9360509515

**AWS Issues:**
- AWS Support Console
- Check billing dashboard
- Review CloudWatch metrics

---

## 🎉 Success!

**Your Fresh Mart website is now:**

✅ Live on the internet  
✅ Accessible from anywhere  
✅ Running on AWS infrastructure  
✅ Secure and optimized  
✅ Ready for customers  

**Start selling groceries online! 🛒**

---

**For complete operations guide, see: `USER_MANUAL_SOP.md`**

**For detailed AWS setup, see: `AWS_EC2_DEPLOYMENT_GUIDE.md`**
