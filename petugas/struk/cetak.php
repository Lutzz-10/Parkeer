<?php
require_once __DIR__ . '/../../includes/auth.php';
cekRole(['petugas']);
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/transaksi_functions.php';

$database = new Database();
$koneksi = $database->connect();

$idParkir = (int) ($_GET['id'] ?? 0);
$transaksi = getDetailTransaksi($koneksi, $idParkir);

if (!$transaksi || $transaksi['status'] !== 'keluar') {
    die('Struk tidak tersedia. Transaksi belum selesai atau tidak ditemukan.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Parkir - <?= htmlspecialchars($transaksi['plat_nomor']) ?></title>
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Plus Jakarta Sans', 'sans-serif'],
                    mono: ['JetBrains Mono', 'monospace'],
                },
                colors: {
                    brand: {
                        50: '#FFF7EA',
                        100: '#FEECC7',
                        200: '#FDD68C',
                        400: '#F8BA4E',
                        500: '#F5A623',
                        600: '#DB8E12',
                        700: '#B0700D',
                        800: '#7A4E09'
                    }
                }
            }
        }
    }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .receipt-font { font-family: 'JetBrains Mono', monospace; }

        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .struk-box { 
                box-shadow: none !important; 
                border: none !important; 
                width: 100% !important; 
                max-width: 80mm !important;
                margin: 0 !important;
                padding: 10px !important;
            }
            @page { margin: 0; size: 80mm auto; }
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col items-center justify-center p-4 sm:p-8">

    <!-- Struk Paper Card -->
    <div class="struk-box bg-white text-slate-900 shadow-2xl shadow-brand-500/10 rounded-2xl p-6 w-[340px] receipt-font text-xs relative overflow-hidden border border-slate-200">
        
        <!-- Header Logo & Branding -->
        <div class="text-center mb-4">
            <p class="font-extrabold text-base tracking-widest text-slate-900">PARKEER SYSTEM</p>
            <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Smart Gate &amp; Auto-Parking</p>
            <p class="text-[9px] text-slate-400 mt-0.5">CV Creative Gama</p>
        </div>

        <!-- Simulated Barcode Header -->
        <div class="flex items-center justify-center gap-1 my-3 opacity-80 h-7 bg-slate-900 rounded p-1">
            <div class="h-full w-1 bg-white"></div>
            <div class="h-full w-2 bg-white"></div>
            <div class="h-full w-1 bg-white"></div>
            <div class="h-full w-3 bg-white"></div>
            <div class="h-full w-1 bg-white"></div>
            <div class="h-full w-2 bg-white"></div>
            <div class="h-full w-4 bg-white"></div>
            <div class="h-full w-1 bg-white"></div>
            <div class="h-full w-2 bg-white"></div>
            <div class="h-full w-1 bg-white"></div>
        </div>

        <div class="border-t-2 border-dashed border-slate-300 my-3"></div>

        <!-- Details Table -->
        <table class="w-full text-xs space-y-1">
            <tr>
                <td class="py-1 text-slate-500">NO. TIKET</td>
                <td class="text-right font-bold text-slate-900">#<?= str_pad($transaksi['id_parkir'], 6, '0', STR_PAD_LEFT) ?></td>
            </tr>
            <tr>
                <td class="py-1 text-slate-500">PLAT NOMOR</td>
                <td class="text-right font-extrabold text-sm text-slate-900 uppercase tracking-wider"><?= htmlspecialchars($transaksi['plat_nomor']) ?></td>
            </tr>
            <tr>
                <td class="py-1 text-slate-500">JENIS KENDARAAN</td>
                <td class="text-right font-semibold capitalize text-slate-800"><?= htmlspecialchars($transaksi['jenis_kendaraan']) ?></td>
            </tr>
            <tr>
                <td class="py-1 text-slate-500">AREA LOKASI</td>
                <td class="text-right font-semibold text-slate-800"><?= htmlspecialchars($transaksi['nama_area']) ?></td>
            </tr>
        </table>

        <div class="border-t border-dashed border-slate-300 my-3"></div>

        <table class="w-full text-xs">
            <tr>
                <td class="py-1 text-slate-500">WAKTU MASUK</td>
                <td class="text-right text-slate-800"><?= date('d/m/Y H:i', strtotime($transaksi['waktu_masuk'])) ?></td>
            </tr>
            <tr>
                <td class="py-1 text-slate-500">WAKTU KELUAR</td>
                <td class="text-right text-slate-800"><?= date('d/m/Y H:i', strtotime($transaksi['waktu_keluar'])) ?></td>
            </tr>
            <tr>
                <td class="py-1 text-slate-500">TOTAL DURASI</td>
                <td class="text-right font-bold text-slate-900"><?= $transaksi['durasi_jam'] ?> Jam</td>
            </tr>
        </table>

        <div class="border-t-2 border-dashed border-slate-300 my-3"></div>

        <!-- Total Payment -->
        <div class="flex items-center justify-between py-1">
            <span class="font-extrabold text-xs text-slate-900">TOTAL BAYAR</span>
            <span class="text-base font-extrabold text-slate-900">Rp <?= number_format($transaksi['biaya_total'], 0, ',', '.') ?></span>
        </div>

        <div class="border-t-2 border-dashed border-slate-300 my-3"></div>

        <!-- Footer Info -->
        <div class="text-center text-[10px] text-slate-500 space-y-1">
            <p>Petugas: <span class="font-semibold text-slate-800"><?= htmlspecialchars($transaksi['nama_petugas']) ?></span></p>
            <p class="pt-1 font-semibold text-slate-700">Terima kasih atas kunjungan Anda!</p>
            <p>Simpan struk ini sebagai bukti pembayaran sah.</p>
        </div>

    </div>

    <!-- Print Action Buttons (Hidden on Print) -->
    <div class="no-print flex items-center gap-3 mt-6">
        <button onclick="window.print()" class="bg-gradient-to-r from-brand-400 to-brand-500 hover:from-brand-300 hover:to-brand-400 text-slate-950 font-extrabold px-6 py-3.5 rounded-xl text-sm transition shadow-xl shadow-brand-500/30 flex items-center gap-2 border border-brand-400">
            <i class="fa-solid fa-print text-base"></i>
            <span>Cetak Struk Sekarang</span>
        </button>
        <a href="/parkeer/petugas/transaksi/index.php" class="bg-slate-900 hover:bg-slate-800 text-slate-200 font-bold px-5 py-3.5 rounded-xl text-sm border border-slate-700 transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

</body>
</html>