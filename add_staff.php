<?php
include 'db.php';
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    header("Location: index.php");
    exit();
}

if(isset($_POST['name'], $_POST['username'], $_POST['email'], $_POST['phone'], $_POST['password'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = 'staff';

    // Check for duplicates
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email' OR phone='$phone'");
    if(mysqli_num_rows($check) > 0){
        $error = "Username, Email, or Phone already exists!";
    } else {
        $insert = "INSERT INTO users (name, username, email, phone, password, role)
                   VALUES ('$name','$username','$email','$phone','$password','$role')";
        if(mysqli_query($conn, $insert)){
            $success = "Staff account created successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css">
    <title>Add Staff</title>
</head>
<body>
<h2>Add New Staff</h2>
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
    <button type="submit">Add Staff</button>
</form>

<br>
<a href="manager_dashboard.php">Back to Dashboard</a>
</body>
</html>