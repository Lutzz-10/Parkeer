<?php
/**
 * Modul Manajemen Transaksi Parkir (Check-In, Check-Out, Billing, & Struk)
 * 
 * Mengelola alur transaksi masuk/keluar kendaraan secara atomik menggunakan
 * Database Transactions (BEGIN TRANSACTION / COMMIT / ROLLBACK) untuk menjamin
 * konsistensi data slot terisi area parkir dan kalkulasi tarif parkir.
 * 
 * @package Parkeer\Functions
 * @author Alwan Lutfi Maulida
 */

/**
 * Mengambil daftar seluruh kendaraan yang sedang aktif parkir di dalam lokasi (status 'masuk').
 * 
 * @param PDO $koneksi Instance koneksi database PDO
 * @param string $keyword Keyword pencarian plat nomor (opsional)
 * @return array Array daftar transaksi kendaraan aktif
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
 * Mencari profil data kendaraan berdasarkan plat nomor persis.
 * 
 * @param PDO $koneksi Instance koneksi database PDO
 * @param string $plat String plat nomor kendaraan (misal: "B 1234 ABC")
 * @return array|false Record kendaraan atau false jika tidak ditemukan
 */
function cariKendaraanByPlat(PDO $koneksi, string $plat): array|false
{
    $stmt = $koneksi->prepare("SELECT * FROM tb_kendaraan WHERE plat_nomor = :plat");
    $stmt->execute([':plat' => strtoupper($plat)]);
    return $stmt->fetch();
}

/**
 * Memverifikasi apakah kendaraan tertentu sedang berada di dalam lokasi parkir.
 * 
 * @param PDO $koneksi Instance koneksi database PDO
 * @param int $idKendaraan ID unik kendaraan
 * @return bool True jika kendaraan sedang di dalam, false jika tidak
 */
function kendaraanMasihDidalam(PDO $koneksi, int $idKendaraan): bool
{
    $stmt = $koneksi->prepare("SELECT id_parkir FROM tb_transaksi WHERE id_kendaraan = :id AND status = 'masuk' LIMIT 1");
    $stmt->execute([':id' => $idKendaraan]);
    return $stmt->fetch() !== false;
}

/**
 * Mengambil data konfigurasi tarif parkir per jam berdasarkan jenis kendaraan.
 * 
 * @param PDO $koneksi Instance koneksi database PDO
 * @param string $jenis Jenis kendaraan ("motor", "mobil", "truk", dll)
 * @return array|false Record tarif parkir atau false
 */
function getTarifByJenis(PDO $koneksi, string $jenis): array|false
{
    $stmt = $koneksi->prepare("SELECT * FROM tb_tarif WHERE jenis_kendaraan = :jenis");
    $stmt->execute([':jenis' => $jenis]);
    return $stmt->fetch();
}

/**
 * Memproses pendaftaran kendaraan masuk (Check-In Gate Barrier).
 * 
 * Mengecek ketersediaan slot area secara atomik, menyimpan transaksi status 'masuk',
 * dan menambah hitungan slot `terisi` pada area parkir yang dipilih.
 * 
 * @param PDO $koneksi Instance koneksi database PDO
 * @param int $idKendaraan ID kendaraan yang masuk
 * @param int $idArea ID area parkir tujuan
 * @param int $idTarif ID tarif berlaku
 * @param int $idUser ID petugas operator gate
 * @return array Result status ['sukses' => bool, 'id_parkir' => int|null, 'pesan' => string|null]
 */
function prosesKendaraanMasuk(PDO $koneksi, int $idKendaraan, int $idArea, int $idTarif, int $idUser): array
{
    // 1. Verifikasi slot kosong pada area parkir tujuan
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
 * Memproses penyelesaian transaksi kendaraan keluar (Check-Out Kasir).
 * 
 * Mengkalkulasi durasi parkir (pembulatan ke atas per jam, minimal 1 jam),
 * mengalikan dengan tarif per jam berlaku, memperbarui status transaksi menjadi 'keluar',
 * dan mengosongkan 1 slot terisi pada area parkir terkait secara atomik.
 * 
 * @param PDO $koneksi Instance koneksi database PDO
 * @param int $idParkir ID unik transaksi parkir
 * @return array Result status ['sukses' => bool, 'durasi_jam' => int|null, 'biaya_total' => float|null, 'pesan' => string|null]
 */
function prosesKendaraanKeluar(PDO $koneksi, int $idParkir): array
{
    // 1. Ambil detail transaksi aktif beserta tarif per jam
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

    // 2. Kalkulasi selisih waktu masuk dan keluar (durasi per jam)
    $waktuMasuk = new DateTime($transaksi['waktu_masuk']);
    $waktuKeluar = new DateTime();
    $selisihDetik = $waktuKeluar->getTimestamp() - $waktuMasuk->getTimestamp();

    // Pembulatan ke atas per jam (misal 1 jam 5 menit dihitung 2 jam), minimal 1 jam
    $durasiJam = (int) ceil($selisihDetik / 3600);
    if ($durasiJam < 1) $durasiJam = 1;

    $biayaTotal = $durasiJam * $transaksi['tarif_per_jam'];

    try {
        $koneksi->beginTransaction();

        // 3. Update data transaksi keluar
        $stmt = $koneksi->prepare(
            "UPDATE tb_transaksi SET waktu_keluar = NOW(), durasi_jam = :durasi, biaya_total = :biaya, status = 'keluar'
             WHERE id_parkir = :id"
        );
        $stmt->execute([
            ':durasi' => $durasiJam,
            ':biaya' => $biayaTotal,
            ':id' => $idParkir,
        ]);

        // 4. Kurangi slot terisi di area parkir
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
 * Mengambil detail lengkap 1 record transaksi parkir (dipakai untuk cetak struk kasir).
 * 
 * @param PDO $koneksi Instance koneksi database PDO
 * @param int $idParkir ID unik transaksi parkir
 * @return array|false Data transaksi lengkap beserta plat, area, dan nama petugas
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
 * Mengambil riwayat transaksi parkir yang telah selesai (status 'keluar') dengan filter tanggal.
 * 
 * @param PDO $koneksi Instance koneksi database PDO
 * @param string|null $dariTanggal Filter awal tanggal (format YYYY-MM-DD)
 * @param string|null $sampaiTanggal Filter akhir tanggal (format YYYY-MM-DD)
 * @param int $limit Batas maksimal record yang diambil (default 50)
 * @return array Array daftar riwayat transaksi
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