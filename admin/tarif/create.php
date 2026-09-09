<?php
$judulHalaman = "Tambah Tarif";
require_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/tarif_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';

$database = new Database();
$koneksi = $database->connect();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'jenis_kendaraan' => $_POST['jenis_kendaraan'],
        'tarif_per_jam' => (int) $_POST['tarif_per_jam'],
    ];

    if ($data['tarif_per_jam'] <= 0) {
        $error = 'Tarif harus lebih dari 0.';
    } elseif (jenisKendaraanSudahAdaTarif($koneksi, $data['jenis_kendaraan'])) {
        $error = 'Jenis kendaraan ini sudah punya tarif. Edit tarif yang ada saja.';
    } else {
        createTarif($koneksi, $data);
        catatLog($koneksi, $_SESSION['id_user'], "Menambahkan tarif: {$data['jenis_kendaraan']}");
        header("Location: index.php?sukses=Tarif berhasil ditambahkan");
        exit;
    }
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
                <option value="motor">Motor</option>
                <option value="mobil">Mobil</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Tarif per Jam (Rp)</label>
            <input type="number" name="tarif_per_jam" min="1" required
                   class="w-full border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <div class="flex gap-2 pt-2">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan</button>
            <a href="index.php" class="bg-slate-200 hover:bg-slate-300 px-4 py-2 rounded-lg">Batal</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>