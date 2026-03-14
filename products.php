<?php
session_start();
include "db.php";

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM products");
?>

<h2>Products List</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>SKU</th>
        <th>Category</th>
        <th>Unit</th>
        <th>Stock</th>
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
        <td><?php echo $row['created_at']; ?></td>
    </tr>
    <?php } ?>
</table>

<br>
<a href="<?php echo $_SESSION['role']=='manager' ? 'manager_dashboard.php' : 'staff_dashboard.php'; ?>">Back to Dashboard</a>