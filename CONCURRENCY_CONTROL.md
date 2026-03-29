# Database-Level Concurrency Control Implementation

## Problem Statement

In a multi-user e-commerce system, race conditions can occur when multiple users attempt to purchase the same product simultaneously:

**Example Scenario:**
- Product stock = 1
- User A clicks "Buy" 
- User B clicks "Buy" at the same time
- **Without control:** Both orders succeed → stock = -1 (INVALID STATE)
- **With control:** Only one order succeeds → stock = 0, other user gets "Out of Stock"

## Solution: Transaction-Based Locking

### Implementation Overview

The solution uses MySQL transactions with row-level locking (`SELECT ... FOR UPDATE`) to ensure atomic order placement.

### Key Components

#### 1. New Function: `create_order_with_transaction()`

Located in: `includes/common.php`

This function replaces the unsafe order creation flow with a transaction-based approach:

```php
function create_order_with_transaction($user_id, $delivery_address = null, $delivery_phone = null)
```

**Transaction Flow:**

1. **START TRANSACTION**
   ```php
   mysqli_begin_transaction($con, MYSQLI_TRANS_START_READ_WRITE);
   ```

2. **LOCK PRODUCT ROWS** (Critical Section)
   ```php
   SELECT id, stock_quantity, name FROM products WHERE id='$product_id' FOR UPDATE
   ```
   - `FOR UPDATE` acquires an exclusive lock on the product row
   - Other transactions must wait until this transaction completes
   - Prevents concurrent modifications

3. **CHECK STOCK INSIDE TRANSACTION**
   ```php
   if ($available_stock < $requested_qty) {
       // Collect insufficient stock items
       $insufficient_stock[] = [...];
   }
   ```
   - Stock check happens AFTER acquiring lock
   - Ensures we see the most current stock value
   - No other transaction can modify stock until we commit/rollback

4. **CONDITIONAL EXECUTION**

   **If Stock is Sufficient:**
   ```php
   // Reduce stock
   UPDATE products SET stock_quantity = stock_quantity - $requested_qty WHERE id='$product_id'
   
   // Create order record
   INSERT INTO orders (...)
   
   // Create order items
   INSERT INTO order_items (...)
   
   // Update cart status
   UPDATE users_products SET status='Confirmed' WHERE user_id='$user_id'
   
   // COMMIT - releases locks and makes changes permanent
   mysqli_commit($con);
   ```

   **If Stock is Insufficient:**
   ```php
   // ROLLBACK - releases locks and discards all changes
   mysqli_rollback($con);
   return ['success' => false, 'error' => 'insufficient_stock', 'items' => $insufficient_stock];
   ```

5. **ERROR HANDLING**
   - Any query failure triggers `ROLLBACK`
   - All changes are atomic (all-or-nothing)
   - Locks are always released (commit or rollback)

#### 2. Updated `success.php`

**Before (Unsafe):**
```php
// Step 1: Check stock (not locked)
validate_stock($item_id, $quantity);

// Step 2: Reduce stock (separate operation)
reduce_stock($item_id, $quantity);

// Step 3: Create order (separate operation)
create_order($user_id);
```

**After (Safe):**
```php
// Single atomic operation
$result = create_order_with_transaction($user_id, $delivery_address, $delivery_phone);

if (!$result['success']) {
    // Handle errors and redirect back to cart
    header('location: cart.php?error=...');
    exit();
}
```

#### 3. Enhanced Error Handling in `cart.php`

Added new error message for order failures:
- `stock_validation`: Shows which items are out of stock
- `order_failed`: Shows general order placement errors
- Improved UX with clear messaging about concurrent purchase scenarios

## How It Prevents Race Conditions

### Scenario: Two Users, One Item

**Timeline with Transaction Control:**

| Time | User A | User B | Stock |
|------|--------|--------|-------|
| T0 | - | - | 1 |
| T1 | BEGIN TRANSACTION | BEGIN TRANSACTION | 1 |
| T2 | SELECT ... FOR UPDATE (LOCKS ROW) | SELECT ... FOR UPDATE (WAITS) | 1 |
| T3 | Check: stock=1 ✓ | (Still waiting for lock) | 1 |
| T4 | UPDATE stock = 0 | (Still waiting) | 0 |
| T5 | INSERT order | (Still waiting) | 0 |
| T6 | COMMIT (releases lock) | (Now acquires lock) | 0 |
| T7 | - | Check: stock=0 ✗ | 0 |
| T8 | - | ROLLBACK | 0 |
| T9 | - | Error: "Out of Stock" | 0 |

**Key Points:**
- User B's transaction **waits** until User A commits
- When User B finally acquires the lock, stock is already 0
- User B gets a proper error message
- Stock never goes negative

### ACID Properties Guaranteed

1. **Atomicity**: All operations (stock reduction, order creation, order items) succeed or fail together
2. **Consistency**: Stock quantity never goes negative
3. **Isolation**: Each transaction sees a consistent snapshot; locks prevent interference
4. **Durability**: Committed orders are permanent

## Testing the Implementation

### Manual Test

1. Set a product's stock to 1
2. Open two browser windows/tabs
3. Add the product to cart in both sessions (different users)
4. Click "Confirm Order" simultaneously in both windows
5. **Expected Result:** One succeeds, one gets "Out of Stock" error

### Automated Test Script

Run: `test_concurrency.php`

This script:
- Creates a test product with stock = 1
- Creates 2 test users
- Adds product to both carts
- Simulates concurrent order placement
- Verifies final stock is 0 (not -1)

## Performance Considerations

### Lock Duration
- Locks are held only during the transaction
- Transaction completes in milliseconds
- Minimal impact on concurrent users

### Deadlock Prevention
- Products are locked in a consistent order (by ID)
- Transactions are kept short
- MySQL automatically detects and resolves deadlocks if they occur

### Scalability
- Row-level locking (not table-level)
- Only locks the specific products being purchased
- Other products can be purchased concurrently without blocking

## Migration Notes

### Backward Compatibility

The original `create_order()` function is preserved for backward compatibility. New code should use `create_order_with_transaction()`.

### Database Requirements

- MySQL 5.5+ (InnoDB engine required for row-level locking)
- Ensure `products` table uses InnoDB engine:
  ```sql
  ALTER TABLE products ENGINE=InnoDB;
  ```

### Error Handling

The new function returns an associative array:

**Success:**
```php
['success' => true, 'order_id' => 123]
```

**Failure:**
```php
['success' => false, 'error' => 'insufficient_stock', 'items' => [...]]
['success' => false, 'error' => 'Cart is empty']
['success' => false, 'error' => 'Failed to create order']
```

## Code Quality Improvements

### What Was Fixed

1. **Race Condition**: Stock validation and reduction now atomic
2. **Negative Stock**: Impossible with transaction control
3. **Overselling**: `SELECT FOR UPDATE` ensures only available stock is sold
4. **Data Integrity**: All order-related operations are atomic

### Security Notes

- SQL injection protection maintained with `mysqli_real_escape_string()`
- Transaction isolation prevents dirty reads
- Rollback on any error ensures data consistency

## Summary

This implementation ensures that:
- ✓ Only ONE user can successfully purchase the last item
- ✓ Other users will receive "Out of Stock" error
- ✓ Stock quantity never goes negative
- ✓ All order data remains consistent
- ✓ No race conditions possible

The solution is production-ready and handles high-concurrency scenarios safely.
