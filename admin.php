<?php
include 'auth.php';
include 'db.php';

$message = "";

// --- ဇာတ်ကားအသစ်လှမ်းတင်သည့် Backend Logic ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_movie'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description1 = mysqli_real_escape_string($conn, $_POST['description1']);    
    $poster_url = mysqli_real_escape_string($conn, $_POST['poster_url']);
    $video_url = mysqli_real_escape_string($conn, $_POST['video_url']);
    $trailer_url = mysqli_real_escape_string($conn, $_POST['trailer_url']);
    $description2 = mysqli_real_escape_string($conn, $_POST['description2']);   
    $download_url1 = mysqli_real_escape_string($conn, $_POST['download_url1']);
    $download_url2 = mysqli_real_escape_string($conn, $_POST['download_url2']);
    $download_url3 = mysqli_real_escape_string($conn, $_POST['download_url3']);
    $telegram_url1 = mysqli_real_escape_string($conn, $_POST['telegram_url1']);
    $file_size = mysqli_real_escape_string($conn, $_POST['file_size']);
    $movie_length = mysqli_real_escape_string($conn, $_POST['movie_length']);
    $movies_seris = mysqli_real_escape_string($conn, $_POST['movies_seris']);
    $release_date = mysqli_real_escape_string($conn, $_POST['release_date']);
    $quality = mysqli_real_escape_string($conn, $_POST['quality']);
    $resolution = mysqli_real_escape_string($conn, $_POST['resolution']);
    $format = mysqli_real_escape_string($conn, $_POST['format']);
    $genres = mysqli_real_escape_string($conn, $_POST['genres']);
    $movies_type = mysqli_real_escape_string($conn, $_POST['movies_type']);
    $subtitle = mysqli_real_escape_string($conn, $_POST['subtitle']);
    $cast_list = mysqli_real_escape_string($conn, $_POST['cast_list']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $original_story = mysqli_real_escape_string($conn, $_POST['original_story']);
    $screenwriter = mysqli_real_escape_string($conn, $_POST['screenwriter']);
    $director = mysqli_real_escape_string($conn, $_POST['director']);

    $insert_query = "INSERT INTO movies (title, description1, poster_url, video_url, trailer_url, description2, download_url1, download_url2, download_url3, telegram_url1, file_size, movie_length, movies_seris, release_date, quality, resolution, format, genres, movies_type, subtitle, cast_list, author, original_story, screenwriter, director ) VALUES ('$title', '$description1', '$poster_url', '$video_url', '$trailer_url', '$description2', '$download_url1', '$download_url2','$download_url3', '$telegram_url1', '$file_size', '$movie_length', '$movies_seris', '$release_date', '$quality','$resolution', '$format', '$genres','$movies_type', '$subtitle', '$cast_list', '$author', '$original_story', '$screenwriter', '$director')";
    
    if (mysqli_query($conn, $insert_query)) {
        $message = "<div class='alert success'>🎬 ဇာတ်ကားအသစ်ကို အောင်မြင်စွာ တင်ပြီးပါပြီ။</div>";
    } else {
        $message = "<div class='alert error'>Error: " . mysqli_error($conn) . "</div>";
    }
}

// --- ဘယ် View ကိုပြမလဲဆိုတာ စစ်ဆေးခြင်း ---
$view = isset($_GET['view']) ? $_GET['view'] : 'add';

if ($view == 'manage') {
    $query = "SELECT * FROM movies ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
}
// --- 🎯 ငွေလွှဲစာရင်းများကို Database မှ ဆွဲထုတ်ယူခြင်း Logic ---
elseif ($view == 'payments') {
    // pending ဖြစ်နေသော ငွေလွှဲမှုများကို အရင်ပြရန် ORDER BY ထည့်ထားပါသည်
    $payment_query = "SELECT p.*, u.username, u.email FROM user_payments p JOIN users u ON p.user_id = u.id ORDER BY p.status DESC, p.id DESC";
    $payment_result = mysqli_query($conn, $payment_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Admin Panel</title>
    <style>
        /* --- PC Style (Default) --- */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #121212; color: #ffffff; margin: 0; padding: 40px; }
        .container { max-width: 900px; margin: 0 auto; background-color: #1a1a1a; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 15px; }
        h2 { margin: 0; color: #ffffff; font-size: 24px; }
        
        .btn-group { display: flex; gap: 8px; }
        
        .btn-toggle { 
            width: 160px; 
            height: 40px; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            color: white; 
            text-decoration: none; 
            border-radius: 4px; 
            font-weight: bold; 
            font-size: 14px; 
            transition: 0.2s; 
            box-sizing: border-box; 
        }
        .btn-red { background-color: #e50914; }
        .btn-red:hover { background-color: #ff1e27; }
        .btn-dark { background-color: #333333; }
        .btn-dark:hover { background-color: #444444; }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #bbb; }
        input[type="text"], textarea { width: 100%; padding: 12px; background-color: #222; border: 1px solid #333; border-radius: 4px; color: white; box-sizing: border-box; }
        input[type="text"]:focus, textarea:focus { border-color: #ff3333; outline: none; }
        
        .btn-container { display: flex; gap: 10px; margin-top: 25px; }
        .btn-submit { flex: 1; background-color: #e50914; color: white; padding: 14px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px; }
        .btn-submit:hover { background-color: #ff1e27; }
        .btn-cancel { flex: 1; background-color: #333; color: white; padding: 14px; text-decoration: none; text-align: center; border-radius: 4px; font-weight: bold; font-size: 16px; }
        .btn-cancel:hover { background-color: #444; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background-color: #222222; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #333; }
        th { background-color: #2d2d2d; color: #ff3333; }
        tr:hover { background-color: #2a2a2a; }
        .poster-preview { width: 45px; height: 65px; object-fit: cover; border-radius: 4px; }
        .btn-edit { background-color: #3498db; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; margin-right: 5px; font-size: 13px; }
        .btn-delete { background-color: #e74c3c; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; }
        
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; font-weight: bold; }
        .success { background-color: #1b5e20; color: #a5d6a7; }
        .error { background-color: #b71c1c; color: #ef9a9a; }

        /* 📱 ---------------- Mobile Responsive CSS ---------------- 📱 */
        @media (max-width: 600px) {
            body { padding: 15px; }
            .container { padding: 20px 15px; }
            
            /* Header ကို ဒေါင်လိုက်ပြောင်းပြီး ခလုတ်များကို အောက်ပို့မည် */
            .header { 
                flex-direction: column; 
                align-items: flex-start; 
                gap: 15px; 
            }
            
            /* ခလုတ်အဖွဲ့အစည်းကို မျက်နှာပြင်အပြည့် ဆွဲဆန့်မည် */
            .btn-group { 
                width: 100%; 
            }
            
            /* ခလုတ်တစ်ခုချင်းစီကို Size တူညီစွာ ဘယ်ညာ တစ်ဝက်စီ ခွဲဝေမည် */
            .btn-toggle { 
                flex: 1; 
                width: auto; 
                font-size: 13px; /* စာသားဆံ့အောင် ဖောင့်အနည်းငယ်သေးရွှေ့ခြင်း */
            }

            /* Form အောက်ခြေခလုတ်များကိုလည်း ဒေါင်လိုက်စီမည် */
            .btn-container {
                flex-direction: column;
                gap: 10px;
            }
            .btn-submit, .btn-cancel {
                flex: 1; 
                width: auto; 
                font-size: 13px; /* စာသားဆံ့အောင် ဖောင့်အနည်းငယ်သေးရွှေ့ခြင်း */
            }

            /* Table ကြီး ဖုန်းမျက်နှာပြင်ကျော်မထွက်ဘဲ ဘေးတိုက် Scroll ဆွဲကြည့်နိုင်ရန် */
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>

<div class="container">
    
    <?php if ($view == 'add'): ?>
        <div class="header">
            <h2>🍿 Add New Movie</h2>
            <div class="btn-group">
                <a href="admin.php?view=manage" class="btn-toggle btn-red">Manage Movies</a>
                <a href="admin.php?view=payments" class="btn-toggle btn-red">Show Payment</a>
                <a href="logout.php" class="btn-toggle btn-red">Logout</a>
                <a href="export_users.php" class="btn-submit" style="background-color: #2e7d32; text-decoration: none; padding: 10px 20px; display: inline-block; margin-bottom: 20px;">Export</a>
            </div>
        </div>

        <?php echo $message; ?>

        <form action="admin.php?view=add" method="POST">
            <input type="hidden" name="add_movie" value="1">
            
            <div class="form-group">
                <label>ရုပ်ရှင်နာမည်</label>
                <input type="text" name="title" required placeholder="ဥပမာ - Big Buck Bunny">
            </div>

            <div class="form-group">
                <label>ဇာတ်လမ်းအကျဉ်း</label>
                <textarea name="description1" rows="5" required placeholder="ရုပ်ရှင်ဇာတ်လမ်းအကျဉ်းရေးရန်..."></textarea>
            </div>

            <div class="form-group">
                <label>Description2</label>
                <textarea name="description2" rows="9" required placeholder="Star.Year.Hour.Type.Director.Release"></textarea>
            </div>

            <div class="form-group">
                <label>Poster ပုံ Link (URL)</label>
                <input type="text" name="poster_url" required placeholder="https://example.com/poster.jpg">
            </div>

            <div class="form-group">
                <label>ဗီဒီယို Link (URL)</label>
                <input type="text" name="video_url" required placeholder="https://example.com/movie.mp4">
            </div>
            
            <div class="form-group">
                <label>Trailer Link (URL)</label>
                <input type="text" name="trailer_url" placeholder="https://example.com/trailer.mp4 သို့မဟုတ် YouTube Link">
            </div>

            <div class="form-group">
                <label>Download Link1 (URL)</label>
                <input type="text" name="download_url1" placeholder="https://example.com/download1 သို့မဟုတ် YouTube Link">
            </div>

            <div class="form-group">
                <label>Download Link2 (URL)</label>
                <input type="text" name="download_url2" placeholder="https://example.com/download2 သို့မဟုတ် YouTube Link">
            </div>

            <div class="form-group">
                <label>Download Link3 (URL)</label>
                <input type="text" name="download_url3" placeholder="https://example.com/download3 သို့မဟုတ် YouTube Link">
            </div>  
            
            <div class="form-group">
                <label>Telegram Link (URL)</label>
                <input type="text" name="telegram_url1" placeholder="https://example.com/telegram1 သို့မဟုတ် YouTube Link">
            </div>

            <div class="form-group">
                <label>File Size</label>
                <input type="text" name="file_size" placeholder="File Size ထည့်ပါ (e.g.. 2.5Gb or 1Gb ...)">
            </div>

            <div class="form-group">
                <label>Movie Length</label>
                <input type="text" name="movie_length" placeholder="ကြာချိန် ထည့်ပါ (e.g.. 90min or 1hr30min...)">
            </div>

            <div class="form-group">
                <label>Movies or Seris</label>
                <input type="text" name="movies_seris" placeholder="Movie or Seris ထည့်ပါ (e.g.. Movie or Seris...)">
            </div>

            <div class="form-group">
                <label>Release Date</label>
                <input type="text" name="release_date" placeholder="ထုတ်လုပ်သည့်ရက်စွဲ ထည့်ပါ (e.g.. 27/09/2007...)">
            </div>             
            
            <div class="form-group">
                <label>Quality</label>
                <input type="text" name="quality" placeholder="Quality ထည့်ပါ (e.g.. SD , HD , FHD , QHD , UHD , UUHD ...)">
            </div>

            <div class="form-group">
                <label>Resolution</label>
                <input type="text" name="resolution" placeholder="Resolution ထည့်ပါ (e.g 480p , 720p , 1080p , 2K , 4K , 8K ...)">
            </div>

            <div class="form-group">
                <label>Format</label>
                <input type="text" name="format" placeholder="Format ထည့်ပါ (e.g MP4 , MKV , MOV , AVI , WMV , WEBM , FLV ...)">
            </div>

            <div class="form-group">
                <label>Genrus</label>
                <input type="text" name="genres" placeholder="ဇာတ်လမ်းအမျိုးအစား ထည့်ပါ (e.g.. Horror (ထိတ်လန့်ဖွယ် ဇာတ်လမ်းများ) , Science Fiction / Sci-Fi (သိပ္ပံကမ္ဘာ စိတ်ကူးယဉ်) , Fantasy (စိတ်ကူးယဉ် မော်ကွန်း) , Thriller & Mystery (သည်းထိတ်ရင်ဖိုနှင့် လျှို့ဝှက်ဆန်းကြယ်) , Romance (အချစ်ဇာတ်လမ်း) , Drama (ဒရာမာ/ဘဝသရုပ်ဖော်) , Action & Adventure (လှုပ်ရှားမှုနှင့် စွန့်စားခန်း) , Historical Fiction (သမိုင်းနောက်ခံ ဇာတ်လမ်းများ) , Comedy (ဟာသ) ...)">
            </div>

            <div class="form-group">
                <label>Movies Type</label>
                <input type="text" name="movies_type" placeholder="ရုပ်ရှင်အမျိုးအစား ထည့်ပါ (e.g.. Live-Action Film , 2D Animation , 2D Animation ...)">
            </div>

            <div class="form-group">
                <label>Subtitle</label>
                <input type="text" name="subtitle" placeholder="ဘာသာစကားထည့်ပါ (e.g.. English , Myanmar , Non ...)">
            </div>

            <div class="form-group">
                <label>Cast List</label>
                <input type="text" name="cast_list" placeholder="သရုပ်ဆောင်များထည့်ပါ (e.g.. ဒွေး , ထက်ထက်မိုးဦး...)">
            </div>

            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" placeholder="စာရေးဆရာနာမည် ထည့်ပါ (e.g.. မောင်ထွန်းသူ , မြသန်းတင့်...)">
            </div>

            <div class="form-group">
                <label>Original Story</label>
                <input type="text" name="original_story" placeholder="မူရင်း ဝတ္ထုနာမည် ထည့်ပါ (e.g.. ဇစ်မြစ် , ရဲတိုက် , သုခမြို့တော်...)">
            </div>

            <div class="form-group">
                <label>Screenwriter</label>
                <input type="text" name="screenwriter" placeholder="ဇာတ်ညွှန်း ထည့်ပါ (e.g.. ကိုဇော်(အာရုဏ်ဦး) , ဝိုင်း(Own Creator)...)">
            </div>

            <div class="form-group">
                <label>Director</label>
                <input type="text" name="director" placeholder="ဒါရိုက်တာ ထည့်ပါ (e.g.. ကိုဇော်(အာရုဏ်ဦး) , ဝိုင်း(Own Creator)...)">
            </div>
            
            <div class="btn-container">
                <button type="submit" class="btn-submit">Upload Movie</button>
                <a href="/" class="btn-cancel">Website သို့ ပြန်သွားရန်</a>
            </div>
        </form>

    <?php elseif ($view == 'manage'): ?>
        <div class="header">
            <h2>🎬 ဇာတ်ကားများ စီမံ Dashboard</h2>
            <div class="btn-group">
                <a href="admin.php?view=add" class="btn-toggle btn-red">Add New Movies</a>
                <a href="logout.php" class="btn-toggle btn-dark">Logout</a>
                <a href="export_users.php" class="btn-submit" style="background-color: #2e7d32; text-decoration: none; padding: 10px 20px; display: inline-block; margin-bottom: 20px;">Export(CSV)</a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Poster</th>
                    <th>ရုပ်ရှင်နာမည်</th>
                    <th>ဇာတ်လမ်းအကျဉ်း</th>
                    <th>လုပ်ဆောင်ချက်</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><img src="<?php echo $row['poster_url']; ?>" class="poster-preview" alt="Poster"></td>
                    <td><strong><?php echo $row['title']; ?></strong></td>
                    <td><?php echo substr($row['description1'], 0, 80) . '...'; ?></td>                   
                    <td>
                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-edit">ပြင်ရန်</a>
                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('ဒီဇာတ်ကားကို တကယ်ပဲ ဖျက်မှာ သေချာပါသလား?')">ဖျက်ရန်</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                
                <?php if(mysqli_num_rows($result) == 0): ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: #888; padding: 20px;">ဇာတ်ကားများ မရှိသေးပါ။</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <!-- ---------------- 🎯 VIEW ၃။ 💳 MANAGE PAYMENTS SCREEN ---------------- -->
    <?php elseif ($view == 'payments'): ?>
        <div class="header">
            <h2>💳 VIP User List</h2>
            <div class="btn-group">
                <a href="admin.php?view=add" class="btn-toggle btn-red">Add New Movies</a>
                <a href="admin.php?view=manage" class="btn-toggle btn-red">Manage Movies</a>
                <a href="logout.php" class="btn-toggle btn-red">Logout</a>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>VIP Plan</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($payment_result)): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($row['username']); ?></strong><br>
                        <small style="color: #888;"><?php echo htmlspecialchars($row['email']); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($row['plan_choice']); ?></td>
                    <td>
                        <?php if ($row['status'] == 'approved'): ?>
                            <span class="badge badge-approved" style="color: #2e7d32; font-weight: bold;">Approved ✅</span>
                        <?php elseif ($row['status'] == 'rejected'): ?>
                            <span class="badge badge-rejected" style="color: #c62828; font-weight: bold;">Rejected ❌</span>
                        <?php else: ?>
                            <span class="badge badge-pending" style="color: #ffa000; font-weight: bold;">Pending ⏳</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px; align-items: center; white-space: nowrap;">
                            <?php if ($row['status'] == 'approved'): ?>
                                <span style="color: #666; font-size: 13px; margin-right: 5px;">Finished</span>
                            <?php else: ?>
                                
                                <!-- 1. Approve Button -->
                                <?php if ($row['status'] == 'rejected'): ?>
                                    <button type="button" style="background-color: #555; color: #888; padding: 6px 10px; border: none; border-radius: 4px; cursor: not-allowed; font-weight: bold; font-size: 13px;" disabled>✅ Approve</button>
                                <?php else: ?>
                                    <button type="button" id="btn-approve-<?php echo $row['id']; ?>" onclick="managePayment(<?php echo $row['id']; ?>, 'approve')" style="background-color: #2e7d32; color: white; padding: 6px 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 13px;">✅ Approve</button>
                                <?php endif; ?>

                                <!-- 2. Reject / Unreject Button -->
                                <?php if ($row['status'] == 'rejected'): ?>
                                    <button type="button" id="btn-unreject-<?php echo $row['id']; ?>" onclick="managePayment(<?php echo $row['id']; ?>, 'unreject')" style="background-color: #f39c12; color: white; padding: 6px 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 13px;">🔄 Unreject</button>
                                <?php else: ?>
                                    <button type="button" id="btn-reject-<?php echo $row['id']; ?>" onclick="managePayment(<?php echo $row['id']; ?>, 'reject')" style="background-color: #d35400; color: white; padding: 6px 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 13px;">❌ Reject</button>
                                <?php endif; ?>

                            <?php endif; ?>

                            <!-- 3. Delete Button -->
                            <button type="button" id="btn-delete-<?php echo $row['id']; ?>" onclick="managePayment(<?php echo $row['id']; ?>, 'delete')" style="background-color: #c0392b; color: white; padding: 6px 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 13px;">Delete</button>
                            
                            <!-- 4. ✨ Reset Password Button (အသစ်တိုးထားသည်) -->
                            <button type="button" onclick="openResetModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>')" style="background-color: #2980b9; color: white; padding: 6px 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 13px;">ResetPass</button>
                            
                            <!-- ပြေစာပုံကို Tab အသစ်ဖြင့် လှမ်းဖွင့်ကြည့်မည့် ခလုတ်အသစ် (Underline ဖျောက်ပြီး) -->
                            <a href="<?php echo $row['screenshot_url']; ?>" target="_blank" class="btn btn-sm btn-success" style="background-color: #2980b9; color: white; padding: 6px 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 13px; text-decoration: none; display: inline-block;">Payment</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- ➖➖➖➖➖➖➖➖ ✨ PASSWORD RESET MODAL POPUP (HTML/CSS) ➖➖➖➖➖➖➖➖ -->
        <div id="passwordModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); align-items: center; justify-content: center;">
            <div style="background-color: #222; border: 1px solid #444; padding: 25px; border-radius: 8px; width: 350px; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">
                <h3 style="margin-top: 0; color: #2980b9; border-bottom: 1px solid #444; padding-bottom: 10px;">🔑 Password Reset ပြုလုပ်ရန်</h3>
                <p style="font-size: 14px; color: #ccc;">User: <strong id="modalUsername" style="color: #fff;"></strong></p>
                
                <input type="hidden" id="modalPaymentId">
                
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 13px; display: block; margin-bottom: 5px; color: #aaa;">New Password</label>
                    <input type="password" id="newPassword" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #555; background-color: #333; color: white; box-sizing: border-box;">
                </div>
                
                <div style="margin-bottom: 10px;">
                    <label style="font-size: 13px; display: block; margin-bottom: 5px; color: #aaa;">Confirm Password</label>
                    <input type="password" id="confirmPassword" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #555; background-color: #333; color: white; box-sizing: border-box;">
                </div>

                <!-- 👁️ Show Password Checkbox -->
                <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 5px;">
                    <input type="checkbox" id="showPasswordToggle" onclick="togglePasswordVisibility()" style="cursor: pointer;">
                    <label for="showPasswordToggle" style="font-size: 12px; color: #bbb; cursor: pointer; user-select: none;">Show Password</label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeResetModal()" style="background-color: #555; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Cancel</button>
                    <button type="button" onclick="submitPasswordReset()" style="background-color: #2980b9; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">OK</button>
                </div>
            </div>
        </div>

        <!-- 🎯 JavaScript Functions -->
        <script>
        // Approve, Reject, Delete တို့အတွက် မူရင်း AJAX Script[cite: 1]
        function managePayment(paymentId, action) {
            let confirmMsg = "";
            if (action === 'approve') confirmMsg = "ဒီ User ကို VIP အဖြစ် အတည်ပြုမှာ သေချာပါသလား?";
            if (action === 'reject') confirmMsg = "ဒီငွေလွှဲမှုကို ငြင်းပယ် (Reject) မှာ သေချာပါသလား?";
            if (action === 'unreject') confirmMsg = "မူလ Pending အခြေအနေသို့ ပြန်ပြောင်းမှာ သေချာပါသလား?";
            if (action === 'delete') confirmMsg = "ဒီမှတ်တမ်းကို Database ထဲကပါ အပြီးဖျက်မှာ သေချာပါသလား?";

            if (confirm(confirmMsg)) {
                let formData = new FormData();
                formData.append('payment_id', paymentId);
                formData.append('action', action);

                fetch('approve_payment.php', { method: 'POST', body: formData })
                .then(res => res.text())
                .then(data => {
                    if (data.trim() === 'success') window.location.reload();
                    else alert("Error: " + data.trim());
                });
            }
        }

        // 🔑 Reset Pass ခလုတ်နှိပ်လျှင် ပထမဆုံး Confirm မေးပြီး Modal ဖွင့်ပေးမည့် Function
        function openResetModal(paymentId, username) {
            if (confirm(`User "${username}" ၏ Password ကို Reset လုပ်ရန် သေချာပါသလား?`)) {
                document.getElementById('modalPaymentId').value = paymentId;
                document.getElementById('modalUsername').innerText = username;
                document.getElementById('newPassword').value = "";
                document.getElementById('confirmPassword').value = "";
                document.getElementById('showPasswordToggle').checked = false;
                document.getElementById('newPassword').type = "password";
                document.getElementById('confirmPassword').type = "password";
                
                document.getElementById('passwordModal').style.display = 'flex';
            }
        }

        function closeResetModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }

        // 👁️ Show Password Toggle
        function togglePasswordVisibility() {
            const show = document.getElementById('showPasswordToggle').checked;
            document.getElementById('newPassword').type = show ? "text" : "password";
            document.getElementById('confirmPassword').type = show ? "text" : "password";
        }

        // Modal ထဲမှာ OK နှိပ်လိုက်လျှင် AJAX ဖြင့် Backend သို့ပို့မည့် Function
        function submitPasswordReset() {
            const paymentId = document.getElementById('modalPaymentId').value;
            const newPass = document.getElementById('newPassword').value;
            const confirmPass = document.getElementById('confirmPassword').value;

            if (newPass === "" || confirmPass === "") {
                alert("Password များကို ပြည့်စုံစွာ ရိုက်ထည့်ပေးပါရန်။");
                return;
            }
            if (newPass !== confirmPass) {
                alert("Password နှစ်ခု ကိုက်ညီမှု မရှိပါ။ ပြန်လည်စစ်ဆေးပါ။");
                return;
            }

            let formData = new FormData();
            formData.append('payment_id', paymentId);
            formData.append('action', 'reset_password');
            formData.append('new_password', newPass);

            fetch('approve_payment.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === 'success') {
                    alert("Password ကို အောင်မြင်စွာ ပြောင်းလဲပေးလိုက်ပါပြီ။");
                    closeResetModal();
                } else {
                    alert("Error: " + data.trim());
                }
            })
            .catch(err => alert("ချိတ်ဆက်မှု မအောင်မြင်ပါ"));
        }
        </script>
    <?php endif; ?>
</div>
</body>
</html>
