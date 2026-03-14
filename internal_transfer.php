<?php
session_start();
include "db.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "manager"){
    header("Location: index.php");
    exit();
}

// Fetch unique products
$products = mysqli_query($conn, "SELECT * FROM products");

// Fetch unique warehouses
$warehouses_res = mysqli_query($conn, "SELECT DISTINCT warehouse FROM product_stock_per_warehouse");
$warehouses = [];
while($w = mysqli_fetch_assoc($warehouses_res)){
    $warehouses[] = $w['warehouse'];
}

// Handle internal transfer submission
if(isset($_POST['product_id'], $_POST['from_warehouse'], $_POST['to_warehouse'], $_POST['quantity'])){
    $product_id = intval($_POST['product_id']);
    $from = mysqli_real_escape_string($conn, $_POST['from_warehouse']);
    $to = mysqli_real_escape_string($conn, $_POST['to_warehouse']);
    $quantity = intval($_POST['quantity']);

    if($from == $to){
        $error = "Source and destination warehouses cannot be the same.";
    } else {
        // Check current stock in source warehouse
        $res = mysqli_query($conn, "SELECT * FROM product_stock_per_warehouse WHERE product_id=$product_id AND warehouse='$from'");
        $source = mysqli_fetch_assoc($res);
        if(!$source || $source['stock'] < $quantity){
            $error = "Not enough stock in source warehouse!";
        } else {
            // Deduct from source
            $new_source_stock = $source['stock'] - $quantity;
            mysqli_query($conn, "UPDATE product_stock_per_warehouse SET stock=$new_source_stock WHERE id=".$source['id']);

            // Add to destination
            $res2 = mysqli_query($conn, "SELECT * FROM product_stock_per_warehouse WHERE product_id=$product_id AND warehouse='$to'");
            if($dest = mysqli_fetch_assoc($res2)){
                $new_dest_stock = $dest['stock'] + $quantity;
                mysqli_query($conn, "UPDATE product_stock_per_warehouse SET stock=$new_dest_stock WHERE id=".$dest['id']);
            } else {
                mysqli_query($conn, "INSERT INTO product_stock_per_warehouse (product_id, warehouse, stock) VALUES ($product_id,'$to',$quantity)");
            }

            // Log transfer
            mysqli_query($conn, "
                INSERT INTO internal_transfers (product_id, from_warehouse, to_warehouse, quantity, status, created_at)
                VALUES ($product_id,'$from','$to',$quantity,'done',NOW())
            ");

            $success = "Stock transferred successfully!";
        }
    }
}

// Fetch transfer history
$transfers = mysqli_query($conn, "
    SELECT it.id, p.name, it.from_warehouse, it.to_warehouse, it.quantity, it.created_at
    FROM internal_transfers it
    JOIN products p ON it.product_id = p.id
    ORDER BY it.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css">
    <title>Internal Transfers</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        form { margin-bottom: 20px; }
    </style>
</head>
<body>

<h2>Internal Stock Transfer</h2>

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

    <select name="from_warehouse" required>
        <option value="">From Warehouse</option>
        <?php foreach($warehouses as $w) echo "<option value='$w'>$w</option>"; ?>
    </select>

    <select name="to_warehouse" required>
        <option value="">To Warehouse</option>
        <?php foreach($warehouses as $w) echo "<option value='$w'>$w</option>"; ?>
    </select>

    <input type="number" name="quantity" placeholder="Quantity" required>
    <button type="submit">Transfer Stock</button>
</form>

<h3>Transfer History</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Product</th>
        <th>From</th>
        <th>To</th>
        <th>Quantity</th>
        <th>Date</th>
    </tr>
    <?php while($t = mysqli_fetch_assoc($transfers)){ ?>
    <tr>
        <td><?php echo $t['id']; ?></td>
        <td><?php echo $t['name']; ?></td>
        <td><?php echo $t['from_warehouse']; ?></td>
        <td><?php echo $t['to_warehouse']; ?></td>
        <td><?php echo $t['quantity']; ?></td>
        <td><?php echo $t['created_at']; ?></td>
    </tr>
    <?php } ?>
</table>

<br>
<a href="manager_dashboard.php">Back to Dashboard</a>
</body>
</html>