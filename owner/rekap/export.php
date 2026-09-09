<?php
require_once __DIR__ . '/../../includes/auth.php';
cekRole(['owner']);
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/rekap_functions.php';

$database = new Database();
$koneksi = $database->connect();

$dari = $_GET['dari'] ?? date('Y-m-d', strtotime('-6 days'));
$sampai = $_GET['sampai'] ?? date('Y-m-d');

$detail = getDetailTransaksiRekap($koneksi, $dari, $sampai, 1000);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=rekap_parkeer_' . $dari . '_sampai_' . $sampai . '.csv');

$output = fopen('php://output', 'w');

// Header kolom CSV
fputcsv($output, ['Plat Nomor', 'Jenis', 'Area', 'Petugas', 'Waktu Masuk', 'Waktu Keluar', 'Durasi (jam)', 'Biaya (Rp)']);

foreach ($detail as $d) {
    fputcsv($output, [
        $d['plat_nomor'],
        $d['jenis_kendaraan'],
        $d['nama_area'],
        $d['nama_petugas'],
        $d['waktu_masuk'],
        $d['waktu_keluar'],
        $d['durasi_jam'],
        $d['biaya_total'],
    ]);
}

fclose($output);
exit;