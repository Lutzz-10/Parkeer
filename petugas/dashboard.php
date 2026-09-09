<?php
$judulHalaman = "Dashboard Petugas Gate";
require_once __DIR__ . '/../includes/header_petugas.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/transaksi_functions.php';

$database = new Database();
$koneksi = $database->connect();

$sedangParkir = getKendaraanSedangParkir($koneksi);
$totalSedangParkir = count($sedangParkir);

$today = date('Y-m-d');
$masukHariIni = (int) $koneksi->query("SELECT COUNT(*) FROM tb_transaksi WHERE DATE(waktu_masuk) = '$today'")->fetchColumn();
$keluarHariIni = (int) $koneksi->query("SELECT COUNT(*) FROM tb_transaksi WHERE DATE(waktu_keluar) = '$today' AND status = 'keluar'")->fetchColumn();

$daftarArea = $koneksi->query("SELECT * FROM tb_area_parkir ORDER BY nama_area ASC")->fetchAll();
?>

<!-- Operator Action Banner -->
<div class="relative bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border border-slate-800 rounded-3xl p-6 sm:p-8 mb-8 overflow-hidden shadow-2xl">
    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold mb-3">
                <i class="fa-solid fa-gate text-xs"></i> Pos Gate Parkir Aktif
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Halo, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>! 🚗
            </h2>
            <p class="text-slate-400 text-sm mt-1 max-w-xl">
                Stasiun kasir &amp; catat gate parkir. Catat kendaraan masuk atau proses pembayaran kendaraan keluar.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <a href="transaksi/masuk.php" class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-slate-950 font-bold px-6 py-3.5 rounded-xl text-sm transition-all duration-200 shadow-lg shadow-brand-500/20 flex items-center gap-2">
                <i class="fa-solid fa-right-to-bracket text-base"></i>
                <span>+ Catat Kendaraan Masuk</span>
            </a>
        </div>
    </div>
</div>

<!-- Operator Stats Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    
    <!-- Stat 1: Parked Vehicles Now -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-5 shadow-xl hover:border-slate-700 transition">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sedang Parkir</span>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                <i class="fa-solid fa-car text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($totalSedangParkir) ?> <span class="text-sm font-normal text-slate-400">Unit</span></p>
        <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
            </span>
            <span>Aktif di dalam area</span>
        </p>
    </div>

    <!-- Stat 2: Check-in Today -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-5 shadow-xl hover:border-slate-700 transition">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Masuk Hari Ini</span>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <i class="fa-solid fa-right-to-bracket text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-emerald-400 tracking-tight"><?= number_format($masukHariIni) ?> <span class="text-sm font-normal text-slate-400">Transaksi</span></p>
        <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
            <span class="text-emerald-400 font-semibold"><i class="fa-solid fa-arrow-down font-bold"></i> Total Masuk</span> Hari ini
        </p>
    </div>

    <!-- Stat 3: Check-out Today -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-5 shadow-xl hover:border-slate-700 transition">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Keluar Hari Ini</span>
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                <i class="fa-solid fa-right-from-bracket text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-purple-400 tracking-tight"><?= number_format($keluarHariIni) ?> <span class="text-sm font-normal text-slate-400">Transaksi</span></p>
        <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
            <span class="text-purple-400 font-semibold"><i class="fa-solid fa-arrow-up font-bold"></i> Total Selesai</span> Hari ini
        </p>
    </div>

</div>

<!-- Area Slot Occupancy Monitor -->
<div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 shadow-xl mb-8">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
        <div>
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-square-parking text-brand-400"></i> Monitoring Slot Area Parkir
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Informasi sisa slot untuk petunjuk pengarahan pengendara</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($daftarArea as $area): 
            $sisa = $area['kapasitas'] - $area['terisi'];
            $persen = $area['kapasitas'] > 0 ? round(($area['terisi'] / $area['kapasitas']) * 100) : 0;
        ?>
            <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-white text-sm flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-brand-400"></i> <?= htmlspecialchars($area['nama_area']) ?>
                    </h4>
                    <span class="text-xs font-extrabold <?= $sisa > 0 ? 'text-emerald-400' : 'text-red-400' ?>">
                        <?= $sisa ?> Slot Sisa
                    </span>
                </div>
                <div class="w-full bg-slate-950 h-2 rounded-full overflow-hidden border border-slate-800 mt-2">
                    <div class="h-full bg-gradient-to-r from-brand-500 to-brand-600 rounded-full" style="width: <?= min(100, $persen) ?>%"></div>
                </div>
                <div class="flex justify-between text-[11px] text-slate-400 mt-2">
                    <span>Terisi: <?= $area['terisi'] ?></span>
                    <span>Kapasitas: <?= $area['kapasitas'] ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Currently Parked Vehicles Table -->
<div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
        <div>
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-car-side text-brand-400"></i> Kendaraan Sedang Parkir Saat Ini
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Daftar kendaraan aktif yang belum proses keluar</p>
        </div>
        <a href="transaksi/index.php" class="text-xs text-brand-400 hover:text-brand-300 font-semibold flex items-center gap-1">
            Lihat Semua <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

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
                <?php if (empty($sedangParkir)): ?>
                    <tr><td colspan="5" class="text-center py-8 text-slate-500">Tidak ada kendaraan yang sedang parkir.</td></tr>
                <?php else: foreach (array_slice($sedangParkir, 0, 8) as $t): ?>
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
                            <a href="transaksi/keluar.php?id=<?= $t['id_parkir'] ?>"
                               class="bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-slate-950 font-bold px-3.5 py-1.5 rounded-xl text-xs transition shadow-md shadow-orange-500/20 inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-receipt"></i> Proses Keluar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_petugas.php'; ?>