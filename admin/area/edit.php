<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/area_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';
require_once __DIR__ . '/../../includes/auth.php';
cekRole(['admin']);

$database = new Database();
$koneksi = $database->connect();
$error = '';

$id = (int) ($_GET['id'] ?? 0);
$area = getAreaById($koneksi, $id);
if (!$area) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_area' => trim($_POST['nama_area']),
        'kapasitas' => (int) $_POST['kapasitas'],
    ];

    if ($data['nama_area'] === '' || $data['kapasitas'] <= 0) {
        $error = 'Nama area wajib diisi dan kapasitas harus lebih dari 0.';
    } elseif ($data['kapasitas'] < $area['terisi']) {
        $error = "Kapasitas tidak boleh kurang dari jumlah slot yang sedang terisi ({$area['terisi']}).";
    } else {
        updateArea($koneksi, $id, $data);
        catatLog($koneksi, $_SESSION['id_user'], "Mengubah area: {$data['nama_area']}");
        header("Location: index.php?sukses=Area berhasil diperbarui");
        exit;
    }
    $area = array_merge($area, $data);
}

$judulHalaman = "Edit Area Parkir";
require_once __DIR__ . '/../../includes/header_admin.php';
?>

<div class="max-w-xl mx-auto">
    
    <div class="mb-6">
        <a href="index.php" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar area
        </a>
    </div>

    <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
        <div class="mb-6 pb-4 border-b border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-500/10 border border-brand-500/30 flex items-center justify-center text-brand-400 text-lg">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">Edit Area Parkir</h2>
                <p class="text-xs text-slate-400">Perbarui informasi area dan batas slot kendaraan</p>
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
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Nama Area / Blok</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-location-dot text-xs"></i>
                    </div>
                    <input type="text" name="nama_area" value="<?= htmlspecialchars($area['nama_area']) ?>" required
                           class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Kapasitas Slot Maksimal</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-hashtag text-xs"></i>
                    </div>
                    <input type="number" name="kapasitas" value="<?= $area['kapasitas'] ?>" min="1" required
                           class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                </div>
                <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-info-circle text-brand-400"></i> Slot terisi saat ini: <b class="text-white"><?= $area['terisi'] ?> unit</b>
                </p>
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