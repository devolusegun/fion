<?php
// db.php - update credentials to match your local MySQL
$DB_HOST = '127.0.0.1';
$DB_NAME = 'fion_backend';
$DB_USER = 'root';
$DB_PASS = '7evenc0d3s'; // set your password

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo "DB connection failed: " . htmlspecialchars($e->getMessage());
    exit;
}
