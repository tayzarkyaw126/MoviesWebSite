<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendNexusEmail($to_email, $to_name, $scenario, $details = []) {
    $mail = new PHPMailer(true);

    try {
        // --- ⚙️ GMAIL SMTP CONFIGURATION ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';                 // Gmail SMTP Host သို့ ပြောင်းလဲခြင်း
        $mail->SMTPAuth   = true;
        $mail->Username   = 'waiwainaing73@gmail.com';          // သင့် Gmail Account
        $mail->Password   = 'ddbx njxw moky sdkg';            // ⚠️ အဆင့် (၁) တွင်ရရှိလာသော အက္ခရာ ၁၆ လုံးပါ App Password ကို ဤနေရာတွင် ထည့်ပါ
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';                          // ဗမာစာ မကွဲစေရန်

        // အီးမေးလ် ပို့မည့်သူနေရာတွင် သင့် Gmail ကို အသေထားပါမည်
        $mail->setFrom('waiwainaing73@gmail.com', 'Nexus Movies Support');

        // --- 📩 SCENARIOS (အခြေအနေအလိုက် အီးမေးလ်ပုံစံများ) ---
        if ($scenario === 'register') {
            // 👤 ပုံစံ (၁) - Register Complete
            $mail->Subject = 'Register Complete !';
            $mail->isHTML(true);
            $mail->Body    = "
                <div style='font-family: sans-serif; color: #222; max-width: 600px; line-height: 1.6;'>
                    <h2 style='font-size: 24px; font-weight: bold; margin-bottom: 5px;'>မင်္ဂလာပါ " . htmlspecialchars($to_name) . "</h2>
                    <p style='font-size: 16px; margin-top: 0;'>Email အကောင့် Register အောင်မြင်ပါတယ်</p>
                    <p style='font-size: 16px; font-weight: bold; margin-top: 25px;'>လူကြီးမင်း Register လုပ်ထားသော Information :</p>
                    <p style='font-size: 15px; margin: 5px 0;'><b>User Name -</b> " . htmlspecialchars($to_name) . "</p>
                    <p style='font-size: 15px; margin: 5px 0;'><b>Email -</b> " . htmlspecialchars($to_email) . "</p>
                    <p style='font-size: 15px; margin: 5px 0;'><b>Password -</b> " . htmlspecialchars($details['password']) . "</p>
                    <br><br>
                    <p style='font-size: 15px; font-weight: bold;'>Nexus Movies NextGen App ကိုအသုံးပြုသည့်အတွက်အထူးပင်ကျေးဇူးတင်ရှိပါသည်။</p>
                </div>";

        } elseif ($scenario === 'payment') {
            // 💳 ပုံစံ (၂) - Payment Received (ငွေလွှဲပြေစာ လက်ခံရရှိကြောင်း)
            $mail->Subject = 'Payment Invoice Received !';
            $mail->isHTML(true);
            $mail->Body    = "
                <div style='font-family: sans-serif; color: #222; max-width: 600px; line-height: 1.6;'>
                    <h2 style='font-size: 24px; font-weight: bold; margin-bottom: 5px;'>မင်္ဂလာပါ " . htmlspecialchars($to_name) . "</h2>
                    <p style='font-size: 16px; margin-top: 0; color: #2e7d32;'>လူကြီးမင်း ပေးပို့လိုက်သော ငွေလွှဲပြေစာအား လက်ခံရရှိပြီးဖြစ်ပါသည်။</p>
                    <p style='font-size: 15px; margin: 5px 0;'><b>ရွေးချယ်ထားသော Plan -</b> " . htmlspecialchars($details['plan_choice']) . "</p>
                    <p style='font-size: 15px; margin: 5px 0;'><b>Transaction ID -</b> " . htmlspecialchars($details['translation_id']) . "</p>
                    <br>
                    <p style='font-size: 15px; background-color: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; color: #856404;'>
                        <b>သတိပြုရန် -</b> လောလောဆယ်တွင် လူကြီးမင်း၏ သက်တမ်းတိုးမှုကို Admin မှ စစ်ဆေးနေဆဲဖြစ်ပါသည်။ စစ်ဆေးပြီးပါက VIP သက်တမ်းတိုးခြင်း အောင်မြင်ကြောင်း အီးမေးလ် ထပ်မံပေးပို့ပေးသွားပါမည်။
                    </p>
                </div>";

        } elseif ($scenario === 'approve') {
            // 👑 ပုံစံ (၃) - Vip Extension Complete (အတည်ပြုပြီးချိန်)
            $mail->Subject = 'Vip Extension Complete !';
            $mail->isHTML(true);
            $mail->Body    = "
                <div style='font-family: sans-serif; color: #222; max-width: 600px; line-height: 1.6;'>
                    <h2 style='font-size: 24px; font-weight: bold; margin-bottom: 5px;'>မင်္ဂလာပါ " . htmlspecialchars($to_name) . "</h2>
                    <p style='font-size: 16px; margin-top: 0; color: #1565c0;'>VIP သက်တမ်းတိုးခြင်း အောင်မြင်ပါပြီ။</p>
                    <p style='font-size: 15px; margin: 5px 0;'><b>Plan -</b> " . htmlspecialchars($details['plan_choice']) . "</p>
                    <p style='font-size: 15px; margin: 5px 0;'><b>မူလသက်တမ်းကုန်ဆုံးရက် -</b> " . htmlspecialchars($details['old_expiry']) . "</p>
                    <p style='font-size: 15px; margin: 5px 0; font-weight: bold;'><b>သက်တမ်းတိုးပြီးနောက်ကုန်ဆုံးရက် -</b> " . htmlspecialchars($details['new_expiry']) . "</p>
                    <br>
                    <p style='font-size: 15px;'>Nexus Movies Channel ကို အသုံးပြုပေးသည့်အတွက် အထူးကျေးဇူးတင်ရှိပါသည်။</p>
                </div>";
        }

        // --- 🚀 SEND EMAIL ---
        $mail->addAddress($to_email, $to_name);
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
