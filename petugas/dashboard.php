<?php
$judulHalaman = "Dashboard Petugas";
require_once __DIR__ . '/../includes/header_petugas.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/transaksi_functions.php';

$database = new Database();
$koneksi = $database->connect();
$sedangParkir = getKendaraanSedangParkir($koneksi);
?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm text-slate-500">Kendaraan sedang parkir</p>
        <p class="text-3xl font-bold text-slate-800"><?= count($sedangParkir) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow p-6 flex items-center justify-center">
        <a href="transaksi/masuk.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg font-medium">
            + Catat Kendaraan Masuk
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_petugas.php'; ?>