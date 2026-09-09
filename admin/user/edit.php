<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/user_functions.php';
require_once __DIR__ . '/../../functions/log_functions.php';
require_once __DIR__ . '/../../includes/auth.php';
cekRole(['admin']);

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
    $user = array_merge($user, $data);
}

$judulHalaman = "Edit Data User";
require_once __DIR__ . '/../../includes/header_admin.php';
?>

<div class="max-w-2xl mx-auto">
    
    <div class="mb-6 flex items-center justify-between">
        <a href="index.php" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar user
        </a>
    </div>

    <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
        <div class="mb-6 pb-4 border-b border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-500/10 border border-brand-500/30 flex items-center justify-center text-brand-400 text-lg">
                <i class="fa-solid fa-user-pen"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">Edit Data User</h2>
                <p class="text-xs text-slate-400">Perbarui profil, username, password, atau status akun</p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="flex items-center gap-3 bg-red-950/80 text-red-300 text-sm rounded-2xl px-4 py-3 mb-6 border border-red-800/60 shadow-lg">
                <i class="fa-solid fa-triangle-exclamation text-red-400"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Nama Lengkap</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-id-card text-xs"></i>
                    </div>
                    <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required
                           class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-user-circle text-xs"></i>
                    </div>
                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required
                           class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Password Baru <span class="text-slate-500 font-normal lowercase">(kosongkan jika tidak ingin mengubah password)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-lock text-xs"></i>
                    </div>
                    <input type="password" name="password" placeholder="Isi jika ingin ganti password"
                           class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Role Akses</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-shield-halved text-xs"></i>
                    </div>
                    <select name="role" class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition appearance-none">
                        <?php foreach (['admin' => 'Admin System', 'petugas' => 'Petugas Gate Parkir', 'owner' => 'Owner (Laporan & Rekap)'] as $roleVal => $roleLabel): ?>
                            <option value="<?= $roleVal ?>" <?= $user['role'] === $roleVal ? 'selected' : '' ?>><?= $roleLabel ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 p-4 rounded-2xl bg-slate-900/60 border border-slate-800">
                <input type="checkbox" name="status_aktif" id="status_aktif" <?= $user['status_aktif'] ? 'checked' : '' ?>
                       class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-brand-500 focus:ring-brand-500">
                <label for="status_aktif" class="text-xs font-semibold text-slate-200 cursor-pointer">
                    Status Akun Aktif
                </label>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-800">
                <button type="submit" class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-slate-950 font-bold px-6 py-3 rounded-xl text-sm transition shadow-lg shadow-brand-500/20">
                    Simpan Perubahan
                </button>
                <a href="index.php" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold px-5 py-3 rounded-xl text-sm transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>