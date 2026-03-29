# Concurrency Control - Quick Reference

## The Problem

```
Product Stock = 1

User A: Check stock (1) → Pass ✓
User B: Check stock (1) → Pass ✓
User A: Reduce stock (1→0)
User B: Reduce stock (0→-1) ← PROBLEM!
Both orders succeed, stock = -1
```

## The Solution

```sql
START TRANSACTION;

-- Lock the row (other transactions wait here)
SELECT stock_quantity FROM products WHERE id=? FOR UPDATE;

-- Check stock inside transaction
IF stock >= requested_quantity THEN
    -- Reduce stock
    UPDATE products SET stock_quantity = stock_quantity - ? WHERE id=?;
    
    -- Create order
    INSERT INTO orders (...) VALUES (...);
    
    -- Create order items
    INSERT INTO order_items (...) VALUES (...);
    
    -- Success!
    COMMIT;
ELSE
    -- Not enough stock
    ROLLBACK;
    RETURN 'Out of Stock';
END IF;
```

## Usage

### New Function (Safe)

```php
$result = create_order_with_transaction($user_id, $delivery_address, $delivery_phone);

if ($result['success']) {
    $order_id = $result['order_id'];
    // Order placed successfully
} else {
    $error = $result['error'];
    // Handle error
    if ($error === 'insufficient_stock') {
        // Show which items are out of stock
        $items = $result['items'];
    }
}
```

### Old Function (Unsafe - Deprecated)

```php
// DO NOT USE - Has race condition
$order_id = create_order($user_id, $delivery_address, $delivery_phone);
```

## Key Benefits

| Feature | Without Transaction | With Transaction |
|---------|-------------------|------------------|
| **Race Condition** | ✗ Possible | ✓ Prevented |
| **Negative Stock** | ✗ Can occur | ✓ Impossible |
| **Overselling** | ✗ Can happen | ✓ Prevented |
| **Data Integrity** | ✗ At risk | ✓ Guaranteed |
| **Concurrent Users** | ✗ Conflicts | ✓ Serialized safely |

## How It Works

### 1. SELECT ... FOR UPDATE

```sql
SELECT stock_quantity FROM products WHERE id=123 FOR UPDATE;
```

- Acquires **exclusive lock** on row(s)
- Other transactions trying to lock same row **WAIT**
- Lock released on COMMIT or ROLLBACK

### 2. Transaction Isolation

```
User A: BEGIN TRANSACTION
User A: SELECT ... FOR UPDATE  ← Locks row
User B: BEGIN TRANSACTION
User B: SELECT ... FOR UPDATE  ← WAITS for User A
User A: UPDATE stock
User A: COMMIT                 ← Releases lock
User B: (Lock acquired)        ← Now sees updated stock
User B: Check fails (stock=0)
User B: ROLLBACK
```

### 3. Atomicity

All operations succeed together or fail together:
- ✓ Stock reduction
- ✓ Order creation
- ✓ Order items insertion
- ✓ Cart status update

If ANY step fails → ROLLBACK (undo everything)

## Testing

### Quick Test

```bash
# Run automated test
open http://localhost:8888/test_concurrency.php
```

### Manual Test

1. Set product stock to 1
2. Open 2 browser windows (different users)
3. Add product to cart in both
4. Click "Confirm Order" simultaneously
5. **Expected:** One succeeds, one gets "Out of Stock"

## Files Modified

- `includes/common.php` - Added `create_order_with_transaction()`
- `success.php` - Uses new transaction-based function
- `cart.php` - Enhanced error messages

## Performance Notes

- **Lock Duration:** Milliseconds (very fast)
- **Blocking:** Only affects same product
- **Scalability:** Different products can be purchased concurrently
- **Deadlock:** MySQL handles automatically (rare with this pattern)

## Database Requirements

- Engine: **InnoDB** (required for row-level locking)
- MySQL: 5.5+ 
- Current setup: ✓ Already using InnoDB

## Error Handling

```php
$result = create_order_with_transaction($user_id);

// Possible errors:
// - 'Cart is empty'
// - 'insufficient_stock' (with items array)
// - 'Product not found: {name}'
// - 'Failed to update stock'
// - 'Failed to create order'
// - 'Failed to create order items'
// - 'Transaction failed: {message}'
```

## Summary

✓ **Atomic Operations** - All-or-nothing guarantee  
✓ **Row-Level Locking** - Prevents concurrent modifications  
✓ **Stock Validation** - Inside transaction, after lock  
✓ **Negative Stock Prevention** - Impossible by design  
✓ **Clear Error Messages** - User-friendly feedback  
✓ **Production Ready** - Handles high concurrency safely  

**Result:** Only ONE user can buy the last item. Others get "Out of Stock" error.
