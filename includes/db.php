<?php
// includes/db.php
// BridgeX Platform — Database Connection (PDO)
// Created by: Nora
// Compatible with PHP 5.6+

$db_host    = 'localhost';
$db_name    = 'bridgex_db';
$db_user    = 'root';
$db_pass    = '';
$db_charset = 'utf8mb4';

function getDB() {
    static $pdo = null;

    if ($pdo === null) {
        global $db_host, $db_name, $db_user, $db_pass, $db_charset;

        $dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name . ";charset=" . $db_charset;

        $options = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        );

        try {
            $pdo = new PDO($dsn, $db_user, $db_pass, $options);
        } catch (PDOException $e) {
            error_log("DB Connection Error: " . $e->getMessage());
            die("<p style='color:red;text-align:center;margin-top:50px;'>
                    تعذّر الاتصال بقاعدة البيانات. يرجى المحاولة لاحقاً.
                 </p>");
        }
    }

    return $pdo;
}