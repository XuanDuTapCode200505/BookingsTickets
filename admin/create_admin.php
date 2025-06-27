<?php
require_once 'config/config.php';

// Kiểm tra xem đã có admin chưa
$check_sql = "SELECT COUNT(*) as count FROM users WHERE role = 'admin'";
$result = $conn->query($check_sql);
$admin_count = $result->fetch_assoc()['count'];

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate
    if (empty($name) || empty($email) || empty($password)) {
        $message = "Vui lòng điền đầy đủ thông tin!";
    } elseif ($password !== $confirm_password) {
        $message = "Mật khẩu xác nhận không khớp!";
    } elseif (strlen($password) < 6) {
        $message = "Mật khẩu phải có ít nhất 6 ký tự!";
    } else {
        // Kiểm tra email đã tồn tại
        $check_email_sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_email_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $email_result = $stmt->get_result();
        
        if ($email_result->num_rows > 0) {
            $message = "Email này đã được sử dụng!";
        } else {
            // Tạo admin
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (name, email, password, role, status, created_at) VALUES (?, ?, ?, 'admin', 'active', NOW())";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $message = "Tạo tài khoản admin thành công! Bạn có thể đăng nhập ngay.";
                $success = true;
                $admin_count++; // Cập nhật số lượng admin
            } else {
                $message = "Có lỗi xảy ra: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Admin - CGV</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .header h1 {
            color: #e74c3c;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            color: #666;
            font-size: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #e74c3c;
        }
        
        .btn {
            background: #e74c3c;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #c0392b;
        }
        
        .btn:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }
        
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 500;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .info-box {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            color: #0d47a1;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .info-box h3 {
            margin-bottom: 0.5rem;
            color: #1976d2;
        }
        
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .back-link a {
            color: #e74c3c;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎬 CGV Admin</h1>
            <p>Tạo tài khoản quản trị viên</p>
        </div>
        
        <?php if ($admin_count > 0): ?>
            <div class="info-box">
                <h3>ℹ️ Thông báo</h3>
                <p>Hệ thống đã có <strong><?php echo $admin_count; ?></strong> admin. Nếu bạn muốn tạo thêm admin, vui lòng đăng nhập vào trang admin để quản lý.</p>
            </div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Họ tên *</label>
                <input type="text" name="name" class="form-control" 
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                       required placeholder="VD: Nguyễn Văn Admin">
            </div>
            
            <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                       required placeholder="VD: admin@cgv.vn">
            </div>
            
            <div class="form-group">
                <label class="form-label">Mật khẩu *</label>
                <input type="password" name="password" class="form-control" 
                       required placeholder="Tối thiểu 6 ký tự">
            </div>
            
            <div class="form-group">
                <label class="form-label">Xác nhận mật khẩu *</label>
                <input type="password" name="confirm_password" class="form-control" 
                       required placeholder="Nhập lại mật khẩu">
            </div>
            
            <button type="submit" class="btn">
                🔑 Tạo tài khoản Admin
            </button>
        </form>
        <?php endif; ?>
        
        <div class="back-link">
            <?php if ($success): ?>
                <a href="index.php">🚀 Đăng nhập Admin</a> | 
            <?php endif; ?>
            <a href="../index.php">🏠 Về trang chủ</a>
        </div>
    </div>
</body>
</html> 