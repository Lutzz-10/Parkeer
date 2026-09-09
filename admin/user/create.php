<?php
$judulHalaman = "Tambah User";
require_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/user_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';

$database = new Database();
$koneksi = $database->connect();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_lengkap' => trim($_POST['nama_lengkap']),
        'username' => trim($_POST['username']),
        'password' => $_POST['password'],
        'role' => $_POST['role'],
        'status_aktif' => isset($_POST['status_aktif']) ? 1 : 0,
    ];

    if ($data['nama_lengkap'] === '' || $data['username'] === '' || $data['password'] === '') {
        $error = 'Semua field wajib diisi.';
    } elseif (usernameSudahAda($koneksi, $data['username'])) {
        $error = 'Username sudah digunakan, pilih username lain.';
    } else {
        createUser($koneksi, $data);
        catatLog($koneksi, $_SESSION['id_user'], "Menambahkan user baru: {$data['username']}");
        header("Location: index.php?sukses=User berhasil ditambahkan");
        exit;
    }
}
?>

<div class="bg-white rounded-xl shadow p-6 max-w-lg">
    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 text-sm rounded-lg px-4 py-2 mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>" required
                   class="w-full border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required
                   class="w-full border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Role</label>
            <select name="role" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                <option value="admin">Admin</option>
                <option value="petugas">Petugas</option>
                <option value="owner">Owner</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="status_aktif" id="status_aktif" checked>
            <label for="status_aktif" class="text-sm">Aktifkan user</label>
        </div>
        <div class="flex gap-2 pt-2">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan</button>
            <a href="index.php" class="bg-slate-200 hover:bg-slate-300 px-4 py-2 rounded-lg">Batal</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>