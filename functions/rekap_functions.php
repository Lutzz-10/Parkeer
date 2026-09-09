<?php
// functions/rekap_functions.php

/**
 * Ringkasan total transaksi & pendapatan dalam rentang waktu.
 * Dihitung langsung oleh MySQL (SUM, COUNT) - tidak ambil data mentah lalu hitung di PHP.
 */
function getRingkasanRekap(PDO $koneksi, string $dari, string $sampai): array
{
    $stmt = $koneksi->prepare(
        "SELECT
            COUNT(*) AS total_transaksi,
            COALESCE(SUM(biaya_total), 0) AS total_pendapatan,
            COALESCE(AVG(biaya_total), 0) AS rata_rata,
            COALESCE(AVG(durasi_jam), 0) AS rata_rata_durasi
         FROM tb_transaksi
         WHERE status = 'keluar' AND waktu_keluar BETWEEN :dari AND :sampai"
    );
    $stmt->execute([':dari' => "$dari 00:00:00", ':sampai' => "$sampai 23:59:59"]);
    return $stmt->fetch();
}

/**
 * Breakdown pendapatan & jumlah transaksi per jenis kendaraan
 */
function getRekapPerJenis(PDO $koneksi, string $dari, string $sampai): array
{
    $sql = "SELECT k.jenis_kendaraan,
                   COUNT(*) AS jumlah_transaksi,
                   SUM(t.biaya_total) AS total_pendapatan
            FROM tb_transaksi t
            JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
            WHERE t.status = 'keluar' AND t.waktu_keluar BETWEEN :dari AND :sampai
            GROUP BY k.jenis_kendaraan
            ORDER BY total_pendapatan DESC";
    $stmt = $koneksi->prepare($sql);
    $stmt->execute([':dari' => "$dari 00:00:00", ':sampai' => "$sampai 23:59:59"]);
    return $stmt->fetchAll();
}

/**
 * Tren pendapatan per hari, untuk digambar sebagai grafik garis/batang
 */
function getRekapPerHari(PDO $koneksi, string $dari, string $sampai): array
{
    $sql = "SELECT DATE(waktu_keluar) AS tanggal,
                   COUNT(*) AS jumlah_transaksi,
                   SUM(biaya_total) AS total_pendapatan
            FROM tb_transaksi
            WHERE status = 'keluar' AND waktu_keluar BETWEEN :dari AND :sampai
            GROUP BY DATE(waktu_keluar)
            ORDER BY tanggal ASC";
    $stmt = $koneksi->prepare($sql);
    $stmt->execute([':dari' => "$dari 00:00:00", ':sampai' => "$sampai 23:59:59"]);
    return $stmt->fetchAll();
}

/**
 * Daftar detail transaksi dalam rentang waktu, dengan LIMIT agar tidak berat
 * jika data besar (sesuai poin PDF: "gunakan limit ketika menggunakan data besar")
 */
function getDetailTransaksiRekap(PDO $koneksi, string $dari, string $sampai, int $limit = 100): array
{
    $sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, a.nama_area, u.nama_lengkap AS nama_petugas
            FROM tb_transaksi t
            JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
            JOIN tb_area_parkir a ON t.id_area = a.id_area
            JOIN tb_user u ON t.id_user = u.id_user
            WHERE t.status = 'keluar' AND t.waktu_keluar BETWEEN :dari AND :sampai
            ORDER BY t.waktu_keluar DESC
            LIMIT :limit";
    $stmt = $koneksi->prepare($sql);
    $stmt->bindValue(':dari', "$dari 00:00:00");
    $stmt->bindValue(':sampai', "$sampai 23:59:59");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}