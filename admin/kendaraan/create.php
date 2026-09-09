<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/kendaraan_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';
require_once __DIR__ . '/../../includes/auth.php';
cekRole(['admin']);

$database = new Database();
$koneksi = $database->connect();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'plat_nomor' => trim($_POST['plat_nomor']),
        'jenis_kendaraan' => $_POST['jenis_kendaraan'],
        'warna' => trim($_POST['warna']),
        'pemilik' => trim($_POST['pemilik']),
    ];

    if ($data['plat_nomor'] === '') {
        $error = 'Plat nomor wajib diisi.';
    } elseif (platNomorSudahAda($koneksi, $data['plat_nomor'])) {
        $error = 'Plat nomor ini sudah terdaftar.';
    } else {
        createKendaraan($koneksi, $data, $_SESSION['id_user']);
        catatLog($koneksi, $_SESSION['id_user'], "Menambahkan kendaraan: {$data['plat_nomor']}");
        header("Location: index.php?sukses=Kendaraan berhasil ditambahkan");
        exit;
    }
}

$judulHalaman = "Tambah Kendaraan";
require_once __DIR__ . '/../../includes/header_admin.php';
?>

<div class="max-w-xl mx-auto">
    
    <div class="mb-6">
        <a href="index.php" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar kendaraan
        </a>
    </div>

    <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
        <div class="mb-6 pb-4 border-b border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-500/10 border border-brand-500/30 flex items-center justify-center text-brand-400 text-lg">
                <i class="fa-solid fa-car font-bold"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">Tambah Data Kendaraan</h2>
                <p class="text-xs text-slate-400">Daftarkan plat nomor dan identitas kendaraan baru</p>
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
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Plat Nomor Kendaraan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-rectangle-list text-xs"></i>
                    </div>
                    <input type="text" name="plat_nomor" value="<?= htmlspecialchars($_POST['plat_nomor'] ?? '') ?>" required placeholder="Contoh: B 1234 ABC"
                           class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500 transition uppercase font-mono font-bold">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Jenis Kendaraan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-car-side text-xs"></i>
                    </div>
                    <select name="jenis_kendaraan" class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition appearance-none">
                        <option value="motor">Motor</option>
                        <option value="mobil">Mobil</option>
                        <option value="lainnya">Lainnya / Bus / Truk</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Warna Kendaraan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-palette text-xs"></i>
                    </div>
                    <input type="text" name="warna" value="<?= htmlspecialchars($_POST['warna'] ?? '') ?>" placeholder="Contoh: Hitam Metalik"
                           class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Nama Pemilik (Opsional)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-user text-xs"></i>
                    </div>
                    <input type="text" name="pemilik" value="<?= htmlspecialchars($_POST['pemilik'] ?? '') ?>" placeholder="Contoh: Budi Santoso"
                           class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-800">
                <button type="submit" class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-slate-950 font-bold px-6 py-3 rounded-xl text-sm transition shadow-lg shadow-brand-500/20">
                    Simpan Kendaraan
                </button>
                <a href="index.php" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold px-5 py-3 rounded-xl text-sm transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>