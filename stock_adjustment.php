<?php
session_start();
include "db.php";

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

// Fetch products
$products = mysqli_query($conn, "SELECT * FROM products");

// Handle adjustment submission
if(isset($_POST['product_id'], $_POST['warehouse'], $_POST['counted_stock'])){
    $product_id = intval($_POST['product_id']);
    $warehouse = mysqli_real_escape_string($conn, $_POST['warehouse']);
    $counted_stock = intval($_POST['counted_stock']);

    // Fetch current stock
    $res = mysqli_query($conn, "SELECT * FROM product_stock_per_warehouse WHERE product_id=$product_id AND warehouse='$warehouse'");
    if($row = mysqli_fetch_assoc($res)){
        $old_stock = $row['stock'];
        $diff = $counted_stock - $old_stock;

        // Update stock
        mysqli_query($conn, "UPDATE product_stock_per_warehouse SET stock=$counted_stock WHERE id=".$row['id']);

        // Log adjustment
        mysqli_query($conn, "
            INSERT INTO stock_adjustments (product_id, warehouse, old_stock, new_stock, difference, adjusted_by, created_at)
            VALUES ($product_id, '$warehouse', $old_stock, $counted_stock, $diff, '{$_SESSION['username']}', NOW())
        ");

        $success = "Stock adjusted successfully!";
    } else {
        $error = "Product not found in selected warehouse!";
    }
}

// Fetch adjustment history
$adjustments = mysqli_query($conn, "
    SELECT sa.id, p.name, sa.warehouse, sa.old_stock, sa.new_stock, sa.difference, sa.adjusted_by, sa.created_at
    FROM stock_adjustments sa
    JOIN products p ON sa.product_id = p.id
    ORDER BY sa.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css">
    <title>Stock Adjustments</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        form { margin-bottom: 20px; }
    </style>
</head>
<body>

<h2>Stock Adjustments</h2>

<?php
if(isset($error)) echo "<p style='color:red;'>$error</p>";
if(isset($success)) echo "<p style='color:green;'>$success</p>";
?>

<form method="POST">
    <select name="product_id" required>
        <option value="">Select Product</option>
        <?php while($p = mysqli_fetch_assoc($products)){
            echo "<option value='{$p['id']}'>{$p['name']} ({$p['sku']})</option>";
        } ?>
    </select>

    <input type="text" name="warehouse" placeholder="Warehouse Name" required>
    <input type="number" name="counted_stock" placeholder="Counted Stock Quantity" required>
    <button type="submit">Adjust Stock</button>
</form>

<h3>Adjustment History</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Product</th>
        <th>Warehouse</th>
        <th>Old Stock</th>
        <th>New Stock</th>
        <th>Difference</th>
        <th>Adjusted By</th>
        <th>Date</th>
    </tr>
    <?php while($a = mysqli_fetch_assoc($adjustments)){ ?>
    <tr>
        <td><?php echo $a['id']; ?></td>
        <td><?php echo $a['name']; ?></td>
        <td><?php echo $a['warehouse']; ?></td>
        <td><?php echo $a['old_stock']; ?></td>
        <td><?php echo $a['new_stock']; ?></td>
        <td><?php echo $a['difference']; ?></td>
        <td><?php echo $a['adjusted_by']; ?></td>
        <td><?php echo $a['created_at']; ?></td>
    </tr>
    <?php } ?>
</table>

<br>
<a href="<?php echo $_SESSION['role']=='manager' ? 'manager_dashboard.php' : 'staff_dashboard.php'; ?>">Back to Dashboard</a>

</body>
</html>