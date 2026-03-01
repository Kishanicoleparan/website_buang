<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once "../db.php";

$booking_id = intval($_GET['booking_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Failed</title>
<link rel="stylesheet" href="../adashboard.css">
</head>
<body>
<div class="page-content" style="text-align:center; padding:50px;">
    <h1>❌ Payment Failed!</h1>
    <p>Your payment for booking #<?= $booking_id ?> did not go through.</p>
    <a href="my_bookings.php" class="action-btn btn-cancel">Back to My Bookings</a>
</div>
</body>
</html>