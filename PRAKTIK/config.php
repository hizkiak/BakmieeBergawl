<?php
// config.php
$host = 'localhost';
$db   = 'bakmie_db';
$user = 'root';        // ganti jika perlu
$pass = '';            // ganti jika perlu

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Harga per porsi
define('HARGA_PER_PORSI', 15000);
?>