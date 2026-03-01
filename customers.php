<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';
$customers = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | UrbanDrive</title>
<link rel="stylesheet" href="adashboard.css">
<style>
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

.btn-primary {
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

.btn-primary:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px rgba(255, 106, 0, 0.5);
}

/* --- CUSTOMER GRID (4 Columns) --- */
.customer-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
}

/* --- CUSTOMER CARD --- */
.customer-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.08);
    transition: all 0.5s ease;
    border: 1px solid #eee;
    text-align: center;
}

.customer-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    border-color: #ff6a00;
}

/* --- PROFILE PICTURE --- */
.customer-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    margin: 0 auto 20px;
    overflow: hidden;
    background: linear-gradient(135deg, #ff6a00, #ff914d);
    display: flex;
    align-items: center;
    justify-content: center;
}

.customer-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.customer-avatar .initials {
    font-size: 32px;
    font-weight: 800;
    color: white;
    text-transform: uppercase;
}

/* --- CUSTOMER INFO --- */
.customer-card h3 {
    margin: 0 0 8px;
    font-size: 1.2rem;
    color: #1a1a2e;
    font-weight: 800;
}

.customer-card p {
    margin: 6px 0;
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 500;
}

.customer-card .role {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 15px;
    margin-top: 10px;
}

.role-admin {
    background: #d4edda;
    color: #155724;
}

.role-customer {
    background: #cce5ff;
    color: #004085;
}

/* --- ACTIONS --- */
.customer-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.customer-actions a {
    flex: 1;
    display: inline-block;
    padding: 12px;
    border-radius: 10px;
    text-decoration: none;
    color: white;
    font-size: 13px;
    font-weight: 700;
    text-align: center;
    transition: all 0.3s ease;
}

.customer-actions a.edit {
    background: #4361ee;
}

.customer-actions a.edit:hover {
    background: #3a56d4;
    transform: translateY(-2px);
}

.customer-actions a.delete {
    background: #e63946;
}

.customer-actions a.delete:hover {
    background: #c1121f;
    transform: translateY(-2px);
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
    .customer-grid { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 1200px) {
    .customer-grid { grid-template-columns: repeat(2, 1fr); }
    .page-content { padding: 40px 60px; }
}

@media (max-width: 768px) {
    .customer-grid { 
        grid-template-columns: 1fr; 
        gap: 20px;
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
        <a href="reports.php" class="active">Dashboard</a>
        <a href="addcar.php">Add Cars</a>
        <a href="viewcar.php">View Cars</a>
        <a href="bookings.php">Bookings</a>
        <a href="customers.php" class="active">Customers</a>
        <a href="profile_admin.php">Profile</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php" class="logout">Logout</a>
    </nav>
</header>

<main class="container">

   <div class="page-header">
    <h1>Customers</h1>
    <a href="addcustomer.php" class="btn-primary">+ Add Customer</a>
</div>

<?php if(mysqli_num_rows($customers) == 0): ?>
    <div class="empty">No customers found.</div>
<?php else: ?>
    <div class="customer-grid">
        <?php while ($customer = mysqli_fetch_assoc($customers)) { ?>
            <div class="customer-card">
                
                <!-- Avatar -->
                <div class="customer-avatar">
                    <?php if(!empty($customer['profile_pic'])): ?>
                        <img src="uploads/<?= htmlspecialchars($customer['profile_pic']) ?>" alt="Profile">
                    <?php else: ?>
                        <span class="initials">
                            <?= strtoupper(substr($customer['name'], 0, 2)) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Info -->
                <h3><?= htmlspecialchars($customer['name']) ?></h3>
                
                <span class="role role-<?= strtolower($customer['role']) ?>">
                    <?= ucfirst($customer['role']) ?>
                </span>

                <p>📧 <?= htmlspecialchars($customer['email']) ?></p>
                <p>📱 <?= htmlspecialchars($customer['phone'] ?? 'N/A') ?></p>
                <p>📅 <?= date("M d, Y", strtotime($customer['created_at'])) ?></p>

                <!-- Actions -->
                <div class="customer-actions">
                    <a href="editcustomer.php?id=<?= $customer['id'] ?>" class="edit">Edit</a>
                    <a href="deletecustomer.php?id=<?= $customer['id'] ?>" class="delete" onclick="return confirm('Delete this customer?')">Delete</a>
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
