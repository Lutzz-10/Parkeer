<?php
$judulHalaman = "Log Aktivitas";
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

<div class="bg-white rounded-xl shadow p-6">
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-4 py-2">Waktu</th>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Role</th>
                <th class="px-4 py-2">Aktivitas</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td class="px-4 py-2 text-slate-500"><?= date('d M Y H:i', strtotime($log['waktu_aktivitas'])) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($log['nama_lengkap']) ?></td>
                    <td class="px-4 py-2 capitalize"><?= $log['role'] ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($log['aktivitas']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer_admin.php'; ?>