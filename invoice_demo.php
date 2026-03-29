<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Demo - Fresh Mart</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            background: #f3f4f6;
            font-family: 'Helvetica', sans-serif;
        }
        
        .demo-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        
        .demo-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .demo-header h1 {
            margin: 0;
            font-size: 2.5rem;
        }
        
        .invoice-preview {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        
        .invoice-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            margin: -40px -40px 30px -40px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        
        .invoice-header h2 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }
        
        .invoice-title {
            text-align: center;
            color: #10b981;
            font-weight: 700;
            margin: 20px 0;
        }
        
        .info-section {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            padding: 20px 0;
            border-top: 2px solid #e5e7eb;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .info-box h5 {
            color: #10b981;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .info-box p {
            margin: 5px 0;
            color: #374151;
            font-size: 0.9rem;
        }
        
        .invoice-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        
        .invoice-table thead {
            background: #10b981;
            color: white;
        }
        
        .invoice-table thead th {
            padding: 12px;
            font-weight: 600;
            text-align: left;
        }
        
        .invoice-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .invoice-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .total-section {
            margin-top: 30px;
            text-align: right;
        }
        
        .total-row {
            display: flex;
            justify-content: flex-end;
            padding: 8px 0;
        }
        
        .total-row .label {
            width: 150px;
            text-align: right;
            padding-right: 20px;
            font-weight: 500;
        }
        
        .total-row .value {
            width: 120px;
            text-align: right;
            font-weight: 600;
        }
        
        .grand-total {
            border-top: 2px solid #10b981;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 1.2rem;
            color: #10b981;
        }
        
        .discount-text {
            color: #dc2626;
        }
        
        .terms {
            margin-top: 30px;
            padding: 20px;
            background: #f9fafb;
            border-left: 4px solid #10b981;
            font-size: 0.9rem;
            color: #6b7280;
        }
        
        .feature-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .feature-box h4 {
            color: #10b981;
            margin-bottom: 15px;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .feature-list li:last-child {
            border-bottom: none;
        }
        
        .feature-list i {
            color: #10b981;
            margin-right: 10px;
        }
        
        .note-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="demo-container">
        <div class="demo-header">
            <h1><i class="fa fa-file-pdf-o"></i> Invoice System Demo</h1>
            <p>Preview of the PDF Invoice Generation Feature</p>
        </div>
        
        <div class="note-box">
            <strong><i class="fa fa-info-circle"></i> Note:</strong> This is a demo preview showing what your invoices will look like. To generate a real invoice, place an order and click the invoice buttons on the success page.
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="invoice-preview">
                    <div class="invoice-header">
                        <h2>FRESH MART</h2>
                        <p>Your Fresh Grocery Store</p>
                    </div>
                    
                    <h3 class="invoice-title">TAX INVOICE</h3>
                    
                    <div class="info-section">
                        <div class="info-box">
                            <h5>Bill To:</h5>
                            <p><strong>John Doe</strong></p>
                            <p>Email: john@example.com</p>
                            <p>Phone: +91 9876543210</p>
                            <p>Address: 123 Main Street, City, State - 123456</p>
                        </div>
                        
                        <div class="info-box">
                            <h5>Invoice Details:</h5>
                            <p><strong>Invoice #:</strong> ORD-0067-20260320-4521</p>
                            <p><strong>Date:</strong> 20 Mar 2026</p>
                            <p><strong>Payment:</strong> Paid</p>
                        </div>
                    </div>
                    
                    <h5 style="color: #374151; font-weight: 600; margin-bottom: 15px;">Order Items</h5>
                    
                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th style="width: 5%; text-align: center;">#</th>
                                <th style="width: 40%;">Product Name</th>
                                <th style="width: 15%; text-align: right;">Price</th>
                                <th style="width: 10%; text-align: center;">Qty</th>
                                <th style="width: 15%; text-align: right;">Discount</th>
                                <th style="width: 15%; text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">1</td>
                                <td>Fresh Apples</td>
                                <td style="text-align: right;">Rs 120.00</td>
                                <td style="text-align: center;">2 kg</td>
                                <td style="text-align: right;">10% (Rs 24.00)</td>
                                <td style="text-align: right;"><strong>Rs 216.00</strong></td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">2</td>
                                <td>Bananas</td>
                                <td style="text-align: right;">Rs 50.00</td>
                                <td style="text-align: center;">1 dozen</td>
                                <td style="text-align: right;">-</td>
                                <td style="text-align: right;"><strong>Rs 50.00</strong></td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">3</td>
                                <td>Orange</td>
                                <td style="text-align: right;">Rs 80.00</td>
                                <td style="text-align: center;">1 kg</td>
                                <td style="text-align: right;">5% (Rs 4.00)</td>
                                <td style="text-align: right;"><strong>Rs 76.00</strong></td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">4</td>
                                <td>Fresh Milk</td>
                                <td style="text-align: right;">Rs 60.00</td>
                                <td style="text-align: center;">2 liter</td>
                                <td style="text-align: right;">-</td>
                                <td style="text-align: right;"><strong>Rs 120.00</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="total-section">
                        <div class="total-row">
                            <div class="label">Subtotal:</div>
                            <div class="value">Rs 490.00</div>
                        </div>
                        <div class="total-row discount-text">
                            <div class="label">Discount:</div>
                            <div class="value">- Rs 28.00</div>
                        </div>
                        <div class="total-row grand-total">
                            <div class="label">Grand Total:</div>
                            <div class="value">Rs 462.00</div>
                        </div>
                    </div>
                    
                    <div class="terms">
                        <strong>Terms & Conditions:</strong><br>
                        All products are subject to availability. Prices are inclusive of all taxes. Please check items upon delivery. For any queries, contact our customer support.
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box">
                    <h4><i class="fa fa-check-circle"></i> Features</h4>
                    <ul class="feature-list">
                        <li><i class="fa fa-file-pdf-o"></i> Professional PDF generation</li>
                        <li><i class="fa fa-eye"></i> HTML preview option</li>
                        <li><i class="fa fa-download"></i> Auto-download PDF</li>
                        <li><i class="fa fa-print"></i> Print from browser</li>
                        <li><i class="fa fa-envelope"></i> Email invoice</li>
                        <li><i class="fa fa-shield"></i> Secure & validated</li>
                        <li><i class="fa fa-mobile"></i> Mobile responsive</li>
                        <li><i class="fa fa-tachometer"></i> Fast generation</li>
                    </ul>
                </div>
                
                <div class="feature-box">
                    <h4><i class="fa fa-info-circle"></i> Invoice Includes</h4>
                    <ul class="feature-list">
                        <li><i class="fa fa-building"></i> Store branding</li>
                        <li><i class="fa fa-user"></i> Customer details</li>
                        <li><i class="fa fa-hashtag"></i> Order number</li>
                        <li><i class="fa fa-calendar"></i> Order date</li>
                        <li><i class="fa fa-list"></i> Product list</li>
                        <li><i class="fa fa-percent"></i> Discounts applied</li>
                        <li><i class="fa fa-calculator"></i> Total calculations</li>
                        <li><i class="fa fa-file-text"></i> Terms & conditions</li>
                    </ul>
                </div>
                
                <div class="feature-box">
                    <h4><i class="fa fa-rocket"></i> How to Use</h4>
                    <ol style="padding-left: 20px; color: #374151;">
                        <li>Place an order</li>
                        <li>Go to success page</li>
                        <li>Click "View Invoice"</li>
                        <li>Review your invoice</li>
                        <li>Download, print, or email</li>
                    </ol>
                </div>
                
                <div class="feature-box">
                    <h4><i class="fa fa-cog"></i> Customization</h4>
                    <p style="color: #6b7280; font-size: 0.9rem;">
                        Easy to customize:
                    </p>
                    <ul style="color: #374151; font-size: 0.9rem;">
                        <li>Store name & logo</li>
                        <li>Color scheme</li>
                        <li>Layout & design</li>
                        <li>Terms & conditions</li>
                        <li>Additional fields</li>
                    </ul>
                </div>
                
                <div class="feature-box" style="background: #10b981; color: white;">
                    <h4 style="color: white;"><i class="fa fa-check"></i> Ready to Use!</h4>
                    <p style="margin: 0;">
                        The invoice system is fully integrated and ready. Place a test order to see it in action!
                    </p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-success btn-lg" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; padding: 12px 30px; font-weight: 600;">
                <i class="fa fa-home"></i> Back to Home
            </a>
            <a href="products.php" class="btn btn-primary btn-lg" style="padding: 12px 30px; font-weight: 600;">
                <i class="fa fa-shopping-cart"></i> Start Shopping
            </a>
        </div>
        
        <div class="mt-5 text-center">
            <h4 style="color: #374151;">Documentation</h4>
            <p style="color: #6b7280;">Check these files for detailed information:</p>
            <div class="btn-group-vertical" style="display: inline-block;">
                <a href="#" class="btn btn-outline-success" onclick="alert('Open: INVOICE_README.md'); return false;">
                    <i class="fa fa-book"></i> Main Documentation
                </a>
                <a href="#" class="btn btn-outline-success" onclick="alert('Open: INVOICE_QUICK_START.md'); return false;">
                    <i class="fa fa-rocket"></i> Quick Start Guide
                </a>
                <a href="#" class="btn btn-outline-success" onclick="alert('Open: INVOICE_TESTING_GUIDE.md'); return false;">
                    <i class="fa fa-check-square"></i> Testing Guide
                </a>
                <a href="#" class="btn btn-outline-success" onclick="alert('Open: INVOICE_VISUAL_GUIDE.md'); return false;">
                    <i class="fa fa-eye"></i> Visual Guide
                </a>
            </div>
        </div>
    </div>
</body>
</html>
