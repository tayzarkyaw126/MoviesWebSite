<?php
include 'db.php';
include_once 'email_sender.php';

if (isset($_GET['payment_id'])) {
    $payment_id = intval($_GET['payment_id']);

    // ၁။ တင်ထားသော Payment အချက်အလက်နှင့် User အချက်အလက်ကို ဆွဲထုတ်ခြင်း
    $pay_query = mysqli_query($conn, "SELECT p.*, u.username, u.email, u.plan_expiry, u.plan_type FROM user_payments p JOIN users u ON p.user_id = u.id WHERE p.id = $payment_id");
    
    if (mysqli_num_rows($pay_query) === 1) {
        $pay_data = mysqli_fetch_assoc($pay_query);
        $user_id = $pay_data['user_id'];
        $user_name = $pay_data['username'];
        $user_email = $pay_data['email'];
        
        // Plan အမျိုးအစားခွဲထုတ်ခြင်း (ဥပမာ "Gold Member (1 Month)" ထဲကနေ ခွဲထုတ်ခြင်း)
        $full_plan = $pay_data['plan_choice']; 
        $plan_name = (strpos($full_plan, 'Platinum') !== false) ? 'Platinum Member' : 'Gold Member';
        
        // ၂။ မူလသက်တမ်းကုန်ဆုံးရက်ကို မှတ်သားခြင်း (မရှိရင် ယနေ့ရက်စွဲ သတ်မှတ်မည်)
        $current_expiry = $pay_data['plan_expiry'];
        $base_time = (!empty($current_expiry) && strtotime($current_expiry) > time()) ? strtotime($current_expiry) : time();
        $old_expiry_display = (!empty($current_expiry)) ? date('d-m-Y', strtotime($current_expiry)) : date('d-m-Y');

        // ၃။ သက်တမ်း ရက်တိုးမြှင့်ခြင်း တွက်ချက်မှု Logic
        if (strpos($full_plan, '1 Year') !== false) {
            $new_expiry_time = strtotime("+1 year", $base_time);
        } elseif (strpos($full_plan, '6 Months') !== false) {
            $new_expiry_time = strtotime("+6 months", $base_time);
        } else {
            $new_expiry_time = strtotime("+1 month", $base_time); // Default ၁ လ
        }
        $new_expiry_date = date('Y-m-d', $new_expiry_time);
        $new_expiry_display = date('d-m-Y', $new_expiry_time);

        // ၄။ Database ထဲတွင် User သက်တမ်းအား Update ပြုလုပ်ခြင်း နှင့် Payment status ကို approved ပြောင်းခြင်း
        mysqli_query($conn, "UPDATE users SET plan_type='$plan_name', plan_expiry='$new_expiry_date' WHERE id=$user_id");
        mysqli_query($conn, "UPDATE user_payments SET status='approved' WHERE id=$payment_id");

        // ✉️ ပုံစံ (၂) - Vip Extension Complete Auto Reply (no-reply@) ပို့ခြင်း
        $mail_details = [
            'plan_choice' => $plan_name,
            'old_expiry'  => $old_expiry_display,
            'new_expiry'  => $new_expiry_display
        ];
        sendNexusEmail($user_email, $user_name, 'approve', $mail_details);

        echo "<script>alert('Approve လုပ်ခြင်း အောင်မြင်ပြီး User ထံသို့ သက်တမ်းတိုး Email ပို့ဆောင်ပြီးပါပြီဗျာ။'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "Payment Record Not Found.";
    }
}
?>
