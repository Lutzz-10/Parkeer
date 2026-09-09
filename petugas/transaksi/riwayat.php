<?php
$judulHalaman = "Riwayat Transaksi Parkir";
require_once __DIR__ . '/../../includes/header_petugas.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/transaksi_functions.php';

$database = new Database();
$koneksi = $database->connect();
$riwayat = getRiwayatTransaksi($koneksi, null, null, 50);
?>

<div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">

    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
        <div>
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-brand-400"></i> Riwayat Transaksi Selesai
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Daftar 50 transaksi parkir yang telah diproses keluar</p>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-900/90 text-slate-400 text-xs uppercase tracking-wider font-semibold">
                <tr>
                    <th class="px-5 py-3.5">Plat Nomor</th>
                    <th class="px-5 py-3.5">Waktu Masuk</th>
                    <th class="px-5 py-3.5">Waktu Keluar</th>
                    <th class="px-5 py-3.5">Durasi</th>
                    <th class="px-5 py-3.5">Biaya Total</th>
                    <th class="px-5 py-3.5 text-right">Struk</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                <?php if (empty($riwayat)): ?>
                    <tr><td colspan="6" class="text-center py-8 text-slate-500">Belum ada riwayat transaksi.</td></tr>
                <?php else: foreach ($riwayat as $r): ?>
                    <tr class="hover:bg-slate-900/50 transition">
                        <td class="px-5 py-3.5">
                            <span class="px-3 py-1 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono font-bold tracking-wider text-xs shadow-inner uppercase">
                                <?= htmlspecialchars($r['plat_nomor']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-slate-400 font-mono"><?= date('d M Y H:i', strtotime($r['waktu_masuk'])) ?></td>
                        <td class="px-5 py-3.5 text-xs text-slate-400 font-mono"><?= date('d M Y H:i', strtotime($r['waktu_keluar'])) ?></td>
                        <td class="px-5 py-3.5 font-bold text-slate-200 text-xs"><?= $r['durasi_jam'] ?> Jam</td>
                        <td class="px-5 py-3.5 font-bold text-emerald-400">Rp <?= number_format($r['biaya_total'], 0, ',', '.') ?></td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="/parkeer/petugas/struk/cetak.php?id=<?= $r['id_parkir'] ?>" 
                               class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-receipt text-brand-400"></i> Struk
                            </a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once __DIR__ . '/../../includes/footer_petugas.php'; ?>