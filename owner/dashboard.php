<?php
$judulHalaman = "Dashboard Owner";
require_once __DIR__ . '/../includes/header_owner.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/rekap_functions.php';

$database = new Database();
$koneksi = $database->connect();

$hariIni = date('Y-m-d');
$ringkasanHariIni = getRingkasanRekap($koneksi, $hariIni, $hariIni);

$totalKendaraanParkir = $koneksi->query(
    "SELECT COUNT(*) FROM tb_transaksi WHERE status = 'masuk'"
)->fetchColumn();
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm text-slate-500">Kendaraan sedang parkir</p>
        <p class="text-3xl font-bold text-slate-800"><?= $totalKendaraanParkir ?></p>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm text-slate-500">Transaksi hari ini</p>
        <p class="text-3xl font-bold text-slate-800"><?= $ringkasanHariIni['total_transaksi'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm text-slate-500">Pendapatan hari ini</p>
        <p class="text-3xl font-bold text-green-600">Rp <?= number_format($ringkasanHariIni['total_pendapatan'], 0, ',', '.') ?></p>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <p class="text-slate-600 text-sm">Untuk melihat rekap dengan rentang waktu tertentu, buka menu <a href="rekap/index.php" class="text-blue-600 hover:underline">Rekap Transaksi</a>.</p>
</div>

<?php require_once __DIR__ . '/../includes/footer_owner.php'; ?>