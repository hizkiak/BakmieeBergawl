<?php
require_once 'config.php';

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit;
}

$order_id = (int)$_GET['order_id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Order tidak ditemukan.");
}

$total = $order['total_harga'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Bakmie Juara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-black text-gray-100 min-h-screen">

<div class="max-w-lg mx-auto bg-neutral-950 min-h-screen">

    <!-- Header -->
    <div class="bg-gradient-to-r from-red-600 to-orange-600 p-8 text-center">
        <h1 class="text-3xl font-black">BAKMIE JUARA</h1>
        <p class="text-red-100">Pembayaran Order #<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></p>
    </div>

    <div class="p-6">

        <!-- Ringkasan -->
        <div class="bg-neutral-900 rounded-3xl p-6 mb-8">
            <h2 class="font-bold mb-4">Ringkasan Pesanan</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-neutral-400">Nama</span><span><?= htmlspecialchars($order['nama_pembeli']) ?></span></div>
                <div class="flex justify-between"><span class="text-neutral-400">No WA</span><span><?= htmlspecialchars($order['no_wa']) ?></span></div>
                <div class="flex justify-between"><span class="text-neutral-400">Porsi</span><span class="font-bold"><?= $order['jumlah_porsi'] ?> porsi</span></div>
                <div class="flex justify-between pt-4 border-t border-neutral-700">
                    <span class="font-bold">Total</span>
                    <span class="text-3xl font-black text-yellow-400">Rp <?= number_format($total) ?></span>
                </div>
            </div>
        </div>

        <h2 class="text-lg font-bold mb-4 px-1">Pilih Metode Pembayaran</h2>

        <div class="grid gap-4">

            <!-- COD -->
            <div onclick="showPaymentDetail('cod')" class="cursor-pointer bg-neutral-900 hover:bg-neutral-800 border border-neutral-700 hover:border-green-500 rounded-3xl p-5 flex items-center gap-4">
                <i class="fas fa-money-bill-wave text-4xl text-green-500"></i>
                <div><strong>Cash On Delivery (COD)</strong><br><small class="text-neutral-400">Bayar saat terima bakmie</small></div>
            </div>

            <!-- Transfer Bank -->
            <div onclick="showPaymentDetail('transfer')" class="cursor-pointer bg-neutral-900 hover:bg-neutral-800 border border-neutral-700 hover:border-blue-500 rounded-3xl p-5 flex items-center gap-4">
                <i class="fas fa-university text-4xl text-blue-500"></i>
                <div><strong>Transfer Bank</strong><br><small class="text-neutral-400">Mandiri - HIZKIA PAKPAHAN</small></div>
            </div>

            <!-- QRIS -->
            <div onclick="showPaymentDetail('qris')" class="cursor-pointer bg-neutral-900 hover:bg-neutral-800 border border-neutral-700 hover:border-purple-500 rounded-3xl p-5 flex items-center gap-4">
                <i class="fas fa-qrcode text-4xl text-purple-500"></i>
                <div><strong>QRIS</strong><br><small class="text-neutral-400">Scan kode QR</small></div>
            </div>

            <!-- WhatsApp -->
            <div onclick="showPaymentDetail('wa')" class="cursor-pointer bg-neutral-900 hover:bg-neutral-800 border border-green-600 rounded-3xl p-5 flex items-center gap-4">
                <i class="fab fa-whatsapp text-4xl text-green-500"></i>
                <div><strong>Chat Admin via WhatsApp</strong><br><small class="text-neutral-400">Konfirmasi langsung</small></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-black/90 flex items-center justify-center z-50">
    <div class="bg-neutral-900 rounded-3xl max-w-md w-full mx-4 overflow-hidden">
        <div id="modalContent" class="p-8"></div>
    </div>
</div>

<script>
function showPaymentDetail(method) {
    let content = '';

    if (method === 'cod') {
        content = `
            <h3 class="text-2xl font-bold text-green-400 mb-6 text-center">Cash On Delivery</h3>
            <p class="text-center text-neutral-300 mb-8">Bayar Rp <?= number_format($total) ?> secara tunai saat bakmie diterima.</p>
            <button onclick="closeModal()" class="w-full py-4 bg-green-600 hover:bg-green-700 rounded-2xl font-bold">OK, Mengerti</button>
        `;
    } 
    else if (method === 'transfer') {
        content = `
            <h3 class="text-2xl font-bold mb-6 text-center">Transfer Bank Mandiri</h3>
            <div class="bg-neutral-800 rounded-2xl p-6 text-center mb-6">
                <p class="text-neutral-400">Atas Nama</p>
                <p class="font-bold text-xl">HIZKIA PAKPAHAN</p>
                <p class="text-3xl font-black text-white mt-4">1090023474539</p>
                <p class="text-sm text-neutral-400 mt-1">Bank Mandiri</p>
            </div>
            <p class="text-center text-sm text-neutral-400 mb-6">Setelah transfer, mohon kirim bukti transfer ke admin</p>
            <button onclick="chatAdmin()" class="w-full py-4 bg-blue-600 hover:bg-blue-700 rounded-2xl font-bold">Kirim Bukti ke Admin</button>
        `;
    } 
    else if (method === 'qris') {
        content = `
            <h3 class="text-2xl font-bold mb-6 text-center">Scan QRIS</h3>
            <div class="bg-white p-4 rounded-2xl mx-auto w-72">
                <img src="qris.png" class="rounded-xl w-full" alt="QRIS Bakmie Juara">
            </div>
            <p class="text-center text-sm text-neutral-400 mt-6">Scan QRIS di atas menggunakan aplikasi bank / e-wallet Anda</p>
            <button onclick="chatAdmin()" class="mt-6 w-full py-4 bg-purple-600 hover:bg-purple-700 rounded-2xl font-bold">Sudah Transfer? Konfirmasi</button>
        `;
    } 
    else if (method === 'wa') {
        chatAdmin();
        return;
    }

    document.getElementById('modalContent').innerHTML = content;
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function chatAdmin() {
    const text = `🔥 *PESANAN BAKMIE JUARA*%0A%0A` +
                 `Nama: *<?= urlencode($order['nama_pembeli']) ?>*%0A` +
                 `No WA: *<?= $order['no_wa'] ?>*%0A` +
                 `Jumlah: *<?= $order['jumlah_porsi'] ?> porsi*%0A` +
                 `Total: *Rp <?= number_format($total) ?>*%0A%0A` +
                 `Sudah saya bayar ya Min 🙏`;
    
    window.location.href = `https://wa.me/085835615247?text=${text}`;
}
</script>

</body>
</html>