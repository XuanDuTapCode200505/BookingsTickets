<?php
require_once 'config/config.php';

// Nếu đã đăng nhập và là admin, redirect đến dashboard
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin') {
    header('Location: index.php');
    exit();
}

$error_message = '';

// Kiểm tra nếu có thông báo timeout
if (isset($_GET['timeout']) && $_GET['timeout'] == '1') {
    $error_message = "Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại!";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']) ? true : false;
    
    if (empty($email) || empty($password)) {
        $error_message = "Vui lòng điền đầy đủ thông tin!";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ? AND role = 'admin'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            
            // Kiểm tra trạng thái
            if ($admin['status'] != 'active') {
                $error_message = "Tài khoản admin đã bị khóa!";
            } else {
                // Kiểm tra mật khẩu
                $password_valid = false;
                
                if (password_verify($password, $admin['password'])) {
                    $password_valid = true;
                } elseif ($password === $admin['password']) {
                    $password_valid = true;
                    // Cập nhật mật khẩu thành hash
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $update_stmt->bind_param("si", $hashed_password, $admin['id']);
                    $update_stmt->execute();
                }
                
                if ($password_valid) {
                    $_SESSION['user_id'] = $admin['id'];
                    $_SESSION['email'] = $admin['email'];
                    $_SESSION['name'] = $admin['name'];
                    $_SESSION['role'] = $admin['role'];
                    $_SESSION['last_activity'] = time();
                    
                    // Nếu chọn "Ghi nhớ đăng nhập", tăng thời gian sống của session
                    if ($remember_me) {
                        // Set cookie lifetime thành 30 ngày
                        $expire = time() + (30 * 24 * 60 * 60); // 30 days
                        setcookie(session_name(), session_id(), $expire, '/');
                        $_SESSION['remember_me'] = true;
                    }
                    
                    header('Location: index.php');
                    exit();
                } else {
                    $error_message = "Mật khẩu không đúng!";
                }
            }
        } else {
            $error_message = "Email không tồn tại hoặc không có quyền admin!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin - CGV</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            padding: 3rem 2rem;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .login-header {
            margin-bottom: 2rem;
        }
        
        .login-header .logo {
            font-size: 3rem;
            color: #e74c3c;
            margin-bottom: 1rem;
        }
        
        .login-header h1 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #666;
            font-size: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
        }
        
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 1.5rem;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 1px solid #f5c6cb;
        }
        
        .links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .links a {
            color: #e74c3c;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        .input-group .form-control {
            padding-left: 3rem;
        }
        
        @media (max-width: 480px) {
            .login-container {
                margin: 1rem;
                padding: 2rem 1.5rem;
            }
            
            .links {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-user-shield"></i>
            </div>
            <h1>CGV Admin</h1>
            <p>Đăng nhập hệ thống quản trị</p>
        </div>
        
        <?php if ($error_message): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Email Admin</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" class="form-control" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           required placeholder="admin@cgv.vn">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" class="form-control" 
                           required placeholder="••••••••">
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
                    <input type="checkbox" name="remember_me" value="1" style="margin: 0;">
                    <span>🔒 Ghi nhớ đăng nhập (30 ngày)</span>
                </label>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i>
                Đăng nhập
            </button>
        </form>
        
        <div class="links">
            <a href="../index.php">
                <i class="fas fa-home"></i> Về trang chủ
            </a>
            <a href="create_admin.php">
                <i class="fas fa-user-plus"></i> Tạo admin
            </a>
        </div>
    </div>
</body>
</html> 