<?php
require_once '../../admin/config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // Kiểm tra mật khẩu (hỗ trợ cả hash và plain text)
        $password_valid = false;
        
        // Kiểm tra nếu là mật khẩu đã hash
        if (password_verify($password, $row['password'])) {
            $password_valid = true;
        } 
        // Kiểm tra nếu là mật khẩu plain text (cho user cũ)
        elseif ($password === $row['password']) {
            $password_valid = true;
            
            // Cập nhật mật khẩu thành hash cho lần sau
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_password, $row['id']);
            $update_stmt->execute();
        }
        
        if ($password_valid) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            
            // Redirect admin đến trang admin
            if ($row['role'] == 'admin') {
                header("Location: ../../admin/index.php");
            } else {
                header("Location: ../../index.php");
            }
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