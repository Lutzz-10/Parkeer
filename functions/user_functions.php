<?php
// functions/user_functions.php

function getAllUsers(PDO $koneksi, string $keyword = '', int $limit = 10, int $offset = 0): array
{
    $sql = "SELECT * FROM tb_user WHERE nama_lengkap LIKE :keyword OR username LIKE :keyword
            ORDER BY id_user DESC LIMIT :limit OFFSET :offset";
    $stmt = $koneksi->prepare($sql);
    $stmt->bindValue(':keyword', "%$keyword%");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function countAllUsers(PDO $koneksi, string $keyword = ''): int
{
    $stmt = $koneksi->prepare(
        "SELECT COUNT(*) AS total FROM tb_user WHERE nama_lengkap LIKE :keyword OR username LIKE :keyword"
    );
    $stmt->execute([':keyword' => "%$keyword%"]);
    return (int) $stmt->fetch()['total'];
}

function getUserById(PDO $koneksi, int $id): array|false
{
    $stmt = $koneksi->prepare("SELECT * FROM tb_user WHERE id_user = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

function usernameSudahAda(PDO $koneksi, string $username, ?int $kecualiId = null): bool
{
    $sql = "SELECT id_user FROM tb_user WHERE username = :username";
    if ($kecualiId) $sql .= " AND id_user != :id";

    $stmt = $koneksi->prepare($sql);
    $stmt->bindValue(':username', $username);
    if ($kecualiId) $stmt->bindValue(':id', $kecualiId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch() !== false;
}

function createUser(PDO $koneksi, array $data): bool
{
    $stmt = $koneksi->prepare(
        "INSERT INTO tb_user (nama_lengkap, username, password, role, status_aktif)
         VALUES (:nama_lengkap, :username, :password, :role, :status_aktif)"
    );
    return $stmt->execute([
        ':nama_lengkap' => $data['nama_lengkap'],
        ':username' => $data['username'],
        ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
        ':role' => $data['role'],
        ':status_aktif' => $data['status_aktif'],
    ]);
}

function updateUser(PDO $koneksi, int $id, array $data): bool
{
    // Jika password dikosongkan saat edit, password lama tidak diubah
    if (!empty($data['password'])) {
        $stmt = $koneksi->prepare(
            "UPDATE tb_user SET nama_lengkap = :nama_lengkap, username = :username,
             password = :password, role = :role, status_aktif = :status_aktif WHERE id_user = :id"
        );
        $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
    } else {
        $stmt = $koneksi->prepare(
            "UPDATE tb_user SET nama_lengkap = :nama_lengkap, username = :username,
             role = :role, status_aktif = :status_aktif WHERE id_user = :id"
        );
    }

    $stmt->bindValue(':nama_lengkap', $data['nama_lengkap']);
    $stmt->bindValue(':username', $data['username']);
    $stmt->bindValue(':role', $data['role']);
    $stmt->bindValue(':status_aktif', $data['status_aktif']);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    return $stmt->execute();
}

function deleteUser(PDO $koneksi, int $id): bool
{
    // Perhatian: FK CASCADE -> kendaraan & log milik user ini ikut terhapus
    $stmt = $koneksi->prepare("DELETE FROM tb_user WHERE id_user = :id");
    return $stmt->execute([':id' => $id]);
}