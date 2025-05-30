<?php
session_start();
require_once '../../admin/config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if ($password === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['name'] = $row['name'];
            header("Location: ../../index.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Mật khẩu không đúng!";
            header("Location: ../../index.php?quanly=dangnhap");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "Email không tồn tại!";
        header("Location: ../../index.php?quanly=dangnhap");
        exit();
    }
}
?>