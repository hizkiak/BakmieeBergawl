<?php
require_once 'config.php';

// Ambil Kuota Harian
$stmt = $pdo->query("SELECT kuota_harian FROM pengaturan WHERE id=1");
$kuota_harian = $stmt->fetchColumn();

// Hitung Total Terjual Hari Ini
$stmt = $pdo->prepare("SELECT COALESCE(SUM(jumlah_porsi), 0) FROM orders WHERE tanggal = CURDATE()");
$stmt->execute();
$total_terjual = $stmt->fetchColumn();

$sisa_kuota = max(0, $kuota_harian - $total_terjual);
$kuota_habis = $sisa_kuota <= 0;

// Harga Per Porsi
if (!defined('HARGA_PER_PORSI')) {
    define('HARGA_PER_PORSI', 20.000);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔥 BAKMIE JUARA - Candu Dalam Setiap Kunyahan 🔥</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #0d0d0d;
        }
        h1, .font-heading {
            font-family: 'Space Grotesk', sans-serif;
        }
        
        .hero-bg {
            background: linear-gradient(180deg, rgba(13, 13, 13, 0.4) 0%, rgba(13, 13, 13, 0.95) 100%), 
                        url('https://images.unsplash.com/photo-1612927601601-6638404737ce?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
        }

        .glow-text {
            text-shadow: 0 0 20px rgba(239, 68, 68, 0.6);
        }

        .glow-box-green {
            box-shadow: 0 0 25px rgba(34, 197, 94, 0.3);
        }
        
        .glow-box-red {
            box-shadow: 0 0 25px rgba(239, 68, 68, 0.3);
        }

        @keyframes pulse-slow {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.03); }
        }
        .order-card-pulse {
            animation: pulse-slow 4s infinite ease-in-out;
        }

        /* Radio Porsi */
        .porsi-radio:checked + .porsi-box {
            background: #ef4444;
            color: white;
            border-color: #f59e0b;
            box-shadow: 0 10px 20px -5px rgba(239, 68, 68, 0.5);
            transform: translateY(-4px);
        }
    </style>
</head>
<body class="text-gray-100 min-h-screen selection:bg-red-600 selection:text-white overflow-x-hidden">

    <!-- Hero -->
    <div class="hero-bg min-h-[60vh] flex items-center justify-center text-center relative px-4 pt-12 pb-24">
        <div class="max-w-3xl mx-auto z-10 space-y-6">
            <div class="inline-flex items-center gap-2 bg-black/60 border-2 border-red-500/50 backdrop-blur-md px-6 py-2 rounded-full shadow-lg">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-ping"></span>
                <span class="font-black tracking-widest text-xs uppercase text-red-400">🔥 HANYA HARI INI!! 🔥</span>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight leading-none text-white uppercase">
                Kelezatan <span class="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 glow-text">Bakmie Juara</span>
            </h1>
            
            <p class="text-sm md:text-lg text-gray-300 max-w-md mx-auto font-medium tracking-wide">
                Gak perlu pusing nyari yang enak. Tekstur mie karet kenyal berpadu topping melimpah siap bikin lidah bergoyang!
            </p>

            <div class="inline-block">
                <div class="bg-gradient-to-b from-neutral-900 to-neutral-950 border-2 border-neutral-800 rounded-3xl p-6 flex items-center gap-8 shadow-2xl <?= $kuota_habis ? 'glow-box-red border-red-900/50' : 'glow-box-green border-green-900/50' ?>">
                    <div class="text-left">
                        <p class="text-neutral-400 text-[10px] tracking-widest font-bold uppercase">SISA PORSI HARI INI</p>
                        <p class="text-4xl md:text-5xl font-black font-heading tracking-tight text-white mt-1">
                            <?= $sisa_kuota ?> <span class="text-lg font-normal text-neutral-500">/ <?= $kuota_harian ?> Porsi</span>
                        </p>
                    </div>
                    <div class="px-4 py-3 bg-neutral-900 border border-neutral-800 rounded-2xl text-center">
                        <span class="text-2xl font-bold <?= $kuota_habis ? 'text-red-500' : 'text-green-400' ?>">
                            <?= $kuota_habis ? 'SOLD OUT' : 'READY' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Pesanan -->
    <div class="max-w-xl mx-auto px-4 -mt-20 relative z-20 pb-24">
        <div class="bg-gradient-to-b from-neutral-900 to-neutral-950 rounded-[2.5rem] border border-neutral-800 shadow-[0_50px_100px_-20px_rgba(0,0,0,0.7)] overflow-hidden order-card-pulse">

            <?php if ($kuota_habis): ?>
                <div class="px-8 py-20 text-center space-y-4">
                    <div class="text-8xl filter drop-shadow-[0_0_20px_rgba(239, 68, 68, 0.4)]">⚡</div>
                    <h2 class="text-3xl font-black text-white tracking-tight uppercase">YAH! TELAT DIKIT HABIS!</h2>
                    <p class="text-gray-400 text-sm md:text-base max-w-sm mx-auto leading-relaxed">
                        Hari ini <span class="text-red-500 font-bold"><?= $kuota_harian ?> porsi</span> ludes terjual tanpa sisa!<br>
                        Pasang alarm kamu, jangan sampai kelewatan besok pagi jam <span class="font-bold text-yellow-400">07:00 WIB</span>.
                    </p>
                    <div class="pt-4">
                        <a href="https://wa.me/628XXXXXXXXXX?text=Min,%20kabari%20kalau%20besok%20bakmienya%20ready%20ya!" 
                           class="inline-flex items-center gap-2 px-6 py-3 bg-neutral-800 hover:bg-neutral-700 text-sm font-bold rounded-xl transition-all border border-neutral-700">
                            <i class="fab fa-whatsapp"></i> Ingatkan Saya Besok
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="p-8 md:p-12 space-y-8">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-black uppercase tracking-tight text-white">Amankan Porsi Mu!</h2>
                        <p class="text-xs text-red-400 font-semibold uppercase mt-1 tracking-wider">⚠️ Slot terbatas, siapa cepat dia dapat.</p>
                    </div>

                    <form action="process_order.php" method="POST" class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-gray-300 font-bold text-sm uppercase tracking-wide">Nama Penikmat</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-500">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" name="nama" required placeholder="Tulis nama lengkap kamu..."
                                       class="w-full pl-12 pr-5 py-4 bg-neutral-950 border-2 border-neutral-800 rounded-2xl focus:border-red-500 focus:ring-0 transition-all outline-none text-white font-medium placeholder:text-neutral-700">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-gray-300 font-bold text-sm uppercase tracking-wide">Nomor WhatsApp (Aktif)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-500">
                                    <i class="fab fa-whatsapp text-lg"></i>
                                </span>
                                <input type="tel" name="no_wa" required placeholder="Contoh: 081234567xxx"
                                       class="w-full pl-12 pr-5 py-4 bg-neutral-950 border-2 border-neutral-800 rounded-2xl focus:border-red-500 focus:ring-0 transition-all outline-none text-white font-medium placeholder:text-neutral-700">
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-gray-300 font-bold text-sm uppercase tracking-wide">Mau Berapa Porsi?</label>
                            <div class="grid grid-cols-4 gap-3">
                                <?php for($i = 1; $i <= min($sisa_kuota, 8); $i++): ?>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="jumlah_porsi" value="<?= $i ?>" class="porsi-radio peer hidden" required>
                                        <div class="porsi-box border-2 border-neutral-800 bg-neutral-950 hover:border-neutral-600 rounded-2xl py-4 text-center font-black text-xl text-neutral-400 transition-all duration-150">
                                            <?= $i ?>
                                        </div>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                    class="w-full py-5 bg-gradient-to-r from-red-600 via-orange-500 to-yellow-500 text-white font-black text-xl rounded-2xl shadow-[0_15px_30px_-5px_rgba(239,68,68,0.4)] hover:shadow-[0_20px_40px_-5px_rgba(239,68,68,0.6)] hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-3 uppercase tracking-wider">
                                <i class="fas fa-shopping-basket text-2xl"></i>
                                SIKAT SEKARANG!
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <!-- Bagian Harga yang Diubah -->
        <div class="text-center mt-12 space-y-4">
            <div class="inline-block bg-neutral-900/80 border border-neutral-800 px-8 py-3 rounded-2xl">
                <p class="text-neutral-400 text-xs font-bold uppercase tracking-widest">
                    INVESTASI PERUT LAPAR: <span class="text-xl font-black text-yellow-400 ml-1">RP 20.000</span>
                </p>
            </div>
            
            <div class="flex justify-center flex-wrap gap-6 text-[11px] text-neutral-500 font-bold uppercase tracking-wider">
                <span class="flex items-center gap-1.5"><i class="fas fa-pepper-hot text-red-500"></i> LEVEL PEDAS REQUEST</span>
                <span class="flex items-center gap-1.5"><i class="fas fa-crown text-yellow-500"></i> DAGING AYAM MELIMPAH</span>
                <span class="flex items-center gap-1.5"><i class="fas fa-hand-holding-heart text-orange-500"></i> 100% HALAL</span>
            </div>
        </div>
    </div>

</body>
</html>