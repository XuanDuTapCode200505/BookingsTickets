<?php
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$movie_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Xử lý các action
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == 'add' || $action == 'edit') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $duration = intval($_POST['duration']);
        $genre = trim($_POST['genre']);
        $release_date = $_POST['release_date'];
        $poster_url = trim($_POST['poster_url']);
        $status = $_POST['status'];
        $rating = floatval($_POST['rating']);
        
        if ($action == 'add') {
            $sql = "INSERT INTO movies (title, description, duration, genre, release_date, poster_url, status, rating) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssissssd", $title, $description, $duration, $genre, $release_date, $poster_url, $status, $rating);
            
            if ($stmt->execute()) {
                echo '<script>alert("Thêm phim thành công!"); window.location.href = "?page=movies";</script>';
            } else {
                echo '<script>alert("Có lỗi xảy ra!");</script>';
            }
        } else {
            $sql = "UPDATE movies SET title = ?, description = ?, duration = ?, genre = ?, release_date = ?, poster_url = ?, status = ?, rating = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssissssdi", $title, $description, $duration, $genre, $release_date, $poster_url, $status, $rating, $movie_id);
            
            if ($stmt->execute()) {
                echo '<script>alert("Cập nhật phim thành công!"); window.location.href = "?page=movies";</script>';
            } else {
                echo '<script>alert("Có lỗi xảy ra!");</script>';
            }
        }
    } elseif ($action == 'delete') {
        $sql = "DELETE FROM movies WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $movie_id);
        
        if ($stmt->execute()) {
            echo '<script>alert("Xóa phim thành công!"); window.location.href = "?page=movies";</script>';
        } else {
            echo '<script>alert("Có lỗi xảy ra!");</script>';
        }
    }
}

if ($action == 'add' || $action == 'edit') {
    $movie = null;
    if ($action == 'edit' && $movie_id > 0) {
        $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
        $stmt->bind_param("i", $movie_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $movie = $result->fetch_assoc();
    }
?>

<div class="content-header">
    <h1 class="content-title"><?php echo $action == 'add' ? 'Thêm phim mới' : 'Chỉnh sửa phim'; ?></h1>
    <div class="breadcrumb">Quản lý phim / <?php echo $action == 'add' ? 'Thêm mới' : 'Chỉnh sửa'; ?></div>
</div>

<div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <form method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Tên phim *</label>
                <input type="text" name="title" value="<?php echo $movie ? htmlspecialchars($movie['title']) : ''; ?>" 
                       required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Thể loại</label>
                <input type="text" name="genre" value="<?php echo $movie ? htmlspecialchars($movie['genre']) : ''; ?>" 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Thời lượng (phút) *</label>
                <input type="number" name="duration" value="<?php echo $movie ? $movie['duration'] : ''; ?>" 
                       required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Ngày khởi chiếu</label>
                <input type="date" name="release_date" value="<?php echo $movie ? $movie['release_date'] : ''; ?>" 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Đánh giá (0-10)</label>
                <input type="number" step="0.1" min="0" max="10" name="rating" value="<?php echo $movie ? $movie['rating'] : ''; ?>" 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">URL Poster</label>
            <input type="url" name="poster_url" value="<?php echo $movie ? htmlspecialchars($movie['poster_url']) : ''; ?>" 
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Trạng thái</label>
            <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <option value="showing" <?php echo ($movie && $movie['status'] == 'showing') ? 'selected' : ''; ?>>Đang chiếu</option>
                <option value="coming_soon" <?php echo ($movie && $movie['status'] == 'coming_soon') ? 'selected' : ''; ?>>Sắp chiếu</option>
                <option value="ended" <?php echo ($movie && $movie['status'] == 'ended') ? 'selected' : ''; ?>>Ngừng chiếu</option>
            </select>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Mô tả</label>
            <textarea name="description" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"><?php echo $movie ? htmlspecialchars($movie['description']) : ''; ?></textarea>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary"><?php echo $action == 'add' ? 'Thêm phim' : 'Cập nhật'; ?></button>
            <a href="?page=movies" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<?php } else { ?>

<div class="content-header">
    <h1 class="content-title">Quản lý phim</h1>
    <div class="breadcrumb">Quản lý phim / Danh sách</div>
</div>

<div style="margin-bottom: 20px;">
    <a href="?page=movies&action=add" class="btn btn-primary">+ Thêm phim mới</a>
</div>

<div style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Poster</th>
                <th>Tên phim</th>
                <th>Thể loại</th>
                <th>Thời lượng</th>
                <th>Đánh giá</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM movies ORDER BY created_at DESC";
            $result = mysqli_query($conn, $sql);
            
            if ($result && mysqli_num_rows($result) > 0) {
                while($movie = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td>' . $movie['id'] . '</td>';
                    echo '<td><img src="' . htmlspecialchars($movie['poster_url']) . '" alt="poster" style="width: 50px; height: 75px; object-fit: cover; border-radius: 5px;"></td>';
                    echo '<td><strong>' . htmlspecialchars($movie['title']) . '</strong></td>';
                    echo '<td>' . htmlspecialchars($movie['genre']) . '</td>';
                    echo '<td>' . $movie['duration'] . ' phút</td>';
                    echo '<td>⭐ ' . $movie['rating'] . '</td>';
                    
                    $status_text = '';
                    $status_class = '';
                    switch($movie['status']) {
                        case 'showing':
                            $status_text = 'Đang chiếu';
                            $status_class = 'status-confirmed';
                            break;
                        case 'coming_soon':
                            $status_text = 'Sắp chiếu';
                            $status_class = 'status-pending';
                            break;
                        case 'ended':
                            $status_text = 'Ngừng chiếu';
                            $status_class = 'status-cancelled';
                            break;
                    }
                    
                    echo '<td><span class="status-badge ' . $status_class . '">' . $status_text . '</span></td>';
                    echo '<td>';
                    echo '<a href="?page=movies&action=edit&id=' . $movie['id'] . '" class="btn btn-primary" style="margin-right: 5px; padding: 5px 10px; font-size: 12px;">Sửa</a>';
                    echo '<a href="?page=movies&action=delete&id=' . $movie['id'] . '" class="btn" style="background-color: #dc3545; color: white; padding: 5px 10px; font-size: 12px;" onclick="return confirm(\'Bạn có chắc muốn xóa phim này?\')">Xóa</a>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="8" style="text-align: center; padding: 50px;">Chưa có phim nào</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php } ?> 