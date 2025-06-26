<div class="menu">
    <div class="menu-left">
        <div class="logo">
            <a href="index.php"><img src="https://www.cgv.vn/skin/frontend/cgv/default/images/cgvlogo.png" alt="logo"></a>
        </div>
    </div>
    <div class="menu-right">
        <ul class="list-menu">
                <li><a href="index.php?quanly=phim">Phim</a></li>
                <li><a href="index.php?quanly=rap">Rạp</a></li>
                <?php
                if (isset($_SESSION['user_id'])) {
                    // Đã đăng nhập
                    echo '<li><a href="index.php?quanly=lich-su-dat-ve">Lịch sử đặt vé</a></li>';
                    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                        echo '<li><a href="index.php?quanly=admin">Quản trị</a></li>';
                    }
                    echo '<li><a href="/BookingsTickets/pages/actions/logout_process.php">Đăng xuất</a></li>';
                    echo '<li><span style="color:#e71a0f;">Xin chào, ' . htmlspecialchars($_SESSION['name']) . '</span></li>';
                } else {
                    // Chưa đăng nhập
                    echo '<li><a href="index.php?quanly=dangnhap">Đăng nhập</a></li>';
                }
                ?>
        </ul>
    </div>
</div>
<div class="menu-line"></div>
<div class="clear"></div>