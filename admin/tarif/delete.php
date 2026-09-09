<?php
require_once __DIR__ . '/../../includes/auth.php';
cekRole(['admin']);
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/tarif_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';

$database = new Database();
$koneksi = $database->connect();
$id = (int) ($_GET['id'] ?? 0);

if (tarifSedangDipakai($koneksi, $id)) {
    header("Location: index.php?gagal=Tarif tidak bisa dihapus karena sudah dipakai transaksi");
    exit;
}

$tarif = getTarifById($koneksi, $id);
if ($tarif) {
    deleteTarif($koneksi, $id);
    catatLog($koneksi, $_SESSION['id_user'], "Menghapus tarif: {$tarif['jenis_kendaraan']}");
}

header("Location: index.php?sukses=Tarif berhasil dihapus");
exit;