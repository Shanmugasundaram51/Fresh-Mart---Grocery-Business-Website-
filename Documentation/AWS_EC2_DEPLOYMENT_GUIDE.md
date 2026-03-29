# 🚀 Fresh Mart - AWS EC2 Deployment Guide

**Complete guide to hosting your e-commerce website on Amazon EC2**

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [AWS Account Setup](#aws-account-setup)
3. [EC2 Instance Creation](#ec2-instance-creation)
4. [Server Configuration](#server-configuration)
5. [LAMP Stack Installation](#lamp-stack-installation)
6. [Project Deployment](#project-deployment)
7. [Database Setup](#database-setup)
8. [Security Configuration](#security-configuration)
9. [Domain & SSL Setup](#domain--ssl-setup)
10. [Performance Optimization](#performance-optimization)
11. [Monitoring & Maintenance](#monitoring--maintenance)
12. [Backup Strategy](#backup-strategy)
13. [Troubleshooting](#troubleshooting)

---

## 1. Prerequisites

### What You Need

- [ ] AWS Account (with billing enabled)
- [ ] Credit/Debit card for AWS verification
- [ ] Domain name (optional, but recommended)
- [ ] SSH client (Terminal for Mac/Linux, PuTTY for Windows)
- [ ] Project files ready
- [ ] Basic Linux command knowledge

### Estimated Costs

**EC2 Instance (t2.micro - Free Tier Eligible):**
- First 12 months: FREE (750 hours/month)
- After free tier: ~$8-10/month

**Additional Costs:**
- Elastic IP: FREE (if attached to running instance)
- EBS Storage: 30GB FREE (first 12 months)
- Data Transfer: 15GB FREE/month
- Domain: ~$10-15/year (if purchasing)

**Total Estimated:** $0-10/month (first year with free tier)

---

## 2. AWS Account Setup

### Step 2.1: Create AWS Account

1. **Visit AWS:**
   - Go to: https://aws.amazon.com
   - Click "Create an AWS Account"

2. **Fill Registration:**
   - Email address
   - Password
   - AWS account name
   - Contact information
   - Payment information (credit/debit card)
   - Phone verification

3. **Choose Support Plan:**
   - Select "Basic Support - Free"

4. **Verify Account:**
   - Check email for verification
   - Complete phone verification
   - Wait for account activation (5-10 minutes)

### Step 2.2: Access AWS Console

1. **Login:**
   - Go to: https://console.aws.amazon.com
   - Enter email and password
   - Complete MFA if enabled

2. **Select Region:**
   - Top-right corner, select region closest to your users
   - Recommended for India: **ap-south-1 (Mumbai)**
   - Or: **ap-southeast-1 (Singapore)**

---

## 3. EC2 Instance Creation

### Step 3.1: Launch EC2 Instance

1. **Navigate to EC2:**
   - AWS Console → Services → EC2
   - Or search "EC2" in top search bar
   - Click "Launch Instance"

2. **Name Your Instance:**
   - Name: `fresh-mart-production`
   - Tags: Add tag `Environment: Production`

### Step 3.2: Choose AMI (Amazon Machine Image)

**Select:** Ubuntu Server 22.04 LTS (HVM), SSD Volume Type

**Why Ubuntu?**
- Free tier eligible
- Easy to configure
- Large community support
- Stable and secure

**Configuration:**
- Architecture: 64-bit (x86)
- Click "Select"

### Step 3.3: Choose Instance Type

**Select:** t2.micro

**Specifications:**
- vCPUs: 1
- Memory: 1 GiB
- Network Performance: Low to Moderate
- **Free tier eligible:** ✓

**For Production (if needed later):**
- t2.small: 2 GiB RAM (~$17/month)
- t2.medium: 4 GiB RAM (~$34/month)

### Step 3.4: Configure Key Pair

**Create New Key Pair:**

1. Click "Create new key pair"
2. Key pair name: `fresh-mart-key`
3. Key pair type: RSA
4. Private key format:
   - Mac/Linux: `.pem`
   - Windows (PuTTY): `.ppk`
5. Click "Create key pair"
6. **IMPORTANT:** Save the downloaded file securely!
   - Mac/Linux: Save to `~/.ssh/fresh-mart-key.pem`
   - Windows: Save to safe location

**Set Permissions (Mac/Linux):**
```bash
chmod 400 ~/.ssh/fresh-mart-key.pem
```

### Step 3.5: Network Settings

**Configure Security Group:**

1. **Create security group:**
   - Name: `fresh-mart-sg`
   - Description: `Security group for Fresh Mart website`

2. **Add Inbound Rules:**

| Type | Protocol | Port Range | Source | Description |
|------|----------|------------|--------|-------------|
| SSH | TCP | 22 | My IP | SSH access |
| HTTP | TCP | 80 | 0.0.0.0/0 | Web traffic |
| HTTPS | TCP | 443 | 0.0.0.0/0 | Secure web traffic |
| Custom TCP | TCP | 8080 | My IP | Alternate web (optional) |

**Important:** 
- Use "My IP" for SSH to restrict access
- Use "0.0.0.0/0" for HTTP/HTTPS to allow public access

### Step 3.6: Configure Storage

**Root Volume:**
- Size: 20 GB (free tier allows up to 30 GB)
- Volume type: General Purpose SSD (gp3)
- Delete on termination: ✓ (check)

**Add Additional Volume (Optional):**
- For database backups
- Size: 10 GB
- Mount point: `/backups`

### Step 3.7: Review and Launch

1. **Review Configuration:**
   - Instance type: t2.micro ✓
   - AMI: Ubuntu 22.04 LTS ✓
   - Security group: fresh-mart-sg ✓
   - Storage: 20 GB ✓

2. **Launch Instance:**
   - Click "Launch Instance"
   - Wait for instance to start (2-3 minutes)
   - Status should show "Running"

3. **Note Instance Details:**
   - Instance ID: `i-xxxxxxxxxxxxx`
   - Public IPv4 address: `xx.xx.xx.xx`
   - Public IPv4 DNS: `ec2-xx-xx-xx-xx.compute.amazonaws.com`

---

## 4. Server Configuration

### Step 4.1: Connect to EC2 Instance

**For Mac/Linux:**

```bash
# Connect via SSH
ssh -i ~/.ssh/fresh-mart-key.pem ubuntu@YOUR_EC2_PUBLIC_IP

# Example:
ssh -i ~/.ssh/fresh-mart-key.pem ubuntu@13.126.45.67
```

**For Windows (Using PuTTY):**

1. Open PuTTY
2. Host Name: `ubuntu@YOUR_EC2_PUBLIC_IP`
3. Port: 22
4. Connection → SSH → Auth → Browse for `.ppk` key file
5. Click "Open"

**First Connection:**
- Type "yes" when asked about fingerprint
- You should see Ubuntu welcome message

### Step 4.2: Update System

```bash
# Update package list
sudo apt update

# Upgrade installed packages
sudo apt upgrade -y

# Install essential tools
sudo apt install -y curl wget git unzip
```

**Time:** 5-10 minutes

### Step 4.3: Set Timezone

```bash
# Set to your timezone (example: Asia/Kolkata)
sudo timedatectl set-timezone Asia/Kolkata

# Verify
timedatectl
```

### Step 4.4: Create Swap Space (Recommended for t2.micro)

```bash
# Create 2GB swap file
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile

# Make permanent
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab

# Verify
free -h
```

**Why?** t2.micro has only 1GB RAM. Swap prevents out-of-memory errors.

---

## 5. LAMP Stack Installation

### Step 5.1: Install Apache Web Server

```bash
# Install Apache
sudo apt install -y apache2

# Start Apache
sudo systemctl start apache2

# Enable Apache to start on boot
sudo systemctl enable apache2

# Check status
sudo systemctl status apache2

# Verify installation
curl http://localhost
```

**Test:** Open browser and visit `http://YOUR_EC2_PUBLIC_IP`
- Should see "Apache2 Ubuntu Default Page"

### Step 5.2: Install MySQL Database

```bash
# Install MySQL Server
sudo apt install -y mysql-server

# Start MySQL
sudo systemctl start mysql

# Enable MySQL to start on boot
sudo systemctl enable mysql

# Secure MySQL installation
sudo mysql_secure_installation
```

**MySQL Secure Installation Prompts:**

1. **Validate Password Component?** → No (or Yes if you want strong passwords)
2. **Remove anonymous users?** → Yes
3. **Disallow root login remotely?** → Yes
4. **Remove test database?** → Yes
5. **Reload privilege tables?** → Yes

**Set Root Password:**

```bash
# Login to MySQL
sudo mysql

# Set root password
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'YourSecurePassword123!';
FLUSH PRIVILEGES;
EXIT;

# Test login
mysql -u root -p
# Enter password when prompted
```

### Step 5.3: Install PHP

```bash
# Install PHP and required extensions
sudo apt install -y php libapache2-mod-php php-mysql php-cli php-curl php-gd php-mbstring php-xml php-zip

# Verify PHP installation
php -v

# Should show PHP 8.1.x or higher
```

**Configure PHP:**

```bash
# Edit php.ini
sudo nano /etc/php/8.1/apache2/php.ini

# Update these settings:
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
```

**Save:** Ctrl+X, then Y, then Enter

**Restart Apache:**
```bash
sudo systemctl restart apache2
```

### Step 5.4: Test LAMP Stack

**Create test PHP file:**

```bash
# Create info.php
echo "<?php phpinfo(); ?>" | sudo tee /var/www/html/info.php
```

**Test:**
- Open browser: `http://YOUR_EC2_PUBLIC_IP/info.php`
- Should see PHP info page
- Verify MySQL section shows enabled

**Remove test file (security):**
```bash
sudo rm /var/www/html/info.php
```

---

## 6. Project Deployment

### Step 6.1: Prepare Web Directory

```bash
# Navigate to web root
cd /var/www/html

# Remove default Apache page
sudo rm index.html

# Create project directory
sudo mkdir -p fresh-mart
cd fresh-mart
```

### Step 6.2: Upload Project Files

**Option A: Using SCP (From Your Local Machine)**

```bash
# From your local machine (Mac/Linux)
cd /path/to/your/ECOMMERCE-WEBSITE/

# Upload entire project
scp -i ~/.ssh/fresh-mart-key.pem -r * ubuntu@YOUR_EC2_PUBLIC_IP:/home/ubuntu/

# Then on EC2, move to web directory
ssh -i ~/.ssh/fresh-mart-key.pem ubuntu@YOUR_EC2_PUBLIC_IP
sudo mv /home/ubuntu/* /var/www/html/fresh-mart/
```

**Option B: Using Git (Recommended)**

```bash
# On EC2 instance
cd /var/www/html/fresh-mart

# Clone your repository
sudo git clone https://github.com/yourusername/fresh-mart.git .

# Or if files are in a zip
wget https://your-server.com/fresh-mart.zip
sudo unzip fresh-mart.zip
```

**Option C: Using FileZilla (Windows/Mac GUI)**

1. Download FileZilla: https://filezilla-project.org
2. File → Site Manager → New Site
3. Protocol: SFTP
4. Host: YOUR_EC2_PUBLIC_IP
5. Logon Type: Key file
6. User: ubuntu
7. Key file: Browse to your .pem file
8. Connect
9. Upload all project files to `/home/ubuntu/`
10. SSH to EC2 and move files: `sudo mv /home/ubuntu/* /var/www/html/fresh-mart/`

### Step 6.3: Set Permissions

```bash
# Navigate to project directory
cd /var/www/html/fresh-mart

# Set ownership to Apache user
sudo chown -R www-data:www-data /var/www/html/fresh-mart

# Set directory permissions
sudo find /var/www/html/fresh-mart -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/html/fresh-mart -type f -exec chmod 644 {} \;

# Set images folder writable
sudo chmod 775 /var/www/html/fresh-mart/images
sudo chown -R www-data:www-data /var/www/html/fresh-mart/images
```

### Step 6.4: Configure Apache Virtual Host

**Create Virtual Host Configuration:**

```bash
# Create config file
sudo nano /etc/apache2/sites-available/fresh-mart.conf
```

**Add this configuration:**

```apache
<VirtualHost *:80>
    ServerAdmin admin@freshmart.com
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/html/fresh-mart
    
    <Directory /var/www/html/fresh-mart>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/fresh-mart-error.log
    CustomLog ${APACHE_LOG_DIR}/fresh-mart-access.log combined
</VirtualHost>
```

**Save:** Ctrl+X, then Y, then Enter

**Enable Site:**

```bash
# Enable the site
sudo a2ensite fresh-mart.conf

# Enable mod_rewrite (for clean URLs)
sudo a2enmod rewrite

# Disable default site
sudo a2dissite 000-default.conf

# Test configuration
sudo apache2ctl configtest

# Should show "Syntax OK"

# Restart Apache
sudo systemctl restart apache2
```

---

## 7. Database Setup

### Step 7.1: Create Database

```bash
# Login to MySQL
mysql -u root -p
# Enter the password you set earlier
```

**In MySQL prompt:**

```sql
-- Create database
CREATE DATABASE onlinesale CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create dedicated database user
CREATE USER 'freshmart_user'@'localhost' IDENTIFIED BY 'SecureDBPassword123!';

-- Grant privileges
GRANT ALL PRIVILEGES ON onlinesale.* TO 'freshmart_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Verify
SHOW DATABASES;

-- Exit
EXIT;
```

### Step 7.2: Import Database Schema

```bash
# Navigate to project directory
cd /var/www/html/fresh-mart

# Import main schema
mysql -u root -p onlinesale < onlinesale.sql

# Import orders tables
mysql -u root -p onlinesale < create_orders_tables.sql

# Import admin setup
mysql -u root -p onlinesale < admin_setup.sql

# Verify tables created
mysql -u root -p onlinesale -e "SHOW TABLES;"
```

**Expected Tables:**
- products
- users
- users_products
- orders
- order_items
- admins

### Step 7.3: Update Database Connection

**Edit Customer Site Connection:**

```bash
sudo nano /var/www/html/fresh-mart/includes/common.php
```

**Update line 5:**
```php
$con = mysqli_connect("localhost", "freshmart_user", "SecureDBPassword123!", "onlinesale");
```

**Edit Admin Panel Connection:**

```bash
sudo nano /var/www/html/fresh-mart/admin/includes/admin_common.php
```

**Update line 5:**
```php
$con = mysqli_connect("localhost", "freshmart_user", "SecureDBPassword123!", "onlinesale");
```

**Save both files:** Ctrl+X, Y, Enter

### Step 7.4: Test Database Connection

```bash
# Create test script
sudo nano /var/www/html/fresh-mart/test_db.php
```

**Add this code:**
```php
<?php
$con = mysqli_connect("localhost", "freshmart_user", "SecureDBPassword123!", "onlinesale");
if ($con) {
    echo "Database connection successful!";
    $result = mysqli_query($con, "SELECT COUNT(*) as count FROM products");
    $row = mysqli_fetch_assoc($result);
    echo "<br>Products in database: " . $row['count'];
} else {
    echo "Connection failed: " . mysqli_connect_error();
}
?>
```

**Test:**
- Visit: `http://YOUR_EC2_PUBLIC_IP/test_db.php`
- Should show "Database connection successful!"

**Remove test file:**
```bash
sudo rm /var/www/html/fresh-mart/test_db.php
```

---

## 8. Security Configuration

### Step 8.1: Configure Firewall (UFW)

```bash
# Enable UFW
sudo ufw enable

# Allow SSH (IMPORTANT - do this first!)
sudo ufw allow 22/tcp

# Allow HTTP
sudo ufw allow 80/tcp

# Allow HTTPS
sudo ufw allow 443/tcp

# Check status
sudo ufw status

# Should show:
# 22/tcp    ALLOW    Anywhere
# 80/tcp    ALLOW    Anywhere
# 443/tcp   ALLOW    Anywhere
```

### Step 8.2: Secure MySQL

**Restrict MySQL to localhost only:**

```bash
# Edit MySQL config
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf

# Find and ensure this line exists:
bind-address = 127.0.0.1

# Save and restart MySQL
sudo systemctl restart mysql
```

### Step 8.3: Change Default Admin Password

```bash
# Login to MySQL
mysql -u root -p onlinesale

# Change admin password
UPDATE admins SET password = MD5('YourSecureAdminPassword123!') WHERE username = 'admin';

# Exit
EXIT;
```

### Step 8.4: Disable Directory Listing

```bash
# Edit Apache config
sudo nano /etc/apache2/apache2.conf

# Find the Directory section for /var/www/ and change:
# FROM: Options Indexes FollowSymLinks
# TO:   Options -Indexes +FollowSymLinks

# Save and restart
sudo systemctl restart apache2
```

### Step 8.5: Remove Test Files

```bash
cd /var/www/html/fresh-mart

# Remove test files (keep for staging, remove for production)
sudo rm test_concurrency.php
sudo rm invoice_demo.php
sudo rm concurrency_demo.html

# Or move to restricted location
sudo mkdir /var/www/html/fresh-mart/tests
sudo mv test_*.php /var/www/html/fresh-mart/tests/
```

### Step 8.6: Setup Fail2Ban (Brute Force Protection)

```bash
# Install Fail2Ban
sudo apt install -y fail2ban

# Create local config
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local

# Edit config
sudo nano /etc/fail2ban/jail.local

# Find [sshd] section and ensure:
enabled = true
maxretry = 3
bantime = 3600

# Start Fail2Ban
sudo systemctl start fail2ban
sudo systemctl enable fail2ban

# Check status
sudo fail2ban-client status
```

---

## 9. Domain & SSL Setup

### Step 9.1: Allocate Elastic IP (Optional but Recommended)

**Why?** EC2 public IP changes on restart. Elastic IP is permanent.

1. **AWS Console → EC2 → Elastic IPs**
2. Click "Allocate Elastic IP address"
3. Click "Allocate"
4. Select the new IP → Actions → Associate Elastic IP address
5. Select your instance → Associate
6. Note your new Elastic IP

### Step 9.2: Configure Domain (If You Have One)

**At Your Domain Registrar (GoDaddy, Namecheap, etc.):**

1. **Add A Record:**
   - Type: A
   - Name: @ (for root domain)
   - Value: YOUR_ELASTIC_IP
   - TTL: 3600

2. **Add A Record for www:**
   - Type: A
   - Name: www
   - Value: YOUR_ELASTIC_IP
   - TTL: 3600

**Wait:** DNS propagation takes 5-60 minutes

**Verify:**
```bash
# Check DNS propagation
nslookup your-domain.com
```

### Step 9.3: Update Virtual Host with Domain

```bash
# Edit virtual host
sudo nano /etc/apache2/sites-available/fresh-mart.conf

# Update ServerName and ServerAlias
ServerName your-domain.com
ServerAlias www.your-domain.com

# Save and restart
sudo systemctl restart apache2
```

### Step 9.4: Install SSL Certificate (Let's Encrypt - FREE)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-apache

# Obtain and install certificate
sudo certbot --apache -d your-domain.com -d www.your-domain.com

# Follow prompts:
# - Enter email address
# - Agree to terms (A)
# - Share email? (Y or N)
# - Redirect HTTP to HTTPS? → 2 (Redirect)

# Certificate will auto-renew
```

**Test Auto-Renewal:**
```bash
sudo certbot renew --dry-run
```

**Verify HTTPS:**
- Visit: `https://your-domain.com`
- Should show secure padlock icon
- Certificate valid

---

## 10. Performance Optimization

### Step 10.1: Enable Apache Modules

```bash
# Enable compression
sudo a2enmod deflate

# Enable caching
sudo a2enmod expires
sudo a2enmod headers

# Enable HTTP/2 (requires SSL)
sudo a2enmod http2

# Restart Apache
sudo systemctl restart apache2
```

### Step 10.2: Configure Apache Performance

```bash
# Edit Apache config
sudo nano /etc/apache2/apache2.conf

# Add at the end:
```

```apache
# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Enable browser caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>

# Security headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
```

**Save and restart:**
```bash
sudo systemctl restart apache2
```

### Step 10.3: Optimize MySQL

```bash
# Edit MySQL config
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf

# Add under [mysqld] section:
```

```ini
# Performance tuning for t2.micro (1GB RAM)
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
max_connections = 50
query_cache_size = 16M
query_cache_type = 1
table_open_cache = 400
```

**Save and restart:**
```bash
sudo systemctl restart mysql
```

### Step 10.4: Enable OPcache (PHP)

```bash
# Edit PHP config
sudo nano /etc/php/8.1/apache2/php.ini

# Find opcache section and enable:
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60

# Restart Apache
sudo systemctl restart apache2
```

---

## 11. Monitoring & Maintenance

### Step 11.1: Setup CloudWatch Monitoring

**Enable Detailed Monitoring:**

1. AWS Console → EC2 → Instances
2. Select your instance
3. Actions → Monitor and troubleshoot → Manage detailed monitoring
4. Enable → Save

**Monitor Metrics:**
- CPU Utilization
- Network In/Out
- Disk Read/Write
- Status Checks

### Step 11.2: Setup Log Monitoring

```bash
# Install log monitoring tool
sudo apt install -y logwatch

# Configure daily email reports (optional)
sudo nano /etc/cron.daily/00logwatch

# Add:
/usr/sbin/logwatch --output mail --mailto your@email.com --detail high
```

**View Logs Manually:**

```bash
# Apache access log
sudo tail -f /var/log/apache2/fresh-mart-access.log

# Apache error log
sudo tail -f /var/log/apache2/fresh-mart-error.log

# MySQL error log
sudo tail -f /var/log/mysql/error.log

# System log
sudo tail -f /var/log/syslog
```

### Step 11.3: Setup Automated Backups

**Create Backup Script:**

```bash
# Create backup directory
sudo mkdir -p /backups/database
sudo mkdir -p /backups/files

# Create backup script
sudo nano /usr/local/bin/backup-fresh-mart.sh
```

**Add this script:**

```bash
#!/bin/bash

# Configuration
DB_NAME="onlinesale"
DB_USER="root"
DB_PASS="YourSecurePassword123!"
BACKUP_DIR="/backups"
DATE=$(date +%Y%m%d_%H%M%S)
S3_BUCKET="s3://your-backup-bucket"  # Optional

# Database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/database/onlinesale_$DATE.sql.gz

# Files backup (images)
tar -czf $BACKUP_DIR/files/images_$DATE.tar.gz /var/www/html/fresh-mart/images

# Delete backups older than 7 days
find $BACKUP_DIR/database -name "*.sql.gz" -mtime +7 -delete
find $BACKUP_DIR/files -name "*.tar.gz" -mtime +7 -delete

# Optional: Upload to S3
# aws s3 cp $BACKUP_DIR/database/onlinesale_$DATE.sql.gz $S3_BUCKET/database/
# aws s3 cp $BACKUP_DIR/files/images_$DATE.tar.gz $S3_BUCKET/files/

echo "Backup completed: $DATE"
```

**Make executable:**
```bash
sudo chmod +x /usr/local/bin/backup-fresh-mart.sh
```

**Schedule Daily Backup:**

```bash
# Edit crontab
sudo crontab -e

# Add this line (runs daily at 2 AM)
0 2 * * * /usr/local/bin/backup-fresh-mart.sh >> /var/log/backup.log 2>&1
```

**Test Backup:**
```bash
sudo /usr/local/bin/backup-fresh-mart.sh
ls -lh /backups/database/
ls -lh /backups/files/
```

### Step 11.4: Setup Health Check Script

```bash
# Create health check script
sudo nano /usr/local/bin/health-check.sh
```

**Add this script:**

```bash
#!/bin/bash

# Check Apache
if ! systemctl is-active --quiet apache2; then
    echo "Apache is down! Restarting..."
    sudo systemctl restart apache2
fi

# Check MySQL
if ! systemctl is-active --quiet mysql; then
    echo "MySQL is down! Restarting..."
    sudo systemctl restart mysql
fi

# Check disk space
DISK_USAGE=$(df -h / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    echo "Warning: Disk usage is ${DISK_USAGE}%"
fi

# Check memory
MEM_USAGE=$(free | awk 'NR==2 {printf "%.0f", $3/$2*100}')
if [ $MEM_USAGE -gt 90 ]; then
    echo "Warning: Memory usage is ${MEM_USAGE}%"
fi

echo "Health check completed: $(date)"
```

**Make executable and schedule:**
```bash
sudo chmod +x /usr/local/bin/health-check.sh

# Run every 5 minutes
sudo crontab -e

# Add:
*/5 * * * * /usr/local/bin/health-check.sh >> /var/log/health-check.log 2>&1
```

---

## 12. Backup Strategy

### Step 12.1: Local Backups

**Daily Automated Backups:**
- Database: Automated via cron (see Step 11.3)
- Files: Automated via cron
- Retention: 7 days

**Manual Backup:**
```bash
# Database
mysqldump -u root -p onlinesale > /backups/database/manual_$(date +%Y%m%d).sql

# Files
tar -czf /backups/files/manual_$(date +%Y%m%d).tar.gz /var/www/html/fresh-mart
```

### Step 12.2: AWS S3 Backups (Recommended)

**Setup S3 Bucket:**

1. **AWS Console → S3 → Create Bucket**
   - Bucket name: `fresh-mart-backups-yourname`
   - Region: Same as EC2
   - Block public access: ✓ (enabled)
   - Create bucket

2. **Install AWS CLI on EC2:**
   ```bash
   sudo apt install -y awscli
   
   # Configure AWS CLI
   aws configure
   # Enter:
   # - AWS Access Key ID
   # - AWS Secret Access Key
   # - Default region: ap-south-1
   # - Default output: json
   ```

3. **Update Backup Script:**
   ```bash
   sudo nano /usr/local/bin/backup-fresh-mart.sh
   
   # Uncomment S3 upload lines:
   aws s3 cp $BACKUP_DIR/database/onlinesale_$DATE.sql.gz s3://fresh-mart-backups-yourname/database/
   aws s3 cp $BACKUP_DIR/files/images_$DATE.tar.gz s3://fresh-mart-backups-yourname/files/
   ```

### Step 12.3: Snapshot EC2 Instance

**Create AMI (Amazon Machine Image):**

1. AWS Console → EC2 → Instances
2. Select your instance
3. Actions → Image and templates → Create image
4. Image name: `fresh-mart-backup-YYYYMMDD`
5. Description: "Fresh Mart production backup"
6. Click "Create image"

**Schedule:** Create snapshot weekly or before major changes

### Step 12.4: Restore Procedures

**Restore Database:**
```bash
# From local backup
gunzip < /backups/database/onlinesale_20260321.sql.gz | mysql -u root -p onlinesale

# From S3
aws s3 cp s3://fresh-mart-backups-yourname/database/onlinesale_20260321.sql.gz .
gunzip < onlinesale_20260321.sql.gz | mysql -u root -p onlinesale
```

**Restore Files:**
```bash
# From local backup
sudo tar -xzf /backups/files/images_20260321.tar.gz -C /

# From S3
aws s3 cp s3://fresh-mart-backups-yourname/files/images_20260321.tar.gz .
sudo tar -xzf images_20260321.tar.gz -C /
```

---

## 13. Troubleshooting

### Issue: Cannot Connect to EC2

**Solutions:**

1. **Check Instance Status:**
   - AWS Console → EC2 → Instances
   - Status should be "Running"
   - Status checks should be 2/2

2. **Check Security Group:**
   - Inbound rules allow SSH (port 22) from your IP
   - Your IP may have changed (update rule)

3. **Check Key Permissions:**
   ```bash
   chmod 400 ~/.ssh/fresh-mart-key.pem
   ```

4. **Verify Connection:**
   ```bash
   ssh -vvv -i ~/.ssh/fresh-mart-key.pem ubuntu@YOUR_EC2_IP
   ```

### Issue: Website Not Loading

**Solutions:**

1. **Check Apache Status:**
   ```bash
   sudo systemctl status apache2
   
   # If not running:
   sudo systemctl start apache2
   ```

2. **Check Security Group:**
   - Inbound rules allow HTTP (port 80) from 0.0.0.0/0
   - Inbound rules allow HTTPS (port 443) from 0.0.0.0/0

3. **Check Apache Logs:**
   ```bash
   sudo tail -f /var/log/apache2/fresh-mart-error.log
   ```

4. **Check Firewall:**
   ```bash
   sudo ufw status
   # Should show 80/tcp and 443/tcp allowed
   ```

### Issue: Database Connection Failed

**Solutions:**

1. **Check MySQL Status:**
   ```bash
   sudo systemctl status mysql
   
   # If not running:
   sudo systemctl start mysql
   ```

2. **Verify Credentials:**
   ```bash
   mysql -u freshmart_user -p onlinesale
   # Test with password
   ```

3. **Check Connection in Code:**
   ```bash
   sudo nano /var/www/html/fresh-mart/includes/common.php
   # Verify: localhost, username, password, database name
   ```

4. **Check MySQL Logs:**
   ```bash
   sudo tail -f /var/log/mysql/error.log
   ```

### Issue: Permission Denied Errors

**Solutions:**

```bash
# Reset permissions
cd /var/www/html/fresh-mart

# Set ownership
sudo chown -R www-data:www-data .

# Set directory permissions
sudo find . -type d -exec chmod 755 {} \;

# Set file permissions
sudo find . -type f -exec chmod 644 {} \;

# Images folder writable
sudo chmod 775 images/
```

### Issue: Image Upload Fails

**Solutions:**

1. **Check Folder Permissions:**
   ```bash
   ls -la /var/www/html/fresh-mart/images/
   # Should show: drwxrwxr-x www-data www-data
   ```

2. **Check PHP Upload Settings:**
   ```bash
   php -i | grep upload_max_filesize
   php -i | grep post_max_size
   ```

3. **Check Disk Space:**
   ```bash
   df -h
   # Ensure / has free space
   ```

### Issue: High Memory Usage

**Solutions:**

1. **Check Memory:**
   ```bash
   free -h
   htop  # Install: sudo apt install htop
   ```

2. **Optimize MySQL:**
   ```bash
   # Reduce buffer pool size
   sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
   # Set: innodb_buffer_pool_size = 128M
   sudo systemctl restart mysql
   ```

3. **Clear Cache:**
   ```bash
   # Clear system cache
   sudo sync && sudo sysctl -w vm.drop_caches=3
   ```

### Issue: Slow Performance

**Solutions:**

1. **Enable OPcache:** (see Step 10.4)

2. **Optimize Images:**
   ```bash
   # Install image optimization tools
   sudo apt install -y jpegoptim optipng
   
   # Optimize existing images
   find /var/www/html/fresh-mart/images -name "*.jpg" -exec jpegoptim --strip-all {} \;
   find /var/www/html/fresh-mart/images -name "*.png" -exec optipng -o2 {} \;
   ```

3. **Check Instance Type:**
   - t2.micro may be too small for high traffic
   - Consider upgrading to t2.small or t2.medium

---

## 14. Post-Deployment Checklist

### ✅ Immediate Tasks (Day 1)

- [ ] EC2 instance running
- [ ] LAMP stack installed
- [ ] Project files deployed
- [ ] Database imported
- [ ] Database connection configured
- [ ] Apache virtual host configured
- [ ] Website accessible via public IP
- [ ] Admin panel accessible
- [ ] Test order placed successfully
- [ ] Concurrency test passes
- [ ] Security group configured
- [ ] Firewall (UFW) enabled
- [ ] Admin password changed
- [ ] Test files removed/secured

### ✅ Within 24 Hours

- [ ] Domain configured (if applicable)
- [ ] SSL certificate installed
- [ ] HTTPS working
- [ ] Elastic IP allocated
- [ ] Automated backups configured
- [ ] Health check script running
- [ ] Monitoring enabled
- [ ] Log rotation configured
- [ ] Fail2Ban installed

### ✅ Within 1 Week

- [ ] S3 backup configured
- [ ] AMI snapshot created
- [ ] Performance optimized
- [ ] Load testing completed
- [ ] Documentation reviewed
- [ ] Team trained
- [ ] Support procedures established
- [ ] Disaster recovery plan documented

---

## 15. Scaling & High Availability

### Step 15.1: Vertical Scaling (Upgrade Instance)

**When to Scale:**
- CPU > 80% consistently
- Memory > 90% consistently
- Response time > 3 seconds
- Concurrent users > 50

**How to Scale:**

1. **Stop Instance:**
   - AWS Console → EC2 → Instances
   - Select instance → Instance State → Stop

2. **Change Instance Type:**
   - Actions → Instance Settings → Change instance type
   - Select: t2.small (2GB RAM) or t2.medium (4GB RAM)
   - Click "Apply"

3. **Start Instance:**
   - Instance State → Start

**No data loss, minimal downtime (5 minutes)**

### Step 15.2: Horizontal Scaling (Multiple Instances)

**For High Traffic:**

1. **Create Load Balancer:**
   - AWS Console → EC2 → Load Balancers
   - Create Application Load Balancer
   - Configure health checks

2. **Create Auto Scaling Group:**
   - Create AMI from current instance
   - Configure launch template
   - Set min/max instances
   - Configure scaling policies

3. **Setup RDS for Database:**
   - AWS Console → RDS
   - Create MySQL database
   - Migrate data from EC2 to RDS
   - Update connection strings

### Step 15.3: Use CloudFront CDN

**For Faster Image Delivery:**

1. AWS Console → CloudFront
2. Create distribution
3. Origin: Your EC2 public DNS
4. Update image URLs to use CloudFront domain

---

## 16. Cost Optimization

### Step 16.1: Monitor Costs

**Setup Billing Alerts:**

1. AWS Console → Billing → Budgets
2. Create budget
3. Set alert threshold (e.g., $10/month)
4. Add email notification

### Step 16.2: Reduce Costs

**Strategies:**

1. **Use Reserved Instances:**
   - 1-year commitment: 40% savings
   - 3-year commitment: 60% savings

2. **Stop Instance When Not Needed:**
   - Development/testing: Stop overnight
   - Production: Keep running 24/7

3. **Optimize Storage:**
   - Delete old snapshots
   - Use S3 Glacier for old backups
   - Clean up unused EBS volumes

4. **Use Elastic IP Wisely:**
   - FREE when attached to running instance
   - $0.005/hour when not attached
   - Release if not using

---

## 17. Security Best Practices

### Step 17.1: Regular Updates

```bash
# Update system weekly
sudo apt update && sudo apt upgrade -y

# Update PHP packages
sudo apt update && sudo apt upgrade php* -y

# Reboot if kernel updated
sudo reboot
```

### Step 17.2: SSH Hardening

**Disable Password Authentication:**

```bash
# Edit SSH config
sudo nano /etc/ssh/sshd_config

# Set these values:
PasswordAuthentication no
PermitRootLogin no
PubkeyAuthentication yes

# Restart SSH
sudo systemctl restart sshd
```

**Change SSH Port (Optional):**

```bash
# Edit SSH config
sudo nano /etc/ssh/sshd_config

# Change port from 22 to custom (e.g., 2222)
Port 2222

# Update security group to allow new port
# Update UFW:
sudo ufw allow 2222/tcp
sudo ufw delete allow 22/tcp

# Restart SSH
sudo systemctl restart sshd
```

### Step 17.3: Database Security

```bash
# Regular security audit
mysql -u root -p

# Check users
SELECT user, host FROM mysql.user;

# Remove unnecessary users
DROP USER 'username'@'host';

# Change passwords regularly
ALTER USER 'freshmart_user'@'localhost' IDENTIFIED BY 'NewSecurePassword123!';
FLUSH PRIVILEGES;
```

### Step 17.4: Setup Intrusion Detection

```bash
# Install AIDE (Advanced Intrusion Detection Environment)
sudo apt install -y aide

# Initialize database
sudo aideinit

# Move database
sudo mv /var/lib/aide/aide.db.new /var/lib/aide/aide.db

# Run check
sudo aide --check

# Schedule daily checks
sudo crontab -e
# Add:
0 3 * * * /usr/bin/aide --check | mail -s "AIDE Report" your@email.com
```

---

## 18. Production Deployment Workflow

### Step 18.1: Pre-Deployment Checklist

- [ ] Code tested locally
- [ ] Database changes documented
- [ ] Backup created
- [ ] Maintenance window scheduled
- [ ] Team notified
- [ ] Rollback plan ready

### Step 18.2: Deployment Steps

```bash
# 1. Create backup
sudo /usr/local/bin/backup-fresh-mart.sh

# 2. Enable maintenance mode (optional)
sudo nano /var/www/html/fresh-mart/.maintenance
# Add: "Site under maintenance. Back soon!"

# 3. Pull latest code
cd /var/www/html/fresh-mart
sudo git pull origin main

# 4. Update database (if needed)
mysql -u root -p onlinesale < migration.sql

# 5. Clear cache (if any)
# Your cache clearing commands here

# 6. Test
curl http://localhost

# 7. Disable maintenance mode
sudo rm /var/www/html/fresh-mart/.maintenance

# 8. Monitor logs
sudo tail -f /var/log/apache2/fresh-mart-error.log
```

### Step 18.3: Post-Deployment Verification

```bash
# Check website
curl -I http://YOUR_DOMAIN

# Check database
mysql -u root -p onlinesale -e "SELECT COUNT(*) FROM products;"

# Check disk space
df -h

# Check memory
free -h

# Check services
sudo systemctl status apache2
sudo systemctl status mysql

# Monitor logs for errors
sudo tail -100 /var/log/apache2/fresh-mart-error.log
```

---

## 19. Disaster Recovery

### Step 19.1: Complete System Failure

**Recovery Steps:**

1. **Launch New EC2 Instance:**
   - Use same configuration
   - Use latest AMI snapshot

2. **Restore from Backup:**
   ```bash
   # Restore database
   aws s3 cp s3://fresh-mart-backups/database/latest.sql.gz .
   gunzip < latest.sql.gz | mysql -u root -p onlinesale
   
   # Restore files
   aws s3 cp s3://fresh-mart-backups/files/latest.tar.gz .
   sudo tar -xzf latest.tar.gz -C /
   ```

3. **Update DNS:**
   - Point domain to new Elastic IP
   - Wait for propagation

4. **Verify System:**
   - Test all features
   - Check database integrity
   - Monitor for errors

### Step 19.2: Database Corruption

**Recovery Steps:**

```bash
# 1. Stop Apache (prevent new writes)
sudo systemctl stop apache2

# 2. Backup current state
mysqldump -u root -p onlinesale > /tmp/corrupted_backup.sql

# 3. Restore from last good backup
gunzip < /backups/database/onlinesale_20260320.sql.gz | mysql -u root -p onlinesale

# 4. Verify data
mysql -u root -p onlinesale -e "SELECT COUNT(*) FROM products;"

# 5. Start Apache
sudo systemctl start apache2
```

### Step 19.3: Security Breach

**Immediate Actions:**

1. **Isolate Instance:**
   ```bash
   # Update security group to block all traffic except your IP
   ```

2. **Investigate:**
   ```bash
   # Check recent logins
   last -a
   
   # Check running processes
   ps aux
   
   # Check network connections
   sudo netstat -tulpn
   
   # Check file modifications
   find /var/www -mtime -1 -type f
   ```

3. **Clean and Restore:**
   - Terminate compromised instance
   - Launch new instance from clean AMI
   - Restore data from backup
   - Change all passwords
   - Review and fix security gaps

---

## 20. Monitoring & Alerts

### Step 20.1: Setup CloudWatch Alarms

**CPU Alarm:**

1. AWS Console → CloudWatch → Alarms
2. Create Alarm
3. Select Metric → EC2 → Per-Instance Metrics → CPUUtilization
4. Conditions: Greater than 80%
5. Period: 5 minutes
6. Action: Send notification to email
7. Create alarm

**Disk Space Alarm:**

```bash
# Install CloudWatch agent
wget https://s3.amazonaws.com/amazoncloudwatch-agent/ubuntu/amd64/latest/amazon-cloudwatch-agent.deb
sudo dpkg -i amazon-cloudwatch-agent.deb

# Configure agent
sudo /opt/aws/amazon-cloudwatch-agent/bin/amazon-cloudwatch-agent-config-wizard

# Start agent
sudo /opt/aws/amazon-cloudwatch-agent/bin/amazon-cloudwatch-agent-ctl \
    -a fetch-config \
    -m ec2 \
    -s \
    -c file:/opt/aws/amazon-cloudwatch-agent/bin/config.json
```

### Step 20.2: Application Monitoring

**Install Monitoring Tools:**

```bash
# Install htop (process monitor)
sudo apt install -y htop

# Install iotop (disk I/O monitor)
sudo apt install -y iotop

# Install nethogs (network monitor)
sudo apt install -y nethogs
```

**Usage:**
```bash
htop          # View processes and memory
sudo iotop    # View disk I/O
sudo nethogs  # View network usage per process
```

### Step 20.3: Setup Uptime Monitoring

**Use External Service (Free Options):**

1. **UptimeRobot** (https://uptimerobot.com)
   - Free: 50 monitors
   - Check interval: 5 minutes
   - Email/SMS alerts

2. **Pingdom** (https://www.pingdom.com)
   - Free trial available
   - Detailed performance reports

**Configure:**
- Monitor URL: `http://your-domain.com`
- Check interval: 5 minutes
- Alert email: your@email.com

---

## 21. Environment-Specific Configuration

### Development Environment

```bash
# Enable error reporting
sudo nano /var/www/html/fresh-mart/includes/common.php

# Add at top:
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Staging Environment

```bash
# Use separate database
CREATE DATABASE onlinesale_staging;

# Update connection
# Use staging database name
```

### Production Environment

```bash
# Disable error display
sudo nano /etc/php/8.1/apache2/php.ini

# Set:
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
```

---

## 22. Quick Command Reference

### Server Management

```bash
# Restart Apache
sudo systemctl restart apache2

# Restart MySQL
sudo systemctl restart mysql

# Check disk space
df -h

# Check memory
free -h

# Check CPU
top

# View running processes
ps aux

# Check open ports
sudo netstat -tulpn
```

### Database Operations

```bash
# Backup database
mysqldump -u root -p onlinesale > backup.sql

# Restore database
mysql -u root -p onlinesale < backup.sql

# Login to MySQL
mysql -u root -p

# Check database size
mysql -u root -p -e "SELECT table_schema AS 'Database', ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)' FROM information_schema.tables WHERE table_schema = 'onlinesale';"
```

### Log Management

```bash
# View Apache access log
sudo tail -f /var/log/apache2/fresh-mart-access.log

# View Apache error log
sudo tail -f /var/log/apache2/fresh-mart-error.log

# View MySQL error log
sudo tail -f /var/log/mysql/error.log

# View system log
sudo tail -f /var/log/syslog

# Clear old logs (if disk full)
sudo journalctl --vacuum-time=7d
```

### File Management

```bash
# Upload file from local to EC2
scp -i ~/.ssh/fresh-mart-key.pem file.php ubuntu@YOUR_EC2_IP:/home/ubuntu/

# Download file from EC2 to local
scp -i ~/.ssh/fresh-mart-key.pem ubuntu@YOUR_EC2_IP:/path/to/file.php ./

# Sync entire directory
rsync -avz -e "ssh -i ~/.ssh/fresh-mart-key.pem" /local/path/ ubuntu@YOUR_EC2_IP:/remote/path/
```

---

## 23. Testing on EC2

### Step 23.1: Functional Testing

**Test Customer Flow:**
```bash
# 1. Register new user
curl -X POST http://YOUR_DOMAIN/signup_script.php \
  -d "first_name=Test&last_name=User&email=test@test.com&password=test123&phone=1234567890"

# 2. Test product page
curl http://YOUR_DOMAIN/products.php

# 3. Test cart
curl http://YOUR_DOMAIN/cart.php
```

### Step 23.2: Concurrency Testing

**Run Automated Test:**
```
http://YOUR_DOMAIN/test_concurrency.php
```

**Expected Result:**
- User A: Success ✓
- User B: Out of Stock ✗
- Stock: 0 (not -1) ✓

### Step 23.3: Load Testing

**Install Apache Bench:**
```bash
sudo apt install -y apache2-utils
```

**Run Load Test:**
```bash
# Test homepage (100 requests, 10 concurrent)
ab -n 100 -c 10 http://YOUR_DOMAIN/

# Test products page
ab -n 100 -c 10 http://YOUR_DOMAIN/products.php

# Analyze results:
# - Requests per second
# - Time per request
# - Failed requests (should be 0)
```

**Performance Benchmarks:**
- Good: > 50 requests/second
- Acceptable: > 20 requests/second
- Slow: < 10 requests/second (optimize!)

---

## 24. Email Configuration

### Step 24.1: Install Mail Server (Optional)

**Option A: Use Amazon SES (Recommended)**

1. **AWS Console → SES (Simple Email Service)**
2. Verify email address or domain
3. Get SMTP credentials
4. Update `email_invoice.php` with SES SMTP settings

**Option B: Use Gmail SMTP**

```bash
# Install msmtp
sudo apt install -y msmtp msmtp-mta

# Configure
sudo nano /etc/msmtprc
```

**Add configuration:**
```
defaults
auth           on
tls            on
tls_trust_file /etc/ssl/certs/ca-certificates.crt
logfile        /var/log/msmtp.log

account        gmail
host           smtp.gmail.com
port           587
from           your-email@gmail.com
user           your-email@gmail.com
password       your-app-password

account default : gmail
```

**Update PHP mail configuration:**
```bash
sudo nano /etc/php/8.1/apache2/php.ini

# Find and update:
sendmail_path = /usr/bin/msmtp -t
```

### Step 24.2: Test Email

```bash
# Send test email
echo "Test email from Fresh Mart" | mail -s "Test" your@email.com

# Check logs
sudo tail -f /var/log/msmtp.log
```

---

## 25. Deployment Checklist

### 🔧 Technical Setup

- [ ] EC2 instance launched (t2.micro)
- [ ] Ubuntu 22.04 LTS installed
- [ ] Elastic IP allocated
- [ ] Security group configured
- [ ] Key pair downloaded and secured
- [ ] SSH access working
- [ ] System updated
- [ ] Swap space created
- [ ] Timezone set

### 🌐 LAMP Stack

- [ ] Apache installed and running
- [ ] MySQL installed and secured
- [ ] PHP 8.1+ installed
- [ ] PHP extensions installed
- [ ] Apache modules enabled
- [ ] Virtual host configured
- [ ] PHP settings optimized

### 📦 Application Deployment

- [ ] Project files uploaded
- [ ] Permissions set correctly
- [ ] Database created
- [ ] Database user created
- [ ] SQL files imported
- [ ] Database connection configured
- [ ] Test order successful

### 🔒 Security

- [ ] Firewall (UFW) enabled
- [ ] Security group configured
- [ ] SSH hardened
- [ ] MySQL secured
- [ ] Admin password changed
- [ ] Test files removed
- [ ] Directory listing disabled
- [ ] Fail2Ban installed
- [ ] SSL certificate installed (if domain)

### 📊 Monitoring

- [ ] CloudWatch enabled
- [ ] Automated backups configured
- [ ] Health check script running
- [ ] Log rotation configured
- [ ] Uptime monitoring setup
- [ ] Billing alerts configured

### 🚀 Production Ready

- [ ] Domain configured (if applicable)
- [ ] SSL working (HTTPS)
- [ ] Performance optimized
- [ ] Load tested
- [ ] Documentation reviewed
- [ ] Team trained
- [ ] Support procedures ready

---

## 26. Quick Deployment Script

**Automated Deployment (Use After Manual Setup Once):**

```bash
#!/bin/bash
# save as: deploy.sh

echo "🚀 Deploying Fresh Mart..."

# 1. Backup current version
echo "📦 Creating backup..."
sudo /usr/local/bin/backup-fresh-mart.sh

# 2. Pull latest code
echo "📥 Pulling latest code..."
cd /var/www/html/fresh-mart
sudo git pull origin main

# 3. Update database (if migration file exists)
if [ -f "migration.sql" ]; then
    echo "🗄️ Running database migration..."
    mysql -u root -p onlinesale < migration.sql
fi

# 4. Set permissions
echo "🔐 Setting permissions..."
sudo chown -R www-data:www-data .
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;
sudo chmod 775 images/

# 5. Clear OPcache
echo "🧹 Clearing cache..."
sudo systemctl restart apache2

# 6. Test
echo "✅ Testing..."
curl -I http://localhost

echo "🎉 Deployment complete!"
echo "🔍 Monitor logs: sudo tail -f /var/log/apache2/fresh-mart-error.log"
```

**Make executable:**
```bash
chmod +x deploy.sh
```

**Usage:**
```bash
./deploy.sh
```

---

## 27. Cost Calculator

### Monthly Cost Breakdown

**Free Tier (First 12 Months):**
```
EC2 t2.micro (750 hours/month)    : $0
EBS Storage (30 GB)                : $0
Data Transfer (15 GB out)          : $0
Elastic IP (attached)              : $0
-------------------------------------------
Total                              : $0/month
```

**After Free Tier:**
```
EC2 t2.micro (24/7)                : $8.50
EBS Storage (30 GB)                : $3.00
Data Transfer (50 GB out)          : $4.50
Elastic IP (attached)              : $0
S3 Backups (10 GB)                 : $0.23
-------------------------------------------
Total                              : ~$16/month
```

**With Upgrades:**
```
EC2 t2.small (2GB RAM)             : $17.00
EBS Storage (50 GB)                : $5.00
RDS MySQL (db.t3.micro)            : $15.00
CloudFront CDN                     : $5.00
Data Transfer (100 GB)             : $9.00
-------------------------------------------
Total                              : ~$51/month
```

---

## 28. Performance Benchmarks

### Expected Performance (t2.micro)

| Metric | Value |
|--------|-------|
| **Concurrent Users** | 20-30 |
| **Requests/Second** | 30-50 |
| **Page Load Time** | 1-2 seconds |
| **Database Queries/Sec** | 100-200 |
| **Memory Usage** | 60-80% |
| **CPU Usage** | 30-50% (normal) |

### When to Upgrade

**Upgrade to t2.small if:**
- Concurrent users > 30
- Memory usage > 90%
- Response time > 3 seconds
- CPU usage > 80% consistently

**Upgrade to t2.medium if:**
- Concurrent users > 100
- Database queries/sec > 500
- Multiple admin users
- High traffic expected

---

## 29. Maintenance Schedule

### Daily Tasks (Automated)

- [x] Automated backups (2 AM)
- [x] Health checks (every 5 minutes)
- [x] Log rotation
- [x] Security scans

### Weekly Tasks (Manual)

- [ ] Review error logs
- [ ] Check disk space
- [ ] Monitor performance metrics
- [ ] Review security group rules
- [ ] Check backup integrity
- [ ] Update system packages

### Monthly Tasks (Manual)

- [ ] Review AWS costs
- [ ] Analyze traffic patterns
- [ ] Optimize database
- [ ] Review security logs
- [ ] Test disaster recovery
- [ ] Update documentation
- [ ] Create AMI snapshot

---

## 30. Support & Resources

### AWS Documentation

- **EC2 User Guide:** https://docs.aws.amazon.com/ec2/
- **RDS User Guide:** https://docs.aws.amazon.com/rds/
- **S3 User Guide:** https://docs.aws.amazon.com/s3/
- **CloudWatch:** https://docs.aws.amazon.com/cloudwatch/

### Fresh Mart Documentation

- **Complete SOP:** `USER_MANUAL_SOP.md`
- **Quick Reference:** `ONE_PAGE_REFERENCE.md`
- **Setup Guide:** `PROJECT_SETUP_GUIDE.md`

### Technical Support

- **AWS Support:** https://console.aws.amazon.com/support/
- **Fresh Mart Support:** +91 9360509515

### Community Resources

- **AWS Forums:** https://forums.aws.amazon.com
- **Stack Overflow:** Tag your questions with `aws-ec2`
- **Ubuntu Forums:** https://ubuntuforums.org

---

## 31. FAQ

### Q: Can I use free tier forever?

**A:** Free tier is valid for 12 months from account creation. After that, you'll be charged standard rates (~$16/month for basic setup).

### Q: What if my instance stops unexpectedly?

**A:** 
1. Check AWS Console for status
2. Review CloudWatch logs
3. Check billing (payment issue?)
4. Restart instance from console

### Q: How do I upgrade instance size?

**A:**
1. Stop instance
2. Actions → Instance Settings → Change instance type
3. Select new type
4. Start instance

### Q: Can I move to a different region?

**A:**
1. Create AMI of current instance
2. Copy AMI to new region
3. Launch instance from AMI in new region
4. Update DNS to point to new IP

### Q: How do I reduce costs?

**A:**
1. Use Reserved Instances (40-60% savings)
2. Stop instance when not needed (dev/test)
3. Use S3 Glacier for old backups
4. Optimize images to reduce bandwidth
5. Use CloudFront CDN

### Q: What about database backups?

**A:**
- Automated daily backups (see Step 11.3)
- Stored locally and S3
- 7-day retention
- Test restore monthly

### Q: How do I handle high traffic?

**A:**
1. Upgrade instance type (vertical scaling)
2. Use Auto Scaling (horizontal scaling)
3. Move database to RDS
4. Use CloudFront CDN
5. Implement caching (Redis/Memcached)

---

## 32. Deployment Timeline

### Day 1: Initial Setup (4 hours)

- Hour 1: Create AWS account, launch EC2
- Hour 2: Install LAMP stack
- Hour 3: Deploy project files
- Hour 4: Configure database, test

### Day 2: Security & Optimization (3 hours)

- Hour 1: Configure security (firewall, SSL)
- Hour 2: Setup monitoring and backups
- Hour 3: Performance optimization

### Day 3: Testing & Go-Live (2 hours)

- Hour 1: Complete testing
- Hour 2: Final checks, go live

**Total:** 9 hours for complete production deployment

---

## 33. Emergency Contacts

### AWS Support

- **Phone:** 1-877-742-2121 (US)
- **Chat:** AWS Console → Support
- **Email:** Through support console

### Fresh Mart Technical

- **Phone:** +91 9360509515
- **Email:** support@freshmart.com (if configured)

### Critical Issues

**Instance Down:**
1. Check AWS Console status
2. Review CloudWatch metrics
3. Check billing status
4. Restart instance if needed

**Database Corrupted:**
1. Stop Apache immediately
2. Restore from latest backup
3. Verify data integrity
4. Resume operations

**Security Breach:**
1. Isolate instance (update security group)
2. Review logs for unauthorized access
3. Restore from clean backup
4. Change all passwords
5. Review security configuration

---

## 34. Success Criteria

### Deployment Successful When:

✅ Website accessible via public IP/domain  
✅ HTTPS working (if domain configured)  
✅ Customer can register and login  
✅ Products display correctly  
✅ Orders can be placed  
✅ Invoices generate successfully  
✅ Admin panel accessible  
✅ Stock management working  
✅ Concurrency test passes  
✅ Backups running automatically  
✅ Monitoring alerts configured  
✅ Performance acceptable (< 2 sec load time)  
✅ Security measures in place  
✅ No errors in logs  

---

## 35. Next Steps After Deployment

### Immediate (Week 1)

1. **Monitor Closely:**
   - Check logs daily
   - Monitor performance
   - Watch for errors

2. **Test Thoroughly:**
   - Place test orders
   - Test all features
   - Verify backups working

3. **Document:**
   - Note any issues
   - Document custom configurations
   - Update team

### Short Term (Month 1)

1. **Optimize:**
   - Review performance metrics
   - Optimize slow queries
   - Compress images

2. **Secure:**
   - Review security logs
   - Update passwords
   - Audit access logs

3. **Scale:**
   - Monitor traffic patterns
   - Plan for scaling if needed
   - Consider CDN for images

### Long Term (Ongoing)

1. **Maintain:**
   - Weekly system updates
   - Monthly security audits
   - Quarterly disaster recovery tests

2. **Improve:**
   - Add new features
   - Optimize performance
   - Enhance security

3. **Scale:**
   - Add more instances if traffic grows
   - Move to RDS for database
   - Implement caching layer

---

## 36. Rollback Plan

### If Deployment Fails

**Quick Rollback:**

```bash
# 1. Restore database
gunzip < /backups/database/onlinesale_PREVIOUS.sql.gz | mysql -u root -p onlinesale

# 2. Restore code
cd /var/www/html/fresh-mart
sudo git checkout PREVIOUS_COMMIT_HASH

# 3. Restart services
sudo systemctl restart apache2
sudo systemctl restart mysql

# 4. Verify
curl http://localhost
```

**Complete Rollback:**

1. Terminate new instance
2. Launch instance from previous AMI
3. Attach Elastic IP
4. Verify functionality

---

## 37. Summary

### What You've Accomplished

✅ **Infrastructure:**
- EC2 instance running 24/7
- LAMP stack configured
- Firewall and security enabled

✅ **Application:**
- Fresh Mart deployed
- Database configured
- All features working

✅ **Security:**
- SSL certificate installed
- Firewall configured
- MySQL secured
- Admin password changed

✅ **Operations:**
- Automated backups
- Health monitoring
- Log management
- Performance optimized

✅ **Production Ready:**
- Domain configured
- HTTPS enabled
- Monitoring active
- Support procedures ready

### Your Website is Now Live!

**Access:**
- **Public:** `http://your-domain.com` or `http://YOUR_EC2_IP`
- **Admin:** `http://your-domain.com/admin/`

**Credentials:**
- Admin: admin / YourSecureAdminPassword123!

**Support:** +91 9360509515

---

## 38. Additional Resources

### Tutorials

- **AWS EC2 Basics:** https://aws.amazon.com/ec2/getting-started/
- **LAMP Stack:** https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-22-04
- **Let's Encrypt SSL:** https://certbot.eff.org/

### Tools

- **SSH Client:** Terminal (Mac/Linux), PuTTY (Windows)
- **FTP Client:** FileZilla
- **Database Client:** MySQL Workbench, phpMyAdmin
- **Monitoring:** CloudWatch, htop, Pingdom

### Documentation

- **In This Folder:**
  - `USER_MANUAL_SOP.md` - Complete operations guide
  - `QUICK_START_GUIDE.md` - Quick reference
  - `ONE_PAGE_REFERENCE.md` - Daily reference

---

**🎉 Congratulations! Your Fresh Mart website is now live on AWS EC2!**

*For operational procedures, refer to USER_MANUAL_SOP.md*

*For technical support: +91 9360509515*
