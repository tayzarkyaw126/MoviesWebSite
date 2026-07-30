<?php
include 'auth.php';
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "DELETE FROM movies WHERE id = $id";
    mysqli_query($conn, $query);
}

// ဖျက်ပြီးရင် admin.php ရဲ့ manage view ဆီ ပြန်သွားမယ်
header("Location: admin.php?view=manage");
exit();
?>
