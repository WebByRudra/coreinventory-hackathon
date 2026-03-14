<?php
session_start();
include "db.php";

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

if(isset($_GET['id'])){
    $id = intval($_GET['id']);

    // Fetch pending delivery
    $res = mysqli_query($conn, "SELECT * FROM stock_out WHERE id=$id AND status='pending'");
    $row = mysqli_fetch_assoc($res);
    if($row){
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];

        // Update product stock
        $new_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stock FROM products WHERE id=$product_id"))['stock'] - $quantity;
        if($new_stock < 0) $new_stock = 0;

        mysqli_query($conn, "UPDATE products SET stock=$new_stock WHERE id=$product_id");

        // Mark as done
        mysqli_query($conn, "UPDATE stock_out SET status='done' WHERE id=$id");

        header("Location: stock_out.php");
        exit();
    } else {
        echo "Pending delivery not found!";
    }
}
?>