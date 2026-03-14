<?php
session_start();
include "db.php";

if(isset($_POST['name'], $_POST['username'], $_POST['email'], $_POST['phone'], $_POST['password'], $_POST['role'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Check for duplicates in username, email, or phone
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email' OR phone='$phone'");
    if(mysqli_num_rows($check) > 0){
        $error = "Username, Email, or Phone already exists!";
    } else {
        $sql = "INSERT INTO users (name, username, email, phone, password, role) 
                VALUES ('$name','$username','$email','$phone','$password','$role')";
        if(mysqli_query($conn, $sql)){
            $success = "Account created successfully! You can now login.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
</head>
<body>
    <h2>Create New Account</h2>
    <?php
    if(isset($error)) echo "<p style='color:red;'>$error</p>";
    if(isset($success)) echo "<p style='color:green;'>$success</p>";
    ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required><br><br>
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="text" name="phone" placeholder="Phone Number" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="manager">Manager</option>
            <option value="staff">Staff</option>
        </select><br><br>
        <button type="submit">Sign Up</button>
    </form>

    <br>
    <a href="index.php">Back to Login</a>
</body>
</html>