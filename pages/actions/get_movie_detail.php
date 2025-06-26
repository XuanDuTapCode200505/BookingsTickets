<?php
require_once '../../admin/config/config.php';

header('Content-Type: application/json');

// Kiểm tra cả GET và POST
$movie_id = 0;
if (isset($_POST['movie_id'])) {
    $movie_id = intval($_POST['movie_id']);
} elseif (isset($_GET['id'])) {
    $movie_id = intval($_GET['id']);
}

if ($movie_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $movie = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'data' => $movie  // Sửa từ 'movie' thành 'data' để match với JS
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy phim'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID phim không hợp lệ'
    ]);
}
?> 