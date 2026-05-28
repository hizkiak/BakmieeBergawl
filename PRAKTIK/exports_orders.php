<?php
require_once 'config.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="pesanan_bakmie_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Nama Pembeli', 'No WA', 'Jumlah Porsi', 'Total Harga', 'Tanggal', 'Waktu']);

$stmt = $pdo->prepare("SELECT * FROM orders WHERE tanggal = CURDATE() ORDER BY created_at DESC");
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['id'],
        $row['nama_pembeli'],
        $row['no_wa'],
        $row['jumlah_porsi'],
        $row['total_harga'],
        $row['tanggal'],
        $row['created_at']
    ]);
}
fclose($output);
exit;
?>