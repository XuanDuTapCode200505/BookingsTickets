<?php error_reporting(E_ALL); ini_set('display_errors', 1); ?>
<?php
// kết nối database
require_once __DIR__ .'/../config/config.php';

// Khởi tạo biến
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$combo_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] =='POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image_url = trim($_POST['image_url']);
    $status = isset($_POST['status']) ? $_POST['status'] : 'active';

    if ($action == 'add') {
        $sql = "INSERT INTO combos (name, description, price, image_url, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdss", $name, $description, $price, $image_url, $status);
        if ($stmt->execute()) {
            $message = "Thêm combo thành công!";
            $message_type = 'success';
        } else {
            $message = "Thêm combo thất bại!" .$conn->error;
            $message_type = 'error';
        }
    } elseif ($action =='edit' && $combo_id > 0) {
        $sql = "UPDATE combos SET name =?, description = ?, price =?, image_url=?, status=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdssi", $name, $description, $price, $image_url, $status, $combo_id);
        if ($stmt->execute()) {
            $message = "Cập nhật combo thành công!";
            $message_type = 'success';
        } else {
            $message = "Cập nhật combo thất bại!" .$conn->error;
            $message_type = 'error';
        }
    }
}

if  ($action == 'delete' && $combo_id > 0) {
    $sql = "DELETE FROM combos WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $combo_id);
    if ($stmt->execute()) {
        $message = "Xóa combo thành công!";
        $message_type = 'success';
    } else {
        $message = "Xóa combo thất bại!" .$conn->error;
        $message_type = 'error';
    }
}

if ($action == 'toggle' && $combo_id >0) {
    $sql = "UPDATE combos SET status = IF(status = 'active', 'inactive', 'active') WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $combo_id);
    $stmt->execute();
    header("Location: admin_combos.php");
    exit();
}

// Nếu là sửa, lấy dữ liệu combo
$combo = null;
if ($action == 'edit' && $combo_id > 0) {
    $sql = "SELECT * FROM combos WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $combo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $combo = $result->fetch_assoc();
}
?>
<div class="container-fluid px-4">
    <h3 class="mt-4 mb-2">
        <i class="fas fa-cocktail"></i> Quản lý Combo
    </h3>
    <p class="mb-4 text-muted">Quản lý danh sách combo bắp nước/đồ ăn trong hệ thống</p>

    <?php if ($action == 'add' || $action == 'edit'): ?>
    <div class="card shadow-sm mb-4" style="max-width: 600px;">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-<?= $action == 'edit' ? 'edit' : 'plus' ?>"></i>
                <?= $action == 'edit' ? 'Chỉnh sửa combo' : 'Thêm combo mới'; ?>
            </h5>
            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type == 'success' ? 'success' : 'danger'; ?>"><?= $message; ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Tên combo</label>
                    <input type="text" name="name" class="form-control" required value="<?= $combo['name'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control"><?= $combo['description'] ?? ''; ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giá (VNĐ)</label>
                    <input type="number" name="price" min="0" step="1000" class="form-control" required value="<?= $combo['price'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Link ảnh</label>
                    <input type="text" name="image_url" class="form-control" value="<?= $combo['image_url'] ?? ''; ?>" oninput="document.getElementById('preview-img').src=this.value">
                </div>
                <div class="mb-3">
                    <label class="form-label">Xem trước ảnh</label><br>
                    <img id="preview-img" src="<?= $combo['image_url'] ?? '' ?>" alt="Preview" style="width:100px; height:100px; object-fit:cover; border:1px solid #eee; border-radius:8px;">
                </div>
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= (($combo['status'] ?? '') == 'active') ? 'selected' : '' ?>>Hiện</option>
                        <option value="inactive" <?= (($combo['status'] ?? '') == 'inactive') ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><?= $action == 'edit' ? 'Cập nhật' : 'Thêm mới'; ?></button>
                <a href="admin_combos.php" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fas fa-list"></i> Danh sách combo bắp nước</h5>
                <a href="?page=combos&action=add" class="btn btn-success">
                    <i class="fas fa-plus"></i> Thêm combo mới
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Ảnh</th>
                            <th>Tên combo</th>
                            <th>Mô tả</th>
                            <th>Giá</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT * FROM combos ORDER BY id DESC";
                    $result = $conn->query($sql);
                    $stt = 1;
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td>#<?= $stt++; ?></td>
                        <td>
                            <?php if ($row['image_url']): ?>
                                <img src="<?= $row['image_url']; ?>" alt="Combo" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #eee;">
                            <?php else: ?>
                                <span class="text-muted">Không có ảnh</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($row['name']); ?></strong></td>
                        <td style="max-width:200px;"><?= nl2br(htmlspecialchars($row['description'])); ?></td>
                        <td class="text-danger fw-bold"><?= number_format($row['price']); ?> VNĐ</td>
                        <td>
                            <?php if ($row['status'] == 'active'): ?>
                                <span class="badge bg-success">Hiện</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Ẩn</span>
                            <?php endif; ?>
                            <a href="?page=combos&action=toggle&id=<?= $row['id']; ?>" class="ms-2" title="Đổi trạng thái">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </td>
                        <td>
                            <a href="?page=combos&action=edit&id=<?= $row['id']; ?>" class="btn btn-warning btn-sm" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?page=combos&action=delete&id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xác nhận xóa combo này?');" title="Xóa">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>