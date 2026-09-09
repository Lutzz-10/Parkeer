<?php
$judulHalaman = "Manajemen User";
require_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/user_functions.php';

$database = new Database();
$koneksi = $database->connect();

$keyword = trim($_GET['q'] ?? '');
$halaman = max(1, (int)($_GET['halaman'] ?? 1));
$limit = 10;
$offset = ($halaman - 1) * $limit;

$daftarUser = getAllUsers($koneksi, $keyword, $limit, $offset);
$totalUser = countAllUsers($koneksi, $keyword);
$totalHalaman = (int) ceil($totalUser / $limit);
?>

<div class="bg-white rounded-xl shadow p-6">

    <div class="flex justify-between items-center mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>"
                   placeholder="Cari nama/username..."
                   class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-64">
            <button class="bg-slate-700 text-white px-4 rounded-lg text-sm">Cari</button>
        </form>
        <a href="create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
            + Tambah User
        </a>
    </div>

    <?php if (isset($_GET['sukses'])): ?>
        <div class="bg-green-100 text-green-700 text-sm rounded-lg px-4 py-2 mb-4">
            <?= htmlspecialchars($_GET['sukses']) ?>
        </div>
    <?php endif; ?>

    <table class="w-full text-sm text-left">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-4 py-2">Nama Lengkap</th>
                <th class="px-4 py-2">Username</th>
                <th class="px-4 py-2">Role</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if (empty($daftarUser)): ?>
                <tr><td colspan="5" class="text-center py-6 text-slate-400">Tidak ada data user.</td></tr>
            <?php else: foreach ($daftarUser as $user): ?>
                <tr>
                    <td class="px-4 py-2"><?= htmlspecialchars($user['nama_lengkap']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($user['username']) ?></td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 rounded-full text-xs bg-slate-200 capitalize"><?= $user['role'] ?></span>
                    </td>
                    <td class="px-4 py-2">
                        <?php if ($user['status_aktif']): ?>
                            <span class="text-green-600 text-xs font-medium">Aktif</span>
                        <?php else: ?>
                            <span class="text-red-500 text-xs font-medium">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-2 text-right space-x-2">
                        <a href="edit.php?id=<?= $user['id_user'] ?>" class="text-blue-600 hover:underline">Edit</a>
                        <a href="delete.php?id=<?= $user['id_user'] ?>"
                           onclick="return confirm('Yakin hapus user ini? Kendaraan & log terkait ikut terhapus.')"
                           class="text-red-600 hover:underline">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($totalHalaman > 1): ?>
        <div class="flex gap-2 mt-4">
            <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
                <a href="?halaman=<?= $i ?>&q=<?= urlencode($keyword) ?>"
                   class="px-3 py-1 rounded-lg text-sm <?= $i == $halaman ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>