<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once "../db.php";

// Only customer can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['booking_id'])) die("Booking ID missing");

$booking_id = intval($_GET['booking_id']);

// Fetch booking + car info
$result = mysqli_query($conn, "
    SELECT b.booking_id, b.booking_date, b.return_date, b.status, b.total_price,
           b.payment_status, b.payment_method, b.payment_date,
           c.car_name, c.brand, c.price_per_day, c.car_image
    FROM bookings b
    JOIN cars c ON b.car_id = c.car_id
    WHERE b.booking_id = $booking_id AND b.id = {$_SESSION['id']}
");
$booking = mysqli_fetch_assoc($result);

if (!$booking) die("Booking not found");

// Calculate rental days
$start = new DateTime($booking['booking_date']);
$end = new DateTime($booking['return_date']);
$days = $end->diff($start)->days + 1; // include start day

$subtotal = $booking['price_per_day'] * $days;
$tax_rate = 0.12; // 12% VAT example
$tax = $subtotal * $tax_rate;
$total = $subtotal + $tax;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Receipt | UrbanDrive</title>
<link rel="stylesheet" href="../adashboard.css">
<style>
body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
.receipt-container { max-width: 800px; margin: 50px auto; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 30px rgba(0,0,0,0.1); }

.receipt-header { background: linear-gradient(135deg, #ff6a00, #ff914d); color: #fff; text-align: center; padding: 30px 20px; }
.receipt-header h1 { margin:0; font-size:28px; }
.receipt-header p { margin:5px 0 0; font-size:16px; }

.receipt-car { text-align:center; padding:20px; }
.receipt-car img { max-width:100%; height:200px; object-fit:cover; border-radius:15px; }

.receipt-info { padding: 20px 30px; }
.receipt-info p { margin: 8px 0; font-size: 16px; }
.receipt-info p strong { width: 150px; display:inline-block; }

.invoice-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.invoice-table th, .invoice-table td { border: 1px solid #ddd; padding: 12px; text-align: center; }
.invoice-table th { background: #ff6a00; color: #fff; }
.invoice-total td { font-weight: bold; }

.print-btn { display:block; width:200px; margin:20px auto 30px; padding:10px 0; text-align:center; background:#ff6a00; color:#fff; text-decoration:none; border-radius:25px; font-weight:600; }
.print-btn:hover { opacity:0.9; }

@media print {
    body { background:#fff; margin:0; }
    .receipt-container { box-shadow:none; margin:0; border-radius:0; }
    .print-btn { display:none; }
    a { text-decoration:none; color:black; }
}
</style>
</head>
<body>

<div class="receipt-container">

    <div class="receipt-header">
        <h1>UrbanDrive Invoice</h1>
        <p>Booking #<?= $booking['booking_id'] ?></p>
    </div>

    <div class="receipt-car">
        <?php if(!empty($booking['car_image'])): ?>
            <img src="../uploads/<?= htmlspecialchars($booking['car_image']) ?>" alt="Car Image">
        <?php else: ?>
            <img src="../assets/car-placeholder.png" alt="Car Image">
        <?php endif; ?>
    </div>

    <div class="receipt-info">
        <p><strong>Customer:</strong> <?= $_SESSION['username'] ?? 'Customer' ?></p>
        <p><strong>Car:</strong> <?= htmlspecialchars($booking['brand'] . ' ' . $booking['car_name']) ?></p>
        <p><strong>Rental Period:</strong> <?= date('M d, Y', strtotime($booking['booking_date'])) ?> → <?= date('M d, Y', strtotime($booking['return_date'])) ?> (<?= $days ?> days)</p>
        <p><strong>Status:</strong> <?= $booking['status'] ?></p>
        <p><strong>Payment Status:</strong> <?= $booking['payment_status'] ?></p>
        <p><strong>Payment Date:</strong> <?= !empty($booking['payment_date']) ? date('M d, Y H:i', strtotime($booking['payment_date'])) : 'N/A' ?></p>
    </div>

    <table class="invoice-table">
        <tr>
            <th>Description</th>
            <th>Days</th>
            <th>Price/Day (₱)</th>
            <th>Amount (₱)</th>
        </tr>
        <tr>
            <td><?= htmlspecialchars($booking['brand'] . ' ' . $booking['car_name']) ?> Rental</td>
            <td><?= $days ?></td>
            <td><?= number_format($booking['price_per_day'], 2) ?></td>
            <td><?= number_format($subtotal, 2) ?></td>
        </tr>
        <tr class="invoice-total">
            <td colspan="3">Tax (12%)</td>
            <td><?= number_format($tax, 2) ?></td>
        </tr>
        <tr class="invoice-total">
            <td colspan="3">Total</td>
            <td><?= number_format($total, 2) ?></td>
        </tr>
    </table>
<a href="download_receipt.php?booking_id=<?= $b['booking_id'] ?>" class="action-btn btn-receipt">
    📥 Download PDF
</a>

</div>

</body>
</html>