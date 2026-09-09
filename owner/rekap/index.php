<?php
$judulHalaman = "Rekap Transaksi";
require_once __DIR__ . '/../../includes/header_owner.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/rekap_functions.php';

$database = new Database();
$koneksi = $database->connect();

// Default: 7 hari terakhir jika belum ada filter
$dari = $_GET['dari'] ?? date('Y-m-d', strtotime('-6 days'));
$sampai = $_GET['sampai'] ?? date('Y-m-d');

// Validasi sederhana supaya "dari" tidak lebih besar dari "sampai"
if ($dari > $sampai) {
    [$dari, $sampai] = [$sampai, $dari];
}

$ringkasan = getRingkasanRekap($koneksi, $dari, $sampai);
$perJenis = getRekapPerJenis($koneksi, $dari, $sampai);
$perHari = getRekapPerHari($koneksi, $dari, $sampai);
$detail = getDetailTransaksiRekap($koneksi, $dari, $sampai, 100);

// Siapkan data untuk Chart.js (format array label & value)
$labelHarian = array_map(fn($r) => date('d M', strtotime($r['tanggal'])), $perHari);
$dataHarian = array_map(fn($r) => (float) $r['total_pendapatan'], $perHari);
?>

<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900/80 backdrop-blur border border-slate-800 p-6 rounded-2xl shadow-xl">
        <div>
            <div class="flex items-center gap-2 text-brand-400 font-semibold text-sm mb-1">
                <i class="fa-solid me-1 fa-file-invoice"></i> Laporan Financial & Operational
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Rekap Transaksi Parkir</h1>
            <p class="text-sm text-slate-400 mt-1">Analisis performa pendapatan dan audit log kendaraan terperinci</p>
        </div>
        <a href="export.php?dari=<?= $dari ?>&sampai=<?= $sampai ?>"
           class="inline-flex items-center justify-center gap-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-semibold px-4 py-2.5 rounded-xl transition duration-200 shadow-lg shadow-emerald-500/5">
            <i class="fa-solid fa-file-excel text-emerald-400"></i>
            <span>Export CSV Data</span>
        </a>
    </div>

    <!-- Filter Tanggal Bar -->
    <div class="bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-5 shadow-xl">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                    <i class="fa-regular fa-calendar me-1"></i> Dari Tanggal
                </label>
                <input type="date" name="dari" value="<?= htmlspecialchars($dari) ?>" 
                       class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                    <i class="fa-regular fa-calendar-check me-1"></i> Sampai Tanggal
                </label>
                <input type="date" name="sampai" value="<?= htmlspecialchars($sampai) ?>" 
                       class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition">
            </div>
            <div>
                <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-400 hover:to-brand-500 text-slate-950 font-bold px-6 py-2.5 rounded-xl transition duration-200 shadow-lg shadow-brand-500/20 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-filter"></i>
                    <span>Tampilkan Laporan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Ringkasan KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Transaksi -->
        <div class="bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Total Transaksi</p>
                    <h3 class="text-2xl font-bold text-white group-hover:text-cyan-400 transition"><?= number_format($ringkasan['total_transaksi'], 0, ',', '.') ?></h3>
                    <p class="text-xs text-slate-500 mt-1">Kendaraan Selesai</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 text-xl shadow-inner">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Total Pendapatan</p>
                    <h3 class="text-2xl font-bold text-brand-400 group-hover:scale-105 transition transform origin-left">Rp <?= number_format($ringkasan['total_pendapatan'], 0, ',', '.') ?></h3>
                    <p class="text-xs text-slate-500 mt-1">Periode Terpilih</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-400 text-xl shadow-inner">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>
        </div>

        <!-- Rata-rata per Transaksi -->
        <div class="bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Rata-Rata / Transaksi</p>
                    <h3 class="text-2xl font-bold text-white group-hover:text-purple-400 transition">Rp <?= number_format($ringkasan['rata_rata'], 0, ',', '.') ?></h3>
                    <p class="text-xs text-slate-500 mt-1">Estimasi Omzet / Tiket</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 text-xl shadow-inner">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
        </div>

        <!-- Rata-rata Durasi -->
        <div class="bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Rata-Rata Durasi</p>
                    <h3 class="text-2xl font-bold text-white group-hover:text-amber-400 transition"><?= number_format($ringkasan['rata_rata_durasi'], 1) ?> <span class="text-sm font-normal text-slate-400">Jam</span></h3>
                    <p class="text-xs text-slate-500 mt-1">Lama Parkir Usaha</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xl shadow-inner">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Tren Harian -->
    <div class="bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-brand-500 animate-pulse"></div>
                <h3 class="font-bold text-white">Tren Pendapatan Harian</h3>
            </div>
            <span class="text-xs text-slate-400">Rp (Rupiah)</span>
        </div>
        <?php if (empty($perHari)): ?>
            <div class="text-center py-12 text-slate-500">
                <i class="fa-solid fa-chart-bar text-4xl mb-3 opacity-40"></i>
                <p class="text-sm">Tidak ada data pendapatan pada rentang tanggal ini.</p>
            </div>
        <?php else: ?>
            <div class="h-64 sm:h-80 w-full">
                <canvas id="chartHarian"></canvas>
            </div>
        <?php endif; ?>
    </div>

    <!-- Breakdown per Jenis Kendaraan & Detail Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Breakdown per Jenis Kendaraan -->
        <div class="bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <h3 class="font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-car text-brand-400"></i> Rekap Per Jenis
                </h3>
            </div>
            <div class="space-y-3">
                <?php if (empty($perJenis)): ?>
                    <p class="text-center py-6 text-slate-500 text-sm">Tidak ada data jenis kendaraan.</p>
                <?php else: foreach ($perJenis as $p): 
                    $jenisLower = strtolower($p['jenis_kendaraan']);
                    $iconClass = 'fa-car';
                    $badgeBg = 'bg-blue-500/10 text-blue-400 border-blue-500/20';
                    if (str_contains($jenisLower, 'motor')) {
                        $iconClass = 'fa-motorcycle';
                        $badgeBg = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                    } elseif (str_contains($jenisLower, 'truk') || str_contains($jenisLower, 'bus')) {
                        $iconClass = 'fa-truck-monster';
                        $badgeBg = 'bg-purple-500/10 text-purple-400 border-purple-500/20';
                    }
                ?>
                    <div class="p-3.5 bg-slate-950/60 rounded-xl border border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl border flex items-center justify-center text-lg <?= $badgeBg ?>">
                                <i class="fa-solid <?= $iconClass ?>"></i>
                            </div>
                            <div>
                                <p class="font-bold text-white text-sm capitalize"><?= htmlspecialchars($p['jenis_kendaraan']) ?></p>
                                <p class="text-xs text-slate-400"><?= $p['jumlah_transaksi'] ?> Transaksi</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-brand-400 text-sm">Rp <?= number_format($p['total_pendapatan'], 0, ',', '.') ?></p>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- Detail Transaksi (2 Cols) -->
        <div class="lg:col-span-2 bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                    <h3 class="font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-brand-400"></i> Detail Log Transaksi
                    </h3>
                    <span class="text-xs text-slate-400">Max 100 Transaksi Terbaru</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-950 text-slate-400 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3">Plat Nomor</th>
                                <th class="px-4 py-3">Jenis</th>
                                <th class="px-4 py-3">Area</th>
                                <th class="px-4 py-3">Masuk / Keluar</th>
                                <th class="px-4 py-3 text-right">Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            <?php if (empty($detail)): ?>
                                <tr><td colspan="5" class="text-center py-8 text-slate-500">Tidak ada transaksi ditemukan.</td></tr>
                            <?php else: foreach ($detail as $d): ?>
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 bg-slate-950 text-brand-400 border border-brand-500/30 rounded-md font-mono font-bold text-xs tracking-wider">
                                            <?= htmlspecialchars($d['plat_nomor']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-300 capitalize text-xs">
                                        <?= htmlspecialchars($d['jenis_kendaraan']) ?>
                                    </td>
                                    <td class="px-4 py-3 text-slate-400 text-xs">
                                        <?= htmlspecialchars($d['nama_area']) ?>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-400">
                                        <div class="text-slate-300"><?= date('d/m H:i', strtotime($d['waktu_masuk'])) ?></div>
                                        <div class="text-slate-500 text-[11px]"><?= date('d/m H:i', strtotime($d['waktu_keluar'])) ?> (<?= $d['durasi_jam'] ?>j)</div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-emerald-400 text-xs">
                                        Rp <?= number_format($d['biaya_total'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<?php if (!empty($perHari)): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('chartHarian').getContext('2d');
    
    // Create subtle gradient fill for chart bars
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, '#F5A623');
    gradient.addColorStop(1, '#DB8E12');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelHarian) ?>,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: <?= json_encode($dataHarian) ?>,
                backgroundColor: gradient,
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 24,
                hoverBackgroundColor: '#F8BA4E'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#f8fafc',
                    bodyColor: '#F5A623',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Pendapatan: Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: { 
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 11 } }
                },
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                    ticks: { 
                        color: '#94a3b8',
                        font: { family: 'Plus Jakarta Sans', size: 11 },
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value/1000000) + 'M';
                            if (value >= 1000) return 'Rp ' + (value/1000) + 'k';
                            return 'Rp ' + value;
                        }
                    } 
                } 
            }
        }
    });
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer_owner.php'; ?>