<?php
// register.php
// BridgeX Platform — صفحة التسجيل
// Created by: Nora
// Compatible with PHP 5.6+

require_once 'includes/auth.php';

if (isLoggedIn()) {
    redirectByRole(getUserRole());
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = isset($_POST['name'])     ? trim($_POST['name'])     : '';
    $email    = isset($_POST['email'])    ? trim($_POST['email'])    : '';
    $password = isset($_POST['password']) ? $_POST['password']       : '';
    $confirm  = isset($_POST['confirm'])  ? $_POST['confirm']        : '';
    $role     = isset($_POST['role'])     ? trim($_POST['role'])     : '';

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = 'يرجى تعبئة جميع الحقول المطلوبة.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'صيغة البريد الإلكتروني غير صحيحة.';
    } elseif (strlen($password) < 8) {
        $error = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.';
    } elseif ($password !== $confirm) {
        $error = 'كلمة المرور وتأكيدها غير متطابقتين.';
    } else {
        $result = registerUser($name, $email, $password, $role);
        if ($result['success']) {
            $success = 'تم إنشاء الحساب بنجاح! يمكنك <a href="login.php">تسجيل الدخول</a> الآن.';
        } else {
            $error = $result['error'];
        }
    }
}

$old_name  = isset($_POST['name'])  ? htmlspecialchars($_POST['name'])  : '';
$old_email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$old_role  = isset($_POST['role'])  ? $_POST['role']                    : '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب — BridgeX</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-logo">
            <span class="logo-bridge">Bridge</span><span class="logo-x">X</span>
        </div>
        <h2 class="auth-title">إنشاء حساب جديد</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="register.php" novalidate>

            <div class="form-group">
                <label for="name">الاسم الكامل <span class="required">*</span></label>
                <input type="text" id="name" name="name"
                       value="<?php echo $old_name; ?>"
                       placeholder="أدخل اسمك الكامل" required>
                <span class="field-error" id="nameError"></span>
            </div>

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
                       placeholder="8 أحرف على الأقل" required>
                <span class="field-error" id="passwordError"></span>
            </div>

            <div class="form-group">
                <label for="confirm">تأكيد كلمة المرور <span class="required">*</span></label>
                <input type="password" id="confirm" name="confirm"
                       placeholder="أعد إدخال كلمة المرور" required>
                <span class="field-error" id="confirmError"></span>
            </div>

            <div class="form-group">
                <label for="role">نوع الحساب <span class="required">*</span></label>
                <select id="role" name="role" required>
                    <option value="">-- اختر نوع الحساب --</option>
                    <option value="client"    <?php echo ($old_role === 'client')    ? 'selected' : ''; ?>>عميل (Client)</option>
                    <option value="developer" <?php echo ($old_role === 'developer') ? 'selected' : ''; ?>>مطور (Developer)</option>
                </select>
                <span class="field-error" id="roleError"></span>
            </div>

            <button type="submit" class="btn-auth">إنشاء الحساب</button>

        </form>

        <p class="auth-switch">
            لديك حساب بالفعل؟ <a href="login.php">سجّل دخولك</a>
        </p>

    </div>
</div>

<script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        var valid = true;
        var errors = document.querySelectorAll('.field-error');
        for (var i = 0; i < errors.length; i++) {
            errors[i].textContent = '';
        }

        var name     = document.getElementById('name').value.trim();
        var email    = document.getElementById('email').value.trim();
        var password = document.getElementById('password').value;
        var confirm  = document.getElementById('confirm').value;
        var role     = document.getElementById('role').value;

        if (name.length < 3) {
            document.getElementById('nameError').textContent = 'الاسم يجب أن يكون 3 أحرف على الأقل.';
            valid = false;
        }

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            document.getElementById('emailError').textContent = 'صيغة البريد الإلكتروني غير صحيحة.';
            valid = false;
        }

        if (password.length < 8) {
            document.getElementById('passwordError').textContent = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.';
            valid = false;
        }

        if (password !== confirm) {
            document.getElementById('confirmError').textContent = 'كلمتا المرور غير متطابقتين.';
            valid = false;
        }

        if (!role) {
            document.getElementById('roleError').textContent = 'يرجى اختيار نوع الحساب.';
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
</script>

</body>
</html>