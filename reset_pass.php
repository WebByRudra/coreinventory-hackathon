<?php
session_start();
include 'db.php';

if(!isset($_SESSION['reset_user_id'])){
    header("Location: forgot_pass.php");
    exit();
}

if(isset($_POST['reset_password'])){
    $otp_input = mysqli_real_escape_string($conn, $_POST['otp']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);

    if($otp_input == $_SESSION['otp']){
        $user_id = $_SESSION['reset_user_id'];
        $update = "UPDATE users SET password='$new_password' WHERE id='$user_id'";
        if(mysqli_query($conn, $update)){
            echo "<p style='color:green;'>Password reset successful! <a href='index.php'>Login here</a></p>";

            // Clear session
            unset($_SESSION['otp']);
            unset($_SESSION['reset_user_id']);
        } else {
            $error = "Error updating password!";
        }
    } else {
        $error = "Invalid OTP!";
    }
}
?>

<h2>Reset Password</h2>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    <input type="text" name="otp" placeholder="Enter OTP" required><br><br>
    <input type="password" name="new_password" placeholder="New Password" required><br><br>
    <button type="submit" name="reset_password">Reset Password</button>
</form>