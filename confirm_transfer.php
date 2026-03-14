<?php
session_start();
include "db.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "manager"){
    header("Location: index.php");
    exit();
}

if(isset($_GET['id'])){
    $id = intval($_GET['id']);

    // Mark transfer as done
    mysqli_query($conn, "UPDATE internal_transfers SET status='done' WHERE id=$id");

    header("Location: internal_transfer.php");
    exit();
}
?>