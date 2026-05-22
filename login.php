<?php
// login.php
// BridgeX Platform — صفحة تسجيل الدخول
// Created by: Nora
// Compatible with PHP 5.6+

require_once 'includes/auth.php';

if (isLoggedIn()) {
    redirectByRole(getUserRole());
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = isset($_POST['email'])    ? trim($_POST['email'])    : '';
    $password = isset($_POST['password']) ? $_POST['password']       : '';

    if (empty($email) || empty($password)) {
        $error = 'يرجى إدخال البريد الإلكتروني وكلمة المرور.';
    } else {
        $result = loginUser($email, $password);
        if ($result['success']) {
            redirectByRole($result['role']);
        } else {
            $error = $result['error'];
        }
    }
}

$old_email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول — BridgeX</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-logo">
            <span class="logo-bridge">Bridge</span><span class="logo-x">X</span>
        </div>
        <h2 class="auth-title">تسجيل الدخول</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">تم إنشاء حسابك بنجاح! سجّل دخولك الآن.</div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="login.php" novalidate>

            <div class="form-group">
                <label for="email">البريد الإلكتروني <span class="required">*</span></label>
                <input type="email" id="email" name="email"
                       value="<?php echo $old_email; ?>"
                       placeholder="example@email.com" required>
                <span class="field-error" id="emailError"></span>
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور <span class="required">*</span></label>
                <input type="password" id="password" name="password"
                       placeholder="كلمة المرور" required>
                <span class="field-error" id="passwordError"></span>
            </div>

            <button type="submit" class="btn-auth">دخول</button>

        </form>

        <p class="auth-switch">
            ليس لديك حساب؟ <a href="register.php">سجّل الآن</a>
        </p>

    </div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        var valid = true;
        var errors = document.querySelectorAll('.field-error');
        for (var i = 0; i < errors.length; i++) {
            errors[i].textContent = '';
        }

        var email    = document.getElementById('email').value.trim();
        var password = document.getElementById('password').value;

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            document.getElementById('emailError').textContent = 'صيغة البريد الإلكتروني غير صحيحة.';
            valid = false;
        }

        if (password.length < 1) {
            document.getElementById('passwordError').textContent = 'يرجى إدخال كلمة المرور.';
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
</script>

</body>
</html>