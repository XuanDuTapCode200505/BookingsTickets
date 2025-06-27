<?php
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$admin_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Xử lý các action
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == 'add') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate
        if ($password !== $confirm_password) {
            echo '<script>alert("Mật khẩu xác nhận không khớp!");</script>';
        } else {
            // Kiểm tra email đã tồn tại
            $check_sql = "SELECT id FROM users WHERE email = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                echo '<script>alert("Email này đã được sử dụng!");</script>';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (name, email, phone, password, role, status) VALUES (?, ?, ?, ?, 'admin', 'active')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);
                
                if ($stmt->execute()) {
                    echo '<script>alert("Thêm admin thành công!"); window.location.href = "?page=admins";</script>';
                } else {
                    echo '<script>alert("Có lỗi xảy ra!");</script>';
                }
            }
        }
    } elseif ($action == 'edit') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $status = $_POST['status'];
        $password = trim($_POST['password']);
        
        // Kiểm tra email đã tồn tại (trừ admin hiện tại)
        $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $email, $admin_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            echo '<script>alert("Email này đã được sử dụng!");</script>';
        } else {
            if (!empty($password)) {
                // Cập nhật với mật khẩu mới
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET name = ?, email = ?, phone = ?, password = ?, status = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssi", $name, $email, $phone, $hashed_password, $status, $admin_id);
            } else {
                // Cập nhật không thay đổi mật khẩu
                $sql = "UPDATE users SET name = ?, email = ?, phone = ?, status = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $name, $email, $phone, $status, $admin_id);
            }
            
            if ($stmt->execute()) {
                echo '<script>alert("Cập nhật admin thành công!"); window.location.href = "?page=admins";</script>';
            } else {
                echo '<script>alert("Có lỗi xảy ra!");</script>';
            }
        }
    } elseif ($action == 'delete') {
        // Không cho phép xóa admin cuối cùng
        $count_sql = "SELECT COUNT(*) as count FROM users WHERE role = 'admin' AND status = 'active'";
        $count_result = $conn->query($count_sql);
        $count_data = $count_result->fetch_assoc();
        
        if ($count_data['count'] <= 1) {
            echo '<script>alert("Không thể xóa admin cuối cùng!");</script>';
        } else {
            $sql = "UPDATE users SET status = 'deleted' WHERE id = ? AND role = 'admin'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $admin_id);
            
            if ($stmt->execute()) {
                echo '<script>alert("Xóa admin thành công!"); window.location.href = "?page=admins";</script>';
            } else {
                echo '<script>alert("Có lỗi xảy ra!");</script>';
            }
        }
    }
}

if ($action == 'add' || $action == 'edit') {
    $admin = null;
    if ($action == 'edit' && $admin_id > 0) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin'");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        
        if (!$admin) {
            echo '<script>alert("Không tìm thấy admin này!"); window.location.href = "?page=admins";</script>';
            exit;
        }
    }
?>

<div class="content-header">
    <h1 class="content-title">👑 Admin - <?php echo $action == 'add' ? 'Thêm admin mới' : 'Chỉnh sửa admin'; ?></h1>
    <div class="breadcrumb">Admin / Quản lý admin / <?php echo $action == 'add' ? 'Thêm mới' : 'Chỉnh sửa'; ?></div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label class="form-label">Họ tên *</label>
                    <input type="text" name="name" class="form-control" 
                           value="<?php echo $admin ? htmlspecialchars($admin['name']) : ''; ?>" 
                           required placeholder="VD: Nguyễn Văn A">
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control"
                           value="<?php echo $admin ? htmlspecialchars($admin['email']) : ''; ?>" 
                           required placeholder="VD: admin@example.com">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <input type="tel" name="phone" class="form-control"
                           value="<?php echo $admin ? htmlspecialchars($admin['phone']) : ''; ?>" 
                           placeholder="VD: 0901234567">
                </div>
                <?php if ($action == 'edit'): ?>
                <div class="form-group">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="active" <?php echo ($admin && $admin['status'] == 'active') ? 'selected' : ''; ?>>🟢 Hoạt động</option>
                        <option value="blocked" <?php echo ($admin && $admin['status'] == 'blocked') ? 'selected' : ''; ?>>🔴 Khóa tài khoản</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label class="form-label">Mật khẩu <?php echo $action == 'add' ? '*' : '(để trống nếu không đổi)'; ?></label>
                    <input type="password" name="password" class="form-control" 
                           <?php echo $action == 'add' ? 'required' : ''; ?> 
                           placeholder="Nhập mật khẩu...">
                </div>
                <?php if ($action == 'add'): ?>
                <div class="form-group">
                    <label class="form-label">Xác nhận mật khẩu *</label>
                    <input type="password" name="confirm_password" class="form-control" 
                           required placeholder="Nhập lại mật khẩu...">
                </div>
                <?php endif; ?>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <?php echo $action == 'add' ? '👑 Thêm admin' : '💾 Cập nhật'; ?>
                </button>
                <a href="?page=admins" class="btn btn-secondary">❌ Hủy</a>
            </div>
        </form>
    </div>
</div>

<?php } else { ?>

<div class="content-header">
    <h1 class="content-title">👑 Admin - Quản lý admin</h1>
    <div class="breadcrumb">Admin / Quản lý admin / Danh sách</div>
</div>

<div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <a href="?page=admins&action=add" class="btn btn-primary">👑 + Thêm admin mới</a>
    
    <div style="display: flex; gap: 10px; align-items: center;">
        <input type="text" placeholder="🔍 Tìm kiếm admin..." 
               style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 20px; width: 250px;"
               onkeyup="searchTable(this, 'admins-table')">
        <select style="padding: 8px; border: 1px solid #ddd; border-radius: 5px;" onchange="filterAdmins(this.value)">
            <option value="">Tất cả trạng thái</option>
            <option value="active">Hoạt động</option>
            <option value="blocked">Đã khóa</option>
        </select>
    </div>
</div>

<div class="card">
    <table class="table" id="admins-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Avatar</th>
                <th>Thông tin</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Ngày tạo</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM users WHERE role = 'admin' AND status != 'deleted' ORDER BY created_at DESC";
            $result = mysqli_query($conn, $sql);
            
            if ($result && mysqli_num_rows($result) > 0) {
                while($admin = mysqli_fetch_assoc($result)) {
                    echo '<tr data-status="' . $admin['status'] . '">';
                    echo '<td><strong>#' . $admin['id'] . '</strong></td>';
                    echo '<td>';
                    echo '<div style="width: 50px; height: 50px; background: linear-gradient(135deg, #e50914, #ff6b6b); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; font-weight: bold;">';
                    echo strtoupper(substr($admin['name'], 0, 1));
                    echo '</div>';
                    echo '</td>';
                    echo '<td>';
                    echo '<div style="font-weight: bold; color: #333; margin-bottom: 5px;">👑 ' . htmlspecialchars($admin['name']) . '</div>';
                    echo '<small style="color: #666;">Quản trị viên</small>';
                    echo '</td>';
                    echo '<td>📧 ' . htmlspecialchars($admin['email']) . '</td>';
                    echo '<td>📞 ' . ($admin['phone'] ? htmlspecialchars($admin['phone']) : '<span style="color: #ccc;">Chưa cập nhật</span>') . '</td>';
                    echo '<td>📅 ' . date('d/m/Y', strtotime($admin['created_at'])) . '</td>';
                    
                    $status_text = '';
                    $status_class = '';
                    $status_icon = '';
                    switch($admin['status']) {
                        case 'active':
                            $status_text = 'Hoạt động';
                            $status_class = 'status-confirmed';
                            $status_icon = '🟢';
                            break;
                        case 'blocked':
                            $status_text = 'Đã khóa';
                            $status_class = 'status-cancelled';
                            $status_icon = '🔴';
                            break;
                    }
                    
                    echo '<td><span class="status-badge ' . $status_class . '">' . $status_icon . ' ' . $status_text . '</span></td>';
                    echo '<td>';
                    echo '<div style="display: flex; gap: 5px;">';
                    echo '<a href="?page=admins&action=edit&id=' . $admin['id'] . '" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;" title="Chỉnh sửa admin">✏️</a>';
                    echo '<a href="?page=admins&action=delete&id=' . $admin['id'] . '" class="btn" style="background-color: #dc3545; color: white; padding: 5px 10px; font-size: 12px;" onclick="return confirm(\'⚠️ Bạn có chắc muốn xóa admin này?\')" title="Xóa admin">🗑️</a>';
                    echo '</div>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="8" style="text-align: center; padding: 60px; color: #666;">';
                echo '<div style="font-size: 64px; margin-bottom: 20px;">👑</div>';
                echo '<h3 style="margin-bottom: 10px;">Chưa có admin nào</h3>';
                echo '<p>Hãy thêm admin đầu tiên để quản lý hệ thống.</p>';
                echo '<a href="?page=admins&action=add" class="btn btn-primary" style="margin-top: 15px;">👑 Thêm admin đầu tiên</a>';
                echo '</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php } ?>

<script>
function searchTable(input, tableId) {
    const searchTerm = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function filterAdmins(status) {
    const table = document.getElementById('admins-table');
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (status === '' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script> 