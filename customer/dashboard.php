<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

$customer_id = $_SESSION['id'];
$customer_name = $_SESSION['name'];

// Fetch stats
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE id='$customer_id'"))['total'];
$active_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE id='$customer_id' AND status='Approved'"))['total'];
$completed_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE id='$customer_id' AND status='Completed'"))['total'];
$total_spent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as total FROM bookings WHERE id='$customer_id' AND status='Completed'"))['total'];

// Fetch latest 4 available cars
$cars = mysqli_query($conn, "SELECT * FROM cars WHERE status='Available' ORDER BY car_id DESC LIMIT 4");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Dashboard | UrbanDrive</title>
<link rel="stylesheet" href="../adashboard.css">
<link rel="stylesheet" href="customer.css">
<link rel="stylesheet" href="available_cars.css">
</head>
<body>

<header class="admin-header">
    <div class="logo">Urban<span>Drive</span></div>
    <nav>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="my_bookings.php">My Bookings</a>
        <a href="available_cars.php">Available Cars</a>
        <a href="profile.php">Profile</a>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<div class="page-content">
    <div class="container">

        <!-- Welcome -->
        <h1>Welcome, <?= htmlspecialchars($customer_name) ?>!</h1>

        <!-- Stats Cards -->
        <div class="stats">
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <p><?= $total_bookings ?></p>
            </div>
            <div class="stat-card">
                <h3>Active Bookings</h3>
                <p><?= $active_bookings ?></p>
            </div>
            <div class="stat-card">
                <h3>Completed Bookings</h3>
                <p><?= $completed_bookings ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Spent</h3>
               <p>₱<?= number_format($total_spent ?? 0, 2) ?></p>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="stats" style="justify-content:center; margin:40px 0;">
            <a href="my_bookings.php" class="rent-btn" style="padding:12px 25px;">View My Bookings</a>
            <a href="available_cars.php" class="rent-btn" style="padding:12px 25px;">Browse Cars</a>
            <a href="profile.php" class="rent-btn" style="padding:12px 25px;">Edit Profile</a>
        </div>

        <!-- Latest Cars -->
        <h2>Recently Added Cars</h2>
        <div class="car-grid">
            <?php while($car = mysqli_fetch_assoc($cars)) { ?>
                <div class="car-card">
                    <div class="car-image">
                        <img src="../uploads/<?= $car['car_image'] ?: 'car-placeholder.png' ?>" alt="<?= $car['car_name'] ?>">
                    </div>
                    <div class="car-info">
                        <h3><?= $car['car_name'] ?></h3>
                        <p>Brand: <?= $car['brand'] ?></p>
                        <p class="car-price">₱<?= number_format($car['price_per_day'],2) ?>/day</p>
                        <a href="rent_process.php?id=<?= $car['car_id'] ?>" class="rent-btn">Rent</a>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>
</div>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

</body>
</html>
