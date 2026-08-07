<?php
session_start();
include 'db.php';

// Login ဝင်ထားခြင်း ရှိမရှိ စစ်ဆေးပြီး User Data ဆွဲထုတ်ခြင်း
$current_user = null;
if (isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    $u_query = mysqli_query($conn, "SELECT * FROM users WHERE id=$u_id");
    $current_user = mysqli_fetch_assoc($u_query);
}

// Database မှ ဇာတ်ကားများကို နောက်ဆုံးတင်ထားသောကား အရင်ပြစနစ်ဖြင့် ဆွဲထုတ်မည်
$query = "SELECT * FROM movies ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXUS MOVIES</title>
    <style>
        /* --- PC Default Style --- */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #121212; color: #ffffff; margin: 0; padding: 20px 40px; }
        
        /* Navigation Bar */
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; margin-bottom: 30px; border-bottom: 1px solid #222; }
        .brand { color: #e50914; font-size: 32px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; margin: 0; text-decoration: none; }
        .btn-admin { background-color: #e50914; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; font-size: 14px; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px; }
        .btn-admin:hover { background-color: #ff1e27; }

        /* Movie Grid */
        .movie-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 30px; max-width: 1400px; margin: 0 auto; }
        
        /* Movie Card Design */
        .movie-card { background-color: #171717; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.5); display: flex; flex-direction: column; transition: transform 0.2s, box-shadow 0.2s; }
        .movie-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(229,9,20,0.2); }
        
        .poster-wrapper { display: block; width: 100%; aspect-ratio: 2/3; overflow: hidden; background-color: #222; text-decoration: none; }
        .movie-poster { width: 100%; height: 100%; object-fit: cover; }
        
        .movie-info { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .movie-title { margin: 0 0 8px 0; font-size: 20px; font-weight: bold; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .movie-desc { margin: 0 0 20px 0; font-size: 14px; color: #aaa; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 42px; }

        .info-row {
            display: flex;
            align-items: baseline; /* စာကြောင်းအမြင့် ညီစေရန် */
            margin-bottom: 8px;   /* တန်းတစ်ခုနှင့်တစ်ခု အကွာအဝေး */
        }

        .info-row .label {
            width: 70px;          /* Label များ၏ အကျယ်ကို တညီတညွတ်တည်း ထားရန် */
            font-weight: bold;
            color: #28a745;       /* ပုံထဲကအတိုင်း အစိမ်းရောင် */
            flex-shrink: 0;       /* အကွက်မကျုံ့သွားစေရန် */
        }

        .info-row .colon {
            width: 20px;          /* Colon များ တညီတညွတ်တည်း ဖြစ်စေရန် */
            font-weight: bold;
            color: #28a745;
            text-align: center;   /* Colon ကို အလယ်တည့်တည့် ထားရန် */
            flex-shrink: 0;
        }

        .info-row .value {
            flex: 1;              /* ကျန်ရှိသော နေရာအလွတ်တစ်ခုလုံးကို ယူရန် */
            color: #ffc107;       /* ပုံထဲကအတိုင်း အဝါ/ဝါညိုရောင် */
            word-break: break-word; /* စာကြောင်းရှည်ပါက အောက်ကြောင်းသို့ စနစ်တကျ ဆင်းစေရန် */
        }
        
        
        .btn-play { background-color: #ffffff; color: #121212; text-decoration: none; text-align: center; padding: 12px; border-radius: 4px; font-weight: bold; font-size: 15px; transition: 0.2s; display: block; }
        .btn-play:hover { background-color: #e50914; color: white; }

        /* Mobile Responsive View */
        @media (max-width: 600px) {
            body { padding: 10px; }
            .navbar { padding: 10px 5px; margin-bottom: 20px; }
            .brand { font-size: 22px; }
            .btn-admin { padding: 8px 12px; font-size: 12px; }
            .movie-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .movie-info { padding: 12px; }
            .movie-title { font-size: 15px; margin-bottom: 4px; }
            .movie-desc { font-size: 11px; margin-bottom: 12px; height: 16px; -webkit-line-clamp: 1; }
            .btn-play { padding: 8px; font-size: 13px; }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="index.php" style="text-decoration:none;"><h1 class="brand">NEXUS MOVIES</h1></a>
        <div style="display: flex; gap: 10px; align-items: center;">
            <?php if ($current_user): ?>
                <a href="profile.php" class="btn-admin" style="background-color: #2a9d8f;">👤 <?php echo htmlspecialchars($current_user['username']); ?></a>
                <a href="user_logout.php" class="btn-admin" style="background-color: #dc3545;">Logout</a>
            <?php else: ?>
                <a href="userlogin.php" class="btn-admin" style="background-color: #333; color: white;">👤 User Login</a>
                <a href="login.php" class="btn-admin">Admin</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="movie-grid">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="movie-card">
            <a href="watch.php?id=<?php echo $row['id']; ?>" class="poster-wrapper">
                <img src="<?php echo htmlspecialchars($row['poster_url']); ?>" class="movie-poster" alt="Poster">
            </a>
            <div class="movie-info">
                <div><h3 class="movie-title"><?php echo htmlspecialchars($row['title']); ?></h3></div>
                
                <div class="info-row">
                    <span class="label">Casts</span>
                    <span class="colon">:</span>
                    <span class="value"><?php echo htmlspecialchars($row['cast_list']); ?></span>
                </div>

                <div class="info-row">
                    <span class="label">Author</span>
                    <span class="colon">:</span>
                    <span class="value"><?php echo htmlspecialchars($row['author']); ?></span>
                </div>

                <div class="info-row">
                    <span class="label">Director</span>
                    <span class="colon">:</span>
                    <span class="value"><?php echo htmlspecialchars($row['director']); ?></span>
                </div>

                <!-- ▶ Play Movie ခလုတ်နှိပ်လျှင် watch.php သို့ သွားမည် -->
                <a href="watch.php?id=<?php echo $row['id']; ?>" class="btn-play">▶ Play Movie</a>
            </div>
        </div>
        <?php endwhile; ?>
        
        <?php if(mysqli_num_rows($result) == 0): ?>
            <div style="grid-column: 1/-1; text-align: center; color: #666; padding: 60px; font-size: 16px;">ဇာတ်ကားများ မရှိသေးပါ။</div>
        <?php endif; ?>
    </div>

</body>
</html>
