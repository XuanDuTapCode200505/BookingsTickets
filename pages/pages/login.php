<link rel="stylesheet" type="text/css" href="/BookingsTickets/css/style.css">
<link rel="stylesheet" type="text/css" href="/BookingsTickets/css/login-form.css">

<?php
if (isset($_SESSION['login_error'])) {
    echo '<div class="error">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
    unset($_SESSION['login_error']);
}
if (isset($_SESSION['register_success'])) {
    echo '<div class="success">' . htmlspecialchars($_SESSION['register_success']) . '</div>';
    unset($_SESSION['register_success']);
}
?>

<div class="login">
    <form class="login-form" action="/BookingsTickets/pages/actions/login_process.php" method="post">
        <span>Đăng nhập CGV</span>
        
        <div class="form-group">
            <input type="email" name="email" placeholder="Email của bạn" required>
        </div>
        
        <div class="form-group">
            <input type="password" name="password" placeholder="Mật khẩu" required>
        </div>
        
        <button type="submit">Đăng nhập</button>
        
        <p>Bạn chưa có tài khoản? <a href="index.php?quanly=dangky">Đăng ký ngay</a></p>
    </form>
</div>
