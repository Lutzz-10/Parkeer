<?php
$judulHalaman = "Edit User";
require_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/user_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';

$database = new Database();
$koneksi = $database->connect();
$error = '';

$id = (int) ($_GET['id'] ?? 0);
$user = getUserById($koneksi, $id);

if (!$user) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_lengkap' => trim($_POST['nama_lengkap']),
        'username' => trim($_POST['username']),
        'password' => $_POST['password'], // boleh kosong = tidak ganti password
        'role' => $_POST['role'],
        'status_aktif' => isset($_POST['status_aktif']) ? 1 : 0,
    ];

    if ($data['nama_lengkap'] === '' || $data['username'] === '') {
        $error = 'Nama dan username wajib diisi.';
    } elseif (usernameSudahAda($koneksi, $data['username'], $id)) {
        $error = 'Username sudah digunakan user lain.';
    } else {
        updateUser($koneksi, $id, $data);
        catatLog($koneksi, $_SESSION['id_user'], "Mengubah data user: {$data['username']}");
        header("Location: index.php?sukses=User berhasil diperbarui");
        exit;
    }
    $user = array_merge($user, $data); // supaya form tetap terisi walau gagal
}
?>

<div class="bg-white rounded-xl shadow p-6 max-w-lg">
    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 text-sm rounded-lg px-4 py-2 mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required
                   class="w-full border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required
                   class="w-full border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Password Baru <span class="text-slate-400">(kosongkan jika tidak diubah)</span></label>
            <input type="password" name="password"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Role</label>
            <select name="role" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                <?php foreach (['admin', 'petugas', 'owner'] as $r): ?>
                    <option value="<?= $r ?>" <?= $user['role'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="status_aktif" id="status_aktif" <?= $user['status_aktif'] ? 'checked' : '' ?>>
            <label for="status_aktif" class="text-sm">Aktifkan user</label>
        </div>
        <div class="flex gap-2 pt-2">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan Perubahan</button>
            <a href="index.php" class="bg-slate-200 hover:bg-slate-300 px-4 py-2 rounded-lg">Batal</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>