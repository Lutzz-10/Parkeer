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

<div class="bg-white rounded-xl shadow p-6">
    <form method="GET" class="mb-4">
        <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Cari plat nomor..."
               class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-64">
        <button class="bg-slate-700 text-white px-4 py-2 rounded-lg text-sm">Cari</button>
    </form>

    <?php if (isset($_GET['sukses'])): ?>
        <div class="bg-green-100 text-green-700 text-sm rounded-lg px-4 py-2 mb-4"><?= htmlspecialchars($_GET['sukses']) ?></div>
    <?php endif; ?>

    <table class="w-full text-sm text-left">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-4 py-2">Plat Nomor</th>
                <th class="px-4 py-2">Jenis</th>
                <th class="px-4 py-2">Area</th>
                <th class="px-4 py-2">Waktu Masuk</th>
                <th class="px-4 py-2 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if (empty($daftar)): ?>
                <tr><td colspan="5" class="text-center py-6 text-slate-400">Tidak ada kendaraan yang sedang parkir.</td></tr>
            <?php else: foreach ($daftar as $t): ?>
                <tr>
                    <td class="px-4 py-2 font-medium"><?= htmlspecialchars($t['plat_nomor']) ?></td>
                    <td class="px-4 py-2 capitalize"><?= htmlspecialchars($t['jenis_kendaraan']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($t['nama_area']) ?></td>
                    <td class="px-4 py-2"><?= date('d M Y H:i', strtotime($t['waktu_masuk'])) ?></td>
                    <td class="px-4 py-2 text-right">
                        <a href="keluar.php?id=<?= $t['id_parkir'] ?>"
                           class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-lg text-xs">
                            Proses Keluar
                        </a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer_petugas.php'; ?>