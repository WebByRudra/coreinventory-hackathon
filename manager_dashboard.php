<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != "manager"){
    header("Location: index.php");
    exit();
}
?>

<h1>Manager Dashboard</h1>

<ul>
    <li><a href="add_staff.php">Create Staff Account</a></li>
    <li><a href="add_product.php">Add Product</a></li>
    <li><a href="products.php">View Products</a></li>
    <li><a href="stock_out.php">Stock Out (Reduce Stock)</a></li>
    <li><a href="stock_in.php">Stock In (Add Stock)</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>