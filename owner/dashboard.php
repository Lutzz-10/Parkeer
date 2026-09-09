<?php
$judulHalaman = "Executive Dashboard Owner";
require_once __DIR__ . '/../includes/header_owner.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/rekap_functions.php';

$database = new Database();
$koneksi = $database->connect();

$hariIni = date('Y-m-d');
$ringkasanHariIni = getRingkasanRekap($koneksi, $hariIni, $hariIni);

$totalKendaraanParkir = $koneksi->query(
    "SELECT COUNT(*) FROM tb_transaksi WHERE status = 'masuk'"
)->fetchColumn();

$daftarArea = $koneksi->query("SELECT * FROM tb_area_parkir ORDER BY nama_area ASC")->fetchAll();
?>

<!-- Executive Banner -->
<div class="relative bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border border-slate-800 rounded-3xl p-6 sm:p-8 mb-8 overflow-hidden shadow-2xl">
    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/30 text-purple-400 text-xs font-semibold mb-3">
                <i class="fa-solid fa-chart-pie text-xs"></i> Executive Management Summary
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Ringkasan Eksekutif Parkeer 📊
            </h2>
            <p class="text-slate-400 text-sm mt-1 max-w-xl">
                Pantau kinerja pendapatan operasional harian, statistik volume transaksi, dan kapasitas area parkir.
            </p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <a href="rekap/index.php" class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-slate-950 font-bold px-6 py-3.5 rounded-xl text-sm transition-all duration-200 shadow-lg shadow-brand-500/20 flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-base"></i>
                <span>Buka Rekap Transaksi Lengkap</span>
            </a>
        </div>
    </div>
</div>

<!-- Financial KPI Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    
    <!-- KPI 1: Today's Revenue -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-5 shadow-xl hover:border-slate-700 transition">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pendapatan Hari Ini</span>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <i class="fa-solid fa-money-bill-trend-up text-lg"></i>
            </div>
        </div>
        <p class="text-2xl sm:text-3xl font-extrabold text-emerald-400 tracking-tight">
            Rp <?= number_format($ringkasanHariIni['total_pendapatan'], 0, ',', '.') ?>
        </p>
        <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
            <span class="text-emerald-400 font-semibold"><i class="fa-solid fa-calendar-day"></i> Hari Ini</span> <?= date('d M Y') ?>
        </p>
    </div>

    <!-- KPI 2: Today's Volume -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-5 shadow-xl hover:border-slate-700 transition">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Transaksi Selesai</span>
            <div class="w-10 h-10 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-400">
                <i class="fa-solid fa-receipt text-lg"></i>
            </div>
        </div>
        <p class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
            <?= number_format($ringkasanHariIni['total_transaksi']) ?> <span class="text-sm font-normal text-slate-400">Unit</span>
        </p>
        <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
            <span class="text-brand-400 font-semibold"><i class="fa-solid fa-check"></i> Gate Keluar</span> Selesai diproses
        </p>
    </div>

    <!-- KPI 3: Average Fee per Transaction -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-5 shadow-xl hover:border-slate-700 transition">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-Rata / Transaksi</span>
            <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400">
                <i class="fa-solid fa-calculator text-lg"></i>
            </div>
        </div>
        <p class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
            Rp <?= number_format($ringkasanHariIni['rata_rata'], 0, ',', '.') ?>
        </p>
        <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
            <span class="text-sky-400 font-semibold"><i class="fa-solid fa-chart-line"></i> Estimasi Rata-rata</span>
        </p>
    </div>

    <!-- KPI 4: Currently Parked -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-5 shadow-xl hover:border-slate-700 transition">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sedang Parkir</span>
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                <i class="fa-solid fa-car text-lg"></i>
            </div>
        </div>
        <p class="text-2xl sm:text-3xl font-extrabold text-purple-400 tracking-tight">
            <?= number_format($totalKendaraanParkir) ?> <span class="text-sm font-normal text-slate-400">Unit</span>
        </p>
        <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-500"></span>
            </span>
            <span>Keterisian Real-time</span>
        </p>
    </div>

</div>

<!-- Area Status Summary Cards -->
<div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
        <div>
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-location-dot text-brand-400"></i> Ringkasan Utilitas Area Parkir
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Persentase utilisasi tiap area lokasi parkir</p>
        </div>
        <a href="rekap/index.php" class="text-xs bg-slate-900 hover:bg-slate-800 text-slate-300 px-4 py-2 rounded-xl border border-slate-800 font-semibold transition flex items-center gap-2">
            <span>Analisis Grafik Trend</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($daftarArea as $area): 
            $sisa = $area['kapasitas'] - $area['terisi'];
            $persen = $area['kapasitas'] > 0 ? round(($area['terisi'] / $area['kapasitas']) * 100) : 0;
        ?>
            <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-bold text-white text-base"><?= htmlspecialchars($area['nama_area']) ?></h4>
                    <span class="text-xs font-extrabold px-2.5 py-1 rounded-full bg-slate-950 text-brand-400 border border-brand-500/30">
                        <?= $persen ?>% Terisi
                    </span>
                </div>
                <div class="w-full bg-slate-950 h-2.5 rounded-full overflow-hidden border border-slate-800">
                    <div class="h-full bg-gradient-to-r from-brand-500 to-brand-600 rounded-full" style="width: <?= min(100, $persen) ?>%"></div>
                </div>
                <div class="flex justify-between text-xs text-slate-400 mt-3 pt-2 border-t border-slate-800/60">
                    <span>Terisi: <b class="text-white"><?= $area['terisi'] ?></b></span>
                    <span>Kapasitas: <b class="text-white"><?= $area['kapasitas'] ?></b></span>
                    <span>Sisa: <b class="text-emerald-400"><?= $sisa ?></b></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_owner.php'; ?>