<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kuota = (int)$_POST['kuota'];
    $stmt = $pdo->prepare("UPDATE pengaturan SET kuota_harian = ? WHERE id=1");
    $stmt->execute([$kuota]);
}

header("Location: admin.php");
exit;
?>