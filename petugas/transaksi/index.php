<?php
$judulHalaman = "Kendaraan Sedang Parkir";
require_once __DIR__ . '/../../includes/header_petugas.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/transaksi_functions.php';

$database = new Database();
$koneksi = $database->connect();
$keyword = trim($_GET['q'] ?? '');
$daftar = getKendaraanSedangParkir($koneksi, $keyword);
?>

<div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <form method="GET" class="flex items-center gap-2">
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Cari plat nomor kendaraan..."
                       class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-2.5 text-sm placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition uppercase font-mono">
            </div>
            <button class="bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold px-4 py-2.5 rounded-xl text-sm transition">
                Cari Plat
            </button>
            <?php if ($keyword !== ''): ?>
                <a href="index.php" class="text-xs text-slate-400 hover:text-white px-2 py-1">Reset</a>
            <?php endif; ?>
        </form>

        <a href="masuk.php" class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-slate-950 font-bold px-4 py-2.5 rounded-xl text-sm transition shadow-lg shadow-brand-500/20 flex items-center gap-2 self-start sm:self-auto">
            <i class="fa-solid fa-right-to-bracket"></i>
            <span>+ Catat Kendaraan Masuk</span>
        </a>
    </div>

    <!-- Alert Sukses -->
    <?php if (isset($_GET['sukses'])): ?>
        <div class="flex items-center gap-2.5 bg-emerald-950/80 text-emerald-300 text-sm rounded-2xl px-4 py-3 mb-6 border border-emerald-800/60 shadow-lg">
            <i class="fa-solid fa-circle-check text-emerald-400"></i>
            <span><?= htmlspecialchars($_GET['sukses']) ?></span>
        </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-900/90 text-slate-400 text-xs uppercase tracking-wider font-semibold">
                <tr>
                    <th class="px-5 py-3.5">Plat Nomor</th>
                    <th class="px-5 py-3.5">Jenis</th>
                    <th class="px-5 py-3.5">Area Parkir</th>
                    <th class="px-5 py-3.5">Waktu Masuk</th>
                    <th class="px-5 py-3.5 text-right">Aksi Kasir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                <?php if (empty($daftar)): ?>
                    <tr><td colspan="5" class="text-center py-8 text-slate-500">Tidak ada kendaraan yang sedang parkir.</td></tr>
                <?php else: foreach ($daftar as $t): ?>
                    <tr class="hover:bg-slate-900/50 transition">
                        <td class="px-5 py-3.5">
                            <span class="px-3 py-1 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono font-bold tracking-wider text-sm shadow-inner uppercase">
                                <?= htmlspecialchars($t['plat_nomor']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 capitalize text-slate-300">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                <i class="fa-solid <?= $t['jenis_kendaraan'] === 'motor' ? 'fa-motorcycle' : 'fa-car' ?> text-brand-400 text-[11px]"></i>
                                <?= htmlspecialchars($t['jenis_kendaraan']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-200 font-medium"><?= htmlspecialchars($t['nama_area']) ?></td>
                        <td class="px-5 py-3.5 text-xs text-slate-400 font-mono">
                            <i class="fa-regular fa-clock text-slate-500 mr-1"></i>
                            <?= date('d M Y H:i', strtotime($t['waktu_masuk'])) ?>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="keluar.php?id=<?= $t['id_parkir'] ?>"
                               class="bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-slate-950 font-bold px-4 py-2 rounded-xl text-xs transition shadow-md shadow-orange-500/20 inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-receipt"></i> Proses Keluar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once __DIR__ . '/../../includes/footer_petugas.php'; ?>