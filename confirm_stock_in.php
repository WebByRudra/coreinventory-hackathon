<?php
session_start();
include "db.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "manager"){
    header("Location: index.php");
    exit();
}

if(isset($_GET['id'])){
    $id = intval($_GET['id']);

    // Fetch pending record
    $res = mysqli_query($conn, "SELECT * FROM stock_in WHERE id=$id AND status='pending'");
    $row = mysqli_fetch_assoc($res);
    if($row){
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];

        // Update product stock
        mysqli_query($conn, "UPDATE products SET stock = stock + $quantity WHERE id=$product_id");

        // Mark as done
        mysqli_query($conn, "UPDATE stock_in SET status='done' WHERE id=$id");

        header("Location: stock_in.php");
        exit();
    } else {
        echo "Pending receipt not found!";
    }
}
?>