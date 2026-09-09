<?php
// functions/kendaraan_functions.php

function getAllKendaraan(PDO $koneksi, string $keyword = ''): array
{
    $sql = "SELECT k.*, u.nama_lengkap AS nama_pencatat
            FROM tb_kendaraan k
            JOIN tb_user u ON k.id_user = u.id_user
            WHERE k.plat_nomor LIKE :keyword OR k.pemilik LIKE :keyword
            ORDER BY k.id_kendaraan DESC";
    $stmt = $koneksi->prepare($sql);
    $stmt->execute([':keyword' => "%$keyword%"]);
    return $stmt->fetchAll();
}

function getKendaraanById(PDO $koneksi, int $id): array|false
{
    $stmt = $koneksi->prepare("SELECT * FROM tb_kendaraan WHERE id_kendaraan = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

function platNomorSudahAda(PDO $koneksi, string $plat, ?int $kecualiId = null): bool
{
    $sql = "SELECT id_kendaraan FROM tb_kendaraan WHERE plat_nomor = :plat";
    if ($kecualiId) $sql .= " AND id_kendaraan != :id";

    $stmt = $koneksi->prepare($sql);
    $stmt->bindValue(':plat', $plat);
    if ($kecualiId) $stmt->bindValue(':id', $kecualiId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch() !== false;
}

function createKendaraan(PDO $koneksi, array $data, int $idUserPencatat): bool
{
    $stmt = $koneksi->prepare(
        "INSERT INTO tb_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik, id_user)
         VALUES (:plat, :jenis, :warna, :pemilik, :id_user)"
    );
    return $stmt->execute([
        ':plat' => strtoupper($data['plat_nomor']),
        ':jenis' => $data['jenis_kendaraan'],
        ':warna' => $data['warna'],
        ':pemilik' => $data['pemilik'],
        ':id_user' => $idUserPencatat,
    ]);
}

function updateKendaraan(PDO $koneksi, int $id, array $data): bool
{
    $stmt = $koneksi->prepare(
        "UPDATE tb_kendaraan SET plat_nomor = :plat, jenis_kendaraan = :jenis, warna = :warna, pemilik = :pemilik
         WHERE id_kendaraan = :id"
    );
    return $stmt->execute([
        ':plat' => strtoupper($data['plat_nomor']),
        ':jenis' => $data['jenis_kendaraan'],
        ':warna' => $data['warna'],
        ':pemilik' => $data['pemilik'],
        ':id' => $id,
    ]);
}

function deleteKendaraan(PDO $koneksi, int $id): bool
{
    $stmt = $koneksi->prepare("DELETE FROM tb_kendaraan WHERE id_kendaraan = :id");
    return $stmt->execute([':id' => $id]);
}

function kendaraanSedangParkir(PDO $koneksi, int $id): bool
{
    $stmt = $koneksi->prepare(
        "SELECT id_parkir FROM tb_transaksi WHERE id_kendaraan = :id AND status = 'masuk' LIMIT 1"
    );
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() !== false;
}