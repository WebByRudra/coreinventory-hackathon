<?php
session_start();
include "db.php";

if(!isset($_SESSION['reset_email'])){
    header("Location: forgot_pass.php");
    exit();
}

if(isset($_POST['otp'])){
    $otp = mysqli_real_escape_string($conn, $_POST['otp']);
    $email = $_SESSION['reset_email'];

    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND otp='$otp' AND otp_expiry >= NOW()");
    if(mysqli_num_rows($res) == 1){
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Invalid or expired OTP!";
    }
}
?>

<form method="POST">
    <input type="text" name="otp" placeholder="Enter OTP" required>
    <button type="submit">Verify OTP</button>
</form>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>