<?php
session_start();
include "db.php";

if(!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']){
    header("Location: forgot_pass.php");
    exit();
}

if(isset($_POST['password'], $_POST['confirm_password'])){
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if($password === $confirm){
        $email = $_SESSION['reset_email'];
        mysqli_query($conn, "UPDATE users SET password='$password', otp=NULL, otp_expiry=NULL WHERE email='$email'");

        session_unset();
        $success = "Password reset successful! <a href='index.php'>Login Now</a>";
    } else {
        $error = "Passwords do not match!";
    }
}
?>

<form method="POST">
    <input type="password" name="password" placeholder="New Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <button type="submit">Reset Password</button>
</form>
<?php
if(isset($error)) echo "<p style='color:red;'>$error</p>";
if(isset($success)) echo "<p style='color:green;'>$success</p>";
?>  