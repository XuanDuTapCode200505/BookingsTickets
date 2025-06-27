<?php
session_start();
require_once 'config/config.php';

echo "<h2>🔧 Debug & Fix Database Issues</h2>";

// 1. Kiểm tra cấu trúc bảng users
echo "<h3>1. Kiểm tra cấu trúc bảng users:</h3>";
$result = $conn->query("DESCRIBE users");
echo "<table border='1' style='border-collapse: collapse; margin-bottom: 20px;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// 2. Thêm column status nếu chưa có
echo "<h3>2. Thêm column 'status' nếu chưa có:</h3>";
$check_status = $conn->query("SHOW COLUMNS FROM users LIKE 'status'");
if ($check_status->num_rows == 0) {
    $sql = "ALTER TABLE users ADD COLUMN status ENUM('active', 'blocked', 'deleted') DEFAULT 'active' AFTER role";
    if ($conn->query($sql) === TRUE) {
        echo "✅ Đã thêm column 'status' thành công!<br>";
    } else {
        echo "❌ Lỗi: " . $conn->error . "<br>";
    }
} else {
    echo "✅ Column 'status' đã tồn tại!<br>";
}

// 3. Kiểm tra và update mật khẩu hash
echo "<h3>3. Kiểm tra users hiện tại:</h3>";
$users_result = $conn->query("SELECT id, name, email, password, role FROM users");
echo "<table border='1' style='border-collapse: collapse; margin-bottom: 20px;'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Password</th><th>Is Hashed?</th></tr>";

$users_to_hash = [];
while($user = $users_result->fetch_assoc()) {
    $is_hashed = (strlen($user['password']) == 60 && substr($user['password'], 0, 4) == '$2y$');
    
    echo "<tr>";
    echo "<td>" . $user['id'] . "</td>";
    echo "<td>" . htmlspecialchars($user['name']) . "</td>";
    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
    echo "<td>" . $user['role'] . "</td>";
    echo "<td>" . substr($user['password'], 0, 20) . "..." . "</td>";
    echo "<td>" . ($is_hashed ? "✅ Đã hash" : "❌ Plain text") . "</td>";
    echo "</tr>";
    
    if (!$is_hashed) {
        $users_to_hash[] = $user;
    }
}
echo "</table>";

// 4. Hash mật khẩu plain text
if (!empty($users_to_hash)) {
    echo "<h3>4. Hash mật khẩu plain text:</h3>";
    foreach ($users_to_hash as $user) {
        $hashed_password = password_hash($user['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user['id']);
        
        if ($stmt->execute()) {
            echo "✅ Đã hash mật khẩu cho user: " . htmlspecialchars($user['name']) . " (Email: " . htmlspecialchars($user['email']) . ")<br>";
        } else {
            echo "❌ Lỗi hash mật khẩu cho user ID " . $user['id'] . ": " . $conn->error . "<br>";
        }
    }
} else {
    echo "<h3>4. Tất cả mật khẩu đã được hash ✅</h3>";
}

// 5. Tạo admin mặc định nếu chưa có
echo "<h3>5. Kiểm tra admin accounts:</h3>";
$admin_check = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
$admin_count = $admin_check->fetch_assoc()['count'];

echo "Số lượng admin hiện tại: <strong>" . $admin_count . "</strong><br>";

if ($admin_count == 0) {
    echo "<h4>Tạo admin mặc định:</h4>";
    $admin_name = "System Admin";
    $admin_email = "admin@cgv.com";
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'admin', 'active')");
    $stmt->bind_param("sss", $admin_name, $admin_email, $admin_password);
    
    if ($stmt->execute()) {
        echo "✅ Đã tạo admin mặc định thành công!<br>";
        echo "📧 Email: admin@cgv.com<br>";
        echo "🔑 Password: admin123<br>";
    } else {
        echo "❌ Lỗi tạo admin: " . $conn->error . "<br>";
    }
}

// 6. Test login function
echo "<h3>6. Test Login Function:</h3>";
function testLogin($email, $password, $conn) {
    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        echo "🔍 Tìm thấy user: " . htmlspecialchars($user['name']) . "<br>";
        
        // Test password verification
        $password_valid = false;
        
        if (password_verify($password, $user['password'])) {
            $password_valid = true;
            echo "✅ Password hash verification: PASSED<br>";
        } elseif ($password === $user['password']) {
            $password_valid = true;
            echo "✅ Plain text password check: PASSED (sẽ được upgrade thành hash)<br>";
        } else {
            echo "❌ Password verification: FAILED<br>";
        }
        
        return $password_valid;
    } else {
        echo "❌ Không tìm thấy user với email này<br>";
        return false;
    }
}

// Test với admin
echo "<h4>Test login admin@cgv.com / admin123:</h4>";
testLogin("admin@cgv.com", "admin123", $conn);

echo "<br><h4>Test login user@test.com / 123456:</h4>";
testLogin("user@test.com", "123456", $conn);

echo "<h3>✅ Debug hoàn tất!</h3>";
echo "<p><a href='login.php'>👉 Thử đăng nhập ngay</a></p>";
echo "<p><a href='index.php'>👉 Về trang admin</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
h2, h3 { color: #333; }
pre { background: #fff; padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
</style> 