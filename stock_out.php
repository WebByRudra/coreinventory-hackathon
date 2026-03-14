<?php
session_start();
include "db.php";

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

// Reduce stock
if(isset($_POST['product_id'], $_POST['quantity'])){
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    $res = mysqli_query($conn, "SELECT stock FROM products WHERE id=$product_id");
    $row = mysqli_fetch_assoc($res);
    if($row){
        $new_stock = $row['stock'] - $quantity;
        if($new_stock < 0) $new_stock = 0;

        mysqli_query($conn, "UPDATE products SET stock=$new_stock WHERE id=$product_id");
        $success = "Stock updated successfully!";
    } else {
        $error = "Product not found!";
    }
}

// Get all products for dropdown
$products = mysqli_query($conn, "SELECT * FROM products");
?>

<h2>Stock Out / Update</h2>

<?php
if(isset($error)) echo "<p style='color:red;'>$error</p>";
if(isset($success)) echo "<p style='color:green;'>$success</p>";
?>

<form method="POST">
    <select name="product_id" required>
        <option value="">Select Product</option>
        <?php while($p = mysqli_fetch_assoc($products)){ ?>
            <option value="<?php echo $p['id']; ?>">
                <?php echo $p['name'] . " (Stock: ".$p['stock'].")"; ?>
            </option>
        <?php } ?>
    </select><br><br>
    <input type="number" name="quantity" placeholder="Quantity to reduce" required><br><br>
    <button type="submit">Update Stock</button>
</form>

<br>
<a href="<?php echo $_SESSION['role']=='manager' ? 'manager_dashboard.php' : 'staff_dashboard.php'; ?>">Back to Dashboard</a>