<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        // Password မှန်/မမှန် တိုက်စစ်ခြင်း
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            header("Location: profile.php"); // Login အောင်မြင်ရင် Profile စာမျက်နှာသို့ သွားမည်
            exit();
        }
    }
    echo "<script>alert('Email သို့မဟုတ် Password မှားယွင်းနေပါသည်။'); window.history.back();</script>";
}
?>
