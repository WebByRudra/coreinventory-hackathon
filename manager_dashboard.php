<?php
session_start();
include 'db.php';

// Only manager can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'manager'){
    header("Location: index.php");
    exit();
}

// Total products
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"))['total'];

// Low stock products (stock < 5 OR negative)
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE stock < 5"))['total'];

// Pending Receipts (from stock_in table, status='pending')
$pending_receipts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM stock_in WHERE status='pending'"))['total'];

// Pending Deliveries (from stock_out table, status='pending')
$pending_deliveries = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM stock_out WHERE status='pending'"))['total'];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f5f5f5; }
        h2 { margin-bottom: 20px; }
        .kpi-container { display: flex; gap: 30px; flex-wrap: wrap; }
        .kpi { border: 1px solid #000; padding: 20px; width: 200px; border-radius: 8px; background-color: #fff; text-align: center; }
        .kpi h3 { margin-bottom: 10px; }
        .kpi p { font-size: 1.5em; font-weight: bold; }
        a { text-decoration: none; color: #007BFF; font-weight: bold; margin-right: 15px; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<h2>Welcome, <?php echo $_SESSION['username']; ?> (Manager)</h2>

<div class="kpi-container">
    <div class="kpi">
        <h3>Total Products</h3>
        <p><?php echo $total_products; ?></p>
    </div>
    <div class="kpi">
        <h3>Low / Negative Stock</h3>
        <p><?php echo $low_stock; ?></p>
    </div>
    <div class="kpi">
        <h3>Pending Receipts</h3>
        <p><?php echo $pending_receipts; ?></p>
    </div>
    <div class="kpi">
        <h3>Pending Deliveries</h3>
        <p><?php echo $pending_deliveries; ?></p>
    </div>
</div>
<h3>Low Stock Alerts</h3>
<div>
<?php
$products_alerts = mysqli_query($conn, "SELECT * FROM products");
while($p = mysqli_fetch_assoc($products_alerts)){
    if($p['stock'] < 5){
        echo "<p style='color:red;'>⚠ ".$p['name']." is low! Current Stock: ".$p['stock']."</p>";
    }
}
?>
</div>
<br>
<a href="products.php">View Products</a> |
<a href="add_product.php">Add Product</a> |
<a href="add_staff.php">Add Staff</a> |
<a href="stock_in.php">Stock In</a> |
<a href="stock_out.php">Stock Out</a> |
<a href="logout.php">Logout</a>

</body>
</html>