<?php
$judulHalaman = "Edit Area";
require_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/area_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';

$database = new Database();
$koneksi = $database->connect();
$error = '';

$id = (int) ($_GET['id'] ?? 0);
$area = getAreaById($koneksi, $id);
if (!$area) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_area' => trim($_POST['nama_area']),
        'kapasitas' => (int) $_POST['kapasitas'],
    ];

    if ($data['nama_area'] === '' || $data['kapasitas'] <= 0) {
        $error = 'Nama area wajib diisi dan kapasitas harus lebih dari 0.';
    } elseif ($data['kapasitas'] < $area['terisi']) {
        $error = "Kapasitas tidak boleh kurang dari jumlah slot yang sedang terisi ({$area['terisi']}).";
    } else {
        updateArea($koneksi, $id, $data);
        catatLog($koneksi, $_SESSION['id_user'], "Mengubah area: {$data['nama_area']}");
        header("Location: index.php?sukses=Area berhasil diperbarui");
        exit;
    }
    $area = array_merge($area, $data);
}
?>

<div class="bg-white rounded-xl shadow p-6 max-w-md">
    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 text-sm rounded-lg px-4 py-2 mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Nama Area</label>
            <input type="text" name="nama_area" value="<?= htmlspecialchars($area['nama_area']) ?>" required
                   class="w-full border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Kapasitas</label>
            <input type="number" name="kapasitas" value="<?= $area['kapasitas'] ?>" min="1" required
                   class="w-full border border-slate-300 rounded-lg px-3 py-2">
            <p class="text-xs text-slate-400 mt-1">Slot terisi saat ini: <?= $area['terisi'] ?></p>
        </div>
        <div class="flex gap-2 pt-2">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan Perubahan</button>
            <a href="index.php" class="bg-slate-200 hover:bg-slate-300 px-4 py-2 rounded-lg">Batal</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>