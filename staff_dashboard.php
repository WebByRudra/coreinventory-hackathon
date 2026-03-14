<?php
session_start();
include 'db.php';

// Only staff can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'staff'){
    header("Location: index.php");
    exit();
}

// Total products
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"))['total'];

// Low stock products
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE stock < 5"))['total'];

?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css">
    <title>Staff Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?> (Staff)</h2>

    <div style="display:flex; gap:30px;">
        <div style="border:1px solid #000; padding:20px;">
            <h3>Total Products</h3>
            <p><?php echo $total_products; ?></p>
        </div>
        <div style="border:1px solid #000; padding:20px;">
            <h3>Low Stock</h3>
            <p><?php echo $low_stock; ?></p>
        </div>
    </div>

    <br><br>
    <a href="products.php">View Products</a> |
    <a href="stock_out.php">Stock Out</a> |
    <a href="logout.php">Logout</a>
</body>
</html>