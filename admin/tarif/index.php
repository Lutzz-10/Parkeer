<?php
$judulHalaman = "Tarif Parkir";
require_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/tarif_functions.php';

$database = new Database();
$koneksi = $database->connect();
$daftarTarif = getAllTarif($koneksi);
?>

<div class="bg-white rounded-xl shadow p-6">

    <div class="flex justify-between items-center mb-4">
        <p class="text-sm text-slate-500">Total: <?= count($daftarTarif) ?> tarif</p>
        <a href="create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">+ Tambah Tarif</a>
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
                <th class="px-4 py-2">Jenis Kendaraan</th>
                <th class="px-4 py-2">Tarif/Jam</th>
                <th class="px-4 py-2 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if (empty($daftarTarif)): ?>
                <tr><td colspan="3" class="text-center py-6 text-slate-400">Belum ada tarif.</td></tr>
            <?php else: foreach ($daftarTarif as $tarif): ?>
                <tr>
                    <td class="px-4 py-2 capitalize"><?= htmlspecialchars($tarif['jenis_kendaraan']) ?></td>
                    <td class="px-4 py-2">Rp <?= number_format($tarif['tarif_per_jam'], 0, ',', '.') ?></td>
                    <td class="px-4 py-2 text-right space-x-2">
                        <a href="edit.php?id=<?= $tarif['id_tarif'] ?>" class="text-blue-600 hover:underline">Edit</a>
                        <a href="delete.php?id=<?= $tarif['id_tarif'] ?>"
                           onclick="return confirm('Yakin hapus tarif ini?')"
                           class="text-red-600 hover:underline">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>