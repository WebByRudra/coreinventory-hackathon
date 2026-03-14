<?php
session_start();
include "db.php";

if(isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if email exists
    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($res) == 1){
        $user = mysqli_fetch_assoc($res);

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        // Store OTP in DB
        mysqli_query($conn, "UPDATE users SET otp='$otp', otp_expiry='$expiry' WHERE id=".$user['id']);

        // Send email
        $subject = "Your OTP Code for CoreStock CoreStock";
        $message = "Hello ".$user['name'].",\n\nYour OTP is: $otp\nIt will expire in 5 minutes.\n\nThanks!";
        $headers = "From: noreply@corestock.com";

        if(mail($email, $subject, $message, $headers)){
            $_SESSION['reset_email'] = $email;
            header("Location: verify_otp.php");
            exit();
        } else {
            $error = "Failed to send OTP email. Try again.";
        }

    } else {
        $error = "Email not found!";
    }
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Enter your registered email" required>
    <button type="submit">Send OTP</button>
</form>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>