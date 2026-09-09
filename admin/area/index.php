<?php
$judulHalaman = "Area Parkir";
require_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/area_functions.php';

$database = new Database();
$koneksi = $database->connect();
$daftarArea = getAllArea($koneksi);
?>

<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <p class="text-sm text-slate-500">Total: <?= count($daftarArea) ?> area</p>
        <a href="create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">+ Tambah Area</a>
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
                <th class="px-4 py-2">Nama Area</th>
                <th class="px-4 py-2">Kapasitas</th>
                <th class="px-4 py-2">Terisi</th>
                <th class="px-4 py-2">Sisa Slot</th>
                <th class="px-4 py-2 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if (empty($daftarArea)): ?>
                <tr><td colspan="5" class="text-center py-6 text-slate-400">Belum ada area.</td></tr>
            <?php else: foreach ($daftarArea as $area): $sisa = $area['kapasitas'] - $area['terisi']; ?>
                <tr>
                    <td class="px-4 py-2"><?= htmlspecialchars($area['nama_area']) ?></td>
                    <td class="px-4 py-2"><?= $area['kapasitas'] ?></td>
                    <td class="px-4 py-2"><?= $area['terisi'] ?></td>
                    <td class="px-4 py-2">
                        <span class="<?= $sisa <= 0 ? 'text-red-600' : 'text-green-600' ?> font-medium"><?= $sisa ?></span>
                    </td>
                    <td class="px-4 py-2 text-right space-x-2">
                        <a href="edit.php?id=<?= $area['id_area'] ?>" class="text-blue-600 hover:underline">Edit</a>
                        <a href="delete.php?id=<?= $area['id_area'] ?>"
                           onclick="return confirm('Yakin hapus area ini?')"
                           class="text-red-600 hover:underline">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>