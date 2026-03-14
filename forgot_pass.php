<?php
session_start();
include 'db.php';

if(isset($_POST['send_otp'])){
    $login_input = mysqli_real_escape_string($conn, $_POST['login_input']);

    // Check if user exists
    $query = "SELECT * FROM users WHERE username='$login_input' OR email='$login_input'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);

        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['otp'] = $otp;

        // In real app, send via email/SMS. For hackathon, display it on screen.
        echo "<p>OTP for password reset: <strong>$otp</strong></p>";
        echo '<p><a href="reset_pass.php">Click here to reset your password</a></p>';
    } else {
        $error = "User not found!";
    }
}
?>

<h2>Forgot Password</h2>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    <input type="text" name="login_input" placeholder="Enter Username or Email" required><br><br>
    <button type="submit" name="send_otp">Send OTP</button>
</form>