<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != "staff"){
    header("Location: index.php");
    exit();
}
?>

<h1>Staff Dashboard</h1>

<ul>
    <li><a href="products.php">View Products</a></li>
    <li><a href="stock_out.php">Stock Out (Reduce Stock)</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>