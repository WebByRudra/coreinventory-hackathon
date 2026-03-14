<?php
session_start();
include "db.php";

// Only manager can access
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

    // Check if SKU already exists
    $check_sku = mysqli_query($conn, "SELECT * FROM products WHERE sku='$sku'");
    if(mysqli_num_rows($check_sku) > 0){
        $error = "SKU '$sku' already exists! Please use a different SKU.";
    } else {
        $sql = "INSERT INTO products (name, sku, category, unit, stock, created_at)
                VALUES ('$name','$sku','$category','$unit',$stock,NOW())";
        if(mysqli_query($conn, $sql)){
            $success = "Product added successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Add Product</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f5f5f5; }
        h2 { margin-bottom: 20px; }
        form input, form button { padding: 10px; margin-bottom: 15px; width: 300px; max-width: 100%; }
        form button { cursor: pointer; background-color: #007BFF; color: #fff; border: none; border-radius: 5px; }
        form button:hover { background-color: #0056b3; }
        p { font-weight: bold; }
        p.error { color: red; }
        p.success { color: green; }
        a { text-decoration: none; color: #007BFF; font-weight: bold; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<h2>Add Product</h2>

<?php
if(isset($error)) echo "<p class='error'>$error</p>";
if(isset($success)) echo "<p class='success'>$success</p>";
?>

<form method="POST">
    <input type="text" name="name" placeholder="Product Name" required><br>
    <input type="text" name="sku" placeholder="SKU" required><br>
    <input type="text" name="category" placeholder="Category" required><br>
    <input type="text" name="unit" placeholder="Unit" required><br>
    <input type="number" name="stock" placeholder="Stock Quantity" required><br>
    <button type="submit">Add Product</button>
</form>

<br>
<a href="manager_dashboard.php">Back to Dashboard</a>

</body>
</html>