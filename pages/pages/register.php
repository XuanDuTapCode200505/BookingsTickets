<link rel="stylesheet" type="text/css" href="/BookingsTickets/css/style.css">
<link rel="stylesheet" type="text/css" href="/BookingsTickets/css/register-form.css">

<?php
if (isset($_SESSION['register_error'])) {
    echo '<div class="error">' . htmlspecialchars($_SESSION['register_error']) . '</div>';
    unset($_SESSION['register_error']);
}
?>

<div class="register">
    <form class="register-form" id="registerForm" action="/BookingsTickets/pages/actions/register_process.php" method="post">
        <span>Đăng ký CGV</span>
        
        <div class="form-group">
            <input type="text" name="name" id="name" placeholder="Họ và tên" required autocomplete="name">
        </div>
        
        <div class="form-group">
            <input type="email" name="email" id="email" placeholder="Email của bạn" required autocomplete="email">
        </div>
        
        <div class="form-group">
            <input type="password" name="password" id="password" placeholder="Mật khẩu" required autocomplete="new-password">
            <div class="password-strength" id="passwordStrength">
                <div class="strength-bar">
                    <div class="strength-fill"></div>
                </div>
                <span class="strength-text"></span>
            </div>
        </div>
        
        <div class="form-group">
            <input type="password" name="confirm_password" id="confirmPassword" placeholder="Xác nhận mật khẩu" required autocomplete="new-password">
        </div>
        
        <button type="submit" id="registerBtn">
            <span class="btn-text">Đăng ký</span>
        </button>
        
        <p>Đã có tài khoản? <a href="index.php?quanly=dangnhap">Đăng nhập ngay</a></p>
    </form>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        let feedback = '';
        
        if (password.length >= 6) strength += 1;
        if (password.length >= 8) strength += 1;
        if (/[a-z]/.test(password)) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;
        
        const strengthEl = document.getElementById('passwordStrength');
        if (!strengthEl) return;
        
        const strengthText = strengthEl.querySelector('.strength-text');
        
        strengthEl.className = 'password-strength';
        
        if (password.length === 0) {
            strengthEl.style.display = 'none';
            return;
        }
        
        strengthEl.style.display = 'block';
        
        if (strength <= 2) {
            strengthEl.classList.add('strength-weak');
            feedback = 'Mật khẩu yếu';
        } else if (strength <= 4) {
            strengthEl.classList.add('strength-medium');
            feedback = 'Mật khẩu trung bình';
        } else {
            strengthEl.classList.add('strength-strong');
            feedback = 'Mật khẩu mạnh';
        }
        
        if (strengthText) {
            strengthText.textContent = feedback;
        }
    }

    // Password confirmation checker
    function checkPasswordMatch() {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirmPassword');
        
        if (!password || !confirmPassword) return;
        
        const passwordValue = password.value;
        const confirmPasswordValue = confirmPassword.value;
        
        if (confirmPasswordValue.length === 0) {
            confirmPassword.classList.remove('error');
            confirmPassword.style.borderColor = '';
            return;
        }
        
        if (passwordValue === confirmPasswordValue) {
            confirmPassword.classList.remove('error');
            confirmPassword.style.borderColor = 'rgba(76, 175, 80, 0.6)';
        } else {
            confirmPassword.classList.add('error');
            confirmPassword.style.borderColor = '#f44336';
        }
    }

    // Email validation
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Phone validation
    function validatePhone(phone) {
        const phoneRegex = /^[0-9+\-\s()]{10,15}$/;
        return phoneRegex.test(phone);
    }

    // Event listeners
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirmPassword');
    const emailField = document.getElementById('email');
    const phoneField = document.getElementById('phone');
    const nameField = document.getElementById('name');
    const form = document.getElementById('registerForm');

    if (passwordField) {
        passwordField.addEventListener('input', function(e) {
            checkPasswordStrength(e.target.value);
            checkPasswordMatch();
        });
    }

    if (confirmPasswordField) {
        confirmPasswordField.addEventListener('input', checkPasswordMatch);
    }

    if (emailField) {
        emailField.addEventListener('blur', function(e) {
            const email = e.target.value;
            if (email && !validateEmail(email)) {
                e.target.classList.add('error');
                e.target.style.borderColor = '#f44336';
            } else if (email) {
                e.target.classList.remove('error');
                e.target.style.borderColor = 'rgba(76, 175, 80, 0.6)';
            }
        });

        emailField.addEventListener('input', function(e) {
            e.target.classList.remove('error');
            e.target.style.borderColor = '';
        });
    }

    if (phoneField) {
        phoneField.addEventListener('blur', function(e) {
            const phone = e.target.value;
            if (phone && !validatePhone(phone)) {
                e.target.classList.add('error');
            } else if (phone) {
                e.target.classList.remove('error');
            }
        });

        phoneField.addEventListener('input', function(e) {
            e.target.classList.remove('error');
        });
    }

    // Form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            const password = passwordField ? passwordField.value : '';
            const confirmPassword = confirmPasswordField ? confirmPasswordField.value : '';
            const email = emailField ? emailField.value : '';
            const phone = phoneField ? phoneField.value : '';
            const name = nameField ? nameField.value : '';

            let hasError = false;

            // Validate all fields
            if (!name.trim()) {
                nameField.classList.add('error');
                hasError = true;
            }

            if (!validateEmail(email)) {
                emailField.classList.add('error');
                hasError = true;
            }

            if (!validatePhone(phone)) {
                phoneField.classList.add('error');
                hasError = true;
            }

            if (password.length < 6) {
                passwordField.classList.add('error');
                hasError = true;
            }

            if (password !== confirmPassword) {
                confirmPasswordField.classList.add('error');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                alert('Vui lòng kiểm tra lại thông tin đã nhập!');
                return;
            }

            // Show loading state
            const btn = document.getElementById('registerBtn');
            const btnText = btn.querySelector('.btn-text');
            
            if (btn && btnText) {
                btn.disabled = true;
                btn.classList.add('loading');
                btnText.textContent = 'Đang tạo tài khoản...';
                
                // Reset sau 10 giây nếu có lỗi
                setTimeout(function() {
                    btn.disabled = false;
                    btn.classList.remove('loading');
                    btnText.textContent = 'Đăng ký';
                }, 10000);
            }
        });
    }

    // Auto focus vào name field
    if (nameField) {
        nameField.focus();
    }
});
    }
});
</script>