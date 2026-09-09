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

<div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <form method="GET" class="flex items-center gap-2">
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>"
                       placeholder="Cari nama atau username..."
                       class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-9 pr-3 py-2.5 text-sm placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
            </div>
            <button class="bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold px-4 py-2.5 rounded-xl text-sm transition">
                Cari
            </button>
            <?php if ($keyword !== ''): ?>
                <a href="index.php" class="text-xs text-slate-400 hover:text-white px-2 py-1">Reset</a>
            <?php endif; ?>
        </form>

        <a href="create.php" class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-slate-950 font-bold px-4 py-2.5 rounded-xl text-sm transition shadow-lg shadow-brand-500/20 flex items-center gap-2 self-start sm:self-auto">
            <i class="fa-solid fa-user-plus"></i>
            <span>+ Tambah User</span>
        </a>
    </div>

    <!-- Alert Sukses -->
    <?php if (isset($_GET['sukses'])): ?>
        <div class="flex items-center gap-2.5 bg-emerald-950/80 text-emerald-300 text-sm rounded-2xl px-4 py-3 mb-6 border border-emerald-800/60 shadow-lg">
            <i class="fa-solid fa-circle-check text-emerald-400"></i>
            <span><?= htmlspecialchars($_GET['sukses']) ?></span>
        </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-900/90 text-slate-400 text-xs uppercase tracking-wider font-semibold">
                <tr>
                    <th class="px-5 py-3.5">Pengguna</th>
                    <th class="px-5 py-3.5">Username</th>
                    <th class="px-5 py-3.5">Role</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                <?php if (empty($daftarUser)): ?>
                    <tr><td colspan="5" class="text-center py-8 text-slate-500">Tidak ada data user ditemukan.</td></tr>
                <?php else: foreach ($daftarUser as $user): ?>
                    <tr class="hover:bg-slate-900/50 transition">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-brand-400 font-bold text-xs uppercase">
                                    <?= substr($user['nama_lengkap'], 0, 2) ?>
                                </div>
                                <span class="font-bold text-white"><?= htmlspecialchars($user['nama_lengkap']) ?></span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-slate-300 font-mono text-xs">@<?= htmlspecialchars($user['username']) ?></td>
                        <td class="px-5 py-3.5">
                            <?php
                            $roleStyle = match($user['role']) {
                                'admin' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                'petugas' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                'owner' => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                                default => 'bg-slate-800 text-slate-300 border-slate-700'
                            };
                            ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold border uppercase tracking-wider <?= $roleStyle ?>">
                                <?= htmlspecialchars($user['role']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <?php if ($user['status_aktif']): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-950 text-emerald-400 border border-emerald-800/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Aktif
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-950 text-red-400 border border-red-800/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Nonaktif
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3.5 text-right space-x-2">
                            <a href="edit.php?id=<?= $user['id_user'] ?>" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium border border-slate-700 transition">
                                <i class="fa-solid fa-pen-to-square text-brand-400"></i> Edit
                            </a>
                            <a href="delete.php?id=<?= $user['id_user'] ?>"
                               onclick="return confirm('Yakin hapus user ini? Kendaraan & log terkait ikut terhapus.')"
                               class="px-3 py-1 rounded-lg bg-red-950/60 hover:bg-red-900 text-red-300 text-xs font-medium border border-red-800/60 transition">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalHalaman > 1): ?>
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-800/80">
            <span class="text-xs text-slate-400">Menampilkan total <b><?= $totalUser ?></b> user</span>
            <div class="flex gap-1.5">
                <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
                    <a href="?halaman=<?= $i ?>&q=<?= urlencode($keyword) ?>"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition <?= $i == $halaman ? 'bg-brand-500 text-slate-950 shadow-md shadow-brand-500/20' : 'bg-slate-900 text-slate-400 hover:bg-slate-800 hover:text-white border border-slate-800' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>