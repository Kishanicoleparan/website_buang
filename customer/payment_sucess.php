<?php
session_start();
require_once "../db.php";

// Only customer can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

// Get booking ID from URL
if (!isset($_GET['booking_id'])) {
    die("Booking ID missing");
}
$booking_id = intval($_GET['booking_id']);

// Fetch booking to verify it exists and belongs to this user
$result = mysqli_query($conn, "SELECT * FROM bookings WHERE booking_id = $booking_id AND id = {$_SESSION['id']}");
$booking = mysqli_fetch_assoc($result);
if (!$booking) {
    die("Booking not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Success | UrbanDrive</title>
<style>
body { font-family: Arial, sans-serif; text-align:center; padding:50px; background:#f7f7f7; }
h1 { color:#4caf50; }
p { font-size:18px; margin:20px 0; }
.btn {
    display:inline-block;
    padding:10px 25px;
    margin:10px;
    color:#fff;
    text-decoration:none;
    border-radius:25px;
    font-weight:600;
    transition:opacity 0.3s;
}
.btn-back { background:#ff6a00; }
.btn-receipt { background:#4caf50; }
.btn:hover { opacity:0.85; }
</style>
</head>
<body>
    <h1>✔ GrabPay Payment Successfully Received</h1>
    <p>You may now go back to your bookings or view your receipt.</p>

    <a href="my_bookings.php" class="btn btn-back">⬅ Back to My Bookings</a>
    <a href="download_receipt.php?booking_id=<?= $booking['booking_id'] ?>" class="btn btn-receipt">📄 View Receipt</a>
</body>
</html>