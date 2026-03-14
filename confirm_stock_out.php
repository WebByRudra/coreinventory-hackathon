<?php
session_start();
include "db.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "manager"){
    header("Location: index.php");
    exit();
}

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    
    // Fetch the pending delivery
    $res = mysqli_query($conn, "SELECT * FROM stock_out WHERE id=$id AND status='pending'");
    if($row = mysqli_fetch_assoc($res)){
        $product_id = $row['product_id'];
        $warehouse = $row['warehouse'];
        $quantity = $row['quantity'];

        // Get current stock
        $stock_res = mysqli_query($conn, "SELECT * FROM product_stock_per_warehouse WHERE product_id=$product_id AND warehouse='$warehouse'");
        if($stock_row = mysqli_fetch_assoc($stock_res)){
            $new_stock = $stock_row['stock'] - $quantity;
            if($new_stock < 0){
                echo "⚠ Warning: Stock will go negative!";
                $new_stock = 0; // optional: keep at zero
            }
            mysqli_query($conn, "UPDATE product_stock_per_warehouse SET stock=$new_stock WHERE id=".$stock_row['id']);
            
            // Also deduct from global products table
            $deduct_qty = $stock_row['stock'] - $new_stock;
            mysqli_query($conn, "UPDATE products SET stock = stock - $deduct_qty WHERE id=$product_id");
        }

        // Update stock_out status to done
        mysqli_query($conn, "UPDATE stock_out SET status='done' WHERE id=$id");
    }
}

header("Location: stock_out.php");
exit();
?>