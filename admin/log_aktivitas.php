<?php
$judulHalaman = "Log Aktivitas Sistem";
require_once __DIR__ . '/../includes/header_admin.php';
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$koneksi = $database->connect();

$halaman = max(1, (int)($_GET['halaman'] ?? 1));
$limit = 20;
$offset = ($halaman - 1) * $limit;

$stmt = $koneksi->prepare(
    "SELECT l.*, u.nama_lengkap, u.role FROM tb_log_aktivitas l
     JOIN tb_user u ON l.id_user = u.id_user
     ORDER BY l.waktu_aktivitas DESC LIMIT :limit OFFSET :offset"
);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll();
?>

<div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">

    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
        <div>
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-brand-400"></i> Audit Log &amp; Aktivitas User
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Catatan riwayat aksi yang dilakukan oleh seluruh pengguna sistem</p>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-900/90 text-slate-400 text-xs uppercase tracking-wider font-semibold">
                <tr>
                    <th class="px-5 py-3.5">Waktu Aktivitas</th>
                    <th class="px-5 py-3.5">Pengguna</th>
                    <th class="px-5 py-3.5">Role</th>
                    <th class="px-5 py-3.5">Rincian Aktivitas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                <?php if (empty($logs)): ?>
                    <tr><td colspan="4" class="text-center py-8 text-slate-500">Belum ada aktivitas tercatat.</td></tr>
                <?php else: foreach ($logs as $log): ?>
                    <tr class="hover:bg-slate-900/50 transition">
                        <td class="px-5 py-3.5 text-xs text-slate-400 font-mono">
                            <i class="fa-regular fa-clock text-slate-500 mr-1.5"></i>
                            <?= date('d M Y H:i:s', strtotime($log['waktu_aktivitas'])) ?>
                        </td>
                        <td class="px-5 py-3.5 font-bold text-white"><?= htmlspecialchars($log['nama_lengkap']) ?></td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700 uppercase">
                                <?= htmlspecialchars($log['role']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-200"><?= htmlspecialchars($log['aktivitas']) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer_admin.php'; ?>