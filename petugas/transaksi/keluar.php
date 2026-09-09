<?php
$judulHalaman = "Proses Kendaraan Keluar";
require_once __DIR__ . '/../../includes/header_petugas.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/transaksi_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';

$database = new Database();
$koneksi = $database->connect();

$idParkir = (int) ($_GET['id'] ?? 0);
$transaksi = getDetailTransaksi($koneksi, $idParkir);

if (!$transaksi || $transaksi['status'] !== 'masuk') {
    echo '<div class="bg-red-100 text-red-700 p-4 rounded-lg">Transaksi tidak ditemukan atau sudah selesai.</div>';
    require_once __DIR__ . '/../../includes/footer_petugas.php';
    exit;
}

// Jika dikonfirmasi, baru diproses (hindari proses tidak sengaja lewat refresh/link)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hasil = prosesKendaraanKeluar($koneksi, $idParkir);
    if ($hasil['sukses']) {
        catatLog($koneksi, $_SESSION['id_user'], "Memproses kendaraan keluar (id_parkir: $idParkir)");
        header("Location: /parkeer/petugas/struk/cetak.php?id=$idParkir");
        exit;
    } else {
        echo '<div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">' . htmlspecialchars($hasil['pesan']) . '</div>';
    }
}

$estimasiDetik = time() - strtotime($transaksi['waktu_masuk']);
$estimasiJam = max(1, (int) ceil($estimasiDetik / 3600));
$tarifStmt = $koneksi->prepare("SELECT tarif_per_jam FROM tb_tarif WHERE id_tarif = :id");
$tarifStmt->execute([':id' => $transaksi['id_tarif']]);
$tarifPerJam = $tarifStmt->fetchColumn();
$estimasiBiaya = $estimasiJam * $tarifPerJam;
?>

<div class="bg-white rounded-xl shadow p-6 max-w-md">
    <div class="space-y-2 text-sm mb-6">
        <p><b>Plat Nomor:</b> <?= htmlspecialchars($transaksi['plat_nomor']) ?></p>
        <p><b>Jenis:</b> <?= htmlspecialchars($transaksi['jenis_kendaraan']) ?></p>
        <p><b>Area:</b> <?= htmlspecialchars($transaksi['nama_area']) ?></p>
        <p><b>Waktu Masuk:</b> <?= date('d M Y H:i', strtotime($transaksi['waktu_masuk'])) ?></p>
        <hr class="my-2">
        <p><b>Estimasi Durasi:</b> <?= $estimasiJam ?> jam</p>
        <p><b>Estimasi Biaya:</b> Rp <?= number_format($estimasiBiaya, 0, ',', '.') ?></p>
        <p class="text-xs text-slate-400">*Biaya final dihitung ulang saat tombol "Proses Keluar" ditekan.</p>
    </div>

    <form method="POST">
        <button class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg w-full">
            Konfirmasi &amp; Proses Keluar
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer_petugas.php'; ?>