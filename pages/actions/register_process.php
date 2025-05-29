<?php
session_start();
require_once '../../admin/config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra mật khẩu xác nhận
    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = "Mật khẩu xác nhận không khớp!";
        header("Location: ../register.php");
        exit();
    }

    // Kiểm tra email đã tồn tại chưa
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['register_error'] = "Email này đã được sử dụng!";
        header("Location: ../register.php");
        exit();
    }

    // Mã hóa mật khẩu
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Thêm người dùng mới vào database
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['register_success'] = "Đăng ký thành công! Bạn có thể đăng nhập.";
        header("Location: ../login.php");
        exit();
    } else {
        $_SESSION['register_error'] = "Đăng ký thất bại: " . $conn->error;
        header("Location: ../register.php");
        exit();
    }
}
?>