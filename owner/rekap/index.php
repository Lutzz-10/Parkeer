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

<!-- Filter Tanggal -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-sm font-medium mb-1">Dari Tanggal</label>
            <input type="date" name="dari" value="<?= htmlspecialchars($dari) ?>" class="border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Sampai Tanggal</label>
            <input type="date" name="sampai" value="<?= htmlspecialchars($sampai) ?>" class="border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Tampilkan</button>
        <a href="export.php?dari=<?= $dari ?>&sampai=<?= $sampai ?>"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
            Export CSV
        </a>
    </form>
</div>

<!-- Ringkasan -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-sm text-slate-500">Total Transaksi</p>
        <p class="text-2xl font-bold text-slate-800"><?= $ringkasan['total_transaksi'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-sm text-slate-500">Total Pendapatan</p>
        <p class="text-2xl font-bold text-green-600">Rp <?= number_format($ringkasan['total_pendapatan'], 0, ',', '.') ?></p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-sm text-slate-500">Rata-rata / Transaksi</p>
        <p class="text-2xl font-bold text-slate-800">Rp <?= number_format($ringkasan['rata_rata'], 0, ',', '.') ?></p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-sm text-slate-500">Rata-rata Durasi</p>
        <p class="text-2xl font-bold text-slate-800"><?= number_format($ringkasan['rata_rata_durasi'], 1) ?> jam</p>
    </div>
</div>

<!-- Grafik Tren Harian -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <p class="font-medium mb-3">Tren Pendapatan Harian</p>
    <?php if (empty($perHari)): ?>
        <p class="text-sm text-slate-400 text-center py-8">Tidak ada data pada rentang ini.</p>
    <?php else: ?>
        <canvas id="chartHarian" height="80"></canvas>
    <?php endif; ?>
</div>

<!-- Breakdown per Jenis Kendaraan -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <p class="font-medium mb-3">Rekap per Jenis Kendaraan</p>
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-4 py-2">Jenis Kendaraan</th>
                <th class="px-4 py-2">Jumlah Transaksi</th>
                <th class="px-4 py-2">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if (empty($perJenis)): ?>
                <tr><td colspan="3" class="text-center py-6 text-slate-400">Tidak ada data.</td></tr>
            <?php else: foreach ($perJenis as $p): ?>
                <tr>
                    <td class="px-4 py-2 capitalize"><?= htmlspecialchars($p['jenis_kendaraan']) ?></td>
                    <td class="px-4 py-2"><?= $p['jumlah_transaksi'] ?></td>
                    <td class="px-4 py-2">Rp <?= number_format($p['total_pendapatan'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Detail Transaksi -->
<div class="bg-white rounded-xl shadow p-6">
    <p class="font-medium mb-3">Detail Transaksi <span class="text-xs text-slate-400 font-normal">(maks. 100 baris terbaru)</span></p>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="px-4 py-2">Plat</th>
                    <th class="px-4 py-2">Jenis</th>
                    <th class="px-4 py-2">Area</th>
                    <th class="px-4 py-2">Petugas</th>
                    <th class="px-4 py-2">Masuk</th>
                    <th class="px-4 py-2">Keluar</th>
                    <th class="px-4 py-2">Durasi</th>
                    <th class="px-4 py-2">Biaya</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($detail)): ?>
                    <tr><td colspan="8" class="text-center py-6 text-slate-400">Tidak ada transaksi.</td></tr>
                <?php else: foreach ($detail as $d): ?>
                    <tr>
                        <td class="px-4 py-2 font-medium"><?= htmlspecialchars($d['plat_nomor']) ?></td>
                        <td class="px-4 py-2 capitalize"><?= htmlspecialchars($d['jenis_kendaraan']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($d['nama_area']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($d['nama_petugas']) ?></td>
                        <td class="px-4 py-2"><?= date('d/m H:i', strtotime($d['waktu_masuk'])) ?></td>
                        <td class="px-4 py-2"><?= date('d/m H:i', strtotime($d['waktu_keluar'])) ?></td>
                        <td class="px-4 py-2"><?= $d['durasi_jam'] ?> jam</td>
                        <td class="px-4 py-2">Rp <?= number_format($d['biaya_total'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($perHari)): ?>
<script>
new Chart(document.getElementById('chartHarian'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labelHarian) ?>,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: <?= json_encode($dataHarian) ?>,
            backgroundColor: '#2563eb'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer_owner.php'; ?>