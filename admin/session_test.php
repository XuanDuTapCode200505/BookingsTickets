<?php
require_once 'config/config.php';

echo "<h2>🔧 Test Session Configuration</h2>";

echo "<h3>📋 Session Info:</h3>";
echo "<table border='1' style='border-collapse: collapse; margin-bottom: 20px;'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>Session Name</td><td>" . session_name() . "</td></tr>";
echo "<tr><td>Session ID</td><td>" . session_id() . "</td></tr>";
echo "<tr><td>Session Status</td><td>" . (session_status() == PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "</td></tr>";
echo "<tr><td>Cookie Lifetime</td><td>" . ini_get('session.cookie_lifetime') . " seconds</td></tr>";
echo "<tr><td>GC Max Lifetime</td><td>" . ini_get('session.gc_maxlifetime') . " seconds</td></tr>";
echo "<tr><td>Use Only Cookies</td><td>" . (ini_get('session.use_only_cookies') ? 'Yes' : 'No') . "</td></tr>";
echo "<tr><td>Cookie HTTPOnly</td><td>" . (ini_get('session.cookie_httponly') ? 'Yes' : 'No') . "</td></tr>";
echo "<tr><td>Cookie Secure</td><td>" . (ini_get('session.cookie_secure') ? 'Yes' : 'No') . "</td></tr>";
echo "</table>";

echo "<h3>🍪 Current Session Data:</h3>";
if (!empty($_SESSION)) {
    echo "<table border='1' style='border-collapse: collapse; margin-bottom: 20px;'>";
    echo "<tr><th>Key</th><th>Value</th></tr>";
    foreach ($_SESSION as $key => $value) {
        echo "<tr><td>" . htmlspecialchars($key) . "</td><td>" . htmlspecialchars(print_r($value, true)) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ Không có session data (chưa đăng nhập)</p>";
}

echo "<h3>⏰ Session Timeout Check:</h3>";
if (isset($_SESSION['user_id'])) {
    echo "✅ Đã đăng nhập<br>";
    
    if (isset($_SESSION['last_activity'])) {
        $time_since_activity = time() - $_SESSION['last_activity'];
        echo "🕐 Thời gian từ lần hoạt động cuối: " . $time_since_activity . " giây<br>";
        
        $timeout_duration = 3600; // 1 giờ
        $remaining_time = $timeout_duration - $time_since_activity;
        
        if ($remaining_time > 0) {
            echo "⏳ Thời gian còn lại: " . gmdate("H:i:s", $remaining_time) . "<br>";
        } else {
            echo "⚠️ Session sẽ hết hạn!<br>";
        }
    }
    
    if (isset($_SESSION['remember_me']) && $_SESSION['remember_me'] === true) {
        echo "🔒 Remember Me: Đã bật (session sẽ không timeout)<br>";
    } else {
        echo "🔓 Remember Me: Chưa bật (session sẽ timeout sau 1 giờ không hoạt động)<br>";
    }
} else {
    echo "❌ Chưa đăng nhập<br>";
}

echo "<h3>🧪 Test Actions:</h3>";
echo "<p><a href='login.php'>👉 Đến trang đăng nhập</a></p>";
echo "<p><a href='index.php'>👉 Đến trang admin</a></p>";

// Test set session
if (isset($_GET['test']) && $_GET['test'] == 'set') {
    $_SESSION['test_time'] = time();
    echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px 0;'>";
    echo "✅ Đã set test session: " . $_SESSION['test_time'];
    echo "</div>";
}

echo "<p><a href='?test=set'>🧪 Test set session data</a></p>";

echo "<h3>🔄 Refresh Test:</h3>";
echo "<p>Mở tab mới và paste URL này để test: <code>" . $_SERVER['REQUEST_URI'] . "</code></p>";
echo "<p>Nếu session hoạt động đúng, bạn sẽ thấy cùng Session ID và data.</p>";

echo "<style>
table { width: 100%; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
code { background-color: #f4f4f4; padding: 2px 4px; border-radius: 3px; }
</style>";
?> 