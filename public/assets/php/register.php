<?php
session_start();
require 'connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $repass = isset($_POST['repass']) ? trim($_POST['repass']) : '';

    // Validation
    $errors = [];
    if (empty($fullname)) {
        $errors[] = 'Nhập họ và tên';
    }
    
    if (empty($email)) {
        $errors[] = 'Nhập email';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
        $errors[] = 'Email không hợp lệ';
    }

    if (empty($username)) {
        $errors[] = 'Nhập tên tài khoản';
    } else if (strlen($username) < 3) {
        $errors[] = 'Tài khoản ít nhất 3 ký tự';
    } 

    if (empty($password)) {
        $errors[] = 'Nhập mật khẩu';
    } else if (strlen($password) < 6) {
        $errors[] = 'Mật khẩu ít nhất 6 ký tự';
    }   

    if (empty($repass)) {
         $errors[] = 'Nhập lại mật khẩu';
    } else if ($password !== $repass) {
    $errors[] = 'Mật khẩu nhập lại không khớp';
    }
    if (!empty($errors)) {
        $error = implode(' | ', $errors);
    } else {
        // kiểm tra trùng lặp
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = 'Tài khoản hoặc email đã tồn tại!';
        } else {
            // mã hoá
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            // thêm vào db
            $add = $conn->prepare("INSERT INTO users (fullname, email, username, password) VALUES (?, ?, ?, ?)");
            $add->bind_param("ssss", $fullname, $email, $username, $hashedPassword);
            if ($add->execute()) {
                $success = 'Đăng ký thành công! Vui lòng đăng nhập.';
                $redirect = true;
            } else {
                $error = 'Lỗi khi đăng ký!';
            }
            $add->close();
        }
        $check->close();
    }
    $conn->close();
}

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản - 36Tech</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/notification.css">
</head>

<body>
    <form id="register-form" method="POST" autocomplete="off" onsubmit="handleRegister(event)">
        <h3>Đăng ký tài khoản mới</h3>
        <label for="fullname">Họ và tên</label>
        <input id="fullname" name="fullname" placeholder="Họ và tên" type="text">

        <label for="email">Email</label>
        <input id="email" name="email" placeholder="Email" type="email">

        <label for="username">Tên tài khoản</label>
        <input id="username" name="username" placeholder="Tên tài khoản" type="text">

        <label for="password">Mật khẩu</label>
        <input id="password" name="password" placeholder="Mật khẩu" type="password">

        <label for="repass">Nhập lại mật khẩu</label>
        <input id="repass" name="repass" placeholder="Nhập lại mật khẩu" type="password">

        <button type="submit" id="registerBtn" class="login-button" onclick="handleRegister(event)">Đăng ký</button>
        <p style="margin-top:18px; text-align:center;">
            Đã có tài khoản? <a href="login.php">Đăng nhập</a>
        </p>
    </form>
    <script src="../js/register.js"></script>
    <script>
        // Hiển thị toast nếu có lỗi từ PHP
        <?php if ($error): ?>
            window.addEventListener('DOMContentLoaded', function() {
                showToast('error', '<?php echo addslashes($error); ?>');
            });
        <?php endif; ?>

        // Hiển thị toast nếu thành công rồi redirect
        <?php if ($success && isset($redirect)): ?>
            window.addEventListener('DOMContentLoaded', function() {
                showToast('success', '<?php echo addslashes($success); ?>');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 1500);
            });
        <?php endif; ?>
    </script>
</body>

</html>
