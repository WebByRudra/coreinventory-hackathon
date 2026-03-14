<?php
session_start();
include "db.php";

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

$products = mysqli_query($conn, "SELECT * FROM products");

if(isset($_POST['product_id'], $_POST['quantity'])){
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Insert as pending delivery
    $sql = "INSERT INTO stock_out (product_id, quantity, status, created_at) 
            VALUES ($product_id, $quantity, 'pending', NOW())";

    if(mysqli_query($conn, $sql)){
        $success = "Delivery order recorded as pending. Confirm to deduct from inventory.";
    } else {
        $error = "Error: ".mysqli_error($conn);
    }
}
?>

<h2>Stock Out / Record Delivery</h2>

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
    <input type="number" name="quantity" placeholder="Quantity" required><br><br>
    <button type="submit">Record Delivery</button>
</form>

<h3>Pending Deliveries</h3>
<ul>
<?php
$pending = mysqli_query($conn, "SELECT so.id, p.name, so.quantity 
                                FROM stock_out so 
                                JOIN products p ON so.product_id=p.id 
                                WHERE so.status='pending'");
while($row = mysqli_fetch_assoc($pending)){
    echo "<li>".$row['name']." → ".$row['quantity']." units 
          <a href='confirm_stock_out.php?id=".$row['id']."'>Confirm</a></li>";
}
?>
</ul>

<br>
<a href="<?php echo $_SESSION['role']=='manager' ? 'manager_dashboard.php' : 'staff_dashboard.php'; ?>">Back to Dashboard</a>