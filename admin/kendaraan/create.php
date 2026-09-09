<?php
$judulHalaman = "Tambah Kendaraan";
require_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/kendaraan_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';

$database = new Database();
$koneksi = $database->connect();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'plat_nomor' => trim($_POST['plat_nomor']),
        'jenis_kendaraan' => $_POST['jenis_kendaraan'],
        'warna' => trim($_POST['warna']),
        'pemilik' => trim($_POST['pemilik']),
    ];

    if ($data['plat_nomor'] === '') {
        $error = 'Plat nomor wajib diisi.';
    } elseif (platNomorSudahAda($koneksi, $data['plat_nomor'])) {
        $error = 'Plat nomor ini sudah terdaftar.';
    } else {
        createKendaraan($koneksi, $data, $_SESSION['id_user']);
        catatLog($koneksi, $_SESSION['id_user'], "Menambahkan kendaraan: {$data['plat_nomor']}");
        header("Location: index.php?sukses=Kendaraan berhasil ditambahkan");
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
            <label class="block text-sm font-medium mb-1">Plat Nomor</label>
            <input type="text" name="plat_nomor" style="text-transform:uppercase"
                   value="<?= htmlspecialchars($_POST['plat_nomor'] ?? '') ?>" required
                   class="w-full border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Jenis Kendaraan</label>
            <select name="jenis_kendaraan" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                <option value="motor">Motor</option>
                <option value="mobil">Mobil</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Warna</label>
            <input type="text" name="warna" value="<?= htmlspecialchars($_POST['warna'] ?? '') ?>"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nama Pemilik</label>
            <input type="text" name="pemilik" value="<?= htmlspecialchars($_POST['pemilik'] ?? '') ?>"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <div class="flex gap-2 pt-2">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan</button>
            <a href="index.php" class="bg-slate-200 hover:bg-slate-300 px-4 py-2 rounded-lg">Batal</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>