# Fresh Mart - One Page Reference Card

**Quick reference for daily operations**

---

## 🚀 Quick Access

| Role | URL | Credentials |
|------|-----|-------------|
| **Customer** | http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/ | Register/Login |
| **Admin** | http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/admin/ | admin / admin123 |

---

## 👤 Customer Operations

### Register & Login
```
Sign Up → Fill Form → Auto-Login
Login → Email + Password → Products Page
```

### Shopping
```
Browse → Add to Cart → Update Qty → Confirm Order → Enter Address → Success
```

### Stock Status
- 🟢 **In Stock** - Available (5+ units)
- 🟡 **Low Stock** - Only X left (1-4 units)
- 🔴 **Out of Stock** - Cannot order (0 units)

---

## 👨‍💼 Admin Operations

### Daily Tasks (15 min)
```
Login → Check Pending Orders → Update Status → Monitor Stock Alerts
```

### Add Product
```
Add Product → Name, Category, Price, Stock, Unit, Image → Submit
```

### Process Order
```
Orders → View Details → Update Status → Pending → Processing → Shipped → Delivered
```

### Update Stock
```
All Products → Edit Icon → Update Stock Quantity → Submit
```

---

## 📦 Order Status Flow

```
PENDING → PROCESSING → SHIPPED → DELIVERED
   ↓
CANCELLED (from any status)
```

---

## 📊 Stock Alerts

| Alert | Stock Range | Action |
|-------|-------------|--------|
| **Out of Stock** | 0 | Reorder immediately |
| **Low Stock** | 1-4 | Reorder soon |
| **Normal** | 5+ | Monitor |

---

## 🔒 Concurrency Control

**What It Does:**
- Prevents overselling
- No negative stock
- Safe concurrent orders

**How It Works:**
```
User A clicks Buy → LOCK product → Check stock → Reduce → COMMIT
User B clicks Buy → WAIT for lock → Check stock (0) → ROLLBACK → Error
```

**Result:** Only ONE user gets last item ✓

---

## 🧪 Testing

### Quick Test
```bash
http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/test_concurrency.php
```

**Expected:** User A succeeds, User B gets error, stock = 0

---

## 🛠️ Troubleshooting

| Issue | Quick Fix |
|-------|-----------|
| Can't login | Check credentials, re-import admin_setup.sql |
| Image upload fails | `chmod 755 images/` |
| Products not showing | Check DB connection, verify images exist |
| Order fails | Check stock, verify DB connection |
| Negative stock | Should NEVER happen! Check transaction usage |

---

## 💾 Database

### Connection (MAMP)
```php
mysqli_connect("localhost:8889", "root", "root", "onlinesale");
```

### Import
```bash
mysql -u root -p onlinesale < onlinesale.sql
mysql -u root -p onlinesale < create_orders_tables.sql
mysql -u root -p onlinesale < admin_setup.sql
```

### Backup
```bash
mysqldump -u root -p onlinesale > backup_$(date +%Y%m%d).sql
```

---

## 📱 Contact

**Support:** +91 9360509515

---

## 📚 Full Documentation

For detailed procedures, see: **[USER_MANUAL_SOP.md](USER_MANUAL_SOP.md)**

---

**Print this page for quick reference at your desk!**
