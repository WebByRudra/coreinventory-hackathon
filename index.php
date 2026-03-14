<?php
session_start();
include "db.php";

if(isset($_POST['username'], $_POST['password'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);
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
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>IMS Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
</body>
</html>