<?php
include 'auth.php'; 
include 'db.php';
include_once 'email_sender.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['payment_id'])) {
    $payment_id = intval($_POST['payment_id']);
    $action = isset($_POST['action']) ? $_POST['action'] : 'approve';

    // 🗑️ ၁။ DELETE လုပ်ဆောင်ချက် (Database ထဲမှ အပြီးဖျက်မည် - Email လုံးဝမပို့ပါ)
    if ($action === 'delete') {
        $delete_stmt = $conn->prepare("DELETE FROM user_payments WHERE id = ?");
        $delete_stmt->bind_param("i", $payment_id);
        if ($delete_stmt->execute()) {
            echo "success";
        } else {
            echo "Delete လုပ်ဆောင်ချက် မအောင်မြင်ပါ: " . $delete_stmt->error;
        }
        $delete_stmt->close();
        exit;
    }
    
    // 🔑 ၅။ RESET PASSWORD လုပ်ဆောင်ချက် (အသစ်တိုးထားသည်)
    if ($action === 'reset_password') {
        $new_password_plain = $_POST['new_password'];
        
        // 🔒 PHP ၏ လုံခြုံစိတ်ချရဆုံး password_hash စနစ်ဖြင့် ပြောင်းလဲခြင်း
        $new_password_hashed = password_hash($new_password_plain, PASSWORD_DEFAULT);

        // payment_id ကို အသုံးပြု၍ user_id ကို ရှာဖွေခြင်း
        $user_stmt = $conn->prepare("SELECT user_id FROM user_payments WHERE id = ?");
        $user_stmt->bind_param("i", $payment_id);
        $user_stmt->execute();
        $user_res = $user_stmt->get_result();

        if ($user_res->num_rows === 1) {
            $user_row = $user_res->fetch_assoc();
            $user_id = $user_row['user_id'];

            // users table ထဲရှိ သက်ဆိုင်ရာ user ၏ password ကို update လုပ်ခြင်း
            $update_pass_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_pass_stmt->bind_param("si", $new_password_hashed, $user_id);
            
            if ($update_pass_stmt->execute()) {
                echo "success";
            } else {
                echo "Password ပြောင်းလဲခြင်း မအောင်မြင်ပါ: " . $update_pass_stmt->error;
            }
            $update_pass_stmt->close();
        } else {
            echo "User သို့မဟုတ် Record ရှာမတွေ့ပါ။";
        }
        $user_stmt->close();
        exit;
    }

    // 🔄 ၂။ UNREJECT လုပ်ဆောင်ချက် (Status ကို 'pending' သို့ ပြန်ပြောင်းမည် - Email လုံးဝမပို့ပါ)
    if ($action === 'unreject') {
        $unreject_stmt = $conn->prepare("UPDATE user_payments SET status='pending' WHERE id=?");
        $unreject_stmt->bind_param("i", $payment_id);
        if ($unreject_stmt->execute()) {
            echo "success";
        } else {
            echo "Unreject လုပ်ဆောင်ချက် မအောင်မြင်ပါ: " . $unreject_stmt->error;
        }
        $unreject_stmt->close();
        exit;
    }

    // 🔍 ၃။ APPROVE သို့မဟုတ် REJECT အတွက် မူရင်း Data ကို စစ်ဆေးခြင်း
    $stmt = $conn->prepare("SELECT p.*, u.username, u.email, u.plan_expiry, u.plan_type FROM user_payments p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $pay_data = $result->fetch_assoc();
        $user_id = $pay_data['user_id'];
        $user_name = $pay_data['username'];
        $user_email = $pay_data['email'];
        $full_plan = $pay_data['plan_choice']; 

        // ❌ ၃.၁။ REJECT လုပ်ဆောင်ချက် (Status ကို 'rejected' သို့ပြောင်းမည် - Email လုံးဝမပို့ပါ)
        if ($action === 'reject') {
            $update_payment = $conn->prepare("UPDATE user_payments SET status='rejected' WHERE id=?");
            $update_payment->bind_param("i", $payment_id);
            if ($update_payment->execute()) {
                // သတ်မှတ်ချက်အတိုင်း Reject ဖြစ်လျှင် Email မပို့တော့ဘဲ 'success' ဟု တိုက်ရိုက်ပြန်မည်။
                echo "success";
            } else {
                echo "Reject လုပ်ဆောင်ချက် မအောင်မြင်ပါ: " . $update_payment->error;
            }
            $update_payment->close();
            exit;
        }

        // ✅ ၃.၂။ APPROVE လုပ်ဆောင်ချက် (နဂိုမူရင်းအတိုင်း - Email ပို့ပါမည်)
        // ✅ ၃။၂။ APPROVE လုပ်ဆောင်ချက် (နဂိုမူရင်းအတိုင်း - Email ပို့ပါမည်)
        if ($action === 'approve') {
            
            // 🛑 အကယ်၍ အရင်ကတည်းက Approved ဖြစ်ပြီးသားဆိုလျှင် ရက်စွဲထပ်မတိုးဘဲ တန်းထွက်ရန်
            if ($pay_data['status'] === 'approved') {
                echo "success";
                exit;
            }

            if (strpos($full_plan, 'Platinum') !== false) {
                $plan_name = 'Platinum Member';
            } else {
                $plan_name = 'Gold Member';
            }
            
            $current_expiry = $pay_data['plan_expiry'];
            $base_time = (!empty($current_expiry) && strtotime($current_expiry) > time()) ? strtotime($current_expiry) : time();
            $old_expiry_display = (!empty($current_expiry)) ? date('d-m-Y', strtotime($current_expiry)) : date('d-m-Y');

            if (strpos($full_plan, '1 Year') !== false || strpos($full_plan, '12 Months') !== false) {
                $new_expiry_time = strtotime("+1 year", $base_time);
            } elseif (strpos($full_plan, '6 Months') !== false) {
                $new_expiry_time = strtotime("+6 months", $base_time);
            } else {
                $new_expiry_time = strtotime("+1 month", $base_time);
            }
            $new_expiry_date = date('Y-m-d', $new_expiry_time);
            $new_expiry_display = date('d-m-Y', $new_expiry_time);

            // Users Status ကို Update လုပ်ခြင်း
            $update_user = $conn->prepare("UPDATE users SET plan_type=?, plan_expiry=? WHERE id=?");
            $update_user->bind_param("ssi", $plan_name, $new_expiry_date, $user_id);
            if(!$update_user->execute()) {
                echo "Users Update Failed: " . $update_user->error;
                exit;
            }
            $update_user->close();

            // Payments Status ကို 'approved' သို့ Update လုပ်ခြင်း
            $update_payment = $conn->prepare("UPDATE user_payments SET status='approved' WHERE id=?");
            $update_payment->bind_param("i", $payment_id);
            if(!$update_payment->execute()) {
                echo "Payments Update Failed: " . $update_payment->error;
                exit;
            }
            $update_payment->close();

            // အတည်ပြုကြောင်း Email ပို့ခြင်း
            $mail_details = [
                'plan_choice' => $plan_name,
                'old_expiry'  => $old_expiry_display,
                'new_expiry'  => $new_expiry_display,
            ];
            
            try {
                sendNexusEmail($user_email, $user_name, 'approve', $mail_details);
            } catch (Exception $e) {
                // Email ပို့ရာတွင် error တက်သော်လည်း database update မပျက်စီးစေရန် error ကိုကျော်သွားမည်
            }

            echo "success";
        }
    } else {
        echo "Record Not Found.";
    }
    $stmt->close();
} else {
    echo "Invalid Request.";
}
?>
