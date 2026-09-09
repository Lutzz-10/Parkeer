<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/tarif_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';
require_once __DIR__ . '/../../includes/auth.php';
cekRole(['admin']);

$database = new Database();
$koneksi = $database->connect();
$error = '';

$id = (int) ($_GET['id'] ?? 0);
$tarif = getTarifById($koneksi, $id);
if (!$tarif) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'jenis_kendaraan' => $_POST['jenis_kendaraan'],
        'tarif_per_jam' => (int) $_POST['tarif_per_jam'],
    ];

    if ($data['tarif_per_jam'] <= 0) {
        $error = 'Tarif harus lebih dari 0.';
    } elseif (jenisKendaraanSudahAdaTarif($koneksi, $data['jenis_kendaraan'], $id)) {
        $error = 'Jenis kendaraan ini sudah punya tarif lain.';
    } else {
        updateTarif($koneksi, $id, $data);
        catatLog($koneksi, $_SESSION['id_user'], "Mengubah tarif: {$data['jenis_kendaraan']}");
        header("Location: index.php?sukses=Tarif berhasil diperbarui");
        exit;
    }
    $tarif = array_merge($tarif, $data);
}

$judulHalaman = "Edit Tarif Parkir";
require_once __DIR__ . '/../../includes/header_admin.php';
?>

<div class="max-w-xl mx-auto">
    
    <div class="mb-6">
        <a href="index.php" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke tarif parkir
        </a>
    </div>

    <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
        <div class="mb-6 pb-4 border-b border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-500/10 border border-brand-500/30 flex items-center justify-center text-brand-400 text-lg">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">Edit Skema Tarif</h2>
                <p class="text-xs text-slate-400">Perbarui nilai tarif per jam untuk <?= htmlspecialchars($tarif['jenis_kendaraan']) ?></p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="flex items-center gap-3 bg-red-950/80 text-red-300 text-sm rounded-2xl px-4 py-3 mb-6 border border-red-800/60 shadow-lg">
                <i class="fa-solid fa-triangle-exclamation text-red-400"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Jenis Kendaraan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-car-side text-xs"></i>
                    </div>
                    <select name="jenis_kendaraan" class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition appearance-none capitalize">
                        <?php foreach (['motor', 'mobil', 'lainnya'] as $j): ?>
                            <option value="<?= $j ?>" <?= $tarif['jenis_kendaraan'] === $j ? 'selected' : '' ?>><?= ucfirst($j) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Tarif per Jam (Rp)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">
                        Rp
                    </div>
                    <input type="number" name="tarif_per_jam" value="<?= $tarif['tarif_per_jam'] ?>" min="1" required
                           class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-10 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition font-mono">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-800">
                <button type="submit" class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-slate-950 font-bold px-6 py-3 rounded-xl text-sm transition shadow-lg shadow-brand-500/20">
                    Simpan Perubahan
                </button>
                <a href="index.php" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold px-5 py-3 rounded-xl text-sm transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>