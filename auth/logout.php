<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['id_user'])) {
    // Catat log sebelum session dihancurkan
    $database = new Database();
    $koneksi = $database->connect();

    $stmt = $koneksi->prepare(
        "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) VALUES (:id_user, :aktivitas, NOW())"
    );
    $stmt->execute([
        ':id_user' => $_SESSION['id_user'],
        ':aktivitas' => 'Logout dari sistem'
    ]);
}

// Hapus semua data session & hancurkan session
$_SESSION = [];
session_destroy();

header("Location: /parkeer/auth/login.php");
exit;