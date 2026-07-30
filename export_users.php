<?php
include 'auth.php'; // Admin ဖြစ်မှ ဝင်ခွင့်ပေးမည်
include 'db.php';

// ကွန်ပျူတာထဲသို့ ဖိုင်အဖြစ် Download တန်းကျလာစေရန် Header သတ်မှတ်ခြင်း
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=nexus_users_' . date('Y-m-d') . '.csv');

// Output Stream ဖွင့်ခြင်း
$output = fopen('php://output', 'w');

// ✨ မြန်မာစာ (Unicode) Excel ထဲမှာ လုံးဝမကွဲစေရန် UTF-8 BOM ထည့်ခြင်း
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// ၁။ အစ်ကို လိုချင်သည့်အတိုင်း Column ခေါင်းစဉ်များကို အစီအစဉ်အသစ် ပြန်ပြင်ခြင်း
fputcsv($output, [
    'User ID', 
    'အမည် (Username)', 
    'အီးမေးလ် (Email)', 
    'အကောင့်အခြေအနေ (Status)', 
    'ဝယ်ယူထားသော VIP Plan',
    'အကောင့်ဖွင့်သည့်နေ့စွဲ',
    'စတင်ဝယ်ယူသည့်နေ့',
    'VIP သက်တမ်းကုန်ရက် (Expiry Date)',
    'လက်ကျန်ရက်ပေါင်း'
]);

// ၂။ Users Data ရော၊ ဝယ်ယူသည့်နေ့ (submitted_at) ပါ ထွက်လာအောင် SQL Query ပြင်ခြင်း
$query = "SELECT u.id, u.username, u.email, u.status, u.plan_expiry, u.created_at, 
          (SELECT plan_choice FROM user_payments WHERE user_id = u.id AND status = 'approved' ORDER BY id DESC LIMIT 1) as active_plan,
          (SELECT submitted_at FROM user_payments WHERE user_id = u.id AND status = 'approved' ORDER BY id DESC LIMIT 1) as purchase_date
          FROM users u 
          ORDER BY u.id DESC";

$result = mysqli_query($conn, $query);

// ၃။ Loop ပတ်ပြီး ဒေတာများကို တွက်ချက်ကာ CSV ထဲသို့ ထည့်ခြင်း
while ($row = mysqli_fetch_assoc($result)) {
    
    // VIP Plan အမည် ရှိမရှိ စစ်ဆေးခြင်း
    $vip_plan = $row['active_plan'] ? $row['active_plan'] : 'မရှိပါ (Free User)';
    
    // VIP Approved ဖြစ်ပြီးသားလူများကို Excel ထဲတွင် Status 'Approved' ဟု ပြောင်းပြခြင်း
    $account_status = $row['active_plan'] ? 'Approved' : $row['status'];
    
    // စတင်ဝယ်ယူသည့်နေ့ကို ပုံစံချခြင်း
    $purchase_date = 'မရှိပါ';
    if ($row['active_plan'] && !empty($row['purchase_date'])) {
        $purchase_date = date('Y-m-d H:i', strtotime($row['purchase_date']));
    }
    
    // VIP သက်တမ်းကုန်ရက် ရှိမရှိ စစ်ဆေးခြင်း
    $expiry_date = 'မရှိပါ';
    if ($row['active_plan'] && !empty($row['plan_expiry'])) {
        $expiry_date = date('Y-m-d', strtotime($row['plan_expiry']));
    }

    // ၄။ လက်ကျန်ရက်ပေါင်းကို ယနေ့ရက်စွဲနှင့် နှိုင်းယှဉ်တွက်ချက်ခြင်း
    $remaining_days = 'မရှိပါ';
    if ($row['active_plan'] && !empty($row['plan_expiry'])) {
        $today = strtotime(date('Y-m-d'));
        $expiry = strtotime($row['plan_expiry']);
        
        if ($expiry >= $today) {
            $diff = $expiry - $today;
            $days = round($diff / (60 * 60 * 24));
            $remaining_days = $days . " ရက်";
        } else {
            $remaining_days = "သက်တမ်းကုန်ပြီ";
        }
    }

    // ၅။ အစီအစဉ်အတိုင်း Excel ထဲသို့ ရိုက်ထည့်ခြင်း
    fputcsv($output, [
        $row['id'],
        $row['username'],
        $row['email'],
        $account_status,
        $vip_plan,
        date('Y-m-d H:i', strtotime($row['created_at'])), // အကောင့်ဖွင့်သည့်နေ့စွဲ
        $purchase_date,                                   // စတင်ဝယ်ယူသည့်နေ့
        $expiry_date,                                     // VIP သက်တမ်းကုန်ရက်
        $remaining_days                                   // လက်ကျန်ရက်ပေါင်း
    ]);
}

fclose($output);
exit;
?>
