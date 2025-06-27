<?php
session_start();
require_once 'config/config.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CGV Admin Test - Quản trị hệ thống</title>
    <link rel="stylesheet" href="css/admin.css">
    <script src="js/admin.js" defer></script>
</head>
<body>
    <div class="admin-header">
        <div class="admin-title">CGV Admin Panel (TEST)</div>
        <div class="admin-user">
            <span>Test Mode</span>
            <a href="../index.php" class="btn btn-secondary">Về trang chủ</a>
        </div>
    </div>

    <div class="admin-container">
        <div class="admin-sidebar">
            <ul class="sidebar-menu">
                <li><a href="?page=dashboard" class="<?php echo $page == 'dashboard' ? 'active' : ''; ?>">📊 Dashboard</a></li>
                <li><a href="?page=movies" class="<?php echo $page == 'movies' ? 'active' : ''; ?>">🎬 Quản lý Phim</a></li>
                <li><a href="?page=theaters" class="<?php echo $page == 'theaters' ? 'active' : ''; ?>">🏢 Quản lý Rạp</a></li>
                <li><a href="?page=showtimes" class="<?php echo $page == 'showtimes' ? 'active' : ''; ?>">⏰ Lịch Chiếu</a></li>
                <li><a href="?page=bookings" class="<?php echo $page == 'bookings' ? 'active' : ''; ?>">🎫 Đặt Vé</a></li>
                <li><a href="?page=users" class="<?php echo $page == 'users' ? 'active' : ''; ?>">👥 Người Dùng</a></li>
                <li><a href="?page=reports" class="<?php echo $page == 'reports' ? 'active' : ''; ?>">📈 Báo Cáo</a></li>
                <li><a href="../index.php" class="btn-back">🏠 Về Trang Chủ</a></li>
            </ul>
        </div>

        <div class="admin-content">
            <?php
            echo '<div style="padding: 20px;">';
            echo '<h2>🔧 Test Admin Panel</h2>';
            echo '<p>Trang hiện tại: <strong>' . htmlspecialchars($page) . '</strong></p>';
            echo '<hr>';
            
            switch($page) {
                case 'movies':
                    echo '<h3>🎬 Test Loading admin_movies.php</h3>';
                    if (file_exists('pages/admin_movies.php')) {
                        echo '<p>✅ File admin_movies.php tồn tại</p>';
                        echo '<div style="border: 2px solid #ddd; padding: 20px; margin: 20px 0; background: #f9f9f9;">';
                        include 'pages/admin_movies.php';
                        echo '</div>';
                    } else {
                        echo '<p>❌ File admin_movies.php không tồn tại</p>';
                    }
                    break;
                    
                case 'theaters':
                    echo '<h3>🏢 Test Loading admin_theaters.php</h3>';
                    if (file_exists('pages/admin_theaters.php')) {
                        echo '<p>✅ File admin_theaters.php tồn tại</p>';
                        echo '<div style="border: 2px solid #ddd; padding: 20px; margin: 20px 0; background: #f9f9f9;">';
                        include 'pages/admin_theaters.php';
                        echo '</div>';
                    } else {
                        echo '<p>❌ File admin_theaters.php không tồn tại</p>';
                    }
                    break;
                    
                case 'showtimes':
                    echo '<h3>⏰ Test Loading admin_showtimes.php</h3>';
                    if (file_exists('pages/admin_showtimes.php')) {
                        echo '<p>✅ File admin_showtimes.php tồn tại</p>';
                        echo '<div style="border: 2px solid #ddd; padding: 20px; margin: 20px 0; background: #f9f9f9;">';
                        include 'pages/admin_showtimes.php';
                        echo '</div>';
                    } else {
                        echo '<p>❌ File admin_showtimes.php không tồn tại</p>';
                    }
                    break;
                    
                case 'bookings':
                    echo '<h3>🎫 Test Loading admin_bookings.php</h3>';
                    if (file_exists('pages/admin_bookings.php')) {
                        echo '<p>✅ File admin_bookings.php tồn tại</p>';
                        echo '<div style="border: 2px solid #ddd; padding: 20px; margin: 20px 0; background: #f9f9f9;">';
                        include 'pages/admin_bookings.php';
                        echo '</div>';
                    } else {
                        echo '<p>❌ File admin_bookings.php không tồn tại</p>';
                    }
                    break;
                    
                case 'users':
                    echo '<h3>👥 Test Loading admin_users.php</h3>';
                    if (file_exists('pages/admin_users.php')) {
                        echo '<p>✅ File admin_users.php tồn tại</p>';
                        echo '<div style="border: 2px solid #ddd; padding: 20px; margin: 20px 0; background: #f9f9f9;">';
                        include 'pages/admin_users.php';
                        echo '</div>';
                    } else {
                        echo '<p>❌ File admin_users.php không tồn tại</p>';
                    }
                    break;
                    
                case 'reports':
                    echo '<h3>📈 Test Loading admin_reports.php</h3>';
                    if (file_exists('pages/admin_reports.php')) {
                        echo '<p>✅ File admin_reports.php tồn tại</p>';
                        echo '<div style="border: 2px solid #ddd; padding: 20px; margin: 20px 0; background: #f9f9f9;">';
                        include 'pages/admin_reports.php';
                        echo '</div>';
                    } else {
                        echo '<p>❌ File admin_reports.php không tồn tại</p>';
                    }
                    break;
                    
                default:
                    echo '<h3>📊 Dashboard Test</h3>';
                    
                    // Test database queries
                    echo '<div style="margin: 20px 0;">';
                    echo '<h4>🔗 Database Test:</h4>';
                    
                    if ($conn) {
                        echo '<p>✅ Kết nối database thành công</p>';
                        
                        $tables = ['movies', 'theaters', 'bookings', 'users'];
                        foreach ($tables as $table) {
                            $query = "SELECT COUNT(*) as count FROM $table";
                            $result = mysqli_query($conn, $query);
                            if ($result) {
                                $count = mysqli_fetch_assoc($result)['count'];
                                echo "<p>✅ Bảng $table: $count bản ghi</p>";
                            } else {
                                echo "<p>❌ Lỗi query bảng $table: " . mysqli_error($conn) . "</p>";
                            }
                        }
                    } else {
                        echo '<p>❌ Không thể kết nối database</p>';
                    }
                    echo '</div>';
                    
                    echo '<div style="margin: 20px 0;">';
                    echo '<h4>📁 Files Check:</h4>';
                    $admin_files = [
                        'css/admin.css' => 'CSS File',
                        'js/admin.js' => 'JavaScript File',
                        'pages/admin_movies.php' => 'Movies Page',
                        'pages/admin_theaters.php' => 'Theaters Page',
                        'pages/admin_showtimes.php' => 'Showtimes Page',
                        'pages/admin_bookings.php' => 'Bookings Page',
                        'pages/admin_users.php' => 'Users Page',
                        'pages/admin_reports.php' => 'Reports Page'
                    ];
                    
                    foreach ($admin_files as $file => $desc) {
                        if (file_exists($file)) {
                            $size = round(filesize($file) / 1024, 1);
                            echo "<p>✅ $desc: $file ($size KB)</p>";
                        } else {
                            echo "<p>❌ $desc: $file (không tồn tại)</p>";
                        }
                    }
                    echo '</div>';
                    
                    break;
            }
            
            echo '</div>';
            ?>
        </div>
    </div>
</body>
</html> 