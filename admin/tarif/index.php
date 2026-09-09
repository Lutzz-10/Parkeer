<?php
$judulHalaman = "Tarif Parkir";
require_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/tarif_functions.php';

$database = new Database();
$koneksi = $database->connect();
$daftarTarif = getAllTarif($koneksi);
?>

<div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Skema Tarif Parkir</h2>
            <p class="text-xs text-slate-400">Total <?= count($daftarTarif) ?> tarif terkonfigurasi</p>
        </div>

        <a href="create.php" class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-slate-950 font-bold px-4 py-2.5 rounded-xl text-sm transition shadow-lg shadow-brand-500/20 flex items-center gap-2 self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i>
            <span>+ Tambah Tarif Baru</span>
        </a>
    </div>

    <!-- Alert Sukses / Gagal -->
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
                    <th class="px-5 py-3.5">Jenis Kendaraan</th>
                    <th class="px-5 py-3.5">Tarif per Jam</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                <?php if (empty($daftarTarif)): ?>
                    <tr><td colspan="3" class="text-center py-8 text-slate-500">Belum ada skema tarif parkir.</td></tr>
                <?php else: foreach ($daftarTarif as $tarif): ?>
                    <tr class="hover:bg-slate-900/50 transition">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <?php
                                $iconClass = match(strtolower($tarif['jenis_kendaraan'])) {
                                    'motor' => 'fa-motorcycle text-emerald-400 bg-emerald-500/10 border-emerald-500/30',
                                    'mobil' => 'fa-car text-blue-400 bg-blue-500/10 border-blue-500/30',
                                    default => 'fa-truck text-purple-400 bg-purple-500/10 border-purple-500/30'
                                };
                                ?>
                                <div class="w-10 h-10 rounded-xl border flex items-center justify-center text-sm <?= $iconClass ?>">
                                    <i class="fa-solid <?= strtok($iconClass, ' ') ?>"></i>
                                </div>
                                <span class="font-bold text-white capitalize text-base"><?= htmlspecialchars($tarif['jenis_kendaraan']) ?></span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-lg font-extrabold text-brand-400">Rp <?= number_format($tarif['tarif_per_jam'], 0, ',', '.') ?></span>
                            <span class="text-xs text-slate-400 font-normal"> / jam</span>
                        </td>
                        <td class="px-5 py-3.5 text-right space-x-2">
                            <a href="edit.php?id=<?= $tarif['id_tarif'] ?>" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium border border-slate-700 transition">
                                <i class="fa-solid fa-pen-to-square text-brand-400"></i> Edit
                            </a>
                            <a href="delete.php?id=<?= $tarif['id_tarif'] ?>"
                               onclick="return confirm('Yakin hapus tarif ini?')"
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