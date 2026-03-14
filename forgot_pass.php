<?php
session_start();
include 'db.php';

if(isset($_POST['send_otp'])){
    $login_input = mysqli_real_escape_string($conn, $_POST['login_input']);
    $query = "SELECT * FROM users WHERE username='$login_input' OR email='$login_input'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);
        $otp = rand(100000, 999999);
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['otp'] = $otp;
        $success_msg = "OTP generated: <span class='font-bold text-blue-600'>$otp</span>";
    } else {
        $error = "User not found!";
    }
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8 border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Forgot Password</h2>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($success_msg)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">
                <p><?php echo $success_msg; ?></p>
                <a href="reset_pass.php" class="inline-block mt-2 text-blue-600 underline font-semibold">Reset Password Now &rarr;</a>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username or Email</label>
                <input type="text" name="login_input" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                       placeholder="Enter Username or Email" required>
            </div>

            <button type="submit" name="send_otp" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                Send OTP
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <a href="login.php" class="text-sm text-gray-500 hover:text-blue-600">Back to Login</a>
        </div>
    </div>

</body>