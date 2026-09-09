<?php
require_once __DIR__ . '/../../includes/auth.php';
cekRole(['admin']);
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/area_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';

$database = new Database();
$koneksi = $database->connect();
$id = (int) ($_GET['id'] ?? 0);

if (areaSedangDipakai($koneksi, $id)) {
    header("Location: index.php?gagal=Area tidak bisa dihapus karena sudah dipakai transaksi");
    exit;
}

$area = getAreaById($koneksi, $id);
if ($area) {
    deleteArea($koneksi, $id);
    catatLog($koneksi, $_SESSION['id_user'], "Menghapus area: {$area['nama_area']}");
}

header("Location: index.php?sukses=Area berhasil dihapus");
exit;