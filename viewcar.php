<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';
$cars = mysqli_query($conn, "SELECT * FROM cars");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Cars | UrbanDrive Admin</title>
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

        .add-btn {
            background: linear-gradient(135deg, #ff6a00, #ff914d);
            color: white;
            padding: 18px 36px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 17px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(255, 106, 0, 0.4);
        }

        .add-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(255, 106, 0, 0.5);
        }

        /* --- CAR GRID (3 Columns) --- */
        .car-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 50px;
        }

        /* --- CAR CARD --- */
        .car-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.08);
            transition: all 0.5s ease;
            border: 1px solid #eee;
        }

        .car-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.18);
            border-color: #ff6a00;
        }

        /* --- IMAGE BOX --- */
        .image-box {
            width: 100%;
            height: 280px;
            overflow: hidden;
            position: relative;
            background: #f5f5f5;
        }

        .image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s ease;
        }

        .car-card:hover .image-box img {
            transform: scale(1.15);
        }

        /* --- PLACEHOLDER --- */
        .no-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            color: #adb5bd;
        }

        .no-image-placeholder svg {
            width: 70px;
            height: 70px;
            margin-bottom: 10px;
            opacity: 0.4;
        }

        .no-image-placeholder span {
            font-weight: 700;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* --- CARD DETAILS --- */
        .car-details {
            padding: 35px;
            text-align: left;
        }

        .car-details h3 {
            margin: 0 0 12px;
            font-size: 1.5rem;
            color: #1a1a2e;
            font-weight: 800;
            line-height: 1.3;
        }

        .car-details .meta {
            color: #6c757d;
            font-size: 1rem;
            margin: 6px 0;
            font-weight: 500;
        }

        .car-details .price {
            color: #e74c3c;
            font-weight: 900;
            font-size: 1.7rem;
            margin: 20px 0;
            display: block;
        }

        .car-details .price span {
            font-size: 0.9rem;
            color: #999;
            font-weight: 500;
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

        .status.available {
            background: #d4edda;
            color: #155724;
        }

        .status.rented {
            background: #f8d7da;
            color: #721c24;
        }

        /* --- ACTIONS --- */
        .actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .actions a {
            flex: 1;
            display: inline-block;
            padding: 16px;
            border-radius: 12px;
            text-decoration: none;
            color: white;
            font-size: 15px;
            font-weight: 700;
            text-align: center;
            transition: all 0.3s ease;
        }

        .actions a.edit {
            background: #4361ee;
        }

        .actions a.edit:hover {
            background: #3a56d4;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
        }

        .actions a.delete {
            background: #e63946;
        }

        .actions a.delete:hover {
            background: #c1121f;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(230, 57, 70, 0.4);
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 1600px) {
            .car-grid { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 1200px) {
            .car-grid { grid-template-columns: repeat(2, 1fr); }
            .page-content { padding: 40px 60px; }
        }

        @media (max-width: 768px) {
            .car-grid { 
                grid-template-columns: 1fr; 
                gap: 30px;
            }
            .page-content {
                padding: 30px 20px;
            }
            .image-box {
                height: 220px;
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
        <a href="viewcar.php" class="active">View Cars</a>
        <a href="bookings.php">Bookings</a>
        <a href="customers.php">Customers</a>
        <a href="profile_admin.php">Profile</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<div class="page-content">

    <div class="page-header">
        <h1>Car List</h1>
        <a href="addcar.php" class="add-btn">+ Add New Car</a>
    </div>

    <div class="car-grid">
        <?php while ($car = mysqli_fetch_assoc($cars)) { ?>
        <div class="car-card">

            <div class="image-box">
                <?php if (!empty($car['car_image']) && file_exists("uploads/".$car['car_image'])) { ?>
                    <img src="uploads/<?php echo $car['car_image']; ?>">
                <?php } else { ?>
                    <div class="no-image-placeholder">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                        </svg>
                        <span>No Image</span>
                    </div>
                <?php } ?>
            </div>

            <div class="car-details">
                <h3><?php echo htmlspecialchars($car['car_name']); ?></h3>
                <p class="meta">Brand: <?php echo htmlspecialchars($car['brand']); ?></p>
                <p class="meta">ID: <?php echo $car['car_id']; ?></p>
                
                <span class="status <?php echo strtolower($car['status']); ?>">
                    <?php echo $car['status']; ?>
                </span>

                <p class="price">₱<?php echo number_format($car['price_per_day'],2); ?> <span>/ day</span></p>

                <div class="actions">
                    <a href="edit_car.php?id=<?php echo $car['car_id']; ?>" class="edit">Edit</a>
                    <a href="delete_car.php?id=<?php echo $car['car_id']; ?>" class="delete" onclick="return confirm('Delete this car?')">Delete</a>
                </div>
            </div>

        </div>
        <?php } ?>

    </div>

</div>

</body>
</html>