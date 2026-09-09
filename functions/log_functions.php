<?php
// functions/log_functions.php

/**
 * Mencatat aktivitas user ke tabel tb_log_aktivitas
 */
function catatLog(PDO $koneksi, int $idUser, string $aktivitas): void
{
    $stmt = $koneksi->prepare(
        "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) VALUES (:id_user, :aktivitas, NOW())"
    );
    $stmt->execute([
        ':id_user' => $idUser,
        ':aktivitas' => $aktivitas
    ]);
}