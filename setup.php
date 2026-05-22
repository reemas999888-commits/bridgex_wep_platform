<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // إنشاء قاعدة البيانات
    $pdo->exec("CREATE DATABASE IF NOT EXISTS bridgex_db 
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE bridgex_db");

    // جدول users
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin','client','developer') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // حساب الأدمن الافتراضي
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute(['admin@bridgex.com']);

    if ($check->rowCount() === 0) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) 
                               VALUES (?, ?, ?, 'admin')");
        $stmt->execute(['Admin', 'admin@bridgex.com', password_hash('admin123', PASSWORD_DEFAULT)]);
        echo "✅ تم إنشاء حساب الأدمن!";
    } else {
        echo "⚠️ الحساب موجود مسبقاً";
    }

} catch (PDOException $e) {
    die("❌ " . $e->getMessage());
}
?>