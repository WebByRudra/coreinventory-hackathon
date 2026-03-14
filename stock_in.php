<?php
session_start();
include "db.php";

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

// Fetch products with their stock per warehouse
$result = mysqli_query($conn, "
    SELECT p.id AS product_id, p.name, p.sku, p.category, p.unit, ps.warehouse, ps.stock, p.created_at
    FROM products p
    LEFT JOIN product_stock_per_warehouse ps ON p.id = ps.product_id
    ORDER BY p.id, ps.warehouse
");

// Fetch all unique warehouses for stock addition
$warehouses_res = mysqli_query($conn, "SELECT DISTINCT warehouse FROM product_stock_per_warehouse");
$warehouses = [];
while($w = mysqli_fetch_assoc($warehouses_res)){
    $warehouses[] = $w['warehouse'];
}
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css">
    <title>Stock In - Multi-Warehouse</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .low-stock { color: red; font-weight: bold; }
        #searchInput { padding: 8px; margin-bottom: 15px; width: 50%; }
        form { margin-bottom: 30px; }
    </style>
</head>
<body>

<h2>Stock In - Multi-Warehouse</h2>

<!-- Form to add stock -->
<form method="POST" action="update_stock_in.php">
    <select name="product_id" required>
        <option value="">Select Product</option>
        <?php
        // Get unique products
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

    <input type="number" name="quantity" placeholder="Quantity to add" required>
    <button type="submit">Add Stock</button>
</form>

<input type="text" id="searchInput" placeholder="Search by Name, SKU, Category, Warehouse...">

<table id="productsTable">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>SKU</th>
        <th>Category</th>
        <th>Unit</th>
        <th>Warehouse</th>
        <th>Stock</th>
        <th>Alert</th>
        <th>Created At</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['product_id']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['sku']; ?></td>
        <td><?php echo $row['category']; ?></td>
        <td><?php echo $row['unit']; ?></td>
        <td><?php echo $row['warehouse'] ?: 'Main Warehouse'; ?></td>
        <td><?php echo $row['stock'] ?? 0; ?></td>
        <td>
            <?php if(($row['stock'] ?? 0) < 5){ echo "<span class='low-stock'>⚠ Low Stock</span>"; } ?>
        </td>
        <td><?php echo $row['created_at']; ?></td>
    </tr>
    <?php } ?>
</table>

<br>
<a href="<?php echo $_SESSION['role']=='manager' ? 'manager_dashboard.php' : 'staff_dashboard.php'; ?>">Back to Dashboard</a>

<script>
// Simple live search/filter
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#productsTable tr:not(:first-child)');
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>