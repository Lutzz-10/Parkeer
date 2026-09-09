<?php
// functions/transaksi_functions.php

/**
 * Ambil semua kendaraan yang sedang berada di dalam area (status masih 'masuk')
 */
function getKendaraanSedangParkir(PDO $koneksi, string $keyword = ''): array
{
    $sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, k.pemilik, a.nama_area
            FROM tb_transaksi t
            JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
            JOIN tb_area_parkir a ON t.id_area = a.id_area
            WHERE t.status = 'masuk' AND k.plat_nomor LIKE :keyword
            ORDER BY t.waktu_masuk DESC";
    $stmt = $koneksi->prepare($sql);
    $stmt->execute([':keyword' => "%$keyword%"]);
    return $stmt->fetchAll();
}

/**
 * Cari kendaraan berdasarkan plat nomor persis (dipakai saat catat masuk)
 */
function cariKendaraanByPlat(PDO $koneksi, string $plat): array|false
{
    $stmt = $koneksi->prepare("SELECT * FROM tb_kendaraan WHERE plat_nomor = :plat");
    $stmt->execute([':plat' => strtoupper($plat)]);
    return $stmt->fetch();
}

/**
 * Cek apakah kendaraan tsb sedang punya transaksi aktif (status='masuk')
 */
function kendaraanMasihDidalam(PDO $koneksi, int $idKendaraan): bool
{
    $stmt = $koneksi->prepare("SELECT id_parkir FROM tb_transaksi WHERE id_kendaraan = :id AND status = 'masuk' LIMIT 1");
    $stmt->execute([':id' => $idKendaraan]);
    return $stmt->fetch() !== false;
}

/**
 * Ambil tarif berdasarkan jenis kendaraan
 */
function getTarifByJenis(PDO $koneksi, string $jenis): array|false
{
    $stmt = $koneksi->prepare("SELECT * FROM tb_tarif WHERE jenis_kendaraan = :jenis");
    $stmt->execute([':jenis' => $jenis]);
    return $stmt->fetch();
}

/**
 * Proses kendaraan masuk: validasi slot, simpan transaksi, tambah slot terisi
 * Dibungkus dalam TRANSACTION supaya insert transaksi & update slot area konsisten (atomic)
 */
function prosesKendaraanMasuk(PDO $koneksi, int $idKendaraan, int $idArea, int $idTarif, int $idUser): array
{
    // Cek area masih ada slot kosong
    $stmt = $koneksi->prepare("SELECT kapasitas, terisi FROM tb_area_parkir WHERE id_area = :id");
    $stmt->execute([':id' => $idArea]);
    $area = $stmt->fetch();

    if (!$area || ($area['kapasitas'] - $area['terisi']) <= 0) {
        return ['sukses' => false, 'pesan' => 'Area parkir sudah penuh.'];
    }

    if (kendaraanMasihDidalam($koneksi, $idKendaraan)) {
        return ['sukses' => false, 'pesan' => 'Kendaraan ini sudah tercatat masuk dan belum keluar.'];
    }

    try {
        $koneksi->beginTransaction();

        $stmt = $koneksi->prepare(
            "INSERT INTO tb_transaksi (id_kendaraan, waktu_masuk, id_tarif, status, id_user, id_area)
             VALUES (:id_kendaraan, NOW(), :id_tarif, 'masuk', :id_user, :id_area)"
        );
        $stmt->execute([
            ':id_kendaraan' => $idKendaraan,
            ':id_tarif' => $idTarif,
            ':id_user' => $idUser,
            ':id_area' => $idArea,
        ]);
        $idParkir = $koneksi->lastInsertId();

        $stmt = $koneksi->prepare("UPDATE tb_area_parkir SET terisi = terisi + 1 WHERE id_area = :id");
        $stmt->execute([':id' => $idArea]);

        $koneksi->commit();
        return ['sukses' => true, 'id_parkir' => $idParkir];
    } catch (Exception $e) {
        $koneksi->rollBack();
        return ['sukses' => false, 'pesan' => 'Gagal menyimpan transaksi: ' . $e->getMessage()];
    }
}

/**
 * Proses kendaraan keluar: hitung durasi & biaya, update transaksi, kurangi slot terisi
 */
function prosesKendaraanKeluar(PDO $koneksi, int $idParkir): array
{
    $stmt = $koneksi->prepare(
        "SELECT t.*, tf.tarif_per_jam FROM tb_transaksi t
         JOIN tb_tarif tf ON t.id_tarif = tf.id_tarif
         WHERE t.id_parkir = :id"
    );
    $stmt->execute([':id' => $idParkir]);
    $transaksi = $stmt->fetch();

    if (!$transaksi || $transaksi['status'] !== 'masuk') {
        return ['sukses' => false, 'pesan' => 'Transaksi tidak ditemukan atau sudah selesai.'];
    }

    $waktuMasuk = new DateTime($transaksi['waktu_masuk']);
    $waktuKeluar = new DateTime();
    $selisihDetik = $waktuKeluar->getTimestamp() - $waktuMasuk->getTimestamp();

    // Pembulatan ke atas per jam, minimal 1 jam
    $durasiJam = (int) ceil($selisihDetik / 3600);
    if ($durasiJam < 1) $durasiJam = 1;

    $biayaTotal = $durasiJam * $transaksi['tarif_per_jam'];

    try {
        $koneksi->beginTransaction();

        $stmt = $koneksi->prepare(
            "UPDATE tb_transaksi SET waktu_keluar = NOW(), durasi_jam = :durasi, biaya_total = :biaya, status = 'keluar'
             WHERE id_parkir = :id"
        );
        $stmt->execute([
            ':durasi' => $durasiJam,
            ':biaya' => $biayaTotal,
            ':id' => $idParkir,
        ]);

        $stmt = $koneksi->prepare("UPDATE tb_area_parkir SET terisi = terisi - 1 WHERE id_area = :id");
        $stmt->execute([':id' => $transaksi['id_area']]);

        $koneksi->commit();
        return ['sukses' => true, 'durasi_jam' => $durasiJam, 'biaya_total' => $biayaTotal];
    } catch (Exception $e) {
        $koneksi->rollBack();
        return ['sukses' => false, 'pesan' => 'Gagal memproses keluar: ' . $e->getMessage()];
    }
}

/**
 * Ambil detail 1 transaksi lengkap (dipakai untuk struk)
 */
function getDetailTransaksi(PDO $koneksi, int $idParkir): array|false
{
    $stmt = $koneksi->prepare(
        "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, k.pemilik, a.nama_area, u.nama_lengkap AS nama_petugas
         FROM tb_transaksi t
         JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
         JOIN tb_area_parkir a ON t.id_area = a.id_area
         JOIN tb_user u ON t.id_user = u.id_user
         WHERE t.id_parkir = :id"
    );
    $stmt->execute([':id' => $idParkir]);
    return $stmt->fetch();
}

/**
 * Riwayat transaksi selesai, dengan filter rentang waktu (dipakai juga di Tahap 8 Owner)
 */
function getRiwayatTransaksi(PDO $koneksi, ?string $dariTanggal = null, ?string $sampaiTanggal = null, int $limit = 50): array
{
    $sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, a.nama_area, u.nama_lengkap AS nama_petugas
            FROM tb_transaksi t
            JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
            JOIN tb_area_parkir a ON t.id_area = a.id_area
            JOIN tb_user u ON t.id_user = u.id_user
            WHERE t.status = 'keluar'";
    $params = [];

    if ($dariTanggal) {
        $sql .= " AND t.waktu_masuk >= :dari";
        $params[':dari'] = $dariTanggal . " 00:00:00";
    }
    if ($sampaiTanggal) {
        $sql .= " AND t.waktu_masuk <= :sampai";
        $params[':sampai'] = $sampaiTanggal . " 23:59:59";
    }

    $sql .= " ORDER BY t.waktu_keluar DESC LIMIT :limit";
    $stmt = $koneksi->prepare($sql);
    foreach ($params as $key => $val) $stmt->bindValue($key, $val);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}