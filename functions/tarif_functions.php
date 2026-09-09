<?php
// functions/tarif_functions.php

function getAllTarif(PDO $koneksi): array
{
    $stmt = $koneksi->query("SELECT * FROM tb_tarif ORDER BY jenis_kendaraan ASC");
    return $stmt->fetchAll();
}

function getTarifById(PDO $koneksi, int $id): array|false
{
    $stmt = $koneksi->prepare("SELECT * FROM tb_tarif WHERE id_tarif = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

function jenisKendaraanSudahAdaTarif(PDO $koneksi, string $jenis, ?int $kecualiId = null): bool
{
    $sql = "SELECT id_tarif FROM tb_tarif WHERE jenis_kendaraan = :jenis";
    if ($kecualiId) $sql .= " AND id_tarif != :id";

    $stmt = $koneksi->prepare($sql);
    $stmt->bindValue(':jenis', $jenis);
    if ($kecualiId) $stmt->bindValue(':id', $kecualiId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch() !== false;
}

function createTarif(PDO $koneksi, array $data): bool
{
    $stmt = $koneksi->prepare(
        "INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam) VALUES (:jenis, :tarif)"
    );
    return $stmt->execute([
        ':jenis' => $data['jenis_kendaraan'],
        ':tarif' => $data['tarif_per_jam'],
    ]);
}

function updateTarif(PDO $koneksi, int $id, array $data): bool
{
    $stmt = $koneksi->prepare(
        "UPDATE tb_tarif SET jenis_kendaraan = :jenis, tarif_per_jam = :tarif WHERE id_tarif = :id"
    );
    return $stmt->execute([
        ':jenis' => $data['jenis_kendaraan'],
        ':tarif' => $data['tarif_per_jam'],
        ':id' => $id,
    ]);
}

function deleteTarif(PDO $koneksi, int $id): bool
{
    $stmt = $koneksi->prepare("DELETE FROM tb_tarif WHERE id_tarif = :id");
    return $stmt->execute([':id' => $id]);
}

/**
 * Cek apakah tarif ini sedang dipakai transaksi (FK RESTRICT -> tidak boleh dihapus kalau iya)
 */
function tarifSedangDipakai(PDO $koneksi, int $id): bool
{
    $stmt = $koneksi->prepare("SELECT id_parkir FROM tb_transaksi WHERE id_tarif = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() !== false;
}