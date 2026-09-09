<?php
$judulHalaman = "Data Kendaraan";
require_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/kendaraan_functions.php';

$database = new Database();
$koneksi = $database->connect();
$keyword = trim($_GET['q'] ?? '');
$daftarKendaraan = getAllKendaraan($koneksi, $keyword);
?>

<div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <form method="GET" class="flex items-center gap-2">
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>"
                       placeholder="Cari plat nomor atau pemilik..."
                       class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-3 py-2.5 text-sm placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
            </div>
            <button class="bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold px-4 py-2.5 rounded-xl text-sm transition">
                Cari
            </button>
            <?php if ($keyword !== ''): ?>
                <a href="index.php" class="text-xs text-slate-400 hover:text-white px-2 py-1">Reset</a>
            <?php endif; ?>
        </form>

        <a href="create.php" class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-slate-950 font-bold px-4 py-2.5 rounded-xl text-sm transition shadow-lg shadow-brand-500/20 flex items-center gap-2 self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i>
            <span>+ Tambah Kendaraan</span>
        </a>
    </div>

    <!-- Alerts -->
    <?php if (isset($_GET['sukses'])): ?>
        <div class="flex items-center gap-2.5 bg-emerald-950/80 text-emerald-300 text-sm rounded-2xl px-4 py-3 mb-6 border border-emerald-800/60 shadow-lg">
            <i class="fa-solid fa-circle-check text-emerald-400"></i>
            <span><?= htmlspecialchars($_GET['sukses']) ?></span>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['gagal'])): ?>
        <div class="flex items-center gap-2.5 bg-red-950/80 text-red-300 text-sm rounded-2xl px-4 py-3 mb-6 border border-red-800/60 shadow-lg">
            <i class="fa-solid fa-triangle-exclamation text-red-400"></i>
            <span><?= htmlspecialchars($_GET['gagal']) ?></span>
        </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-900/90 text-slate-400 text-xs uppercase tracking-wider font-semibold">
                <tr>
                    <th class="px-5 py-3.5">Plat Nomor</th>
                    <th class="px-5 py-3.5">Jenis Kendaraan</th>
                    <th class="px-5 py-3.5">Warna</th>
                    <th class="px-5 py-3.5">Nama Pemilik</th>
                    <th class="px-5 py-3.5">Dicatat Oleh</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                <?php if (empty($daftarKendaraan)): ?>
                    <tr><td colspan="6" class="text-center py-8 text-slate-500">Belum ada data kendaraan.</td></tr>
                <?php else: foreach ($daftarKendaraan as $k): ?>
                    <tr class="hover:bg-slate-900/50 transition">
                        <td class="px-5 py-3.5">
                            <span class="px-3 py-1 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono font-bold tracking-wider text-sm shadow-inner uppercase">
                                <?= htmlspecialchars($k['plat_nomor']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 capitalize">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                <i class="fa-solid <?= $k['jenis_kendaraan'] === 'motor' ? 'fa-motorcycle' : 'fa-car' ?> text-brand-400 text-[11px]"></i>
                                <?= htmlspecialchars($k['jenis_kendaraan']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-300"><?= htmlspecialchars($k['warna'] ?? '-') ?></td>
                        <td class="px-5 py-3.5 text-slate-200 font-medium"><?= htmlspecialchars($k['pemilik'] ?? '-') ?></td>
                        <td class="px-5 py-3.5 text-xs text-slate-400"><?= htmlspecialchars($k['nama_pencatat']) ?></td>
                        <td class="px-5 py-3.5 text-right space-x-2">
                            <a href="edit.php?id=<?= $k['id_kendaraan'] ?>" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium border border-slate-700 transition">
                                <i class="fa-solid fa-pen-to-square text-brand-400"></i> Edit
                            </a>
                            <a href="delete.php?id=<?= $k['id_kendaraan'] ?>"
                               onclick="return confirm('Yakin hapus kendaraan ini?')"
                               class="px-3 py-1.5 rounded-lg bg-red-950/60 hover:bg-red-900 text-red-300 text-xs font-medium border border-red-800/60 transition">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>