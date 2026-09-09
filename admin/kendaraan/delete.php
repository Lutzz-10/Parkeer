<?php
require_once __DIR__ . '/../../includes/auth.php';
cekRole(['admin']);
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/kendaraan_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';

$database = new Database();
$koneksi = $database->connect();
$id = (int) ($_GET['id'] ?? 0);

if (kendaraanSedangParkir($koneksi, $id)) {
    header("Location: index.php?gagal=Kendaraan tidak bisa dihapus karena sedang parkir");
    exit;
}

$kendaraan = getKendaraanById($koneksi, $id);
if ($kendaraan) {
    deleteKendaraan($koneksi, $id);
    catatLog($koneksi, $_SESSION['id_user'], "Menghapus kendaraan: {$kendaraan['plat_nomor']}");
}

header("Location: index.php?sukses=Kendaraan berhasil dihapus");
exit;