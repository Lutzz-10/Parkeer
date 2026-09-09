<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/transaksi_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';
require_once __DIR__ . '/../../includes/auth.php';
cekRole(['petugas']);

$database = new Database();
$koneksi = $database->connect();

$idParkir = (int) ($_GET['id'] ?? 0);
$transaksi = getDetailTransaksi($koneksi, $idParkir);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hasil = prosesKendaraanKeluar($koneksi, $idParkir);
    if ($hasil['sukses']) {
        catatLog($koneksi, $_SESSION['id_user'], "Memproses kendaraan keluar (id_parkir: $idParkir)");
        header("Location: /parkeer/petugas/struk/cetak.php?id=$idParkir");
        exit;
    } else {
        $error = $hasil['pesan'];
    }
}

$judulHalaman = "Proses Pembayaran Keluar";
require_once __DIR__ . '/../../includes/header_petugas.php';

if (!$transaksi || $transaksi['status'] !== 'masuk') {
    echo '<div class="bg-red-950/80 text-red-300 p-4 rounded-2xl border border-red-800/80">Transaksi tidak ditemukan atau sudah selesai.</div>';
    require_once __DIR__ . '/../../includes/footer_petugas.php';
    exit;
}

if (isset($error)) {
    echo '<div class="bg-red-950/80 text-red-300 p-4 rounded-2xl border border-red-800/80 mb-4">' . htmlspecialchars($error) . '</div>';
}

$estimasiDetik = time() - strtotime($transaksi['waktu_masuk']);
$estimasiJam = max(1, (int) ceil($estimasiDetik / 3600));
$tarifStmt = $koneksi->prepare("SELECT tarif_per_jam FROM tb_tarif WHERE id_tarif = :id");
$tarifStmt->execute([':id' => $transaksi['id_tarif']]);
$tarifPerJam = (int) $tarifStmt->fetchColumn();
$estimasiBiaya = $estimasiJam * $tarifPerJam;
?>

<div class="max-w-xl mx-auto">

    <div class="mb-6">
        <a href="index.php" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition">
            <i class="fa-solid fa-arrow-left"></i> Batal / Kembali ke kendaraan parkir
        </a>
    </div>

    <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
        
        <!-- Header -->
        <div class="mb-6 pb-4 border-b border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-500/10 border border-orange-500/30 flex items-center justify-center text-orange-400 text-lg">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">Kasir Gate Keluar</h2>
                <p class="text-xs text-slate-400">Hitung durasi dan cetak bukti pembayaran parkir</p>
            </div>
        </div>

        <!-- Vehicle & Ticket Info Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 mb-6 space-y-3">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800/80">
                <span class="text-xs text-slate-400 uppercase font-semibold">Plat Nomor</span>
                <span class="px-3 py-1 rounded-lg bg-slate-950 border border-slate-700 font-mono font-bold text-white text-base uppercase">
                    <?= htmlspecialchars($transaksi['plat_nomor']) ?>
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <span class="text-slate-500 block">Jenis Kendaraan</span>
                    <span class="text-slate-200 font-bold capitalize"><?= htmlspecialchars($transaksi['jenis_kendaraan']) ?></span>
                </div>
                <div>
                    <span class="text-slate-500 block">Area Lokasi</span>
                    <span class="text-slate-200 font-bold"><?= htmlspecialchars($transaksi['nama_area']) ?></span>
                </div>
                <div>
                    <span class="text-slate-500 block">Waktu Masuk</span>
                    <span class="text-slate-200 font-mono"><?= date('d M Y H:i', strtotime($transaksi['waktu_masuk'])) ?></span>
                </div>
                <div>
                    <span class="text-slate-500 block">Skema Tarif</span>
                    <span class="text-slate-200 font-mono">Rp <?= number_format($tarifPerJam, 0, ',', '.') ?> / jam</span>
                </div>
            </div>
        </div>

        <!-- Fee Calculator Big Display -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900 border border-brand-500/30 rounded-2xl p-6 mb-6 text-center shadow-inner">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Estimasi Durasi: <?= $estimasiJam ?> Jam</span>
            <p class="text-4xl font-extrabold text-brand-400 tracking-tight mt-1">
                Rp <?= number_format($estimasiBiaya, 0, ',', '.') ?>
            </p>
            <p class="text-[11px] text-slate-500 mt-2">*Biaya akhir dihitung otomatis secara presisi saat diproses</p>
        </div>

        <form method="POST">
            <button type="submit"
                    class="w-full bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-slate-950 font-extrabold py-4 px-6 rounded-xl text-base transition duration-200 shadow-xl shadow-orange-500/25 flex items-center justify-center gap-2">
                <i class="fa-solid fa-print text-lg"></i>
                <span>Konfirmasi Pembayaran &amp; Cetak Struk</span>
            </button>
        </form>

    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer_petugas.php'; ?>