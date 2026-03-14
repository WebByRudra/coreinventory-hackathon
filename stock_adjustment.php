<?php
session_start();
include "db.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'manager'){
    header("Location: index.php");
    exit();
}

$products = mysqli_query($conn, "SELECT * FROM products");

if(isset($_POST['product_id'], $_POST['new_stock'], $_POST['reason'])){
    $product_id = intval($_POST['product_id']);
    $new_stock = intval($_POST['new_stock']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $adjusted_by = $_SESSION['user_id'];

    $res = mysqli_query($conn, "SELECT stock FROM products WHERE id=$product_id");
    $row = mysqli_fetch_assoc($res);
    if($row){
        $old_stock = $row['stock'];
        // Update stock
        mysqli_query($conn, "UPDATE products SET stock=$new_stock WHERE id=$product_id");
        // Log adjustment
        mysqli_query($conn, "INSERT INTO stock_adjustments (product_id, old_stock, new_stock, reason, adjusted_by) 
                             VALUES ($product_id, $old_stock, $new_stock, '$reason', $adjusted_by)");
        $success = "Stock adjusted successfully from $old_stock → $new_stock";
    } else {
        $error = "Product not found!";
    }
}
?>

<h2>Stock Adjustment</h2>

<?php
if(isset($error)) echo "<p style='color:red;'>$error</p>";
if(isset($success)) echo "<p style='color:green;'>$success</p>";
?>

<form method="POST">
    <select name="product_id" required>
        <option value="">Select Product</option>
        <?php while($p = mysqli_fetch_assoc($products)){ ?>
            <option value="<?php echo $p['id']; ?>"><?php echo $p['name']." (Current: ".$p['stock'].")"; ?></option>
        <?php } ?>
    </select><br><br>

    <input type="number" name="new_stock" placeholder="New Stock Quantity" required><br><br>
    <input type="text" name="reason" placeholder="Reason for adjustment" required><br><br>
    <button type="submit">Adjust Stock</button>
</form>

<br>
<a href="manager_dashboard.php">Back to Dashboard</a>