<?php
$judulHalaman = "Dashboard Admin";
require_once __DIR__ . '/../includes/header_admin.php';
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$koneksi = $database->connect();

// Metric Queries
$totalUser = (int) $koneksi->query("SELECT COUNT(*) FROM tb_user")->fetchColumn();
$totalArea = (int) $koneksi->query("SELECT COUNT(*) FROM tb_area_parkir")->fetchColumn();
$totalKendaraan = (int) $koneksi->query("SELECT COUNT(*) FROM tb_kendaraan")->fetchColumn();
$kendaraanParkir = (int) $koneksi->query("SELECT COUNT(*) FROM tb_transaksi WHERE status = 'masuk'")->fetchColumn();

// Area Slot Capacity Data
$daftarArea = $koneksi->query("SELECT * FROM tb_area_parkir ORDER BY nama_area ASC")->fetchAll();

// Recent Activity Log Data
$logsTerbaru = $koneksi->query(
    "SELECT l.*, u.nama_lengkap, u.role 
     FROM tb_log_aktivitas l 
     LEFT JOIN tb_user u ON l.id_user = u.id_user 
     ORDER BY l.waktu_aktivitas DESC LIMIT 5"
)->fetchAll();
?>

<!-- Welcome Banner Card -->
<div class="relative bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border border-slate-800 rounded-3xl p-6 sm:p-8 mb-8 overflow-hidden shadow-2xl">
    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/10 border border-brand-500/30 text-brand-400 text-xs font-semibold mb-3">
                <i class="fa-solid fa-crown text-xs"></i> System Administrator Control Center
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Selamat Datang, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>! 👋
            </h2>
            <p class="text-slate-400 text-sm mt-1 max-w-xl">
                Kelola seluruh konfigurasi user, area parkir, tarif, dan pantau log aktivitas sistem secara real-time.
            </p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <a href="user/create.php" class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-slate-950 font-bold px-5 py-3 rounded-xl text-sm transition-all duration-200 shadow-lg shadow-brand-500/20 flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i>
                <span>+ Tambah User Baru</span>
            </a>
        </div>
    </div>
</div>

<!-- Metrics Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    
    <!-- Stat 1: Total Users -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-5 shadow-xl hover:border-slate-700 transition duration-200">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total User</span>
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                <i class="fa-solid fa-users text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($totalUser) ?></p>
        <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
            <span class="text-emerald-400 font-semibold"><i class="fa-solid fa-circle-check"></i> Aktif</span> Terdaftar di sistem
        </p>
    </div>

    <!-- Stat 2: Active Parking Areas -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-5 shadow-xl hover:border-slate-700 transition duration-200">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Area Parkir</span>
            <div class="w-10 h-10 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-400">
                <i class="fa-solid fa-square-parking text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($totalArea) ?> <span class="text-sm font-normal text-slate-400">Lokasi</span></p>
        <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
            <span class="text-brand-400 font-semibold"><i class="fa-solid fa-layer-group"></i> Total Area</span> Operasional
        </p>
    </div>

    <!-- Stat 3: Total Vehicles -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-5 shadow-xl hover:border-slate-700 transition duration-200">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Kendaraan Terdaftar</span>
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                <i class="fa-solid fa-car text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($totalKendaraan) ?></p>
        <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
            <span class="text-purple-400 font-semibold"><i class="fa-solid fa-database"></i> Master Data</span> Kendaraan
        </p>
    </div>

    <!-- Stat 4: Parked Vehicles Now -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-5 shadow-xl hover:border-slate-700 transition duration-200">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sedang Parkir</span>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <i class="fa-solid fa-square-parking text-lg"></i>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-emerald-400 tracking-tight"><?= number_format($kendaraanParkir) ?> <span class="text-sm font-normal text-slate-400">Unit</span></p>
        <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span>Real-time Occupancy</span>
        </p>
    </div>

</div>

<!-- Area Slot Capacity Visualizer & Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    
    <!-- Left Column: Area Slot Capacity Gauge -->
    <div class="lg:col-span-2 bg-slate-950/80 border border-slate-800 rounded-3xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-chart-simple text-brand-400"></i> Monitoring Kapasitas Slot Parkir
                </h3>
                <p class="text-xs text-slate-400 mt-1">Status keterisian slot per area lokasi secara visual</p>
            </div>
            <a href="area/index.php" class="text-xs text-brand-400 hover:text-brand-300 font-semibold flex items-center gap-1">
                Kelola Area <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="space-y-5">
            <?php if (empty($daftarArea)): ?>
                <div class="text-center py-8 text-slate-500 text-sm">Belum ada area parkir yang didaftarkan.</div>
            <?php else: foreach ($daftarArea as $area): 
                $kapasitas = (int) $area['kapasitas'];
                $terisi = (int) $area['terisi'];
                $persen = $kapasitas > 0 ? round(($terisi / $kapasitas) * 100) : 0;
                
                // Color badge based on percentage
                if ($persen >= 90) {
                    $statusBg = 'bg-red-500/20 text-red-400 border-red-500/30';
                    $barBg = 'bg-gradient-to-r from-red-500 to-rose-600';
                    $statusText = 'Penuh';
                } elseif ($persen >= 70) {
                    $statusBg = 'bg-amber-500/20 text-amber-400 border-amber-500/30';
                    $barBg = 'bg-gradient-to-r from-amber-500 to-brand-500';
                    $statusText = 'Hampir Penuh';
                } else {
                    $statusBg = 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30';
                    $barBg = 'bg-gradient-to-r from-emerald-500 to-teal-400';
                    $statusText = 'Tersedia';
                }
            ?>
                <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-300 text-sm">
                                <i class="fa-solid fa-location-dot text-brand-400"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-sm"><?= htmlspecialchars($area['nama_area']) ?></h4>
                                <span class="text-[10px] px-2 py-0.5 rounded-full border font-semibold <?= $statusBg ?>"><?= $statusText ?></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-extrabold text-white"><?= $terisi ?> / <?= $kapasitas ?> <span class="text-xs font-normal text-slate-400">Slot</span></p>
                            <p class="text-xs font-semibold text-slate-400"><?= $persen ?>% Terisi</p>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-950 h-2.5 rounded-full overflow-hidden border border-slate-800/80 mt-3">
                        <div class="h-full rounded-full transition-all duration-500 <?= $barBg ?>" style="width: <?= min(100, $persen) ?>%"></div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Right Column: Quick Action Grid -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 shadow-xl flex flex-col justify-between">
        <div>
            <h3 class="text-lg font-bold text-white flex items-center gap-2 mb-1">
                <i class="fa-solid fa-rocket text-brand-400"></i> Akses Cepat
            </h3>
            <p class="text-xs text-slate-400 mb-6">Pintasan menu manajemen sistem</p>

            <div class="grid grid-cols-1 gap-3">
                <a href="user/index.php" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-brand-500/50 hover:bg-slate-800/60 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-users-gear"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white group-hover:text-brand-400 transition">Manajemen User</p>
                            <p class="text-[11px] text-slate-400">Tambah / Edit akun user</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-500 group-hover:text-brand-400 transition"></i>
                </a>

                <a href="tarif/index.php" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-brand-500/50 hover:bg-slate-800/60 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white group-hover:text-brand-400 transition">Tarif Parkir</p>
                            <p class="text-[11px] text-slate-400">Atur skema tarif parkir</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-500 group-hover:text-brand-400 transition"></i>
                </a>

                <a href="area/index.php" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-brand-500/50 hover:bg-slate-800/60 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-500/10 text-brand-400 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-square-parking"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white group-hover:text-brand-400 transition">Area Parkir</p>
                            <p class="text-[11px] text-slate-400">Atur batas lokasi &amp; slot</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-500 group-hover:text-brand-400 transition"></i>
                </a>

                <a href="kendaraan/index.php" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-brand-500/50 hover:bg-slate-800/60 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-car"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white group-hover:text-brand-400 transition">Kendaraan</p>
                            <p class="text-[11px] text-slate-400">Direktori seluruh plat nomor</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-500 group-hover:text-brand-400 transition"></i>
                </a>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-800 text-center">
            <a href="log_aktivitas.php" class="text-xs text-slate-400 hover:text-white font-medium flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-clock-rotate-left text-brand-400"></i> Lihat Log Aktivitas Lengkap
            </a>
        </div>
    </div>

</div>

<!-- Recent Activity Log Section -->
<div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 shadow-xl">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
        <div>
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-list-check text-brand-400"></i> Aktivitas Sistem Terbaru
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Catatan aktivitas pengguna dalam sistem</p>
        </div>
        <a href="log_aktivitas.php" class="text-xs bg-slate-900 hover:bg-slate-800 text-slate-300 px-3.5 py-2 rounded-xl border border-slate-800 font-semibold transition">
            Selengkapnya
        </a>
    </div>

    <div class="space-y-3">
        <?php if (empty($logsTerbaru)): ?>
            <div class="text-center py-8 text-slate-500 text-sm">Belum ada aktivitas tercatat.</div>
        <?php else: foreach ($logsTerbaru as $log): ?>
            <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800/80 hover:bg-slate-900 transition">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-brand-400 text-xs font-bold">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white">
                            <?= htmlspecialchars($log['nama_lengkap'] ?? 'System') ?> 
                            <span class="text-[10px] px-2 py-0.2 rounded-full bg-slate-800 text-slate-400 border border-slate-700 capitalize font-normal"><?= htmlspecialchars($log['role'] ?? 'system') ?></span>
                        </p>
                        <p class="text-xs text-slate-300 mt-0.5"><?= htmlspecialchars($log['aktivitas']) ?></p>
                    </div>
                </div>
                <span class="text-[11px] text-slate-400 font-medium shrink-0 ml-4">
                    <i class="fa-regular fa-clock text-slate-500"></i> <?= date('d M Y H:i', strtotime($log['waktu_aktivitas'])) ?>
                </span>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_admin.php'; ?>