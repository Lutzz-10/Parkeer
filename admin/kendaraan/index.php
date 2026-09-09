<?php
$judulHalaman = "Data Kendaraan";
require_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/kendaraan_functions.php';

$database = new Database();
$koneksi = $database->connect();
$keyword = trim($_GET['q'] ?? '');
$daftarKendaraan = getAllKendaraan($koneksi, $keyword);
?>

<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>"
                   placeholder="Cari plat/pemilik..."
                   class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-64">
            <button class="bg-slate-700 text-white px-4 rounded-lg text-sm">Cari</button>
        </form>
        <a href="create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">+ Tambah Kendaraan</a>
    </div>

    <?php if (isset($_GET['sukses'])): ?>
        <div class="bg-green-100 text-green-700 text-sm rounded-lg px-4 py-2 mb-4"><?= htmlspecialchars($_GET['sukses']) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['gagal'])): ?>
        <div class="bg-red-100 text-red-700 text-sm rounded-lg px-4 py-2 mb-4"><?= htmlspecialchars($_GET['gagal']) ?></div>
    <?php endif; ?>

    <table class="w-full text-sm text-left">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-4 py-2">Plat Nomor</th>
                <th class="px-4 py-2">Jenis</th>
                <th class="px-4 py-2">Warna</th>
                <th class="px-4 py-2">Pemilik</th>
                <th class="px-4 py-2">Dicatat Oleh</th>
                <th class="px-4 py-2 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if (empty($daftarKendaraan)): ?>
                <tr><td colspan="6" class="text-center py-6 text-slate-400">Belum ada data kendaraan.</td></tr>
            <?php else: foreach ($daftarKendaraan as $k): ?>
                <tr>
                    <td class="px-4 py-2 font-medium"><?= htmlspecialchars($k['plat_nomor']) ?></td>
                    <td class="px-4 py-2 capitalize"><?= htmlspecialchars($k['jenis_kendaraan']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($k['warna'] ?? '-') ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($k['pemilik'] ?? '-') ?></td>
                    <td class="px-4 py-2 text-slate-500"><?= htmlspecialchars($k['nama_pencatat']) ?></td>
                    <td class="px-4 py-2 text-right space-x-2">
                        <a href="edit.php?id=<?= $k['id_kendaraan'] ?>" class="text-blue-600 hover:underline">Edit</a>
                        <a href="delete.php?id=<?= $k['id_kendaraan'] ?>"
                           onclick="return confirm('Yakin hapus kendaraan ini?')"
                           class="text-red-600 hover:underline">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>