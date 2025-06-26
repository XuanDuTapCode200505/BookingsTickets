<?php
session_start();
require_once 'config/config.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo '<script>alert("Bạn không có quyền truy cập!"); window.location.href = "../index.php";</script>';
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CGV Admin - Quản trị hệ thống</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        .admin-header {
            background-color: #e50914;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .admin-title {
            font-size: 24px;
            font-weight: bold;
        }

        .admin-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-container {
            display: flex;
            min-height: calc(100vh - 70px);
        }

        .admin-sidebar {
            width: 250px;
            background-color: #1a1a1a;
            color: white;
            padding: 0;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            border-bottom: 1px solid #333;
        }

        .sidebar-menu a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: #e50914;
            border-left: 4px solid white;
        }

        .admin-content {
            flex: 1;
            padding: 30px;
            background-color: #f9f9f9;
        }

        .content-header {
            margin-bottom: 30px;
        }

        .content-title {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        .breadcrumb {
            color: #666;
            font-size: 14px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #e50914;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .btn-primary {
            background-color: #e50914;
            color: white;
        }

        .btn-primary:hover {
            background-color: #cc0812;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-back {
            background-color: #007bff;
            color: white;
            margin-bottom: 20px;
        }

        .recent-bookings {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        .table tr:hover {
            background-color: #f5f5f5;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="admin-title">CGV Admin Panel</div>
        <div class="admin-user">
            <span>Xin chào, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
            <a href="../pages/actions/logout_process.php" class="btn btn-secondary">Đăng xuất</a>
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
            switch($page) {
                case 'movies':
                    include 'pages/movies.php';
                    break;
                case 'theaters':
                    include 'pages/theaters.php';
                    break;
                case 'showtimes':
                    include 'pages/showtimes.php';
                    break;
                case 'bookings':
                    include 'pages/bookings.php';
                    break;
                case 'users':
                    include 'pages/users.php';
                    break;
                case 'reports':
                    include 'pages/reports.php';
                    break;
                default:
                    // Dashboard
                    echo '<div class="content-header">';
                    echo '<h1 class="content-title">Dashboard</h1>';
                    echo '<div class="breadcrumb">Trang chủ / Dashboard</div>';
                    echo '</div>';
                    
                    // Thống kê
                    $stats = [
                        'total_movies' => 0,
                        'total_theaters' => 0,
                        'total_bookings' => 0,
                        'total_revenue' => 0
                    ];
                    
                    // Đếm số phim
                    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM movies WHERE status = 'showing'");
                    if ($result) {
                        $stats['total_movies'] = mysqli_fetch_assoc($result)['count'];
                    }
                    
                    // Đếm số rạp
                    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM theaters");
                    if ($result) {
                        $stats['total_theaters'] = mysqli_fetch_assoc($result)['count'];
                    }
                    
                    // Đếm số đặt vé
                    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'confirmed'");
                    if ($result) {
                        $stats['total_bookings'] = mysqli_fetch_assoc($result)['count'];
                    }
                    
                    // Tính tổng doanh thu
                    $result = mysqli_query($conn, "SELECT SUM(total_amount) as revenue FROM bookings WHERE payment_status = 'paid'");
                    if ($result) {
                        $stats['total_revenue'] = mysqli_fetch_assoc($result)['revenue'] ?? 0;
                    }
                    
                    echo '<div class="stats-grid">';
                    echo '<div class="stat-card">';
                    echo '<div class="stat-number">' . $stats['total_movies'] . '</div>';
                    echo '<div class="stat-label">Phim đang chiếu</div>';
                    echo '</div>';
                    
                    echo '<div class="stat-card">';
                    echo '<div class="stat-number">' . $stats['total_theaters'] . '</div>';
                    echo '<div class="stat-label">Rạp chiếu</div>';
                    echo '</div>';
                    
                    echo '<div class="stat-card">';
                    echo '<div class="stat-number">' . $stats['total_bookings'] . '</div>';
                    echo '<div class="stat-label">Vé đã bán</div>';
                    echo '</div>';
                    
                    echo '<div class="stat-card">';
                    echo '<div class="stat-number">' . number_format($stats['total_revenue'], 0, ',', '.') . ' VNĐ</div>';
                    echo '<div class="stat-label">Doanh thu</div>';
                    echo '</div>';
                    echo '</div>';
                    
                    // Đặt vé gần đây
                    echo '<div class="recent-bookings">';
                    echo '<h3>Đặt vé gần đây</h3>';
                    
                    $recent_bookings_sql = "SELECT b.booking_code, b.total_amount, b.created_at, b.booking_status,
                                                   u.name as user_name, m.title as movie_title
                                            FROM bookings b
                                            INNER JOIN users u ON b.user_id = u.id
                                            INNER JOIN showtimes st ON b.showtime_id = st.id
                                            INNER JOIN movies m ON st.movie_id = m.id
                                            ORDER BY b.created_at DESC
                                            LIMIT 10";
                    $recent_result = mysqli_query($conn, $recent_bookings_sql);
                    
                    if ($recent_result && mysqli_num_rows($recent_result) > 0) {
                        echo '<table class="table">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>Mã đặt vé</th>';
                        echo '<th>Khách hàng</th>';
                        echo '<th>Phim</th>';
                        echo '<th>Tổng tiền</th>';
                        echo '<th>Trạng thái</th>';
                        echo '<th>Ngày đặt</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        
                        while($booking = mysqli_fetch_assoc($recent_result)) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($booking['booking_code']) . '</td>';
                            echo '<td>' . htmlspecialchars($booking['user_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($booking['movie_title']) . '</td>';
                            echo '<td>' . number_format($booking['total_amount'], 0, ',', '.') . ' VNĐ</td>';
                            
                            $status_class = 'status-' . $booking['booking_status'];
                            $status_text = '';
                            switch($booking['booking_status']) {
                                case 'confirmed': $status_text = 'Đã xác nhận'; break;
                                case 'pending': $status_text = 'Chờ xác nhận'; break;
                                case 'cancelled': $status_text = 'Đã hủy'; break;
                            }
                            
                            echo '<td><span class="status-badge ' . $status_class . '">' . $status_text . '</span></td>';
                            echo '<td>' . date('d/m/Y H:i', strtotime($booking['created_at'])) . '</td>';
                            echo '</tr>';
                        }
                        
                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<p>Chưa có đặt vé nào.</p>';
                    }
                    
                    echo '</div>';
                    
                    break;
            }
            ?>
        </div>
    </div>
</body>
</html> 