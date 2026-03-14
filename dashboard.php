<?php
session_start();

if(!isset($_SESSION['user'])){
header("Location: index.php");
exit();
}
?>

<!DOCTYPE html>
<html>

<head>

<title>CoreStock Dashboard</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<?php include "sidebar.php"; ?>

<div class="main">

<h1>Dashboard</h1>

<div class="cards">

<div class="card">
<h3>Total Products</h3>
<p>0</p>
</div>

<div class="card">
<h3>Total Stock</h3>
<p>0</p>
</div>

<div class="card">
<h3>Low Stock</h3>
<p>0</p>
</div>

<div class="card">
<h3>Transactions</h3>
<p>0</p>
</div>

</div>

</div>

</body>

</html>