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
    

    $update_query = "UPDATE movies SET title='$title', description1='$description1', poster_url='$poster_url', video_url='$video_url', trailer_url='$trailer_url', description2='$description2', download_url1='$download_url1', download_url2='$download_url2', download_url3='$download_url3', telegram_url1='$telegram_url1', file_size='$file_size', movie_length='$movie_length', movies_seris='$movies_seris', release_date='$release_date', quality='$quality', resolution='$resolution', format='$format', genres='$genres', movies_type='$movies_type', subtitle='$subtitle', cast_list='$cast_list', author='$author', original_story='$original_story', screenwriter='$screenwriter', director='$director' WHERE id=$id";
    
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
            <textarea name="description1" rows="5" required><?php echo htmlspecialchars($movie['description1']); ?></textarea>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description2" rows="9" required><?php echo htmlspecialchars($movie['description2']); ?></textarea>
        </div>
        <div class="form-group">
            <label>Poster ပုံ Link (URL)</label>
            <input type="text" name="poster_url" value="<?php echo htmlspecialchars($movie['poster_url']); ?>" required>
        </div>
        <div class="form-group">
            <label>ဗီဒီယို Link (URL)</label>
            <input type="text" name="video_url" value="<?php echo htmlspecialchars($movie['video_url']); ?>" required>
        </div>
        <div class="form-group">
            <label>Trailer Link (URL)</label>
            <input type="text" name="trailer_url" value="<?php echo htmlspecialchars($movie['trailer_url']); ?>" required>
        </div>
        <div class="form-group">
            <label>Download Link1 (URL)</label>
            <input type="text" name="download_url1" value="<?php echo htmlspecialchars($movie['download_url1']); ?>" required>
        </div>
        <div class="form-group">
            <label>Download Link2 (URL)</label>
            <input type="text" name="download_url2" value="<?php echo htmlspecialchars($movie['download_url2']); ?>" required>
        </div>
        <div class="form-group">
            <label>Download Link3 (URL)</label>
            <input type="text" name="download_url3" value="<?php echo htmlspecialchars($movie['download_url3']); ?>" required>
        </div>
        <div class="form-group">
            <label>Telegram Link (URL)</label>
            <input type="text" name="telegram_url1" value="<?php echo htmlspecialchars($movie['telegram_url1']); ?>" required>
        </div>
        <div class="form-group">
            <label>File Size</label>
            <input type="text" name="file_size" value="<?php echo htmlspecialchars($movie['file_size']); ?>" required>
        </div>
        <div class="form-group">
            <label>Movie Length</label>
            <input type="text" name="movie_length" value="<?php echo htmlspecialchars($movie['movie_length']); ?>" required>
        </div>
        <div class="form-group">
            <label>Movie or Seris</label>
            <input type="text" name="movies_seris" value="<?php echo htmlspecialchars($movie['movies_seris']); ?>" required>
        </div>
        <div class="form-group">
            <label>Release Date</Data></label>
            <input type="text" name="release_date" value="<?php echo htmlspecialchars($movie['release_date']); ?>" required>
        </div>
        <div class="form-group">
            <label>Quality</label>
            <input type="text" name="quality" value="<?php echo htmlspecialchars($movie['quality']); ?>" required>
        </div>
        <div class="form-group">
            <label>Resolution</label>
            <input type="text" name="resolution" value="<?php echo htmlspecialchars($movie['resolution']); ?>" required>
        </div>
        <div class="form-group">
            <label>Format</label>
            <input type="text" name="format" value="<?php echo htmlspecialchars($movie['format']); ?>" required>
        </div>
        <div class="form-group">
            <label>Genres</label>
            <input type="text" name="genres" value="<?php echo htmlspecialchars($movie['genres']); ?>" required>
        </div>
        <div class="form-group">
            <label>Movies Type</label>
            <input type="text" name="movies_type" value="<?php echo htmlspecialchars($movie['movies_type']); ?>" required>
        </div>
        <div class="form-group">
            <label>Subtitle</label>
            <input type="text" name="subtitle" value="<?php echo htmlspecialchars($movie['subtitle']); ?>" required>
        </div>
        <div class="form-group">
            <label>Cast List</label>
            <input type="text" name="cast_list" value="<?php echo htmlspecialchars($movie['cast_list']); ?>" required>
        </div>        
        <div class="form-group">
            <label>Author</label>
            <input type="text" name="author" value="<?php echo htmlspecialchars($movie['author']); ?>" required>
        </div>
        <div class="form-group">
            <label>Original Story</label>
            <input type="text" name="original_story" value="<?php echo htmlspecialchars($movie['original_story']); ?>" required>
        </div>
        <div class="form-group">
            <label>Screenwriter</label>
            <input type="text" name="screenwriter" value="<?php echo htmlspecialchars($movie['screenwriter']); ?>" required>
        </div>
        <div class="form-group">
            <label>Director</label>
            <input type="text" name="director" value="<?php echo htmlspecialchars($movie['director']); ?>" required>
        </div>
        <div class="btn-container">
            <button type="submit" class="btn-submit">သိမ်းဆည်းမည်</button>
            <a href="admin.php?view=manage" class="btn-cancel">မလုပ်တော့ပါ</a>
        </div>
    </form>
</div>

</body>
</html>
