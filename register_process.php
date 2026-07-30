<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "<script>alert('Password များ ကိုက်ညီမှု မရှိပါ!'); window.history.back();</script>";
        exit();
    }

    // Email ရှိပြီးသားလား စစ်ဆေးခြင်း
    $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check_email) > 0) {
        echo "<script>alert('ဤ Email ဖြင့် အကောင့်ဖွင့်ထားပြီးသား ဖြစ်နေပါသည်!'); window.history.back();</script>";
        exit();
    }

    // Password ကို Security အတွက် Hash လုပ်ပြီး သိမ်းဆည်းခြင်း
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

   $query = "INSERT INTO users (username, email, password, plan_type) VALUES ('$username', '$email', '$hashed_password', 'Normal Member')";
    // ... (အပေါ်ပိုင်း Email စစ်ဆေးခြင်း ကုဒ်များ)
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO users (username, email, password, plan_type) VALUES ('$username', '$email', '$hashed_password', 'Normal Member')";
    if (mysqli_query($conn, $query)) {
        
        // ✉️ Register Complete Auto Reply (support@) ပို့ခြင်း
        include_once 'email_sender.php';
        sendNexusEmail($email, $username, 'register', ['password' => $password]);

        echo "<script>alert('အကောင့်ဖွင့်ခြင်း အောင်မြင်ပါသည်။ Login ဝင်နိုင်ပါပြီ။'); window.location.href='userlogin.php';</script>";
    }
    // Database ထဲ SQL Insert အောင်မြင်ပြီးသွားတဲ့နေရာမှာ ထည့်ရန်
    if (mysqli_query($conn, $query)) {
    
    // ✉️ Auto Reply Email လှမ်းပို့ခြင်း
    include_once 'email_sender.php';
    sendNexusEmail($email, $username, 'register', ['password' => $password]);

    echo "<script>alert('Register အောင်မြင်ပါသည်'); window.location.href='login.php';</script>";
    }    else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
