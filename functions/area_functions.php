<?php
// functions/area_functions.php

function getAllArea(PDO $koneksi): array
{
    $stmt = $koneksi->query("SELECT * FROM tb_area_parkir ORDER BY nama_area ASC");
    return $stmt->fetchAll();
}

function getAreaById(PDO $koneksi, int $id): array|false
{
    $stmt = $koneksi->prepare("SELECT * FROM tb_area_parkir WHERE id_area = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

function createArea(PDO $koneksi, array $data): bool
{
    $stmt = $koneksi->prepare(
        "INSERT INTO tb_area_parkir (nama_area, kapasitas, terisi) VALUES (:nama, :kapasitas, 0)"
    );
    return $stmt->execute([
        ':nama' => $data['nama_area'],
        ':kapasitas' => $data['kapasitas'],
    ]);
}

function updateArea(PDO $koneksi, int $id, array $data): bool
{
    $stmt = $koneksi->prepare(
        "UPDATE tb_area_parkir SET nama_area = :nama, kapasitas = :kapasitas WHERE id_area = :id"
    );
    return $stmt->execute([
        ':nama' => $data['nama_area'],
        ':kapasitas' => $data['kapasitas'],
        ':id' => $id,
    ]);
}

function deleteArea(PDO $koneksi, int $id): bool
{
    $stmt = $koneksi->prepare("DELETE FROM tb_area_parkir WHERE id_area = :id");
    return $stmt->execute([':id' => $id]);
}

function areaSedangDipakai(PDO $koneksi, int $id): bool
{
    $stmt = $koneksi->prepare("SELECT id_parkir FROM tb_transaksi WHERE id_area = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() !== false;
}

/**
 * Menambah/mengurangi jumlah slot terisi di sebuah area.
 * $delta: +1 saat kendaraan masuk, -1 saat kendaraan keluar
 */
function ubahSlotTerisi(PDO $koneksi, int $idArea, int $delta): bool
{
    $stmt = $koneksi->prepare(
        "UPDATE tb_area_parkir SET terisi = terisi + :delta WHERE id_area = :id"
    );
    return $stmt->execute([':delta' => $delta, ':id' => $idArea]);
}