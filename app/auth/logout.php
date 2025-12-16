<?php
session_start();

// Kiểm tra xem có confirm đăng xuất không
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Đã confirm, tiến hành xóa session
    session_unset();
    session_destroy();

    // Xóa cookie
    setcookie('auth_token', '', time() - 3600, '/', '', false, true);
    setcookie('username', '', time() - 3600, '/', '', false, false);

    // Redirect về trang chủ
    header('Location: ../../public/index.php');
    exit;
} else {
?>
    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Đăng xuất</title>
    </head>

    <body>
        <script>
            if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
                // Nếu chọn OK, redirect với ?confirm=yes
                window.location.href = 'logout.php?confirm=yes';
            } else {
                // Nếu chọn Cancel, quay lại trang trước
                window.history.back();
            }
        </script>
    </body>

    </html>
<?php
}
?>