<?php
// Cấu hình session để có thể duy trì đăng nhập
if (session_status() == PHP_SESSION_NONE) {
    // Cấu hình session lifetime linh hoạt
    ini_set('session.gc_maxlifetime', 86400); // 24 hours server-side
    
    // Chỉ sử dụng cookies (không dùng URL rewriting)
    ini_set('session.use_only_cookies', 1);
    
    // Bảo mật session
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Set thành 1 nếu dùng HTTPS
    ini_set('session.cookie_samesite', 'Lax');
    
    // Session cookie sẽ tồn tại cho đến khi browser đóng (mặc định)
    // Trừ khi user chọn "Remember Me" thì sẽ được extend
    ini_set('session.cookie_lifetime', 0);
    
    // Tên session
    session_name('CGV_ADMIN_SESSION');
    
    session_start();
}

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'phimchill');

// Kết nối đến database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Kiểm tra kết nối
if($conn === false){
    die("ERROR: Không thể kết nối. " . mysqli_connect_error());
}

// Set timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');
?> 