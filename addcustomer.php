<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    mysqli_query($conn, "INSERT INTO users (name,email,phone,role) VALUES ('$name','$email','$phone','customer')");
    header("Location: customers.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Customer | UrbanDrive Admin</title>
<link rel="stylesheet" href="adashboard.css">
<style>
/* Form container inside dashboard style */
.form-container {
    max-width: 600px;
    margin: 50px auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}

.form-container h2 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 24px;
    color: #333;
}

.form-container input[type=text],
.form-container input[type=email] {
    width: 100%;
    padding: 12px;
    margin: 8px 0 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

.form-container button {
    width: 100%;
    padding: 12px;
    background: #e67f0a;
    border: none;
    color: white;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}
.form-container button:hover {
    background: #45a049;
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
        <a href="bookings.php">Bookings</a>
        <a href="customers.php">Customers</a>
        <a href="profile_admin.php">Profile</a>
        <a href="settings.php">Settings</a>
        <a class="logout" href="logout.php">Logout</a>
    </nav>
</header>

<main class="page-content">
    <div class="form-container">
        <h2>Add New Customer</h2>
        <form method="post">
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone">
            <button type="submit" name="add">Add Customer</button>
        </form>
    </div>
</main>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

</body>
</html>
