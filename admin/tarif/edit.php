<?php
$judulHalaman = "Edit Tarif";
require_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/tarif_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';

$database = new Database();
$koneksi = $database->connect();
$error = '';

$id = (int) ($_GET['id'] ?? 0);
$tarif = getTarifById($koneksi, $id);
if (!$tarif) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'jenis_kendaraan' => $_POST['jenis_kendaraan'],
        'tarif_per_jam' => (int) $_POST['tarif_per_jam'],
    ];

    if ($data['tarif_per_jam'] <= 0) {
        $error = 'Tarif harus lebih dari 0.';
    } elseif (jenisKendaraanSudahAdaTarif($koneksi, $data['jenis_kendaraan'], $id)) {
        $error = 'Jenis kendaraan ini sudah punya tarif lain.';
    } else {
        updateTarif($koneksi, $id, $data);
        catatLog($koneksi, $_SESSION['id_user'], "Mengubah tarif: {$data['jenis_kendaraan']}");
        header("Location: index.php?sukses=Tarif berhasil diperbarui");
        exit;
    }
    $tarif = array_merge($tarif, $data);
}
?>

<div class="bg-white rounded-xl shadow p-6 max-w-md">
    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 text-sm rounded-lg px-4 py-2 mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Jenis Kendaraan</label>
            <select name="jenis_kendaraan" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                <?php foreach (['motor', 'mobil', 'lainnya'] as $j): ?>
                    <option value="<?= $j ?>" <?= $tarif['jenis_kendaraan'] === $j ? 'selected' : '' ?>><?= ucfirst($j) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Tarif per Jam (Rp)</label>
            <input type="number" name="tarif_per_jam" value="<?= $tarif['tarif_per_jam'] ?>" min="1" required
                   class="w-full border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <div class="flex gap-2 pt-2">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan Perubahan</button>
            <a href="index.php" class="bg-slate-200 hover:bg-slate-300 px-4 py-2 rounded-lg">Batal</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>