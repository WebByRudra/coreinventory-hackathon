<?php
session_start();
include "db.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "manager"){
    header("Location: index.php");
    exit();
}

if(isset($_POST['name'], $_POST['sku'], $_POST['category'], $_POST['unit'], $_POST['stock'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $sku = mysqli_real_escape_string($conn, $_POST['sku']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $unit = mysqli_real_escape_string($conn, $_POST['unit']);
    $stock = intval($_POST['stock']);

    $sql = "INSERT INTO products (name, sku, category, unit, stock, created_at)
            VALUES ('$name','$sku','$category','$unit',$stock,NOW())";
    if(mysqli_query($conn, $sql)){
        $success = "Product added successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<h2>Add Product</h2>

<?php
if(isset($error)) echo "<p style='color:red;'>$error</p>";
if(isset($success)) echo "<p style='color:green;'>$success</p>";
?>

<form method="POST">
    <input type="text" name="name" placeholder="Product Name" required><br><br>
    <input type="text" name="sku" placeholder="SKU" required><br><br>
    <input type="text" name="category" placeholder="Category" required><br><br>
    <input type="text" name="unit" placeholder="Unit" required><br><br>
    <input type="number" name="stock" placeholder="Stock Quantity" required><br><br>
    <button type="submit">Add Product</button>
</form>

<br>
<a href="manager_dashboard.php">Back to Dashboard</a>