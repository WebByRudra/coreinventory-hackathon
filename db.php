<?php
$servername = "localhost";
$username = "root"; // MySQL username
$password = "";     // MySQL password
$dbname = "coreinventory"; // Database name

$conn = mysqli_connect($servername, $username, $password, $dbname);

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}
?>