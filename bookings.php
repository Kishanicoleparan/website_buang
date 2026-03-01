<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch all bookings with customer and car info
$bookings = mysqli_query($conn, "
    SELECT b.booking_id, b.booking_date, b.return_date, b.status AS booking_status, b.total_price,
           c.car_name, c.brand, c.car_image, cu.name AS customer_name
    FROM bookings b
    JOIN cars c ON b.car_id = c.car_id
    JOIN users cu ON b.id = cu.id
    ORDER BY b.booking_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bookings | UrbanDrive Admin</title>
<link rel="stylesheet" href="adashboard.css">

<style>
    body {
        background: #f8f9fa;
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
    }

    .page-content {
        padding: 60px 100px;
        max-width: 2200px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 50px;
        padding-bottom: 25px;
        border-bottom: 3px solid #e0e0e0;
    }

    .page-header h1 {
        font-size: 42px;
        color: #1a1a2e;
        margin: 0;
        font-weight: 800;
    }

    /* --- BOOKING GRID (3 Columns) --- */
    .booking-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 40px;
    }

    /* --- BIG BOOKING CARD --- */
    .booking-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0,0,0,0.08);
        transition: all 0.5s ease;
        border: 1px solid #eee;
    }

    .booking-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.18);
        border-color: #ff6a00;
    }

    /* --- CAR IMAGE --- */
    .booking-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .booking-card:hover img {
        transform: scale(1.1);
    }

    /* --- PLACEHOLDER --- */
    .no-image-box {
        width: 100%;
        height: 220px;
        background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-weight: 700;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* --- CARD CONTENT --- */
    .booking-info {
        padding: 30px;
        text-align: left;
    }

    .booking-info h3 {
        margin: 0 0 12px;
        font-size: 1.4rem;
        color: #1a1a2e;
        font-weight: 800;
    }

    .booking-info p {
        margin: 8px 0;
        color: #6c757d;
        font-size: 1rem;
        font-weight: 500;
    }

    .booking-info .price {
        font-weight: 900;
        font-size: 1.5rem;
        color: #e74c3c;
        margin: 15px 0;
    }

    /* --- STATUS BADGE --- */
    .status {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
    }

    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d4edda; color: #155724; }
    .status-completed { background: #d1ecf1; color: #0c5460; }
    .status-cancelled { background: #f8d7da; color: #721c24; }

    /* --- ACTIONS --- */
    .booking-actions {
        display: flex;
        gap: 12px;
        margin-top: 20px;
    }

    .booking-actions a {
        flex: 1;
        display: inline-block;
        padding: 14px;
        border-radius: 12px;
        text-decoration: none;
        color: white;
        font-size: 14px;
        font-weight: 700;
        text-align: center;
        transition: all 0.3s ease;
    }

    .booking-actions a.edit {
        background: #4361ee;
    }

    .booking-actions a.edit:hover {
        background: #3a56d4;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
    }

    .booking-actions a.delete {
        background: #e63946;
    }

    .booking-actions a.delete:hover {
        background: #c1121f;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(230, 57, 70, 0.4);
    }

    .empty {
        text-align: center;
        padding: 100px;
        color: #777;
        font-size: 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    /* --- RESPONSIVE --- */
    @media (max-width: 1600px) {
        .booking-grid { grid-template-columns: repeat(3, 1fr); }
    }

    @media (max-width: 1200px) {
        .booking-grid { grid-template-columns: repeat(2, 1fr); }
        .page-content { padding: 40px 60px; }
    }

    @media (max-width: 768px) {
        .booking-grid { 
            grid-template-columns: 1fr; 
            gap: 30px;
        }
        .page-content {
            padding: 30px 20px;
        }
        .page-header {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }
    }
</style>
</head>
<body>

<header class="admin-header">
    <div class="logo">Urban<span>Drive</span> Admin</div>
    <nav>
        <a href="reports.php">Dashboard</a>
        <a href="addcar.php">Add Car</a>
        <a href="viewcar.php">View Cars</a>
        <a href="bookings.php" class="active">Bookings</a>
        <a href="customers.php">Customers</a>
        <a href="profile_admin.php">Profile</a>
        <a href="settings.php">Settings</a>
        <a class="logout-btn" href="logout.php">Logout</a>
    </nav>
</header>

<main class="page-content">
    <div class="page-header">
        <h1>All Bookings</h1>
    </div>

    <?php if(mysqli_num_rows($bookings) == 0): ?>
        <div class="empty">No bookings found.</div>
    <?php else: ?>
        <div class="booking-grid">
            <?php while($booking = mysqli_fetch_assoc($bookings)) { ?>
                <div class="booking-card">
                    <?php if(!empty($booking['car_image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($booking['car_image']) ?>" alt="Car Image">
                    <?php else: ?>
                        <div class="no-image-box">No Image</div>
                    <?php endif; ?>
                    
                    <div class="booking-info">
                        <h3><?= htmlspecialchars($booking['car_name']) ?></h3>
                        <p>Brand: <?= htmlspecialchars($booking['brand']) ?></p>
                        <p>Customer: <?= htmlspecialchars($booking['customer_name']) ?></p>
                        <p>📅 <?= date('M d, Y', strtotime($booking['booking_date'])) ?> - <?= date('M d, Y', strtotime($booking['return_date'])) ?></p>
                        
                        <div class="price">₱<?= number_format($booking['total_price'], 2) ?></div>
                        
                        <div class="status <?= strtolower($booking['booking_status']) ?>"><?= $booking['booking_status'] ?></div>
                        
                        <div class="booking-actions">
                            <a href="edit_booking.php?id=<?= $booking['booking_id'] ?>" class="edit">Edit</a>
                            <a href="delete_booking.php?id=<?= $booking['booking_id'] ?>" class="delete" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php endif; ?>
</main>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

</body>
</html>