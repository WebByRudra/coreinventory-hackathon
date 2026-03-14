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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS | Secure Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --glass: rgba(255, 255, 255, 0.95);
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --danger: #ef4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-gradient);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Login Card Container */
        .login-container {
            background: var(--glass);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            z-index: 10;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header .logo-icon {
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
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
        }

        .login-header h2 { font-size: 1.75rem; color: var(--text-dark); font-weight: 700; }
        .login-header p { color: var(--text-muted); margin-top: 5px; font-size: 0.9rem; }

        /* Form Styling */
        .form-group {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            transition: color 0.3s;
        }

        input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            outline: none;
            background: #f8fafc;
        }

        input:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        input:focus + i { color: var(--primary); }

        .error-msg {
            background: #fef2f2;
            color: var(--danger);
            padding: 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #fee2e2;
            animation: shake 0.4s ease-in-out;
        }

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
            transition: all 0.3s;
            margin-top: 0.5rem;
        }

        button:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }

        /* Links */
        .footer-links {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .footer-links p { margin-bottom: 8px; color: var(--text-muted); }
        .footer-links a { 
            color: var(--primary); 
            text-decoration: none; 
            font-weight: 600; 
            transition: color 0.2s;
        }

        .footer-links a:hover { color: var(--primary-hover); text-decoration: underline; }

        /* Animations */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Decorative Background Bubbles */
        .bubble {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 1;
        }
    </style>
</head>
<body>

    <div class="bubble" style="width: 200px; height: 200px; top: -50px; left: -50px;"></div>
    <div class="bubble" style="width: 300px; height: 300px; bottom: -100px; right: -100px;"></div>

    <div class="login-container">
        <div class="login-header">
            <div class="logo-icon"><i class="fas fa-warehouse"></i></div>
            <h2>Welcome Back</h2>
            <p>Please enter your details to sign in</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="error-msg">
                <i class="fas fa-circle-exclamation"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="login_input" placeholder="Username, Email, or Phone" required autofocus>
            </div>
            
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit">Sign In</button>
        </form>

        <div class="footer-links">
            <p><a href="forgot_pass.php">Forgot Password?</a></p>
            <p>New to IMS? <a href="signup.php">Create Account</a></p>
        </div>
    </div>

</body>
</html>