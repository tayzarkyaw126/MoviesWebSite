<?php
include 'auth.php';
include 'db.php';

$message = "";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM movies WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $movie = mysqli_fetch_assoc($result);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $poster_url = mysqli_real_escape_string($conn, $_POST['poster_url']);
    $video_url = mysqli_real_escape_string($conn, $_POST['video_url']);

    $update_query = "UPDATE movies SET title='$title', description='$description', poster_url='$poster_url', video_url='$video_url' WHERE id=$id";
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: admin.php?view=manage");
        exit();
    } else {
        $message = "<div class='alert error'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #121212; color: #ffffff; margin: 0; padding: 40px; }
        .container { max-width: 900px; margin: 0 auto; background-color: #1a1a1a; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
        h2 { margin-top: 0; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 15px; color: #ffffff; font-size: 24px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #bbb; }
        input[type="text"], textarea { width: 100%; padding: 12px; background-color: #222; border: 1px solid #333; border-radius: 4px; color: white; box-sizing: border-box; }
        input[type="text"]:focus, textarea:focus { border-color: #ff3333; outline: none; }
        .btn-container { display: flex; gap: 10px; margin-top: 25px; }
        .btn-submit { flex: 1; background-color: #e50914; color: white; padding: 14px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px; }
        .btn-submit:hover { background-color: #ff1e27; }
        .btn-cancel { flex: 1; background-color: #333; color: white; padding: 14px; text-decoration: none; text-align: center; border-radius: 4px; font-weight: bold; font-size: 16px; }
        .btn-cancel:hover { background-color: #444; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; font-weight: bold; }
        .error { background-color: #b71c1c; color: #ef9a9a; }

        /* 📱 Mobile Responsive */
        @media (max-width: 600px) {
            body { padding: 15px; }
            .container { padding: 20px 15px; }
            .btn-container { flex-direction: column; gap: 10px; }
            .btn-submit, .btn-cancel { flex: 1; width: auto; font-size: 13px; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🛠 ပြင်ဆင်ရန် - <?php echo htmlspecialchars($movie['title']); ?></h2>
    
    <?php echo $message; ?>

    <form action="edit.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $movie['id']; ?>">
        
        <div class="form-group">
            <label>ရုပ်ရှင်နာမည်</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
        </div>
        <div class="form-group">
            <label>ဇာတ်လမ်းအကျဉ်း</label>
            <textarea name="description" rows="5" required><?php echo htmlspecialchars($movie['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label>Poster ပုံ Link (URL)</label>
            <input type="text" name="poster_url" value="<?php echo htmlspecialchars($movie['poster_url']); ?>" required>
        </div>
        <div class="form-group">
            <label>ဗီဒီယို Link (URL)</label>
            <input type="text" name="video_url" value="<?php echo htmlspecialchars($movie['video_url']); ?>" required>
        </div>
        <div class="btn-container">
            <button type="submit" class="btn-submit">သိမ်းဆည်းမည်</button>
            <a href="admin.php?view=manage" class="btn-cancel">မလုပ်တော့ပါ</a>
        </div>
    </form>
</div>

</body>
</html>
