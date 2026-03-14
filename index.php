<?php
session_start();
include "db.php";

if(isset($_POST['login_input'], $_POST['password'])){
    $login_input = mysqli_real_escape_string($conn, $_POST['login_input']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Login using username OR email OR phone
    $sql = "SELECT * FROM users 
            WHERE (username='$login_input' OR email='$login_input' OR phone='$login_input') 
            AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if($user['role'] == 'manager'){
            header("Location: manager_dashboard.php");
            exit();
        } else {
            header("Location: staff_dashboard.php");
            exit();
        }
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css">
    <title>IMS Login</title>
</head>
<body>
    <h2>Login</h2>

    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="login_input" placeholder="Username / Email / Phone" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
    <p><a href="forgot_pass.php">Forgot Password?</a></p>
</body>
</html>