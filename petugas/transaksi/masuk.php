<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/transaksi_functions.php';
require_once __DIR__ . '/../../functions/kendaraan_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';
require_once __DIR__ . '/../../includes/auth.php';
cekRole(['petugas']);

$database = new Database();
$koneksi = $database->connect();
$error = '';

// Ambil daftar area yang masih memiliki slot kosong
$daftarArea = $koneksi->query(
    "SELECT * FROM tb_area_parkir WHERE (kapasitas - terisi) > 0 ORDER BY nama_area"
)->fetchAll();

// Ambil tarif terkonfigurasi untuk daftar jenis kendaraan
$daftarTarif = $koneksi->query("SELECT * FROM tb_tarif ORDER BY jenis_kendaraan")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $platNomor = strtoupper(trim($_POST['plat_nomor'] ?? ''));
    $jenisKendaraan = $_POST['jenis_kendaraan'] ?? 'mobil';
    $idArea = (int) ($_POST['id_area'] ?? 0);
    $warna = trim($_POST['warna'] ?? '');
    $pemilik = trim($_POST['pemilik'] ?? '');

    if ($platNomor === '' || $idArea <= 0) {
        $error = 'Plat nomor dan area parkir wajib diisi.';
    } else {
        // Cek atau buat otomatis data kendaraan jika belum ada
        $kendaraan = cariKendaraanByPlat($koneksi, $platNomor);
        if (!$kendaraan) {
            createKendaraan($koneksi, [
                'plat_nomor' => $platNomor,
                'jenis_kendaraan' => $jenisKendaraan,
                'warna' => $warna,
                'pemilik' => $pemilik,
            ], $_SESSION['id_user']);
            $kendaraan = cariKendaraanByPlat($koneksi, $platNomor);
        }

        if ($kendaraan) {
            // Ambil tarif berdasarkan jenis kendaraan (prioritas dari database kendaraan, lalu dari form)
            $tarif = getTarifByJenis($koneksi, $kendaraan['jenis_kendaraan']);
            if (!$tarif) {
                $tarif = getTarifByJenis($koneksi, $jenisKendaraan);
            }

            if (!$tarif) {
                $error = "Tarif untuk jenis kendaraan '{$kendaraan['jenis_kendaraan']}' belum diatur di sistem. Silakan hubungi Admin.";
            } else {
                $hasil = prosesKendaraanMasuk($koneksi, $kendaraan['id_kendaraan'], $idArea, $tarif['id_tarif'], $_SESSION['id_user']);
                if ($hasil['sukses']) {
                    catatLog($koneksi, $_SESSION['id_user'], "Mencatat kendaraan masuk: {$platNomor} (id_parkir: {$hasil['id_parkir']})");
                    header("Location: index.php?sukses=Kendaraan {$platNomor} berhasil dicatat masuk! Gate Barrier dibuka.");
                    exit;
                } else {
                    $error = $hasil['pesan'];
                }
            }
        } else {
            $error = 'Gagal memproses data kendaraan.';
        }
    }
}

$judulHalaman = "Catat Kendaraan Masuk";
require_once __DIR__ . '/../../includes/header_petugas.php';
?>

<div class="max-w-2xl mx-auto">

    <div class="mb-6 flex items-center justify-between">
        <a href="index.php" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar kendaraan parkir
        </a>
        <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold">
            <i class="fa-solid fa-bolt me-1"></i> Direct Express Check-In
        </span>
    </div>

    <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
        
        <div class="mb-6 pb-4 border-b border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-500/10 border border-brand-500/30 flex items-center justify-center text-brand-400 text-lg">
                <i class="fa-solid fa-right-to-bracket"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">Input Kendaraan Masuk</h2>
                <p class="text-xs text-slate-400">Langsung isi detail kendaraan &amp; area untuk membuka gate barrier</p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="flex items-center gap-3 bg-red-950/80 text-red-300 text-sm rounded-2xl px-4 py-3.5 mb-6 border border-red-800/60 shadow-lg">
                <i class="fa-solid fa-triangle-exclamation text-red-400 text-base"></i>
                <span class="font-medium text-xs sm:text-sm"><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Form Direct Entry Cepat (1-Step Process) -->
        <form method="POST" class="space-y-5">
            
            <!-- Plat Nomor -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Plat Nomor Kendaraan <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-car-side text-sm"></i>
                    </div>
                    <input type="text" name="plat_nomor" value="<?= htmlspecialchars($_POST['plat_nomor'] ?? '') ?>"
                           placeholder="Contoh: B 1234 ABC" required autofocus
                           class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-11 pr-4 py-3.5 text-base placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition uppercase font-mono font-bold tracking-wider">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Jenis Kendaraan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Jenis Kendaraan <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-layer-group text-xs"></i>
                        </div>
                        <select name="jenis_kendaraan" class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition capitalize">
                            <?php if (!empty($daftarTarif)): ?>
                                <?php foreach ($daftarTarif as $t): ?>
                                    <option value="<?= htmlspecialchars($t['jenis_kendaraan']) ?>" <?= (($_POST['jenis_kendaraan'] ?? '') === $t['jenis_kendaraan']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(ucfirst($t['jenis_kendaraan'])) ?> (Rp <?= number_format($t['tarif_per_jam'], 0, ',', '.') ?>/jam)
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="motor">Motor</option>
                                <option value="mobil" selected>Mobil</option>
                                <option value="truk">Truk / Lainnya</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Area Parkir Tujuan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Area Parkir Tujuan <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-square-parking text-xs"></i>
                        </div>
                        <select name="id_area" required class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                            <?php if (empty($daftarArea)): ?>
                                <option value="">-- Maaf, semua area parkir penuh --</option>
                            <?php else: foreach ($daftarArea as $area): 
                                $sisa = $area['kapasitas'] - $area['terisi'];
                            ?>
                                <option value="<?= $area['id_area'] ?>" <?= (($_POST['id_area'] ?? '') == $area['id_area']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($area['nama_area']) ?> (Sisa <?= $sisa ?> slot)
                                </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Detail Opsional (Warna & Pemilik) -->
            <div class="pt-2 border-t border-slate-800/80">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-3">Detail Pelengkap (Opsional)</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <input type="text" name="warna" value="<?= htmlspecialchars($_POST['warna'] ?? '') ?>" placeholder="Warna Kendaraan (misal: Hitam)" 
                               class="w-full bg-slate-900/60 border border-slate-800 text-white rounded-xl px-4 py-2.5 text-xs placeholder:text-slate-600 focus:outline-none focus:ring-1 focus:ring-brand-500 transition">
                    </div>
                    <div>
                        <input type="text" name="pemilik" value="<?= htmlspecialchars($_POST['pemilik'] ?? '') ?>" placeholder="Nama Pemilik / Pengendara" 
                               class="w-full bg-slate-900/60 border border-slate-800 text-white rounded-xl px-4 py-2.5 text-xs placeholder:text-slate-600 focus:outline-none focus:ring-1 focus:ring-brand-500 transition">
                    </div>
                </div>
            </div>

            <!-- Submit Button Direct -->
            <div class="pt-4">
                <button type="submit" <?= empty($daftarArea) ? 'disabled' : '' ?>
                        class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 disabled:opacity-50 text-slate-950 font-bold px-6 py-4 rounded-2xl text-sm transition duration-200 shadow-xl shadow-emerald-500/20 flex items-center justify-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="tracking-wide">Proses Kendaraan Masuk (Buka Gate Barrier)</span>
                </button>
            </div>
        </form>

    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer_petugas.php'; ?>