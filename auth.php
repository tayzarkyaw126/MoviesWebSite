<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Login မဝင်ထားရင် login.php စာမျက်နှာဆီ အတင်းမောင်းထုတ်မည်
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>
