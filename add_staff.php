<?php
include 'db.php';
session_start();
if(!isset($_SESSION['role']) || trim(strtolower($_SESSION['role'])) != 'manager') {
    $redirect = (isset($_SESSION['role']) && trim(strtolower($_SESSION['role'])) == 'staff') ? 'staff_dashboard.php' : 'index.php';
    header("Location: " . $redirect);
    exit();
}

if(isset($_POST['name'], $_POST['username'], $_POST['email'], $_POST['phone'], $_POST['password'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Staff | CoreStock Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --sidebar: #0f172a;
            --danger: #ef4444;
            --success: #10b981;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text-main); display: flex; min-height: 100vh; }

        /* Sidebar Navigation */
        nav { width: 260px; background: var(--sidebar); color: white; padding: 2rem 1rem; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; }
        nav h2 { font-size: 1.2rem; margin-bottom: 2rem; padding-left: 1rem; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; }
        nav a { text-decoration: none; color: #94a3b8; padding: 0.8rem 1rem; margin-bottom: 0.5rem; border-radius: 8px; display: flex; align-items: center; gap: 12px; transition: 0.2s; }
        nav a:hover { background: rgba(255,255,255,0.05); color: white; }
        nav a.active { background: var(--primary); color: white; }
        nav a.back-btn { margin-top: auto; color: #94a3b8; border: 1px solid rgba(255,255,255,0.1); justify-content: center; }

        /* Main Content */
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        
        .form-card {
            background: var(--card-bg);
            width: 100%;
            max-width: 550px;
            padding: 3rem;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            animation: fadeIn 0.5s ease-out;
        }

        .form-card h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 8px; }
        .form-card p.subtitle { color: var(--text-muted); margin-bottom: 2rem; font-size: 0.9rem; }

        /* Grid Form Layout */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .full-width { grid-column: span 2; }

        .input-group { margin-bottom: 1.2rem; position: relative; }
        .input-group label { display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; color: var(--text-muted); }
        .input-group i { position: absolute; left: 14px; top: 35px; color: var(--text-muted); }
        
        input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.3s;
            background: #f8fafc;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        /* Notifications */
        .alert { padding: 12px 15px; border-radius: 12px; font-size: 0.85rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; }
        .alert-error { background: #fef2f2; color: var(--danger); border: 1px solid #fee2e2; }
        .alert-success { background: #f0fdf4; color: var(--success); border: 1px solid #dcfce7; }

        button {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 1rem;
        }

        button:hover { background: #4f46e5; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3); }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } .full-width { grid-column: span 1; } }
    </style>
</head>
<body>

<nav>
    <h2>CoreStock Management</h2>
    <a href="manager_dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
    <a href="products.php"><i class="fas fa-boxes"></i> Products</a>
    <a href="add_product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
    <a href="add_staff.php" class="active"><i class="fas fa-user-plus"></i> Add Staff</a>
    <a href="stock_in.php"><i class="fas fa-arrow-down"></i> Stock In</a>
    <a href="stock_out.php"><i class="fas fa-arrow-up"></i> Stock Out</a>
    <a href="manager_dashboard.php" class="back-btn"><i class="fas fa-chevron-left"></i> Back</a>
</nav>

<main>
    <div class="form-card">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="background: rgba(99, 102, 241, 0.1); color: var(--primary); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem;">
                <i class="fas fa-users-cog"></i>
            </div>
            <h2>Create Staff Account</h2>
            <p class="subtitle">Set up new credentials for warehouse personnel</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <?php if(isset($success)): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" class="form-grid">
            <div class="input-group full-width">
                <label>Full Name</label>
                <i class="fas fa-id-card"></i>
                <input type="text" name="name" placeholder="John Doe" required>
            </div>

            <div class="input-group">
                <label>Username</label>
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="johndoe88" required>
            </div>

            <div class="input-group">
                <label>Phone Number</label>
                <i class="fas fa-phone"></i>
                <input type="text" name="phone" placeholder="+1 234 567 890" required>
            </div>

            <div class="input-group full-width">
                <label>Email Address</label>
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="staff@company.com" required>
            </div>

            <div class="input-group full-width">
                <label>Temporary Password</label>
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="full-width">
                <button type="submit">Create Account</button>
            </div>
        </form>
    </div>
</main>

</body>
</html>