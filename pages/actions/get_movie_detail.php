<?php
require_once '../../admin/config/config.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $movie_id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $movie = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'movie' => $movie
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