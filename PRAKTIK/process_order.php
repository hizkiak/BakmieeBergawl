<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $no_wa    = trim($_POST['no_wa'] ?? '');
    $jumlah   = (int)($_POST['jumlah_porsi'] ?? 0);
    $total    = $jumlah * HARGA_PER_PORSI;
    $tanggal  = date('Y-m-d');

    // Validasi
    if (empty($nama) || empty($no_wa) || $jumlah <= 0) {
        die("Data tidak lengkap. Silakan isi semua field.");
    }

    // Cek Kuota
    $stmt = $pdo->prepare("SELECT kuota_harian FROM pengaturan WHERE id=1");
    $stmt->execute();
    $kuota = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COALESCE(SUM(jumlah_porsi), 0) FROM orders WHERE tanggal = ?");
    $stmt->execute([$tanggal]);
    $terjual = $stmt->fetchColumn();

    if ($terjual + $jumlah > $kuota) {
        die("Maaf, kuota hari ini sudah habis.");
    }

    // Simpan ke Database
    try {
        $stmt = $pdo->prepare("INSERT INTO orders (nama_pembeli, no_wa, jumlah_porsi, total_harga, tanggal) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nama, $no_wa, $jumlah, $total, $tanggal]);

        $order_id = $pdo->lastInsertId();

        // Redirect ke halaman pembayaran
        header("Location: payment.php?order_id=$order_id");
        exit;

    } catch (Exception $e) {
        die("Error menyimpan data: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}
?>