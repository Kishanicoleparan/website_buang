<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Summary counts
$total_cars = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM cars"))['total'];
$available_cars = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM cars WHERE status='Available'"))['total'];
$rented_cars = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM cars WHERE status='Rented'"))['total'];

$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='customer'"))['total'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings"))['total'];
$completed_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE status='Completed'"))['total'];

// Fetch all bookings with customer and car info
$bookings = mysqli_query($conn, "
    SELECT b.booking_id, b.booking_date, b.status,
           u.name AS customer_name, c.car_name
    FROM bookings b
    JOIN users u ON b.id = u.id
    JOIN cars c ON b.car_id = c.car_id
    ORDER BY b.booking_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports - Admin Dashboard | UrbanDrive</title>
<link rel="stylesheet" href="adashboard.css">
<link rel="stylesheet" href="css/reports.css">
</head>
<body>

<header class="admin-header">
    <div class="logo">Urban<span>Drive</span> Admin</div>
    <nav>
        <a href="reports.php" class="active">Dashboard</a>
        <a href="addcar.php">Add Car</a>
        <a href="viewcar.php">View Cars</a>
        <a href="bookings.php">Bookings</a>
        <a href="customers.php">Customers</a>
        <a href="profile_admin.php">Profile</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<main class="page-content">

    <h1>Reports</h1>

    <!-- Summary Cards -->
    <div class="stats">
        <div class="stat-card">
            <h3>Total Cars</h3>
            <p><?= $total_cars ?></p>
        </div>
        <div class="stat-card">
            <h3>Available Cars</h3>
            <p><?= $available_cars ?></p>
        </div>
        <div class="stat-card">
            <h3>Rented Cars</h3>
            <p><?= $rented_cars ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Customers</h3>
            <p><?= $total_customers ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Bookings</h3>
            <p><?= $total_bookings ?></p>
        </div>
        <div class="stat-card">
            <h3>Completed Bookings</h3>
            <p><?= $completed_bookings ?></p>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card" style="margin-top: 50px;">
        <h2>All Bookings</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Car</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($booking = mysqli_fetch_assoc($bookings)) { ?>
                <tr>
                    <td><?= $booking['booking_id'] ?></td>
                    <td><?= $booking['customer_name'] ?></td>
                    <td><?= $booking['car_name'] ?></td>
                    <td><?= date("M d, Y", strtotime($booking['booking_date'])) ?></td>
                    <td>
                        <?php 
                        $status = $booking['status'];
                        if ($status=="Pending") echo "<span style='color:orange;font-weight:bold;'>Pending</span>";
                        elseif ($status=="Approved") echo "<span style='color:blue;font-weight:bold;'>Approved</span>";
                        elseif ($status=="Completed") echo "<span style='color:green;font-weight:bold;'>Completed</span>";
                        elseif ($status=="Cancelled") echo "<span style='color:red;font-weight:bold;'>Cancelled</span>";
                        ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</main>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

</body>
</html>
