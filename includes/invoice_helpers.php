<?php

if (!function_exists('format_currency')) {
    function format_currency($amount) {
        return 'Rs ' . number_format($amount, 2);
    }
}

if (!function_exists('format_invoice_date')) {
    function format_invoice_date($date) {
        return date('d M Y', strtotime($date));
    }
}

if (!function_exists('get_invoice_filename')) {
    function get_invoice_filename($order_number) {
        return 'Invoice_' . $order_number . '.pdf';
    }
}

if (!function_exists('validate_invoice_access')) {
    function validate_invoice_access($order_id, $user_id) {
        global $con;
        $order_id = mysqli_real_escape_string($con, $order_id);
        $user_id = mysqli_real_escape_string($con, $user_id);
        
        $query = "SELECT id FROM orders WHERE id='$order_id' AND user_id='$user_id'";
        $result = mysqli_query($con, $query);
        
        return $result && mysqli_num_rows($result) > 0;
    }
}

if (!function_exists('get_store_info')) {
    function get_store_info() {
        return array(
            'name' => 'FRESH MART',
            'tagline' => 'Your Fresh Grocery Store',
            'email' => 'support@freshmart.com',
            'phone' => '+91 1234567890',
            'address' => '123 Market Street, City, State - 123456',
            'website' => 'www.freshmart.com',
            'gst_number' => '',
            'logo_path' => 'images/logo.png'
        );
    }
}

if (!function_exists('calculate_order_totals')) {
    function calculate_order_totals($order_items) {
        $totals = array(
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'final_total' => 0
        );
        
        foreach ($order_items as $item) {
            $totals['subtotal'] += $item['subtotal'];
            $totals['discount'] += $item['discount_amount'];
            $totals['final_total'] += $item['final_price'];
        }
        
        return $totals;
    }
}

if (!function_exists('generate_invoice_html')) {
    function generate_invoice_html($order, $order_items) {
        ob_start();
        include 'invoice_template.php';
        return ob_get_clean();
    }
}

if (!function_exists('save_invoice_pdf')) {
    function save_invoice_pdf($order_id, $pdf_content) {
        $invoice_dir = 'invoices/';
        if (!file_exists($invoice_dir)) {
            mkdir($invoice_dir, 0755, true);
        }
        
        $order = get_order_details($order_id);
        if (!$order) return false;
        
        $filename = get_invoice_filename($order['order_number']);
        $filepath = $invoice_dir . $filename;
        
        return file_put_contents($filepath, $pdf_content) !== false;
    }
}

if (!function_exists('get_saved_invoice_path')) {
    function get_saved_invoice_path($order_id) {
        $order = get_order_details($order_id);
        if (!$order) return null;
        
        $filename = get_invoice_filename($order['order_number']);
        $filepath = 'invoices/' . $filename;
        
        return file_exists($filepath) ? $filepath : null;
    }
}

if (!function_exists('format_discount_display')) {
    function format_discount_display($discount_percent, $discount_amount) {
        if ($discount_percent > 0) {
            return $discount_percent . '% (Rs ' . number_format($discount_amount, 2) . ')';
        }
        return '-';
    }
}

if (!function_exists('get_invoice_terms')) {
    function get_invoice_terms() {
        return 'Terms & Conditions: All products are subject to availability. Prices are inclusive of all taxes. Please check items upon delivery. For any queries, contact our customer support.';
    }
}

if (!function_exists('log_invoice_generation')) {
    function log_invoice_generation($order_id, $user_id, $action = 'download') {
        global $con;
        $order_id = mysqli_real_escape_string($con, $order_id);
        $user_id = mysqli_real_escape_string($con, $user_id);
        $action = mysqli_real_escape_string($con, $action);
        
        $query = "INSERT INTO invoice_logs (order_id, user_id, action, created_at) 
                  VALUES ('$order_id', '$user_id', '$action', NOW())";
        
        return mysqli_query($con, $query);
    }
}

if (!function_exists('get_invoice_count')) {
    function get_invoice_count($order_id) {
        global $con;
        $order_id = mysqli_real_escape_string($con, $order_id);
        
        $query = "SELECT COUNT(*) as count FROM invoice_logs WHERE order_id='$order_id'";
        $result = mysqli_query($con, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return (int)$row['count'];
        }
        
        return 0;
    }
}
?>
