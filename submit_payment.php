<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    exit("Access Denied");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = (int)$_SESSION['user_id']; // Integer ဖြစ်အောင် Cast လုပ်ပြီး SQL Injection ကာကွယ်ခြင်း
    
    // String concatenate လုပ်ပြီးမှ input ကို ယူခြင်း
    $plan_choice = $_POST['plan_choice'] . " (" . $_POST['plan_duration'] . ")";
    $translation_id = $_POST['translation_id'];
    
    // ဓာတ်ပုံ သိမ်းဆည်းရန် နေရာဖန်တီးခြင်း
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // --- 🛡️ FILE UPLOAD VALIDATION START ---
    if (!isset($_FILES["screenshot"]) || $_FILES["screenshot"]["error"] !== UPLOAD_ERR_OK) {
        exit("ဖိုင်တင်ရာတွင် အမှားအယွင်းရှိနေပါသည်။");
    }

    // စစ်ဆေးမည့် File Extensions များ
    $allowed_extensions = array("jpg", "jpeg", "png");
    $file_extension = strtolower(pathinfo($_FILES["screenshot"]["name"], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        exit("Error: JPG, JPEG နှင့် PNG ဓာတ်ပုံဖိုင်များသာ လက်ခံပါသည်။");
    }

    // File Size ကန့်သတ်ခြင်း (ဥပမာ- 5MB ထက်မကြီးရ)
    if ($_FILES["screenshot"]["size"] > 5 * 1024 * 1024) {
        exit("Error: ဖိုင်ဆိုဒ် 5MB ထက် မကျော်ရပါ။");
    }

    // File Name ကို ပိုလုံခြုံအောင် Random String နဲ့ ပြောင်းပေးခြင်း (Malicious script injection ကာကွယ်ရန်)
    $file_name = time() . "_" . bin2hex(random_bytes(8)) . "." . $file_extension;
    $target_file = $target_dir . $file_name;
    // --- 🛡️ FILE UPLOAD VALIDATION END ---

    if (move_uploaded_file($_FILES["screenshot"]["tmp_name"], $target_file)) {
        
        // 🔒 Prepared Statement သုံးပြီး SQL Injection ကို ရာနှုန်းပြည့်ကာကွယ်ခြင်း
        $stmt = $conn->prepare("INSERT INTO user_payments (user_id, plan_choice, translation_id, screenshot_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $plan_choice, $translation_id, $target_file);
        
        if ($stmt->execute()) {
            // ✉️ Email ပို့ခြင်း Logic
            include_once 'email_sender.php';
            $details_data = [
                'plan_choice' => $plan_choice,
                'translation_id' => $translation_id
            ];
            
            // Session variables တွေ ရှိမရှိ အရင်စစ်ဆေးပါ
            $user_email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
            $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
            
            if (!empty($user_email)) {
                sendNexusEmail($user_email, $username, 'payment', $details_data);
            }

            echo "<script>alert('ငွေလွှဲပြေစာ ပေးပို့ခြင်း အောင်မြင်ပါသည်။ Admin အတည်ပြုချက်ကို စောင့်ဆိုင်းပေးပါဗျာ။'); window.location.href='profile.php';</script>";
        } else {
            echo "DB Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "ဖိုင်ကို Upload Folder ထဲသို့ ရွှေ့ပြောင်း၍ မရပါ (Permission Error ဖြစ်နိုင်သည်)။";
    }
}
?>
