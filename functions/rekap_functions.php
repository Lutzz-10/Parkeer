<?php
/**
 * Modul Analisis & Pelaporan Financial Rekap Transaksi (Role Executive Owner)
 * 
 * Menyediakan fungsi-fungsi agregasi data finansial (SUM, COUNT, AVG, GROUP BY)
 * langsung pada level database MySQL demi efisiensi memori & kecepatan loading.
 * 
 * @package Parkeer\Functions
 * @author Alwan Lutfi Maulida
 */

/**
 * Mengalkulasi ringkasan KPI (Key Performance Indicator) total transaksi,
 * total omzet pendapatan, rata-rata omzet per transaksi, dan rata-rata durasi.
 * 
 * @param PDO $koneksi Instance koneksi database PDO
 * @param string $dari Tanggal awal rentang (format YYYY-MM-DD)
 * @param string $sampai Tanggal akhir rentang (format YYYY-MM-DD)
 * @return array Array ringkasan finansial [total_transaksi, total_pendapatan, rata_rata, rata_rata_durasi]
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
 * Mengambil persentase breakdown pendapatan & volume transaksi berdasarkan jenis kendaraan.
 * 
 * @param PDO $koneksi Instance koneksi database PDO
 * @param string $dari Tanggal awal rentang
 * @param string $sampai Tanggal akhir rentang
 * @return array Array rekap per jenis kendaraan
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
 * Mengambil tren omzet harian yang disiapkan khusus untuk rendering grafik bar Chart.js.
 * 
 * @param PDO $koneksi Instance koneksi database PDO
 * @param string $dari Tanggal awal rentang
 * @param string $sampai Tanggal akhir rentang
 * @return array Array deret tanggal dan total pendapatan harian
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
 * Mengambil rincian log transaksi terperinci dengan pembatasan LIMIT demi performa optimal.
 * (Sesuai petunjuk Coding Guidelines: "Gunakan limit ketika menggunakan data besar").
 * 
 * @param PDO $koneksi Instance koneksi database PDO
 * @param string $dari Tanggal awal rentang
 * @param string $sampai Tanggal akhir rentang
 * @param int $limit Batas maksimal baris data (default 100)
 * @return array Array detail data transaksi
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