<?php
include "db.php";

if(isset($_POST['signup'])){

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

$query = "INSERT INTO users(name,email,password)
VALUES('$name','$email','$password')";

mysqli_query($conn,$query);

header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>CoreStock Signup</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="login-box">

<h2>📦 CoreStock</h2>
<h3>Create Account</h3>

<form method="POST">

<input type="text" name="name" placeholder="Full Name" required>

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit" name="signup">Signup</button>

</form>

<a href="index.php">Already have account? Login</a>

</div>

</body>
</html>