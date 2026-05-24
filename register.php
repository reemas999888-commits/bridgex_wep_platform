<?php
// register.php
// BridgeX Platform — Registration Page
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
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Password and confirmation do not match.';
    } else {
        $result = registerUser($name, $email, $password, $role);
        if ($result['success']) {
            $success = 'Account created successfully! You can <a href="login.php">login</a> now.';
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
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — BridgeX</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-logo">
            <span class="logo-bridge">Bridge</span><span class="logo-x">X</span>
        </div>
        <h2 class="auth-title">Create New Account</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="register.php" novalidate>

            <div class="form-group">
                <label for="name">Full Name <span class="required">*</span></label>
                <input type="text" id="name" name="name"
                       value="<?php echo $old_name; ?>"
                       placeholder="Enter your full name" required>
                <span class="field-error" id="nameError"></span>
            </div>

            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email"
                       value="<?php echo $old_email; ?>"
                       placeholder="example@email.com" required>
                <span class="field-error" id="emailError"></span>
            </div>

            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password"
                       placeholder="At least 8 characters" required>
                <span class="field-error" id="passwordError"></span>
            </div>

            <div class="form-group">
                <label for="confirm">Confirm Password <span class="required">*</span></label>
                <input type="password" id="confirm" name="confirm"
                       placeholder="Re-enter your password" required>
                <span class="field-error" id="confirmError"></span>
            </div>

            <div class="form-group">
                <label for="role">Account Type <span class="required">*</span></label>
                <select id="role" name="role" required>
                    <option value="">-- Select account type --</option>
                    <option value="client"    <?php echo ($old_role === 'client')    ? 'selected' : ''; ?>>Client</option>
                    <option value="developer" <?php echo ($old_role === 'developer') ? 'selected' : ''; ?>>Developer</option>
                </select>
                <span class="field-error" id="roleError"></span>
            </div>

            <button type="submit" class="btn-auth">Create Account</button>

        </form>

        <p class="auth-switch">
            Already have an account? <a href="login.php">Login</a>
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
            document.getElementById('nameError').textContent = 'Name must be at least 3 characters.';
            valid = false;
        }

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            document.getElementById('emailError').textContent = 'Invalid email format.';
            valid = false;
        }

        if (password.length < 8) {
            document.getElementById('passwordError').textContent = 'Password must be at least 8 characters.';
            valid = false;
        }

        if (password !== confirm) {
            document.getElementById('confirmError').textContent = 'Passwords do not match.';
            valid = false;
        }

        if (!role) {
            document.getElementById('roleError').textContent = 'Please select an account type.';
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
</script>

</body>
</html>