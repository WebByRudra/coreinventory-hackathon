<?php
session_start();
include "db.php";

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

// Determine role
$role = strtolower($_SESSION['role']); // normalize role

// Fetch products with stock per warehouse
$products = mysqli_query($conn, "
    SELECT p.id AS product_id, p.name, p.sku, p.category, p.unit, ps.warehouse, ps.stock
    FROM products p
    LEFT JOIN product_stock_per_warehouse ps ON p.id = ps.product_id
    ORDER BY p.id, ps.warehouse
");

// Fetch warehouses
$warehouses = [];
if($role == 'manager'){
    $warehouses_res = mysqli_query($conn, "SELECT DISTINCT warehouse FROM product_stock_per_warehouse");
    while($w = mysqli_fetch_assoc($warehouses_res)){
        $warehouses[] = $w['warehouse'];
    }
} else if(isset($_SESSION['warehouse'])){
    $staff_warehouse = $_SESSION['warehouse'];
    $warehouses_res = mysqli_query($conn, "SELECT DISTINCT warehouse FROM product_stock_per_warehouse WHERE warehouse='$staff_warehouse'");
    while($w = mysqli_fetch_assoc($warehouses_res)){
        $warehouses[] = $w['warehouse'];
    }
}

// Handle stock out submission
if(isset($_POST['product_id'], $_POST['warehouse'], $_POST['quantity'])){
    $product_id = intval($_POST['product_id']);
    $warehouse = mysqli_real_escape_string($conn, $_POST['warehouse']);
    $quantity = intval($_POST['quantity']);

    // Insert as pending delivery
    $sql = "INSERT INTO stock_out (product_id, warehouse, quantity, status, created_at)
            VALUES ($product_id, '$warehouse', $quantity, 'pending', NOW())";

    if(mysqli_query($conn, $sql)){
        $success = "Delivery order recorded as pending. Confirm to deduct from inventory.";
    } else {
        $error = "Error: ".mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css">
    <title>Stock Out - Multi-Warehouse</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .low-stock { color: red; font-weight: bold; }
        form { margin-bottom: 20px; }
    </style>
</head>
<body>

<h2>Stock Out / Record Delivery</h2>

<?php
if(isset($error)) echo "<p style='color:red;'>$error</p>";
if(isset($success)) echo "<p style='color:green;'>$success</p>";
?>

<form method="POST">
    <select name="product_id" required>
        <option value="">Select Product</option>
        <?php
        $unique_products = mysqli_query($conn, "SELECT * FROM products");
        while($p = mysqli_fetch_assoc($unique_products)){
            echo "<option value='{$p['id']}'>{$p['name']} ({$p['sku']})</option>";
        }
        ?>
    </select>

    <select name="warehouse" required>
        <option value="">Select Warehouse</option>
        <?php foreach($warehouses as $w){
            echo "<option value='{$w}'>{$w}</option>";
        } ?>
    </select>

    <input type="number" name="quantity" placeholder="Quantity" required>
    <button type="submit">Record Delivery</button>
</form>

<h3>Pending Deliveries</h3>
<ul>
<?php
// Prepare pending deliveries query
if($role == 'manager'){
    $pending = mysqli_query($conn, "
        SELECT so.id, p.name, so.quantity, so.warehouse
        FROM stock_out so
        JOIN products p ON so.product_id=p.id
        WHERE so.status='pending'
    ");
} else if(isset($_SESSION['warehouse'])){
    $staff_warehouse = $_SESSION['warehouse'];
    $pending = mysqli_query($conn, "
        SELECT so.id, p.name, so.quantity, so.warehouse
        FROM stock_out so
        JOIN products p ON so.product_id=p.id
        WHERE so.status='pending' AND so.warehouse='$staff_warehouse'
    ");
} else {
    // Safe empty mysqli result to prevent TypeError
    $pending = mysqli_query($conn, "SELECT 0 AS id, '' AS name, 0 AS quantity, '' AS warehouse WHERE 0");
}

// Display pending deliveries
if(mysqli_num_rows($pending) == 0){
    echo "<li>No pending deliveries</li>";
} else {
    while($row = mysqli_fetch_assoc($pending)){
        echo "<li>{$row['name']} ({$row['warehouse']}) → {$row['quantity']} units 
              <a href='confirm_stock_out.php?id={$row['id']}'>Confirm</a></li>";
    }
}
?>
</ul>

<br>
<a href="<?php echo ($role=='manager') ? 'manager_dashboard.php' : 'staff_dashboard.php'; ?>">Back to Dashboard</a>

</body>
</html>