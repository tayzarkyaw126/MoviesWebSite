<?php
session_start();
include 'db.php';

// Login မဝင်ထားရင် နှင်ထုတ်မယ်
if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$user = mysqli_fetch_assoc($query);

// 🔔 သက်တမ်းကုန်ရက်၊ လက်ကျန်ရက် တွက်ချက်ခြင်း Logic
$noti_message = "";
$days_left = 0;

if (!empty($user['plan_expiry'])) {
    $expiry_date = strtotime($user['plan_expiry']);
    $current_date = time();
    $days_left = ceil(($expiry_date - $current_date) / 86400);

    if ($days_left <= 0) {
        // သက်တမ်းကုန်သွားရင် Normal ပြန်ပြောင်းမယ်
        mysqli_query($conn, "UPDATE users SET plan_type='Normal Member', plan_expiry=NULL WHERE id=$user_id");
        $user['plan_type'] = 'Normal Member';
        $user['plan_expiry'] = null;
        $noti_message = "❌ သင့်ရဲ့ Package သက်တမ်း ကုန်ဆုံးသွားပါပြီ။ ဇာတ်ကားများကြည့်ရှုရန် သက်တမ်းထပ်တိုးပေးပါ။";
    } elseif ($days_left <= 3) {
        $noti_message = "⚠️ သတိပေးချက်: သင့်ရဲ့ Premium သက်တမ်း ကုန်ဆုံးရန် $days_left ရက်သာ လိုပါတော့သည်။ ကျေးဇူးပြု၍ သက်တမ်းတိုးပေးပါရန်။";
    }
} else {
    $noti_message = "ℹ️ သင်သည် Normal Member ဖြစ်နေပါသည်။ ဇာတ်ကားများကြည့်ရှုရန် Package တစ်ခုခု ဝယ်ယူ/သက်တမ်းတိုးပေးပါ။";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile & Subscription</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f3f7fa; color: #333; margin: 0; padding: 20px 10px; display: flex; flex-direction: column; align-items: center; }
        .profile-container { width: 100%; max-width: 500px; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 30px 20px; box-sizing: border-box; text-align: center; }
        
        /* Noti Banner Style */
        .noti-banner { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; text-align: left; font-weight: 500; width: 100%; max-width: 500px; box-sizing: border-box; }
        .noti-expired { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        
        h2.section-title { color: #b80614; font-size: 24px; margin-bottom: 25px; }
        .info-row { display: flex; justify-content: space-between; padding: 12px 5px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .info-label { font-weight: bold; color: #555; }
        .badge { background-color: #1a237e; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        
        .btn-home { background-color: #007aff; color: white; text-decoration: none; display: block; padding: 12px; border-radius: 8px; font-weight: bold; margin-top: 20px; font-size: 14px; }
        .btn-logout { background-color: #dc3545; color: white; text-decoration: none; display: block; padding: 12px; border-radius: 8px; font-weight: bold; margin-top: 10px; font-size: 14px; }
        
        h3.sub-title { color: #dc3545; margin-top: 35px; font-size: 18px; margin-bottom: 5px; }
        .p-detail { color: #666; font-size: 12px; margin-bottom: 20px; }
        
        /* Table Premium Styles */
        .plan-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
        .plan-table th { background-color: #edf2f7; padding: 10px; font-size: 13px; color: #4a5568; }
        .plan-table td { padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
        .text-red { color: #b80614; font-weight: bold; }
        
        /* Form Inputs */
        .form-group { text-align: left; margin-top: 20px; }
        .form-group label { font-size: 13px; color: #666; display: block; margin-bottom: 8px; }
        .radio-group { display: flex; gap: 20px; margin-bottom: 15px; font-size: 13px; }
        input[type="text"], input[type="file"] { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; font-size: 14px; }
        
        .btn-submit { background: linear-gradient(180deg, #b80614 0%, #80000a 100%); color: white; border: none; width: 100%; padding: 14px; border-radius: 8px; font-size: 15px; font-weight: bold; cursor: pointer; margin-top: 20px; }
        
        /* Payment Footer */
        .payment-footer { width: 100%; max-width: 500px; margin-top: 40px; text-align: center; }
        .payment-title { font-weight: bold; font-size: 16px; margin-bottom: 15px; color: #1e293b; border-bottom: 2px solid #ddd; padding-bottom: 8px; }
        .pay-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .pay-card { background: white; padding: 12px; border-radius: 8px; border-top: 4px solid #ddd; text-align: left; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
        .pay-card.kpay { border-top-color: #007aff; }
        .pay-card.wave { border-top-color: #ffcc00; }
        .pay-card.cb { border-top-color: #00bcd4; }
        .pay-card.aya { border-top-color: #e60012; }
        .pay-name { font-size: 12px; font-weight: bold; margin: 5px 0 2px 0; }
        .pay-holder { font-size: 10px; color: #777; }
        .pay-number-box { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; color: #1e3a8a; }
        .btn-copy { background: #b80614; color: white; border: none; padding: 2px 6px; border-radius: 3px; font-size: 9px; cursor: pointer; }
    </style>
</head>
<body>

    <?php if(!empty($noti_message)): ?>
        <div class="noti-banner <?php echo ($days_left <= 0) ? 'noti-expired' : ''; ?>"><?php echo $noti_message; ?></div>
    <?php endif; ?>

    <div class="profile-container">
        <h2 class="section-title">User Profile</h2>
        
        <div class="info-row">
            <span class="info-label">Username :</span>
            <span><?php echo htmlspecialchars($user['username']); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Email :</span>
            <span><?php echo htmlspecialchars($user['email']); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Member Plan :</span>
            <span class="badge"><?php echo htmlspecialchars($user['plan_type']); ?></span>
        </div>
        
        <?php if(!empty($user['plan_expiry']) && $days_left > 0): ?>
        <div class="info-row">
            <span class="info-label">သက်တမ်းကုန်မည့်ရက် :</span>
            <span style="color:#2e7d32; font-weight:bold;"><?php echo $user['plan_expiry']; ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">လက်ကျန်ရက် :</span>
            <span style="color:#1e3a8a; font-weight:bold;"><?php echo $days_left; ?> ရက်</span>
        </div>
        <?php else: ?>
        <div class="info-row">
            <span class="info-label">လက်ကျန်ရက် :</span>
            <span style="color:#dc3545; font-weight:bold;">0 ရက် (သက်တမ်းကုန်ဆုံး/Plan မရှိ)</span>
        </div>
        <?php endif; ?>
        
        <!-- Back to Home Page & Logout Buttons -->
        <a href="index.php" class="btn-home">🏠 Back to Home Page</a>
        <a href="user_logout.php" class="btn-logout">ထွက်ရန် (Logout)</a>
        
        <h3 class="sub-title">Vip Subscribe လုပ်ရန်</h3>
        <p class="p-detail">Plan အသေးစိတ်ကို ဤနေရာတွင်ကြည့်ပါ</p>
        
        <table class="plan-table">
            <thead><tr><th colspan="2">Member Plan (၁) လ ဈေးနှုန်းများ</th></tr></thead>
            <tbody>
                <tr><td>Silver Member (၁ လ)</td><td class="text-red">မရသေးပါ</td></tr>
                <tr><td>Gold Member (၁ လ)</td><td class="text-red">5000 Ks</td></tr>
                <tr><td>Platinum Member (၁ လ)</td><td class="text-red">7000 Ks</td></tr>
            </tbody>
        </table>

        <table class="plan-table">
            <thead><tr><th colspan="2">Member Plan (၆) လ ဈေးနှုန်းများ</th></tr></thead>
            <tbody>
                <tr><td>Silver Member (၆ လ)</td><td class="text-red">မရသေးပါ</td></tr>
                <tr><td>Gold Member (၆ လ)</td><td class="text-red">25000 Ks</td></tr>
                <tr><td>Platinum Member (၆ လ)</td><td class="text-red">35000 Ks</td></tr>
            </tbody>
        </table>

        <table class="plan-table">
            <thead><tr><th colspan="2">Member Plan (၁) နှစ် ဈေးနှုန်းများ</th></tr></thead>
            <tbody>
                <tr><td>Silver Member (၁ နှစ်)</td><td class="text-red">မရသေးပါ</td></tr>
                <tr><td>Gold Member (၁ နှစ်)</td><td class="text-red">50000 Ks</td></tr>
                <tr><td>Platinum Member (၁ နှစ်)</td><td class="text-red">70000 Ks</td></tr>
            </tbody>
        </table>

        <form action="submit_payment.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Plan ရွေးချယ်ပါ</label>
                <div class="radio-group">
                    <label><input type="radio" name="plan_choice" value="Gold Member" required> Gold Member</label>
                    <label><input type="radio" name="plan_choice" value="Platinum Member"> Platinum Member</label>
                </div>
                <div class="radio-group" style="margin-top:-5px;">
                    <label><input type="radio" name="plan_duration" value="1 Month" required> ၁ လ သက်တမ်း</label>
                    <label><input type="radio" name="plan_duration" value="6 Months"> ၆ လ သက်တမ်း</label>
                    <label><input type="radio" name="plan_duration" value="1 Year"> ၁ နှစ် သက်တမ်း</label>
                </div>
            </div>
            
            <div class="form-group">
                <input type="text" name="translation_id" placeholder="ငွေလွှဲ Translation ID" required>
            </div>
            
            <div class="form-group">
                <label style="margin-top:10px;">ငွေလွှဲ Screenshot ထည့်ပါ</label>
                <input type="file" name="screenshot" accept="image/*" required>
            </div>
            
            <button type="submit" class="btn-submit">Submit လုပ်ရန်</button>
        </form>
    </div>

    <div class="payment-footer">
        <div class="payment-title">ငွေပေးချေရန်</div>
        <div class="pay-grid">
            <div class="pay-card kpay">
                <div class="pay-name">KBZ Pay</div>
                <div class="pay-holder">Tayzar Kyaw</div>
                <div class="pay-number-box"><span>09795253183</span><button class="btn-copy" onclick="copyNum('09795253183')">ကူးယူရန်</button></div>
            </div>
            <div class="pay-card wave">
                <div class="pay-name">Wave Pay</div>
                <div class="pay-holder">Tayzar Kyaw</div>
                <div class="pay-number-box"><span>09795253183</span><button class="btn-copy" onclick="copyNum('09795253183')">ကူးယူရန်</button></div>
            </div>
            <div class="pay-card cb">
                <div class="pay-name">CB Pay</div>
                <div class="pay-holder">Tayzar Kyaw</div>
                <div class="pay-number-box"><span>09795253183</span><button class="btn-copy" onclick="copyNum('09795253183')">ကူးယူရန်</button></div>
            </div>
            <div class="pay-card aya">
                <div class="pay-name">AYA Pay</div>
                <div class="pay-holder">Tayzar Kyaw</div>
                <div class="pay-number-box"><span>09795253183</span><button class="btn-copy" onclick="copyNum('09795253183')">ကူးယူရန်</button></div>
            </div>
        </div>
    </div>

    <script>
        function copyNum(num) {
            navigator.clipboard.writeText(num);
            alert("ဖုန်းနံပါတ် " + num + " ကို Copy ကူးပြီးပါပြီဗျာ။");
        }
    </script>
</body>
</html>
