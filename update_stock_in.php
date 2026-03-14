<?php
session_start();
include "db.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "manager") {
    header("Location: index.php");
    exit();
}

if(isset($_POST['product_id'], $_POST['warehouse'], $_POST['quantity'])){
    $product_id = intval($_POST['product_id']);
    $warehouse = mysqli_real_escape_string($conn, $_POST['warehouse']);
    $quantity = intval($_POST['quantity']);

    $res = mysqli_query($conn, "SELECT * FROM product_stock_per_warehouse WHERE product_id=$product_id AND warehouse='$warehouse'");
    if(mysqli_num_rows($res) > 0){
        $row = mysqli_fetch_assoc($res);
        $new_stock = $row['stock'] + $quantity;
        mysqli_query($conn, "UPDATE product_stock_per_warehouse SET stock=$new_stock WHERE id=".$row['id']);
    } else {
        mysqli_query($conn, "INSERT INTO product_stock_per_warehouse (product_id, warehouse, stock) VALUES ($product_id,'$warehouse',$quantity)");
    }
    
    // Update the overall stock in the products table
    mysqli_query($conn, "UPDATE products SET stock = stock + $quantity WHERE id=$product_id");

    header("Location: stock_in.php");
    exit();
}
?>