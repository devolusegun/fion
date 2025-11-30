<?php
// Run once from browser or CLI to create DB/tables + sample user
$DB_HOST = '127.0.0.1';
$DB_NAME = 'fion_backend';
$DB_USER = 'root';
$DB_PASS = '7evenc0d3s'; // adjust

try {
    $pdo = new PDO("mysql:host=$DB_HOST;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE $DB_NAME");
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      email VARCHAR(255) NOT NULL UNIQUE,
      password VARCHAR(255) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );");
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS password_resets (
      id INT AUTO_INCREMENT PRIMARY KEY,
      email VARCHAR(255) NOT NULL,
      token VARCHAR(128) NOT NULL,
      expires_at DATETIME NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );");

    $email = 'test@example.com';
    $password = password_hash('Password123', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT IGNORE INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $password]);

    echo "DB + sample user created. Login: test@example.com / Password123";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
