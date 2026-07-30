<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Authentication</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff; /* ပုံပါအတိုင်း နောက်ခံ အဖြူရောင်စတိုင် */
            color: #333333;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Login Container Card */
        .login-box {
            width: 100%;
            max-width: 450px;
            padding: 40px 30px;
            text-align: center;
        }

        h2.title {
            font-size: 36px;
            color: #1f2937;
            margin-bottom: 40px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        /* Input Form Styles */
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 16px;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            font-size: 15px;
            color: #333;
            box-sizing: border-box;
            background-color: #fff;
        }

        input:focus {
            outline: none;
            border-color: #b80614;
        }

        /* Dark Red Buttons */
        .btn-red {
            width: 100%;
            padding: 16px;
            background: linear-gradient(180deg, #b80614 0%, #80000a 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .btn-red:hover {
            background: #b80614;
        }

        /* Toggle Link */
        .toggle-link {
            margin-top: 25px;
            font-size: 15px;
            color: #6b7280;
        }

        .toggle-link a {
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 500;
        }

        .toggle-link a:hover {
            text-decoration: underline;
        }

        /* ------------------ 🟥 SIGN UP MODAL STYLES ------------------ */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4); /* နောက်ခံကို မှိန်ချမည့်အလွှာ */
            display: none; /* အစပိုင်းမှာ ဖျောက်ထားမည် */
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .modal-box {
            background-color: #ffffff;
            width: 90%;
            max-width: 450px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            position: relative;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 22px;
            color: #111827;
            font-weight: bold;
            margin: 0;
        }

        .close-btn {
            font-size: 24px;
            color: #9ca3af;
            cursor: pointer;
            border: none;
            background: none;
        }

        .close-btn:hover {
            color: #111827;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <h2 class="title">သင့်အကောင့်သို့ ဝင်ရောက်ပါ</h2>
        <form action="login_process.php" method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn-red">ဝင်ရောက်ရန်</button>
        </form>
        <div class="toggle-link">
            အကောင့်မရှိသေးဘူးလား? <a href="#" id="openSignUp">အကောင့်အသစ်ဖွင့်ပါ</a>
        </div>
    </div>


    <div class="modal-overlay" id="signUpModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">အကောင့်အသစ် ပြုလုပ်ရန်</h3>
                <button class="close-btn" id="closeSignUp">&times;</button>
            </div>
            <form action="register_process.php" method="POST">
                <div class="form-group">
                    <input type="text" name="username" placeholder="အမည် (Username)" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_password" placeholder="Password ကိုအတည်ပြုပါ" required>
                </div>
                <button type="submit" class="btn-red">အကောင့်ပြုလုပ်ရန်</button>
            </form>
        </div>
    </div>

    <script>
        const openSignUpBtn = document.getElementById('openSignUp');
        const closeSignUpBtn = document.getElementById('closeSignUp');
        const signUpModal = document.getElementById('signUpModal');

        // အကောင့်အသစ်ဖွင့်ပါ ကိုနှိပ်ရင် Modal ပေါ်လာစေရန်
        openSignUpBtn.addEventListener('click', function(e) {
            e.preventDefault();
            signUpModal.style.display = 'flex';
        });

        // 'X' ပိတ်ခလုတ်ကိုနှိပ်ရင် Modal ပြန်ပျောက်သွားစေရန်
        closeSignUpBtn.addEventListener('click', function() {
            signUpModal.style.display = 'none';
        });

        // Modal ရဲ့ အပြင်ဘက်အမည်းနေရာကို နှိပ်ရင်လည်း ပိတ်သွားစေရန်
        window.addEventListener('click', function(e) {
            if (e.target === signUpModal) {
                signUpModal.style.display = 'none';
            }
        });
    </script>

</body>
</html>
