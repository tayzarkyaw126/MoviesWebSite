<?php
// db.php ကို ချိတ်ဆက်ခြင်း
include 'db.php';

// ဇာတ်ကားအားလုံးကို Database ထဲက ဆွဲထုတ်ခြင်း
$query = "SELECT * FROM movies ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Movies - Admin Panel</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #121212; color: #ffffff; margin: 0; padding: 40px; }
        .container { max-width: 1000px; margin: 0 auto; background-color: #1a1a1a; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        h2 { margin: 0; color: #ff3333; }
        .btn-add { background-color: #e50914; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #222222; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #333; }
        th { background-color: #2d2d2d; color: #ff3333; }
        tr:hover { background-color: #2a2a2a; }
        .poster-preview { width: 50px; height: 70px; object-fit: cover; border-radius: 4px; }
        .btn-edit { background-color: #3498db; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; margin-right: 5px; font-size: 14px; }
        .btn-delete { background-color: #e74c3c; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn-edit:hover { background-color: #2980b9; }
        .btn-delete:hover { background-color: #c0392b; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>🎬 ဇာတ်ကားများ စီမံခန့်ခွဲရန် Dashboard</h2>
        <a href="index.php" class="btn-add">+ ဇာတ်ကားအသစ်တင်ရန်</a>
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
                <td><?php echo substr($row['description'], 0, 100) . '...'; ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-edit">ပြင်ရန်</a>
                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('ဒီဇာတ်ကားကို တကယ်ပဲ ဖျက်မှာ သေချာပါသလား?')">ဖျက်ရန်</a>
                </td>
            </tr>
            <?php endwhile; ?>
            
            <?php if(mysqli_num_rows($result) == 0): ?>
            <tr>
                <td colspan="4" style="text-align: center; color: #888;">ဇာတ်ကားများ မရှိသေးပါ။</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
