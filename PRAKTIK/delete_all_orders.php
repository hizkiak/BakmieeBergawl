<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("DELETE FROM orders WHERE tanggal = CURDATE()");
    $stmt->execute();
    
    header("Location: admin.php?status=deleted");
    exit;
}
?>