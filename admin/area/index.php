<?php
$judulHalaman = "Area Parkir";
require_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/area_functions.php';

$database = new Database();
$koneksi = $database->connect();
$daftarArea = getAllArea($koneksi);
?>

<div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Manajemen Area Parkir</h2>
            <p class="text-xs text-slate-400">Total <?= count($daftarArea) ?> lokasi area terdaftar</p>
        </div>

        <a href="create.php" class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-slate-950 font-bold px-4 py-2.5 rounded-xl text-sm transition shadow-lg shadow-brand-500/20 flex items-center gap-2 self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i>
            <span>+ Tambah Area Baru</span>
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
                    <th class="px-5 py-3.5">Nama Area</th>
                    <th class="px-5 py-3.5">Kapasitas Total</th>
                    <th class="px-5 py-3.5">Slot Terisi</th>
                    <th class="px-5 py-3.5">Sisa Slot</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                <?php if (empty($daftarArea)): ?>
                    <tr><td colspan="5" class="text-center py-8 text-slate-500">Belum ada area parkir.</td></tr>
                <?php else: foreach ($daftarArea as $area): 
                    $sisa = $area['kapasitas'] - $area['terisi'];
                    $persen = $area['kapasitas'] > 0 ? round(($area['terisi'] / $area['kapasitas']) * 100) : 0;
                ?>
                    <tr class="hover:bg-slate-900/50 transition">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-brand-500/10 border border-brand-500/30 flex items-center justify-center text-brand-400 font-bold text-sm">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-white text-base"><?= htmlspecialchars($area['nama_area']) ?></p>
                                    <div class="w-32 bg-slate-900 h-1.5 rounded-full overflow-hidden border border-slate-800 mt-1">
                                        <div class="h-full bg-brand-500 rounded-full" style="width: <?= min(100, $persen) ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 font-bold text-white"><?= $area['kapasitas'] ?> <span class="text-xs text-slate-400 font-normal">Slot</span></td>
                        <td class="px-5 py-3.5 font-bold text-slate-300"><?= $area['terisi'] ?> <span class="text-xs text-slate-400 font-normal">Unit</span></td>
                        <td class="px-5 py-3.5">
                            <?php if ($sisa <= 0): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-950 text-red-400 border border-red-800">
                                    <i class="fa-solid fa-ban text-[10px]"></i> Penuh (0)
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-950 text-emerald-400 border border-emerald-800">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Tersedia (<?= $sisa ?>)
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3.5 text-right space-x-2">
                            <a href="edit.php?id=<?= $area['id_area'] ?>" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium border border-slate-700 transition">
                                <i class="fa-solid fa-pen-to-square text-brand-400"></i> Edit
                            </a>
                            <a href="delete.php?id=<?= $area['id_area'] ?>"
                               onclick="return confirm('Yakin hapus area ini?')"
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