<?php
session_start();
include "db.php";

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

// Fetch products
$result = mysqli_query($conn, "SELECT * FROM products");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products List</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .low-stock { color: red; font-weight: bold; }
        #searchInput { padding: 8px; margin-bottom: 15px; width: 50%; }
    </style>
</head>
<body>

<h2>Products List</h2>

<input type="text" id="searchInput" placeholder="Search by Name, SKU, or Category...">

<table id="productsTable">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>SKU</th>
        <th>Category</th>
        <th>Unit</th>
        <th>Stock</th>
        <th>Alert</th>
        <th>Created At</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['sku']; ?></td>
        <td><?php echo $row['category']; ?></td>
        <td><?php echo $row['unit']; ?></td>
        <td><?php echo $row['stock']; ?></td>
        <td>
            <?php if($row['stock'] < 5){ echo "<span class='low-stock'>⚠ Low Stock</span>"; } ?>
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