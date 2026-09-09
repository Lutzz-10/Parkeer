<?php
$judulHalaman = "Catat Kendaraan Masuk";
require_once __DIR__ . '/../../includes/header_petugas.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/transaksi_functions.php';
require_once __DIR__ . '/../../functions/kendaraan_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';

$database = new Database();
$koneksi = $database->connect();
$error = '';
$kendaraanDitemukan = null;
$platDicari = trim($_POST['plat_nomor'] ?? $_GET['plat'] ?? '');

// Ambil daftar area untuk dropdown
$daftarArea = $koneksi->query(
    "SELECT * FROM tb_area_parkir WHERE (kapasitas - terisi) > 0 ORDER BY nama_area"
)->fetchAll();

// Langkah 1: cari kendaraan berdasarkan plat
if ($platDicari !== '') {
    $kendaraanDitemukan = cariKendaraanByPlat($koneksi, $platDicari);
}

// Langkah 2: jika submit tambah kendaraan baru sekaligus proses masuk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'daftar_baru') {
    $dataKendaraan = [
        'plat_nomor' => trim($_POST['plat_nomor']),
        'jenis_kendaraan' => $_POST['jenis_kendaraan'],
        'warna' => trim($_POST['warna']),
        'pemilik' => trim($_POST['pemilik']),
    ];
    if (platNomorSudahAda($koneksi, $dataKendaraan['plat_nomor'])) {
        $error = 'Plat nomor sudah terdaftar, silakan cari ulang.';
    } else {
        createKendaraan($koneksi, $dataKendaraan, $_SESSION['id_user']);
        $kendaraanDitemukan = cariKendaraanByPlat($koneksi, $dataKendaraan['plat_nomor']);
    }
}

// Langkah 3: proses transaksi masuk (kendaraan sudah pasti ada)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'proses_masuk') {
    $idKendaraan = (int) $_POST['id_kendaraan'];
    $idArea = (int) $_POST['id_area'];

    $kendaraanRow = $koneksi->prepare("SELECT jenis_kendaraan FROM tb_kendaraan WHERE id_kendaraan = :id");
    $kendaraanRow->execute([':id' => $idKendaraan]);
    $jenis = $kendaraanRow->fetchColumn();

    $tarif = getTarifByJenis($koneksi, $jenis);
    if (!$tarif) {
        $error = "Tarif untuk jenis kendaraan '$jenis' belum diatur. Hubungi admin.";
    } else {
        $hasil = prosesKendaraanMasuk($koneksi, $idKendaraan, $idArea, $tarif['id_tarif'], $_SESSION['id_user']);
        if ($hasil['sukses']) {
            catatLog($koneksi, $_SESSION['id_user'], "Mencatat kendaraan masuk (id_parkir: {$hasil['id_parkir']})");
            header("Location: index.php?sukses=Kendaraan berhasil dicatat masuk");
            exit;
        } else {
            $error = $hasil['pesan'];
        }
    }
}
?>

<div class="bg-white rounded-xl shadow p-6 max-w-lg">

    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 text-sm rounded-lg px-4 py-2 mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Form cari plat nomor -->
    <form method="POST" class="flex gap-2 mb-6">
        <input type="text" name="plat_nomor" value="<?= htmlspecialchars($platDicari) ?>"
               placeholder="Masukkan plat nomor..." required
               class="flex-1 border border-slate-300 rounded-lg px-3 py-2 uppercase">
        <button class="bg-slate-700 text-white px-4 rounded-lg text-sm">Cari</button>
    </form>

    <?php if ($platDicari !== '' && !$kendaraanDitemukan): ?>
        <!-- Kendaraan belum terdaftar -> form tambah cepat -->
        <div class="bg-amber-50 text-amber-700 text-sm rounded-lg px-4 py-2 mb-4">
            Kendaraan dengan plat "<?= htmlspecialchars($platDicari) ?>" belum terdaftar. Isi data di bawah untuk mendaftarkan sekaligus.
        </div>
        <form method="POST" class="space-y-3">
            <input type="hidden" name="aksi" value="daftar_baru">
            <input type="hidden" name="plat_nomor" value="<?= htmlspecialchars($platDicari) ?>">
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
                <input type="text" name="warna" class="w-full border border-slate-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Nama Pemilik</label>
                <input type="text" name="pemilik" class="w-full border border-slate-300 rounded-lg px-3 py-2">
            </div>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Daftarkan &amp; Lanjutkan</button>
        </form>

    <?php elseif ($kendaraanDitemukan): ?>
        <!-- Kendaraan ditemukan -> pilih area lalu proses masuk -->
        <div class="bg-green-50 rounded-lg p-4 mb-4 text-sm">
            <p><b>Plat:</b> <?= htmlspecialchars($kendaraanDitemukan['plat_nomor']) ?></p>
            <p><b>Jenis:</b> <?= htmlspecialchars($kendaraanDitemukan['jenis_kendaraan']) ?></p>
            <p><b>Pemilik:</b> <?= htmlspecialchars($kendaraanDitemukan['pemilik'] ?? '-') ?></p>
        </div>
        <form method="POST" class="space-y-3">
            <input type="hidden" name="aksi" value="proses_masuk">
            <input type="hidden" name="id_kendaraan" value="<?= $kendaraanDitemukan['id_kendaraan'] ?>">
            <div>
                <label class="block text-sm font-medium mb-1">Pilih Area Parkir</label>
                <select name="id_area" required class="w-full border border-slate-300 rounded-lg px-3 py-2">
                    <?php if (empty($daftarArea)): ?>
                        <option value="">-- Semua area penuh --</option>
                    <?php else: foreach ($daftarArea as $area): ?>
                        <option value="<?= $area['id_area'] ?>">
                            <?= htmlspecialchars($area['nama_area']) ?> (sisa <?= $area['kapasitas'] - $area['terisi'] ?> slot)
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg" <?= empty($daftarArea) ? 'disabled' : '' ?>>
                Proses Masuk
            </button>
        </form>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../../includes/footer_petugas.php'; ?>