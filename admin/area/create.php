<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/area_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';
require_once __DIR__ . '/../../includes/auth.php';
cekRole(['admin']);

$database = new Database();
$koneksi = $database->connect();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_area' => trim($_POST['nama_area']),
        'kapasitas' => (int) $_POST['kapasitas'],
    ];

    if ($data['nama_area'] === '' || $data['kapasitas'] <= 0) {
        $error = 'Nama area wajib diisi dan kapasitas harus lebih dari 0.';
    } else {
        createArea($koneksi, $data);
        catatLog($koneksi, $_SESSION['id_user'], "Menambahkan area: {$data['nama_area']}");
        header("Location: index.php?sukses=Area berhasil ditambahkan");
        exit;
    }
}

$judulHalaman = "Tambah Area Parkir";
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
                <i class="fa-solid fa-square-parking"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">Tambah Area Parkir</h2>
                <p class="text-xs text-slate-400">Atur nama lokasi dan jumlah maksimal slot kendaraan</p>
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
                    <input type="text" name="nama_area" value="<?= htmlspecialchars($_POST['nama_area'] ?? '') ?>" required placeholder="Contoh: Area A - Lantai 1"
                           class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Kapasitas Slot Maksimal</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-hashtag text-xs"></i>
                    </div>
                    <input type="number" name="kapasitas" min="1" required placeholder="Contoh: 50"
                           class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-800">
                <button type="submit" class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-slate-950 font-bold px-6 py-3 rounded-xl text-sm transition shadow-lg shadow-brand-500/20">
                    Simpan Area Baru
                </button>
                <a href="index.php" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold px-5 py-3 rounded-xl text-sm transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>