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

// Bersihkan output buffer dari HTML / deprecation notice agar file CSV murni
if (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=rekap_parkeer_' . $dari . '_sampai_' . $sampai . '.csv');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// Output UTF-8 BOM agar Excel otomatis membaca karakter & kolom secara rapi
fputs($output, "\xEF\xBB\xBF");

// Header kolom CSV (Gunakan delimiter titik koma ';' & escape '\\' agar kompatibel penuh dengan Excel & PHP 8.4)
fputcsv($output, ['Plat Nomor', 'Jenis Kendaraan', 'Area Parkir', 'Petugas', 'Waktu Masuk', 'Waktu Keluar', 'Durasi (Jam)', 'Biaya Total (Rp)'], ';', '"', '\\');

foreach ($detail as $d) {
    fputcsv($output, [
        $d['plat_nomor'],
        ucfirst($d['jenis_kendaraan']),
        $d['nama_area'],
        $d['nama_petugas'],
        date('d/m/Y H:i:s', strtotime($d['waktu_masuk'])),
        date('d/m/Y H:i:s', strtotime($d['waktu_keluar'])),
        $d['durasi_jam'],
        $d['biaya_total'],
    ], ';', '"', '\\');
}

fclose($output);
exit;