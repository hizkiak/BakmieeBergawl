<?php
require_once 'config.php';

$password = 'admin123'; // GANTI PASSWORD INI!

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    if (isset($_POST['password']) && $_POST['password'] === $password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin Login - Bakmie Juara</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        </head>
        <body class="bg-black min-h-screen flex items-center justify-center">
            <div class="max-w-md w-full px-6">
                <div class="text-center mb-10">
                    <h1 class="text-4xl font-black text-white tracking-tighter">BAKMIE JUARA</h1>
                    <p class="text-red-500 font-bold">ADMIN DASHBOARD</p>
                </div>
                <form method="post" class="bg-neutral-900 rounded-3xl p-10 border border-neutral-800">
                    <h2 class="text-2xl font-bold text-white mb-6 text-center">Masuk ke Admin</h2>
                    <input type="password" name="password" placeholder="Masukkan Password" 
                           class="w-full px-6 py-5 bg-neutral-950 border border-neutral-700 rounded-2xl text-white focus:border-red-500 outline-none">
                    <button type="submit" class="mt-6 w-full bg-red-600 hover:bg-red-700 py-5 rounded-2xl font-bold text-lg transition">
                        LOGIN
                    </button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// PAKSA HARGA 20.000
if (!defined('HARGA_PER_PORSI')) {
    define('HARGA_PER_PORSI', 20000);
}

$stmt = $pdo->query("SELECT kuota_harian FROM pengaturan WHERE id=1");
$kuota_harian = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bakmie Juara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-black text-gray-100 min-h-screen">

<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-4xl font-black tracking-tighter">BAKMIE JUARA</h1>
            <p class="text-red-500 font-bold">ADMIN DASHBOARD</p>
        </div>
        <div class="flex gap-3">
            <a href="export_orders.php" class="flex items-center gap-3 bg-green-600 hover:bg-green-700 px-6 py-4 rounded-2xl font-bold transition">
                <i class="fas fa-download"></i> EKSPOR EXCEL
            </a>
            <form action="delete_all_orders.php" method="POST" onsubmit="return confirm('Yakin hapus SEMUA data hari ini?')">
                <button type="submit" class="flex items-center gap-3 bg-red-600 hover:bg-red-700 px-6 py-4 rounded-2xl font-bold transition">
                    <i class="fas fa-trash-alt"></i> HAPUS SEMUA
                </button>
            </form>
        </div>
    </div>

    <!-- Kuota -->
    <div class="bg-neutral-900 rounded-3xl p-8 border border-neutral-800 mb-8">
        <h2 class="text-xl font-bold mb-6">PENGATURAN KUOTA HARIAN</h2>
        <form action="update_quota.php" method="POST" class="flex gap-6 items-end">
            <div class="flex-1">
                <label class="block text-neutral-400 text-sm mb-2">JUMLAH KUOTA HARI INI</label>
                <input type="number" name="kuota" value="<?= $kuota_harian ?>" 
                       class="w-full bg-neutral-950 border border-neutral-700 rounded-2xl px-6 py-6 text-4xl font-black focus:border-red-500 outline-none">
            </div>
            <button type="submit" class="bg-red-600 hover:bg-red-700 px-10 py-6 rounded-2xl font-bold text-lg transition">
                SIMPAN KUOTA
            </button>
        </form>
    </div>

    <!-- Tabel Pesanan -->
    <div class="bg-neutral-900 rounded-3xl overflow-hidden border border-neutral-800">
        <div class="px-8 py-6 border-b border-neutral-800 flex justify-between items-center">
            <h2 class="text-2xl font-bold">PESANAN HARI INI</h2>
            <span class="bg-neutral-800 px-5 py-2 rounded-full text-sm"><?= date('d F Y') ?></span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-neutral-950">
                        <th class="px-8 py-5 text-left">ID</th>
                        <th class="px-8 py-5 text-left">NAMA</th>
                        <th class="px-8 py-5 text-left">NO WA</th>
                        <th class="px-8 py-5 text-center">PORSI</th>
                        <th class="px-8 py-5 text-right">HARGA/PORSI</th>
                        <th class="px-8 py-5 text-right">TOTAL</th>
                        <th class="px-8 py-5 text-center">WAKTU</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-800">
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM orders WHERE tanggal = CURDATE() ORDER BY created_at DESC");
                    $stmt->execute();
                    while ($row = $stmt->fetch()): 
                        $harga_per_porsi = 20000; // Paksa 20.000
                        $total_harga = $row['jumlah_porsi'] * $harga_per_porsi;
                    ?>
                    <tr class="hover:bg-neutral-950 transition">
                        <td class="px-8 py-6"><?= $row['id'] ?></td>
                        <td class="px-8 py-6"><?= htmlspecialchars($row['nama_pembeli']) ?></td>
                        <td class="px-8 py-6"><?= htmlspecialchars($row['no_wa']) ?></td>
                        <td class="px-8 py-6 text-center font-bold"><?= $row['jumlah_porsi'] ?></td>
                        <td class="px-8 py-6 text-right">Rp <?= number_format($harga_per_porsi) ?></td>
                        <td class="px-8 py-6 text-right font-bold text-orange-400">Rp <?= number_format($total_harga) ?></td>
                        <td class="px-8 py-6 text-center text-sm text-neutral-500"><?= date('H:i', strtotime($row['created_at'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>