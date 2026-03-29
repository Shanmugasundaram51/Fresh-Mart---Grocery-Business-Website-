<?php
session_start();
require 'includes/admin_common.php';
check_admin_login();

if (!isset($_GET['order_id'])) {
    header('Location: orders.php');
    exit();
}

require "../includes/common.php";
require "../includes/fpdf.php";

$order_id = mysqli_real_escape_string($con, $_GET['order_id']);

$order = get_order_details($order_id);
if (!$order) {
    header('Location: orders.php?error=Order not found');
    exit();
}

$order_items = get_order_items($order_id);

class PDF extends FPDF {
    function Header() {
        $this->SetFillColor(16, 185, 129);
        $this->Rect(0, 0, 210, 18, 'F');
        
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Helvetica', 'B', 14);
        $this->SetXY(10, 4);
        $this->Cell(0, 5, 'FRESH MART', 0, 1, 'C');
        
        $this->SetFont('Helvetica', '', 7);
        $this->SetX(10);
        $this->Cell(0, 3, 'Your Fresh Grocery Store', 0, 1, 'C');
        
        $this->SetTextColor(0, 0, 0);
    }
    
    function Footer() {
        $this->SetY(-12);
        $this->SetFont('Helvetica', 'I', 7);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 5, 'Thank you for shopping with Fresh Mart!', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->SetMargins(10, 5, 10);
$pdf->SetAutoPageBreak(true, 12);
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetY(20);

$customer_name = $order['first_name'] . ' ' . $order['last_name'];

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor(16, 185, 129);
$pdf->Cell(0, 4, 'TAX INVOICE', 0, 1, 'C');
$pdf->Ln(1);

$pdf->SetFont('Helvetica', 'B', 8);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(95, 3.5, 'Bill To:', 0, 0);
$pdf->Cell(95, 3.5, 'Invoice Details:', 0, 1);
$pdf->Ln(0.3);

$pdf->SetFont('Helvetica', 'B', 8);
$pdf->Cell(95, 3.5, $customer_name, 0, 0);
$pdf->SetFont('Helvetica', '', 7);
$pdf->Cell(50, 3.5, 'Invoice #: ', 0, 0, 'R');
$pdf->SetFont('Helvetica', 'B', 7);
$pdf->Cell(45, 3.5, $order['order_number'], 0, 1);

$pdf->SetFont('Helvetica', '', 7);
$pdf->Cell(95, 3.5, 'Email: ' . substr($order['email_id'], 0, 35), 0, 0);
$pdf->Cell(50, 3.5, 'Date: ', 0, 0, 'R');
$pdf->SetFont('Helvetica', 'B', 7);
$pdf->Cell(45, 3.5, date('d M Y', strtotime($order['order_date'])), 0, 1);

if (!empty($order['phone'])) {
    $pdf->SetFont('Helvetica', '', 7);
    $pdf->Cell(95, 3.5, 'Phone: ' . $order['phone'], 0, 0);
} else {
    $pdf->Cell(95, 3.5, '', 0, 0);
}
$pdf->SetFont('Helvetica', '', 7);
$pdf->Cell(50, 3.5, 'Payment Method: ', 0, 0, 'R');
$pdf->SetFont('Helvetica', 'B', 7);
$pdf->Cell(45, 3.5, $order['payment_method'], 0, 1);

if (!empty($order['delivery_address'])) {
    $pdf->SetFont('Helvetica', '', 7);
    $address = substr($order['delivery_address'], 0, 60);
    $pdf->Cell(95, 3.5, 'Address: ' . $address, 0, 1);
}

$pdf->Ln(1.5);
$pdf->SetDrawColor(200, 200, 200);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(1.5);

$pdf->SetFont('Helvetica', 'B', 8);
$pdf->Cell(0, 3.5, 'Order Items', 0, 1);
$pdf->Ln(0.5);

$pdf->SetFillColor(16, 185, 129);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Helvetica', 'B', 7);

$pdf->Cell(8, 4, '#', 1, 0, 'C', true);
$pdf->Cell(72, 4, 'Product Name', 1, 0, 'L', true);
$pdf->Cell(23, 4, 'Price', 1, 0, 'R', true);
$pdf->Cell(18, 4, 'Qty', 1, 0, 'C', true);
$pdf->Cell(25, 4, 'Discount', 1, 0, 'R', true);
$pdf->Cell(24, 4, 'Total', 1, 1, 'R', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Helvetica', '', 7);

$item_num = 1;
foreach ($order_items as $item) {
    $fill = ($item_num % 2 == 0);
    if ($fill) {
        $pdf->SetFillColor(245, 245, 245);
    }
    
    $product_name = substr($item['product_name'], 0, 32);
    $discount_text = ($item['discount_percent'] > 0) 
        ? $item['discount_percent'] . '% (' . number_format($item['discount_amount'], 0) . ')' 
        : '-';
    
    $pdf->Cell(8, 4, $item_num, 1, 0, 'C', $fill);
    $pdf->Cell(72, 4, $product_name, 1, 0, 'L', $fill);
    $pdf->Cell(23, 4, number_format($item['product_price'], 2), 1, 0, 'R', $fill);
    $pdf->Cell(18, 4, $item['quantity'] . ' ' . substr($item['product_unit'], 0, 2), 1, 0, 'C', $fill);
    $pdf->Cell(25, 4, $discount_text, 1, 0, 'R', $fill);
    $pdf->Cell(24, 4, number_format($item['final_price'], 2), 1, 1, 'R', $fill);
    
    $item_num++;
}

$pdf->Ln(1);
$pdf->SetFont('Helvetica', '', 7);

$pdf->Cell(146, 3.5, '', 0, 0);
$pdf->Cell(18, 3.5, 'Subtotal:', 0, 0, 'R');
$pdf->SetFont('Helvetica', 'B', 7);
$pdf->Cell(26, 3.5, 'Rs ' . number_format($order['total_amount'], 2), 0, 1, 'R');

$pdf->SetFont('Helvetica', '', 7);
$pdf->SetTextColor(220, 38, 38);
$pdf->Cell(146, 3.5, '', 0, 0);
$pdf->Cell(18, 3.5, 'Discount:', 0, 0, 'R');
$pdf->SetFont('Helvetica', 'B', 7);
$pdf->Cell(26, 3.5, '- Rs ' . number_format($order['discount_amount'], 2), 0, 1, 'R');

$pdf->SetDrawColor(16, 185, 129);
$pdf->SetLineWidth(0.2);
$pdf->Line(146, $pdf->GetY(), 190, $pdf->GetY());
$pdf->Ln(0.3);

$pdf->SetTextColor(16, 185, 129);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(146, 4, '', 0, 0);
$pdf->Cell(18, 4, 'Total:', 0, 0, 'R');
$pdf->Cell(26, 4, 'Rs ' . number_format($order['final_amount'], 2), 0, 1, 'R');

$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.2);

$pdf->Ln(2);
$pdf->SetFont('Helvetica', 'I', 6);
$pdf->SetTextColor(100, 100, 100);
$pdf->MultiCell(0, 2.5, 'Terms & Conditions: All products are subject to availability. Prices are inclusive of all taxes. Please check items upon delivery. For any queries, contact our customer support.', 0, 'L');

$filename = 'Invoice_' . $order['order_number'] . '.pdf';
$pdf->Output('D', $filename);
?>
