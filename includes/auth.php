<?php
// includes/auth.php
// BridgeX Platform — Authentication Helpers
// Created by: Nora
// Compatible with PHP 5.6+

require_once __DIR__ . '/db.php';

// بدء الجلسة إن لم تكن مفعّلة
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ════════════════════════════════════════
// دوال التحقق من حالة تسجيل الدخول
// ════════════════════════════════════════

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
}

function getUserId() {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
}

function getUserName() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
}

// ════════════════════════════════════════
// دوال الحماية
// ════════════════════════════════════════

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if (getUserRole() !== $role) {
        header('Location: /login.php');
        exit;
    }
}

// ════════════════════════════════════════
// تسجيل مستخدم جديد
// ════════════════════════════════════════

function registerUser($name, $email, $password, $role) {
    $allowedRoles = array('client', 'developer');
    if (!in_array($role, $allowedRoles, true)) {
        return array('success' => false, 'error' => 'دور غير صالح.');
    }

    try {
        $pdo = getDB();

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute(array($email));
        if ($stmt->fetch()) {
            return array('success' => false, 'error' => 'البريد الإلكتروني مسجّل مسبقاً.');
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute(array($name, $email, $hashed, $role));

        return array('success' => true);

    } catch (PDOException $e) {
        error_log("registerUser Error: " . $e->getMessage());
        return array('success' => false, 'error' => 'حدث خطأ في الخادم، حاولي مجدداً.');
    }
}

// ════════════════════════════════════════
// تسجيل الدخول
// ════════════════════════════════════════

function loginUser($email, $password) {
    try {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->execute(array($email));
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return array('success' => false, 'error' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.');
        }

        session_regenerate_id(true);

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        return array('success' => true, 'role' => $user['role']);

    } catch (PDOException $e) {
        error_log("loginUser Error: " . $e->getMessage());
        return array('success' => false, 'error' => 'حدث خطأ في الخادم، حاولي مجدداً.');
    }
}

// ════════════════════════════════════════
// توجيه المستخدم حسب دوره
// ════════════════════════════════════════

function redirectByRole($role) {
    $routes = array(
        'admin'     => '/admin/dashboard.php',
        'client'    => '/client/dashboard.php',
        'developer' => '/developer/dashboard.php',
    );
    $url = isset($routes[$role]) ? $routes[$role] : '/index.php';
    header("Location: $url");
    exit;
}