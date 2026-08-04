<?php
session_start();
include 'db.php';

// index.php မှ ပေးပို့လိုက်သော ID ကို ဖတ်ယူခြင်း
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$movie_id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM movies WHERE id = '$movie_id'";
$result = mysqli_query($conn, $query);
$movie = mysqli_fetch_assoc($result);

if (!$movie) {
    echo "ဇာတ်ကား ရှာမတွေ့ပါ။";
    exit;
}

// User Authorization & Subscription Check
$is_logged_in = isset($_SESSION['user_id']);
$can_play_movie = false;
$current_user = null;

if ($is_logged_in) {
    $u_id = $_SESSION['user_id'];
    $u_query = mysqli_query($conn, "SELECT * FROM users WHERE id=$u_id");
    $current_user = mysqli_fetch_assoc($u_query);

    if ($current_user && !empty($current_user['plan_expiry'])) {
        $expiry_date = strtotime($current_user['plan_expiry']);
        $current_date = time();
        $days_left = ceil(($expiry_date - $current_date) / 86400);

        // Plan သည် Normal Member မဟုတ်ဘဲ သက်တမ်းရက် (days_left) ကျန်သေးမှ Play အလုပ်လုပ်မည်
        if ($days_left > 0 && $current_user['plan_type'] !== 'Normal Member') {
            $can_play_movie = true;
        }
    }
}

// Play ခလုတ် နှိပ်ထားခြင်း ရှိမရှိ စစ်ဆေးခြင်း
$play_mode = isset($_GET['play']) ? $_GET['play'] : '';
$alert_message = "";

if ($play_mode === 'movie') {
    if (!$is_logged_in) {
        // ၁။ Login မဝင်ထားရင် userlogin.php ကို တန်းပို့မည်
        header("Location: userlogin.php");
        exit;
    } elseif (!$can_play_movie) {
        // ၂။ Login ဝင်ထားသော်လည်း Package မရှိလျှင်/သက်တမ်းကုန်လျှင် Play မပေးဘဲ Message ပြမည်
        $play_mode = ''; 
        $alert_message = "Package ဝယ်ပါ (သို့မဟုတ်) သက်တမ်းတိုးမှသာ Play Movie ကို ကြည့်ရှုနိုင်ပါမည်။";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?> - NEXUS MOVIES</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #0b0f14; color: #ffffff; margin: 0; padding: 20px; }
        .wrapper { max-width: 1000px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }
        
        /* Top Navigation in Watch Page */
        .watch-nav { display: flex; justify-content: space-between; align-items: center; background: #131c26; padding: 15px 25px; border-radius: 8px; border: 1px solid #1e2936; }
        .w-brand { color: #e50914; font-size: 24px; font-weight: bold; text-decoration: none; text-transform: uppercase; }
        
        /* 📺 VIDEO PLAYER BOX */
        .player-section { width: 100%; aspect-ratio: 16/9; background: #000; border-radius: 8px; overflow: hidden; border: 1px solid #222; }
        .video-frame { width: 100%; height: 100%; border: none; }

        /* 🔝 TOP INFO BLOCK */
        .movie-header-block { background-color: #131c26; padding: 25px; border-radius: 8px; display: flex; gap: 30px; border: 1px solid #1e2936; }
        .header-left { width: 220px; flex-shrink: 0; }
        .header-left img { width: 100%; aspect-ratio: 2/3; object-fit: cover; border-radius: 6px; box-shadow: 0 4px 20px rgba(0,0,0,0.6); }
        
        .header-right { flex-grow: 1; display: flex; flex-direction: column; gap: 12px; }
        .m-title { font-size: 32px; font-weight: bold; margin: 0; color: #fff; }
        
        /* Badges Row */
        .meta-badges { display: flex; align-items: center; gap: 10px; font-size: 14px; margin-top: -5px; }
        .badge-star { color: #f1c40f; font-weight: bold; }
        .badge-pill { background: #52b755; color: white; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        
        .info-row { font-size: 14px; color: #8e9aa8; margin: 4px 0; }
        .info-row strong { color: #fff; display: inline-block; width: 110px; }
        
        /* Play Buttons */
        .action-buttons { display: flex; gap: 12px; margin-top: 15px; }
        .p-btn { padding: 12px 24px; border: none; border-radius: 4px; font-weight: bold; font-size: 15px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
        .p-btn-trailer { background-color: #34495e; color: white; }
        .p-btn-trailer:hover { background-color: #2c3e50; }
        .p-btn-movie { background-color: #e50914; color: white; }
        .p-btn-movie:hover { background-color: #ff1e27; }

        /* 📝 MID BLOCK */
        .details-block { background-color: #131c26; border-radius: 8px; border: 1px solid #1e2936; overflow: hidden; }
        .tabs-bar { display: flex; background: #0e141b; border-bottom: 1px solid #1e2936; }
        .tab-item { padding: 12px 30px; font-weight: bold; font-size: 14px; cursor: pointer; }
        .tab-active { background: #52b755; color: white; }
        .tab-inactive { color: #8e9aa8; }
        
        .content-panel { padding: 25px; }
        .synopsis-text { font-size: 15px; color: #cbd5e1; line-height: 1.7; margin-bottom: 25px; text-align: justify; }
        
        .tech-specs { display: flex; flex-direction: column; gap: 8px; border-top: 1px solid #1e2936; padding-top: 20px; font-size: 14px; color: #cbd5e1; }
        .spec-line span { color: #8e9aa8; }

        /* 📥 BOTTOM BLOCK */
        .download-block { background-color: #131c26; padding: 25px; border-radius: 8px; border: 1px solid #1e2936; }
        .block-title { font-size: 18px; font-weight: bold; margin: 0 0 15px 0; border-bottom: 2px solid #1e2936; padding-bottom: 8px; color: #fff; }
        
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #1e2936; }
        th { color: #8e9aa8; font-weight: normal; background: #0e141b; }
        td a { color: #52b755; text-decoration: none; font-weight: bold; }
        td a:hover { text-decoration: underline; }
        .txt-green { color: #52b755; }

        /* 📱 Mobile Responsive View */
        @media (max-width: 768px) {
            .watch-nav { padding: 12px 15px; }
            .w-brand { font-size: 18px; }
            .movie-header-block { flex-direction: column; align-items: center; text-align: center; }
            .header-left { width: 180px; }
            .info-row strong { width: auto; display: block; margin-bottom: 2px; }
            .action-buttons { justify-content: center; width: 100%; }
            .p-btn { flex: 1; justify-content: center; }
            th, td { padding: 8px 10px; font-size: 12px; }
        }
    </style>
</head>
<body>

<?php if (!empty($alert_message)): ?>
    <script>
        alert("<?php echo $alert_message; ?>");
        if (confirm("Package ဝယ်ယူရန်/သက်တမ်းတိုးရန် Profile စာမျက်နှာသို့ သွားမလား?")) {
            window.location.href = "profile.php";
        }
    </script>
<?php endif; ?>

<div class="wrapper">

    <!-- Header bar with User info -->
    <div class="watch-nav">
        <a href="index.php" class="w-brand">NEXUS MOVIES</a>
        <div style="display: flex; gap: 10px; align-items: center;">
            <?php if ($current_user): ?>
                <a href="profile.php" style="background: #2a9d8f; color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; font-weight: bold; font-size: 14px;">👤 <?php echo htmlspecialchars($current_user['username']); ?></a>
                <a href="user_logout.php" style="background: #dc3545; color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; font-weight: bold; font-size: 14px;">Logout</a>
            <?php else: ?>
                <a href="userlogin.php" style="background: #34495e; color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; font-weight: bold; font-size: 14px;">👤 User Login</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 🎯 Play Button နှိပ်လိုက်မှ အပေါ်ဆုံးတွင် ဗီဒီယို Player ပေါ်လာစေမည့် စနစ် -->
    <?php if ($play_mode === 'movie' || $play_mode === 'trailer'): ?>
        <div class="player-section">
            <?php 
                $target_url = ($play_mode === 'trailer') ? $movie['trailer_url'] : $movie['video_url'];
                
                $is_youtube = false;
                if(strpos($target_url, 'youtube.com/watch?v=') !== false) {
                    $vid = explode('v=', $target_url)[1];
                    $vid = explode('&', $vid)[0];
                    $target_url = "https://www.youtube.com/embed/" . $vid . "?autoplay=1";
                    $is_youtube = true;
                } elseif(strpos($target_url, 'youtu.be/') !== false) {
                    $vid = explode('youtu.be/', $target_url)[1];
                    $vid = explode('?', $vid)[0];
                    $target_url = "https://www.youtube.com/embed/" . $vid . "?autoplay=1";
                    $is_youtube = true;
                } elseif(strpos($target_url, 'youtube.com/embed/') !== false) {
                    $is_youtube = true;
                }

                $is_direct_video = false;
                $lower_url = strtolower($target_url);
                if (strpos($lower_url, '.mp4') !== false || strpos($lower_url, '.webm') !== false || strpos($lower_url, '.mkv') !== false || strpos($lower_url, '.ogg') !== false) {
                    $is_direct_video = true;
                }
            ?>

            <?php if ($is_direct_video): ?>
                <video src="<?php echo htmlspecialchars($target_url); ?>" controls autoplay class="video-frame" style="width: 100%; height: 100%; object-fit: contain; background: #000;"></video>
            <?php else: ?>
                <iframe class="video-frame" src="<?php echo htmlspecialchars($target_url); ?>" allow="autoplay; fullscreen" allowfullscreen style="width: 100%; height: 100%; border: none;"></iframe>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- 🔝 ၁။ MOVIE HEADER BLOCK -->
    <div class="movie-header-block">
        <div class="header-left">
            <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" alt="Poster">
        </div>
        <div class="header-right">
            <h1 class="m-title"><?php echo htmlspecialchars($movie['title']); ?></h1>
            
            <div class="meta-badges">
                <div class="synopsis-text">
                <?php echo nl2br(htmlspecialchars($movie['description2'])); ?></div>
            </div>
            
            <div style="margin-top: 10px;">
                <div class="info-row"><strong>Director</strong> Phil Volken</div>
                <div class="info-row"><strong>Release Date</strong> 7/2/2026</div>
            </div>

            <div class="action-buttons">
                <a href="watch.php?id=<?php echo $movie['id']; ?>&play=trailer" class="p-btn p-btn-trailer">🎬 Play Trailer</a>
                <a href="watch.php?id=<?php echo $movie['id']; ?>&play=movie" class="p-btn p-btn-movie">▶ Play Movie</a>
            </div>
        </div>
    </div>

    <!-- 📝 ၂။ MID BLOCK (Synopsis & Technical Specs) -->
    <div class="details-block">
        <div class="tabs-bar">
            <div id="tab-synopsis" class="tab-item tab-active" onclick="switchTab('synopsis')">Synopsis</div>
            <div id="tab-cast" class="tab-item tab-inactive" onclick="switchTab('cast')">CastList</div>
        </div>
    
        <div class="content-panel">
            <div id="content-synopsis" class="synopsis-text">
                <?php echo nl2br(htmlspecialchars($movie['description1'])); ?>
            </div>
        
            <div id="content-cast" class="CastList-text" style="display: none;">
                <?php echo isset($movie['cast']) ? nl2br(htmlspecialchars($movie['cast'])) : "Cast list information is coming soon..."; ?>
            </div>
            
            <br>
            <div class="tech-specs">
                <div id="content-synopsis" class="synopsis-text">
                <?php echo nl2br(htmlspecialchars($movie['description2'])); ?></div>
                <div class="spec-line"><span>File size..</span> 2.34 GB / 1.36 GB</div>
                <div class="spec-line"><span>Quality..</span> WEB-DL 1080p / 720p</div>
                <div class="spec-line"><span>Format..</span> mp4</div>
                <div class="spec-line"><span>Genre..</span> Horror, Thriller</div>
                <div class="spec-line"><span>Subtitle..</span> Myanmar Subtitle (Hard Sub)</div>
            </div>
        </div>
    </div>

    <script>
    function switchTab(tabName) {
        document.getElementById('content-synopsis').style.display = 'none';
        document.getElementById('content-cast').style.display = 'none';
        
        document.getElementById('tab-synopsis').className = 'tab-item tab-inactive';
        document.getElementById('tab-cast').className = 'tab-item tab-inactive';
        
        if (tabName === 'synopsis') {
            document.getElementById('content-synopsis').style.display = 'block';
            document.getElementById('tab-synopsis').className = 'tab-item tab-active';
        } else if (tabName === 'cast') {
            document.getElementById('content-cast').style.display = 'block';
            document.getElementById('tab-cast').className = 'tab-item tab-active';
        }
    }
    </script>

    <!-- 📥 ၃။ BOTTOM BLOCK (Download Links Table) -->
    <div class="download-block">
        <h3 class="block-title">Download Links</h3>
        <table>
            <thead>
                <tr>
                    <th style="padding:12px; text-align:left;">No</th>
                    <th style="padding:12px; text-align:left;">Server Name</th>
                    <th style="padding:12px; text-align:left;">Size</th>
                    <th style="padding:12px; text-align:left;">Quality</th>
                    <th style="padding:12px; text-align:left;">Resolution</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:12px; border-top:1px solid #1e2936;">1</td>
                    <td style="padding:12px; border-top:1px solid #1e2936;"><a href="<?php echo htmlspecialchars($movie['download_url1']); ?>" target="_blank">Download Link1</a></td>
                    <td style="padding:12px; border-top:1px solid #1e2936;" class="txt-green">2.3 GB</td>
                    <td style="padding:12px; border-top:1px solid #1e2936;" class="txt-green">WEB-DL</td>
                    <td style="padding:12px; border-top:1px solid #1e2936;" class="txt-green">1080p</td>
                </tr>
                <tr>
                    <td style="padding:12px; border-top:1px solid #1e2936;">2</td>
                    <td style="padding:12px; border-top:1px solid #1e2936;"><a href="<?php echo htmlspecialchars($movie['download_url2']); ?>" target="_blank">Download Link2</a></td>
                    <td style="padding:12px; border-top:1px solid #1e2936;" class="txt-green">1.34 GB</td>
                    <td style="padding:12px; border-top:1px solid #1e2936;" class="txt-green">WEB-DL</td>
                    <td style="padding:12px; border-top:1px solid #1e2936;" class="txt-green">720p</td>
                </tr>
                <tr>
                    <td style="padding:12px; border-top:1px solid #1e2936;">3</td>
                    <td style="padding:12px; border-top:1px solid #1e2936;"><a href="<?php echo htmlspecialchars($movie['download_url3']); ?>" target="_blank">Download Link3</a></td>
                    <td style="padding:12px; border-top:1px solid #1e2936;" class="txt-green">1.34 GB</td>
                    <td style="padding:12px; border-top:1px solid #1e2936;" class="txt-green">WEB-DL</td>
                    <td style="padding:12px; border-top:1px solid #1e2936;" class="txt-green">720p</td>
                </tr>
                <tr>
                    <td style="padding:12px; border-top:1px solid #1e2936;">4</td>
                    <td style="padding:12px; border-top:1px solid #1e2936;"><a href="<?php echo htmlspecialchars($movie['download_url3']); ?>" onclick="alert('Telegram App ထဲသို့သွားပါမယ်')">Telegram Link</a></td>
                    <td style="padding:12px; border-top:1px solid #1e2936;" class="txt-green">1.34 GB</td>
                    <td style="padding:12px; border-top:1px solid #1e2936;" class="txt-green">WEB-DL</td>
                    <td style="padding:12px; border-top:1px solid #1e2936;" class="txt-green">720p</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
