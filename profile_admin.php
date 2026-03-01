<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$id = $_SESSION['id'];

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$id"));

/* UPDATE PROFILE */
if(isset($_POST['update'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    /* IMAGE UPLOAD */
    if(!empty($_FILES['profile_pic']['name'])){

        $image_name = time() . "_" . $_FILES['profile_pic']['name'];
        $target = "../uploads/" . $image_name;

        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target);

        mysqli_query($conn, "UPDATE users SET profile_pic='$image_name' WHERE id=$id");
    }

    mysqli_query($conn, "
        UPDATE users SET 
        name='$name',
        email='$email',
        phone='$phone',
        address='$address'
        WHERE id=$id
    ");

    header("Location: profile.php");
    exit();
}

/* DELETE PROFILE PICTURE */
if(isset($_GET['delete_pic'])){
    mysqli_query($conn, "UPDATE users SET profile_pic=NULL WHERE id=$id");
    header("Location: profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>My Profile | UrbanDrive</title>
<link rel="stylesheet" href="adashboard.css">
<style>
    body {
        background: linear-gradient(to right, #fff5ec, #fffaf6);
    }

    .page-content {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
        min-height: auto;
    }

    .page-title {
        text-align: left;
        font-size: 36px;
        margin-bottom: 30px;
        color: #2b1d16;
    }

    /* --- MODERN PROFILE CARD --- */
    .profile-wrapper {
        display: flex;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }

    /* LEFT SIDE - Profile Picture */
    .profile-left {
        width: 35%;
        background: linear-gradient(135deg, #ff6a00, #ff914d);
        padding: 50px 30px;
        text-align: center;
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .profile-left img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid rgba(255,255,255,0.3);
        margin-bottom: 20px;
        background: white;
    }

    /* IMAGE PREVIEW STYLE */
    .profile-left #imagePreview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid rgba(255,255,255,0.3);
        margin-bottom: 20px;
        display: none; /* Hidden by default */
        background: white;
    }

    .profile-left h3 {
        font-size: 1.5rem;
        margin: 0 0 5px 0;
        font-weight: 700;
    }

    .profile-left p {
        font-size: 1rem;
        opacity: 0.9;
        margin-bottom: 20px;
    }

    /* RIGHT SIDE - Form */
    .profile-right {
        width: 65%;
        padding: 40px;
    }

    .profile-right h2 {
        font-size: 1.8rem;
        margin-bottom: 25px;
        color: #2b1d16;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 15px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border-radius: 10px;
        border: 1px solid #ddd;
        font-size: 1rem;
        transition: all 0.3s;
        background: #f9f9f9;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #ff6a00;
        background: white;
        box-shadow: 0 0 0 3px rgba(255,106,0,0.1);
    }

    .form-group textarea {
        height: 80px;
        resize: vertical;
    }

    /* FILE INPUT STYLE */
    .file-input-wrapper {
        position: relative;
    }
    
    .file-input {
        width: 100%;
        padding: 10px;
        background: #f9f9f9;
        border: 1px dashed #ccc;
        border-radius: 10px;
        cursor: pointer;
    }

    /* BUTTONS */
    .btn-primary {
        background: #ff6a00;
        color: white;
        padding: 14px 30px;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background: #e65a00;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255,106,0,0.3);
    }

    .btn-danger {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        border: 1px solid rgba(255,255,255,0.5);
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-danger:hover {
        background: white;
        color: #ff6a00;
    }

    /* MOBILE RESPONSIVE */
    @media(max-width: 768px) {
        .profile-wrapper {
            flex-direction: column;
        }

        .profile-left,
        .profile-right {
            width: 100%;
        }

        .profile-left {
            padding: 40px 20px;
        }
        
        .profile-left img,
        .profile-left #imagePreview {
            width: 120px;
            height: 120px;
        }

        .profile-right {
            padding: 30px 20px;
        }
    }
</style>
</head>
<body>

<header class="admin-header">
    <div class="logo">Urban<span>Drive</span></div>
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

<div class="page-content">
    <h1 class="page-title">My Profile</h1>

    <div class="profile-wrapper">
        
        <!-- LEFT SIDE -->
        <div class="profile-left">
            <?php if(!empty($user['profile_pic'])) { ?>
                <img id="currentImage" src="../uploads/<?= htmlspecialchars($user['profile_pic']) ?>">
            <?php } else { ?>
                <img id="currentImage" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjYzZjNmN2IiIHN0cm9rZS13aWR0aD0iMiI+PHBhdGggZD0iTTEyIDJDNi40OCAyIDIgNi40OCAyIDEyczQuNDggMTAgMTAgMTAgMTAtNC40OCAxMC0xMFMxNy41MiAyIDEyIDJ6bTAgMjBjLTMuMzEgMC02LTIuNjktNi02czIuNjktNiA2LTYgNiAyLjY5IDYgNi0yLjY5IDYtNiA2em0tOCAxMGMtMS4xMSAwLTIgLjg5LTIgMnMuODktMiAyLTIgMi0uODkgMi0yLS44OS0yLTItMnptOCAwYy0xLjExIDAtMiAuODktMiAydHMuODkgMiAyIDIgMi0uODkgMi0yLS44OS0yLTItMnoiLz48L3N2Zz4=">
            <?php } ?>

            <!-- Preview Image (Hidden by default) -->
            <img id="imagePreview" src="" alt="Preview">

            <h3><?= htmlspecialchars($user['name']) ?></h3>
            <p><?= ucfirst(htmlspecialchars($user['role'])) ?> Account</p>

            <?php if(!empty($user['profile_pic'])) { ?>
                <a href="?delete_pic=1" class="btn-danger">Delete Picture</a>
            <?php } ?>
        </div>

        <!-- RIGHT SIDE -->
        <div class="profile-right">
            <h2>Update Profile</h2>

            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Change Profile Picture</label>
                    <input type="file" name="profile_pic" class="file-input" accept="image/*" onchange="previewImage(event)">
                </div>

                <button type="submit" name="update" class="btn-primary">
                    Save Changes
                </button>

            </form>
        </div>

    </div>
</div>

<footer class="admin-footer">
    © 2026 UrbanDrive
</footer>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        var imageField = document.getElementById("imagePreview");
        var currentImage = document.getElementById("currentImage");
        
        reader.onload = function(){
            imageField.src = reader.result;
            imageField.style.display = "block";
            currentImage.style.display = "none";
        }
        
        if(event.target.files[0]){
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>

</body>
</html>