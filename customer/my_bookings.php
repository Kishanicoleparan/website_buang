<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['id'];

$bookings = mysqli_query($conn, "
    SELECT b.booking_id, b.booking_date, b.return_date, b.status, b.total_price,
           b.payment_status, b.payment_method, b.payment_date,
           c.car_name, c.brand, c.model, c.price_per_day, c.car_image
    FROM bookings b
    JOIN cars c ON b.car_id = c.car_id
    WHERE b.id = $user_id
    ORDER BY b.booking_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Bookings | UrbanDrive</title>
<link rel="stylesheet" href="../adashboard.css">
<style>
.page-content {
    max-width: 1400px; /* Wider container */
    margin: 40px auto;
    padding: 0 30px;
}

.page-title {
    text-align: left;
    font-size: 36px;
    margin-bottom: 40px;
    color: #2b1d16;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 15px;
}

/* --- BIGGER GRID LAYOUT: 4 Columns --- */
.bookings-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr); 
    gap: 30px; /* Bigger gap */
    padding: 20px 0;
    margin-bottom: 40px;
}

/* --- BIGGER CARD DESIGN --- */
.booking-card {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 1px solid #eaeaea;
    display: flex;
    flex-direction: column;
}

.booking-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    border-color: #ff6a00;
}

/* --- BIGGER IMAGE --- */
.car-image {
    width: 100%;
    height: 220px; /* Much bigger height */
    overflow: hidden;
    position: relative;
    background-color: #f4f4f4;
}

.car-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.booking-card:hover .car-image img {
    transform: scale(1.08);
}

/* --- PLACEHOLDER --- */
.no-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    color: #adb5bd;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 1px;
}

/* --- BIGGER INFO SECTION --- */
.booking-info {
    padding: 25px; /* Bigger padding */
    text-align: left;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.booking-info h3 {
    margin: 0 0 10px;
    color: #2c3e50;
    font-size: 1.3rem; /* Bigger font */
    font-weight: 700;
    line-height: 1.4;
}

.booking-info .dates {
    color: #666;
    font-size: 0.95rem; /* Bigger font */
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* --- Price & Status Row --- */
.card-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.price {
    font-weight: 800;
    color: #e74c3c;
    font-size: 1.4rem; /* Bigger price */
}

.status-badge {
    padding: 6px 12px;
    border-radius: 6px;
    color: #fff;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
}

.status-pending { background: #f39c12; }
.status-approved { background: #3498db; }
.status-completed { background: #27ae60; }
.status-cancelled { background: #c0392b; }

/* --- BIGGER BUTTONS --- */
.booking-actions {
    margin-top: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.action-btn {
    width: 100%;
    padding: 14px; /* Bigger button */
    border-radius: 10px;
    font-weight: 700;
    text-decoration: none;
    text-align: center;
    font-size: 0.95rem;
    transition: all 0.2s;
    letter-spacing: 0.5px;
}

.btn-cancel { 
    background: #fff0f0; 
    color: #c0392b; 
    border: 1px solid #ffcccc;
}
.btn-cancel:hover { background: #ffe6e6; }

.btn-pay { 
    background: #2c3e50; 
    color: #fff; 
}
.btn-pay:hover { background: #34495e; transform: scale(1.02); }

.btn-receipt { 
    background: #27ae60; 
    color: #fff; 
}
.btn-receipt:hover { background: #219150; transform: scale(1.02); }

.empty {
    text-align: center;
    font-size: 20px;
    color: #777;
    padding: 80px 0;
}

/* --- RESPONSIVE --- */
@media (max-width: 1200px) {
    .bookings-grid { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 900px) {
    .bookings-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 600px) {
    .bookings-grid { 
        grid-template-columns: 1fr; 
        gap: 20px;
    }
    .car-image { height: 200px; }
    .page-title { text-align: center; }
}
</style>
</head>
<body>

<header class="admin-header">
    <div class="logo">Urban<span>Drive</span></div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="my_bookings.php" class="active">My Bookings</a>
        <a href="available_cars.php">Available Cars</a>
        <a href="profile.php">Profile</a>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<main class="page-content">
    <h1 class="page-title">My Bookings</h1>

    <?php if(mysqli_num_rows($bookings) == 0): ?>
        <div class="empty">You have no bookings yet. <a href="available_cars.php" style="color:#ff6a00;">Browse available cars</a></div>
    <?php else: ?>
        <div class="bookings-grid">
            <?php while($b = mysqli_fetch_assoc($bookings)): ?>
                <div class="booking-card">

                    <div class="car-image">
                        <?php if(!empty($b['car_image'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($b['car_image']) ?>" alt="Car Image">
                        <?php else: ?>
                            <div class="no-image-placeholder">NO IMAGE</div>
                        <?php endif; ?>
                    </div>

                    <div class="booking-info">
                        <h3><?= htmlspecialchars($b['brand'] . ' ' . $b['model']) ?></h3>
                        <p class="dates">📅 <?= date('M d, Y', strtotime($b['booking_date'])) ?> - <?= date('M d, Y', strtotime($b['return_date'])) ?></p>
                        
                        <div class="card-meta">
                            <div class="price">₱<?= number_format($b['total_price'], 2) ?></div>
                            <?php 
                            $status_class = strtolower($b['status']);
                            echo "<div class='status-badge status-$status_class'>{$b['status']}</div>";
                            ?>
                        </div>

                        <div class="booking-actions">
                            <?php if($b['status'] == 'Pending' && $b['payment_status'] == 'pending'): ?>
                                <a href="create_payment.php?booking_id=<?= $b['booking_id'] ?>" class="action-btn btn-pay">Pay Now</a>
                                <a href="cancel_booking.php?id=<?= $b['booking_id'] ?>" class="action-btn btn-cancel" onclick="return confirm('Cancel this booking?')">Cancel</a>
                            <?php elseif($b['payment_status'] == 'paid'): ?>
                                <a href="payment_receipt.php?booking_id=<?= $b['booking_id'] ?>" class="action-btn btn-receipt">View Receipt</a>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</main>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

</body>
</html>