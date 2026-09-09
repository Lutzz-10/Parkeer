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
    <title>Struk Parkir - <?= htmlspecialchars($transaksi['plat_nomor']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Saat di-print: sembunyikan tombol, hilangkan margin halaman, atur lebar seperti struk kasir */
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .struk-box { box-shadow: none; border: none; width: 100%; }
            @page { margin: 0; size: 80mm auto; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center py-8">

    <div class="struk-box bg-white shadow-lg rounded-lg p-5 w-[320px] font-mono text-sm">

        <div class="text-center mb-3">
            <p class="font-bold text-base">APLIKASI PARKEER</p>
            <p class="text-xs text-slate-500">Struk Bukti Pembayaran Parkir</p>
        </div>

        <div class="border-t border-dashed border-slate-400 my-2"></div>

        <table class="w-full text-xs">
            <tr><td class="py-0.5">No. Transaksi</td><td class="text-right">#<?= str_pad($transaksi['id_parkir'], 6, '0', STR_PAD_LEFT) ?></td></tr>
            <tr><td class="py-0.5">Plat Nomor</td><td class="text-right font-bold"><?= htmlspecialchars($transaksi['plat_nomor']) ?></td></tr>
            <tr><td class="py-0.5">Jenis</td><td class="text-right capitalize"><?= htmlspecialchars($transaksi['jenis_kendaraan']) ?></td></tr>
            <tr><td class="py-0.5">Area</td><td class="text-right"><?= htmlspecialchars($transaksi['nama_area']) ?></td></tr>
        </table>

        <div class="border-t border-dashed border-slate-400 my-2"></div>

        <table class="w-full text-xs">
            <tr><td class="py-0.5">Masuk</td><td class="text-right"><?= date('d/m/Y H:i', strtotime($transaksi['waktu_masuk'])) ?></td></tr>
            <tr><td class="py-0.5">Keluar</td><td class="text-right"><?= date('d/m/Y H:i', strtotime($transaksi['waktu_keluar'])) ?></td></tr>
            <tr><td class="py-0.5">Durasi</td><td class="text-right"><?= $transaksi['durasi_jam'] ?> jam</td></tr>
        </table>

        <div class="border-t border-dashed border-slate-400 my-2"></div>

        <table class="w-full text-sm">
            <tr>
                <td class="py-1 font-bold">TOTAL BAYAR</td>
                <td class="text-right font-bold text-base">Rp <?= number_format($transaksi['biaya_total'], 0, ',', '.') ?></td>
            </tr>
        </table>

        <div class="border-t border-dashed border-slate-400 my-2"></div>

        <div class="text-center text-xs text-slate-500 mt-3">
            <p>Petugas: <?= htmlspecialchars($transaksi['nama_petugas']) ?></p>
            <p class="mt-2">Terima kasih telah parkir di sini</p>
            <p>Simpan struk ini sebagai bukti</p>
        </div>
    </div>

    <div class="no-print flex gap-2 mt-6">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm">
            🖨️ Cetak Struk
        </button>
        <a href="/parkeer/petugas/transaksi/index.php" class="bg-slate-200 hover:bg-slate-300 px-5 py-2 rounded-lg text-sm">
            Kembali
        </a>
    </div>

</body>
</html>