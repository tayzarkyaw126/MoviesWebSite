<?php
session_start();

// Login ဝင်ထားပြီးသားဖြစ်ရင် admin.php ကို တန်းပို့မည်
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 📂 ၁။ ပြင်ပ Config ဖိုင်ရှိမရှိ စစ်ဆေးပြီး လှမ်းချိတ်ဆက်ခြင်း
    if (file_exists('admin_config.php')) {
        include 'admin_config.php';
    } else {
        die("❌ စနစ်အမှားအယွင်း: admin_config.php ဖိုင် ရှာမတွေ့ပါ။");
    }

    if ($username === $correct_username && $password === $correct_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "❌ Username သို့မဟုတ် Password မှားယွင်းနေပါသည်။";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Nexus Movies</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #121212; color: #ffffff; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background-color: #1a1a1a; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); width: 100%; max-width: 380px; text-align: center; }
        h2 { color: #ff3333; margin-bottom: 25px; }
        .form-group { text-align: left; margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #bbb; font-size: 14px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 12px; background-color: #222; border: 1px solid #333; border-radius: 4px; color: white; box-sizing: border-box; }
        input[type="text"]:focus, input[type="password"]:focus { border-color: #ff3333; outline: none; }
        
        /* 👁️ Show Password Container အတွက် CSS Styling */
        .show-pass-container {
            display: flex;
            align-items: center;
            margin-top: 10px;
            font-size: 13px;
            color: #bbb;
            cursor: pointer;
            user-select: none;
        }
        .show-pass-container input {
            margin: 0 6px 0 0;
            cursor: pointer;
            width: 16px;
            height: 16px;
        }

        .btn-login { width: 100%; background-color: #e50914; color: white; padding: 12px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px; margin-top: 15px; transition: 0.2s; }
        .btn-login:hover { background-color: #ff1e27; }
        .error-msg { color: #ef9a9a; background-color: #b71c1c; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>🔐 Admin Login</h2>
    
    <?php if(!empty($error)): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required autocomplete="off">
        </div>
        <div class="form-group">
            <label>Password</label>
            <!-- 🔑 ID သတ်မှတ်ပေးထားသည် -->
            <input type="password" name="password" id="password" required>
            
            <!-- 👁️ ၂။ Show Password Checkbox ထည့်သွင်းခြင်း -->
            <label class="show-pass-container">
                <input type="checkbox" id="togglePassword"> Show Password
            </label>
        </div>
        <button type="submit" class="btn-login">Sign In</button>
    </form>
</div>

<!-- 🌐 ၃။ Password အဖွင့်အပိတ် လုပ်ဆောင်ပေးမည့် JavaScript -->
<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('change', function () {
        // Checkbox မှာ အမှန်ခြစ်ရင် text ပြောင်းပြီး၊ ဖြုတ်ရင် password ပုံစံ ပြန်ပြောင်းမည်
        if (this.checked) {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    });
</script>

</body>
</html>
