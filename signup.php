<?php
session_start();
include "db.php";

// Logic for registration
if(isset($_POST['register'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $warehouse = mysqli_real_escape_string($conn, $_POST['warehouse']);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if(mysqli_num_rows($check) > 0){
        $error = "Username already exists!";
    } else {
        $sql = "INSERT INTO users (username, password, role, warehouse) VALUES ('$username', '$password', '$role', '$warehouse')";
        if(mysqli_query($conn, $sql)){
            $success = "Account created! You can now login.";
        } else {
            $error = "Registration failed: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | IMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg); 
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 20px 20px;
            color: var(--text-main); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
        }

        .signup-container {
            background: var(--card-bg);
            width: 100%;
            max-width: 450px;
            padding: 3rem;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: fadeIn 0.6s ease-out;
        }

        .header { text-align: center; margin-bottom: 2.5rem; }
        .header .logo-icon { 
            background: var(--primary); 
            color: white; 
            width: 50px; 
            height: 50px; 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.5rem; 
            margin: 0 auto 1rem;
        }
        .header h2 { font-size: 1.75rem; font-weight: 800; }
        .header p { color: var(--text-muted); font-size: 0.95rem; margin-top: 5px; }

        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--text-main); }
        
        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
        
        input, select {
            width: 100%;
            padding: 12px 12px 12px 42px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s;
            background: #fcfcfd;
        }

        input:focus, select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
            background: white;
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 1rem;
        }

        .btn-register:hover { background: var(--primary-hover); transform: translateY(-1px); }

        .footer-text { text-align: center; margin-top: 2rem; font-size: 0.9rem; color: var(--text-muted); }
        .footer-text a { color: var(--primary); text-decoration: none; font-weight: 600; }

        .alert { padding: 12px; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.85rem; text-align: center; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .alert-success { background: #dcfce7; color: #166534; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="signup-container">
    <div class="header">
        <div class="logo-icon"><i class="fas fa-user-plus"></i></div>
        <h2>Join the Team</h2>
        <p>Create your warehouse access account</p>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <div class="input-wrapper">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Choose a username" required>
            </div>
        </div>

        <div class="form-group">
            <label>Password</label>
            <div class="input-wrapper">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Create a strong password" required>
            </div>
        </div>

        <div class="form-group">
            <label>Access Role</label>
            <div class="input-wrapper">
                <i class="fas fa-shield-alt"></i>
                <select name="role" required>
                    <option value="staff">Warehouse Staff</option>
                    <option value="manager">System Manager</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Assigned Warehouse</label>
            <div class="input-wrapper">
                <i class="fas fa-warehouse"></i>
                <input type="text" name="warehouse" placeholder="e.g. Main Hub" required>
            </div>
        </div>

        <button type="submit" name="register" class="btn-register">Create Account</button>
    </form>

    <div class="footer-text">
        Already have an account? <a href="index.php">Log In</a>
    </div>
</div>

</body>
</html>