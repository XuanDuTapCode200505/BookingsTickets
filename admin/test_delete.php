<?php
require_once 'config/config.php';

echo "<h2>🧪 Test chức năng xóa phim</h2>";

// Kiểm tra kết nối database
if (!$conn) {
    echo "<p style='color: red;'>❌ Không thể kết nối database!</p>";
    exit();
}

echo "<p style='color: green;'>✅ Kết nối database thành công!</p>";

// Hiển thị danh sách phim
echo "<h3>📋 Danh sách phim hiện có:</h3>";
$sql = "SELECT id, title, status FROM movies ORDER BY id ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background: #f8f9fa;'>";
    echo "<th style='padding: 10px;'>ID</th>";
    echo "<th style='padding: 10px;'>Tên phim</th>";
    echo "<th style='padding: 10px;'>Trạng thái</th>";
    echo "<th style='padding: 10px;'>Hành động</th>";
    echo "</tr>";
    
    while ($movie = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='padding: 10px; text-align: center;'>" . $movie['id'] . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($movie['title']) . "</td>";
        echo "<td style='padding: 10px;'>" . $movie['status'] . "</td>";
        echo "<td style='padding: 10px; text-align: center;'>";
        echo "<a href='?delete_id=" . $movie['id'] . "' onclick='return confirm(\"Xóa phim này?\")' style='background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;'>Xóa</a>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Không có phim nào trong database.</p>";
}

// Xử lý xóa phim
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    echo "<h3>🗑️ Đang thử xóa phim ID: $delete_id</h3>";
    
    // Kiểm tra phim có tồn tại không
    $check_sql = "SELECT title FROM movies WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $delete_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows == 0) {
        echo "<p style='color: red;'>❌ Phim với ID $delete_id không tồn tại!</p>";
    } else {
        $movie_title = $check_result->fetch_assoc()['title'];
        echo "<p>📽️ Tìm thấy phim: <strong>" . htmlspecialchars($movie_title) . "</strong></p>";
        
        // Kiểm tra ràng buộc foreign key
        echo "<h4>🔍 Kiểm tra ràng buộc:</h4>";
        
        // Kiểm tra showtimes
        $showtime_check = "SELECT COUNT(*) as count FROM showtimes WHERE movie_id = ?";
        $showtime_stmt = $conn->prepare($showtime_check);
        $showtime_stmt->bind_param("i", $delete_id);
        $showtime_stmt->execute();
        $showtime_result = $showtime_stmt->get_result();
        $showtime_count = $showtime_result->fetch_assoc()['count'];
        
        echo "<p>📅 Lịch chiếu liên quan: <strong>$showtime_count</strong> record(s)</p>";
        
        // Kiểm tra bookings (qua showtimes)
        $booking_check = "SELECT COUNT(*) as count FROM bookings b 
                         INNER JOIN showtimes s ON b.showtime_id = s.id 
                         WHERE s.movie_id = ?";
        $booking_stmt = $conn->prepare($booking_check);
        $booking_stmt->bind_param("i", $delete_id);
        $booking_stmt->execute();
        $booking_result = $booking_stmt->get_result();
        $booking_count = $booking_result->fetch_assoc()['count'];
        
        echo "<p>🎫 Đặt vé liên quan: <strong>$booking_count</strong> record(s)</p>";
        
        if ($showtime_count > 0 || $booking_count > 0) {
            echo "<p style='color: orange;'>⚠️ <strong>Không thể xóa trực tiếp</strong> - Phim này có dữ liệu liên quan!</p>";
            echo "<p>💡 <strong>Gợi ý:</strong> Thay đổi trạng thái thành 'ended' thay vì xóa.</p>";
            
            // Cung cấp tùy chọn soft delete
            echo "<a href='?soft_delete_id=$delete_id' style='background: #ffc107; color: #212529; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 10px 5px; display: inline-block;'>🔄 Chuyển thành 'Ngừng chiếu'</a>";
            echo "<a href='?force_delete_id=$delete_id' onclick='return confirm(\"CẢNH BÁO: Điều này sẽ xóa TẤT CẢ dữ liệu liên quan! Bạn có chắc?\")' style='background: #dc3545; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 10px 5px; display: inline-block;'>💥 Xóa cưỡng chế</a>";
        } else {
            // Thực hiện xóa
            $delete_sql = "DELETE FROM movies WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $delete_id);
            
            if ($delete_stmt->execute()) {
                if ($delete_stmt->affected_rows > 0) {
                    echo "<p style='color: green;'>✅ <strong>Xóa thành công!</strong> Phim đã được xóa khỏi database.</p>";
                    echo "<script>setTimeout(function(){ window.location.href = 'test_delete.php'; }, 2000);</script>";
                } else {
                    echo "<p style='color: red;'>❌ Không có dòng nào bị ảnh hưởng. Có thể phim đã bị xóa trước đó.</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ <strong>Lỗi SQL:</strong> " . $conn->error . "</p>";
            }
        }
    }
}

// Xử lý soft delete
if (isset($_GET['soft_delete_id'])) {
    $soft_delete_id = intval($_GET['soft_delete_id']);
    echo "<h3>🔄 Chuyển phim thành 'Ngừng chiếu'</h3>";
    
    $update_sql = "UPDATE movies SET status = 'ended' WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $soft_delete_id);
    
    if ($update_stmt->execute()) {
        echo "<p style='color: green;'>✅ Đã chuyển trạng thái phim thành 'Ngừng chiếu'</p>";
        echo "<script>setTimeout(function(){ window.location.href = 'test_delete.php'; }, 2000);</script>";
    } else {
        echo "<p style='color: red;'>❌ Lỗi: " . $conn->error . "</p>";
    }
}

// Xử lý force delete
if (isset($_GET['force_delete_id'])) {
    $force_delete_id = intval($_GET['force_delete_id']);
    echo "<h3>💥 Xóa cưỡng chế phim và TẤT CẢ dữ liệu liên quan</h3>";
    
    try {
        // Bắt đầu transaction
        $conn->autocommit(FALSE);
        
        // 1. Xóa booking_seats
        $conn->query("DELETE bs FROM booking_seats bs 
                     INNER JOIN bookings b ON bs.booking_id = b.id 
                     INNER JOIN showtimes s ON b.showtime_id = s.id 
                     WHERE s.movie_id = $force_delete_id");
        echo "<p>🗑️ Đã xóa booking_seats liên quan</p>";
        
        // 2. Xóa bookings
        $conn->query("DELETE b FROM bookings b 
                     INNER JOIN showtimes s ON b.showtime_id = s.id 
                     WHERE s.movie_id = $force_delete_id");
        echo "<p>🗑️ Đã xóa bookings liên quan</p>";
        
        // 3. Xóa showtimes
        $conn->query("DELETE FROM showtimes WHERE movie_id = $force_delete_id");
        echo "<p>🗑️ Đã xóa showtimes liên quan</p>";
        
        // 4. Xóa movie
        $conn->query("DELETE FROM movies WHERE id = $force_delete_id");
        echo "<p>🗑️ Đã xóa movie</p>";
        
        // Commit transaction
        $conn->commit();
        echo "<p style='color: green;'>✅ <strong>Xóa cưỡng chế thành công!</strong> Tất cả dữ liệu liên quan đã được xóa.</p>";
        echo "<script>setTimeout(function(){ window.location.href = 'test_delete.php'; }, 2000);</script>";
        
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        echo "<p style='color: red;'>❌ <strong>Lỗi:</strong> " . $e->getMessage() . "</p>";
    }
    
    $conn->autocommit(TRUE);
}

echo "<hr>";
echo "<p><a href='index.php' style='color: #007bff;'>← Quay lại Admin Dashboard</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f8f9fa; }
h2, h3, h4 { color: #333; }
table { background: white; }
</style> 