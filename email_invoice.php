<?php
require "includes/common.php";
require "includes/fpdf.php";

session_start();

if (!isset($_POST['order_id']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$order_id = mysqli_real_escape_string($con, $_POST['order_id']);
$user_id = $_SESSION['user_id'];

$order = get_order_details($order_id);
if (!$order || $order['user_id'] != $user_id) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

$order_items = get_order_items($order_id);

class PDF extends FPDF {
    function Header() {
        if ($this->page == 1) {
            $this->SetFillColor(16, 185, 129);
            $this->Rect(0, 0, 210, 30, 'F');
            
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Helvetica', 'B', 20);
            $this->SetY(8);
            $this->Cell(0, 8, 'FRESH MART', 0, 1, 'C');
            
            $this->SetFont('Helvetica', '', 9);
            $this->Cell(0, 4, 'Your Fresh Grocery Store', 0, 1, 'C');
            
            $this->SetTextColor(0, 0, 0);
            $this->SetY(35);
        }
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Helvetica', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Thank you for shopping with Fresh Mart!', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AliasNbPages();
$pdf->AddPage();

$customer_name = $order['first_name'] . ' ' . $order['last_name'];

$pdf->SetFont('Helvetica', 'B', 14);
$pdf->SetTextColor(16, 185, 129);
$pdf->Cell(0, 6, 'TAX INVOICE', 0, 1, 'C');
$pdf->Ln(3);

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(95, 5, 'Bill To:', 0, 0);
$pdf->Cell(95, 5, 'Invoice Details:', 0, 1);
$pdf->Ln(1);

$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(95, 4, $customer_name, 0, 0);
$pdf->SetFont('Helvetica', '', 8);
$pdf->Cell(50, 4, 'Invoice #: ', 0, 0, 'R');
$pdf->SetFont('Helvetica', 'B', 8);
$pdf->Cell(45, 4, $order['order_number'], 0, 1);

$pdf->SetFont('Helvetica', '', 8);
$pdf->Cell(95, 4, 'Email: ' . substr($order['email_id'], 0, 35), 0, 0);
$pdf->Cell(50, 4, 'Date: ', 0, 0, 'R');
$pdf->SetFont('Helvetica', 'B', 8);
$pdf->Cell(45, 4, date('d M Y', strtotime($order['order_date'])), 0, 1);

if (!empty($order['phone'])) {
    $pdf->SetFont('Helvetica', '', 8);
    $pdf->Cell(95, 4, 'Phone: ' . $order['phone'], 0, 0);
} else {
    $pdf->Cell(95, 4, '', 0, 0);
}
$pdf->SetFont('Helvetica', '', 8);
$pdf->Cell(50, 4, 'Payment Method: ', 0, 0, 'R');
$pdf->SetFont('Helvetica', 'B', 8);
$pdf->Cell(45, 4, $order['payment_method'], 0, 1);

if (!empty($order['delivery_address'])) {
    $pdf->SetFont('Helvetica', '', 8);
    $address = substr($order['delivery_address'], 0, 60);
    $pdf->Cell(95, 4, 'Address: ' . $address, 0, 1);
}

$pdf->Ln(4);
$pdf->SetDrawColor(200, 200, 200);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(3);

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 5, 'Order Items', 0, 1);
$pdf->Ln(1);

$pdf->SetFillColor(16, 185, 129);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Helvetica', 'B', 9);

$pdf->Cell(10, 6, '#', 1, 0, 'C', true);
$pdf->Cell(70, 6, 'Product Name', 1, 0, 'L', true);
$pdf->Cell(25, 6, 'Price', 1, 0, 'R', true);
$pdf->Cell(20, 6, 'Qty', 1, 0, 'C', true);
$pdf->Cell(25, 6, 'Discount', 1, 0, 'R', true);
$pdf->Cell(30, 6, 'Total', 1, 1, 'R', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Helvetica', '', 8);

$item_num = 1;
foreach ($order_items as $item) {
    $fill = ($item_num % 2 == 0);
    if ($fill) {
        $pdf->SetFillColor(245, 245, 245);
    }
    
    $product_name = substr($item['product_name'], 0, 28);
    $discount_text = ($item['discount_percent'] > 0) 
        ? $item['discount_percent'] . '% (' . number_format($item['discount_amount'], 0) . ')' 
        : '-';
    
    $pdf->Cell(10, 5, $item_num, 1, 0, 'C', $fill);
    $pdf->Cell(70, 5, $product_name, 1, 0, 'L', $fill);
    $pdf->Cell(25, 5, number_format($item['product_price'], 2), 1, 0, 'R', $fill);
    $pdf->Cell(20, 5, $item['quantity'] . ' ' . substr($item['product_unit'], 0, 3), 1, 0, 'C', $fill);
    $pdf->Cell(25, 5, $discount_text, 1, 0, 'R', $fill);
    $pdf->Cell(30, 5, number_format($item['final_price'], 2), 1, 1, 'R', $fill);
    
    $item_num++;
}

$pdf->Ln(2);
$pdf->SetFont('Helvetica', '', 9);

$pdf->Cell(150, 5, '', 0, 0);
$pdf->Cell(20, 5, 'Subtotal:', 0, 0, 'R');
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(20, 5, 'Rs ' . number_format($order['total_amount'], 2), 0, 1, 'R');

$pdf->SetFont('Helvetica', '', 9);
$pdf->SetTextColor(220, 38, 38);
$pdf->Cell(150, 5, '', 0, 0);
$pdf->Cell(20, 5, 'Discount:', 0, 0, 'R');
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(20, 5, '- Rs ' . number_format($order['discount_amount'], 2), 0, 1, 'R');

$pdf->SetDrawColor(16, 185, 129);
$pdf->SetLineWidth(0.3);
$pdf->Line(150, $pdf->GetY(), 190, $pdf->GetY());
$pdf->Ln(1);

$pdf->SetTextColor(16, 185, 129);
$pdf->SetFont('Helvetica', 'B', 11);
$pdf->Cell(150, 6, '', 0, 0);
$pdf->Cell(20, 6, 'Total:', 0, 0, 'R');
$pdf->Cell(20, 6, 'Rs ' . number_format($order['final_amount'], 2), 0, 1, 'R');

$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.2);

$pdf->Ln(5);
$pdf->SetFont('Helvetica', 'I', 7);
$pdf->SetTextColor(100, 100, 100);
$pdf->MultiCell(0, 3, 'Terms & Conditions: All products are subject to availability. Prices are inclusive of all taxes. Please check items upon delivery. For any queries, contact our customer support.', 0, 'L');

$temp_dir = sys_get_temp_dir();
$filename = 'Invoice_' . $order['order_number'] . '.pdf';
$filepath = $temp_dir . '/' . $filename;

$pdf->Output('F', $filepath);

$to = $order['email_id'];
$subject = 'Your Fresh Mart Invoice - ' . $order['order_number'];
$message = "Dear " . $customer_name . ",\n\n";
$message .= "Thank you for shopping with Fresh Mart!\n\n";
$message .= "Please find attached your invoice for order " . $order['order_number'] . ".\n\n";
$message .= "Order Summary:\n";
$message .= "Total Amount: Rs " . number_format($order['final_amount'], 2) . "\n";
$message .= "Order Date: " . date('d M Y', strtotime($order['order_date'])) . "\n\n";
$message .= "Thank you for your business!\n\n";
$message .= "Best regards,\n";
$message .= "Fresh Mart Team";

$content = file_get_contents($filepath);
$content = chunk_split(base64_encode($content));

$separator = md5(time());

$headers = "From: noreply@freshmart.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"\r\n";

$body = "--" . $separator . "\r\n";
$body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
$body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$body .= $message . "\r\n";

$body .= "--" . $separator . "\r\n";
$body .= "Content-Type: application/pdf; name=\"" . $filename . "\"\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n";
$body .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\r\n";
$body .= $content . "\r\n";
$body .= "--" . $separator . "--";

$mail_sent = mail($to, $subject, $body, $headers);

unlink($filepath);

if ($mail_sent) {
    echo json_encode(['success' => true, 'message' => 'Invoice sent to your email successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send email. Please download the invoice instead.']);
}
?>
