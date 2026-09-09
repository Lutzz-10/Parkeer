<?php
require_once __DIR__ . '/../../includes/auth.php';
cekRole(['admin']);
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/user_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';

$database = new Database();
$koneksi = $database->connect();

$id = (int) ($_GET['id'] ?? 0);

// Cegah admin menghapus akunnya sendiri saat sedang login
if ($id === (int) $_SESSION['id_user']) {
    header("Location: index.php?sukses=Tidak bisa menghapus akun sendiri");
    exit;
}

$user = getUserById($koneksi, $id);
if ($user) {
    deleteUser($koneksi, $id);
    catatLog($koneksi, $_SESSION['id_user'], "Menghapus user: {$user['username']}");
}

header("Location: index.php?sukses=User berhasil dihapus");
exit;