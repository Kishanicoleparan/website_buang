<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

if (isset($_POST['add'])) {
    $car_name = mysqli_real_escape_string($conn, $_POST['car_name']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $status = $_POST['status'];

    $image_name = null;
    if (!empty($_FILES['car_image']['name'])) {
        $image_name = time() . '_' . $_FILES['car_image']['name'];
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        move_uploaded_file($_FILES['car_image']['tmp_name'], $target_dir . $image_name);
    }

    mysqli_query($conn, "INSERT INTO cars (car_name, brand, price_per_day, status, car_image)
                         VALUES ('$car_name', '$brand', '$price', '$status', '$image_name')");

    header("Location: viewcar.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Car | UrbanDrive Admin</title>
<link rel="stylesheet" href="addacar.css">
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
        <a class="logout" href="logout.php">Logout</a>
    </nav>
</header>

<main class="content">
    <h1>Add New Car</h1>

    <div class="addcar-wrapper">
        <!-- LEFT FORM -->
        <form class="car-form" method="post" enctype="multipart/form-data">
            <label>Car Name</label>
            <input type="text" name="car_name" required>

            <label>Brand</label>
            <input type="text" name="brand" required>

            <label>Price Per Day</label>
            <input type="number" step="0.01" name="price" required>

            <label>Status</label>
            <select name="status">
                <option value="Available">Available</option>
                <option value="Rented">Rented</option>
            </select>

            <label>Car Image</label>
            <input type="file" name="car_image" accept="image/*" onchange="previewImage(event)">

            <button type="submit" name="add">Add Car</button>
        </form>

        <!-- RIGHT IMAGE PREVIEW -->
        <div class="image-preview">
            <p>Car Image Preview</p>
            <img id="preview" src="../assets/car-placeholder.png" alt="Preview">
        </div>
    </div>
</main>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

<script>
function previewImage(event) {
    const img = document.getElementById('preview');
    img.src = URL.createObjectURL(event.target.files[0]);
}
</script>

</body>
</html>
