<?php
session_start();
include "db.php";

if(isset($_POST['login'])){

$email = $_POST['email'];
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
$result = mysqli_query($conn,$query);

if(mysqli_num_rows($result)==1){

$_SESSION['user']=$email;
header("Location: dashboard.php");
exit();

}else{
$error = "Invalid Email or Password";
}

}
?>

<!DOCTYPE html>
<html>

<head>

<title>CoreStock Login</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<div class="login-box">

<h2 class="logo">📦 CoreStock</h2>

<h3>Login to Your Account</h3>

<form method="POST">

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit" name="login">Login</button>

</form>

<?php
if(isset($error)){
echo "<p style='color:red;'>$error</p>";
}
?>

<p>Don't have an account? <a href="signup.php">Signup</a></p>

</div>

</body>

</html>