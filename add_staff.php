<?php
session_start();
include "db.php";

// Only manager can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != "manager"){
    header("Location: index.php");
    exit();
}

if(isset($_POST['name'], $_POST['username'], $_POST['password'])){

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if(mysqli_num_rows($check) > 0){
        $error = "Username already exists!";
    } else {
        $sql = "INSERT INTO users (name, username, password, role) VALUES ('$name','$username','$password','staff')";
        if(mysqli_query($conn, $sql)){
            $success = "Staff account created successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<h2>Create Staff Account</h2>

<?php
if(isset($error)) echo "<p style='color:red;'>$error</p>";
if(isset($success)) echo "<p style='color:green;'>$success</p>";
?>

<form method="POST">
    <input type="text" name="name" placeholder="Staff Name" required><br><br>
    <input type="text" name="username" placeholder="Username" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Create Staff</button>
</form>

<br>
<a href="manager_dashboard.php">Back to Dashboard</a>