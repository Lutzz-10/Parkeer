<?php
$judulHalaman = "Riwayat Transaksi";
require_once __DIR__ . '/../../includes/header_petugas.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/transaksi_functions.php';

$database = new Database();
$koneksi = $database->connect();
$riwayat = getRiwayatTransaksi($koneksi, null, null, 50);
?>

<div class="bg-white rounded-xl shadow p-6">
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-4 py-2">Plat</th>
                <th class="px-4 py-2">Masuk</th>
                <th class="px-4 py-2">Keluar</th>
                <th class="px-4 py-2">Durasi</th>
                <th class="px-4 py-2">Biaya</th>
                <th class="px-4 py-2 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php foreach ($riwayat as $r): ?>
                <tr>
                    <td class="px-4 py-2 font-medium"><?= htmlspecialchars($r['plat_nomor']) ?></td>
                    <td class="px-4 py-2"><?= date('d M H:i', strtotime($r['waktu_masuk'])) ?></td>
                    <td class="px-4 py-2"><?= date('d M H:i', strtotime($r['waktu_keluar'])) ?></td>
                    <td class="px-4 py-2"><?= $r['durasi_jam'] ?> jam</td>
                    <td class="px-4 py-2">Rp <?= number_format($r['biaya_total'], 0, ',', '.') ?></td>
                    <td class="px-4 py-2 text-right">
    <a href="/parkeer/petugas/struk/cetak.php?id=<?= $r['id_parkir'] ?>" class="text-blue-600 hover:underline">Lihat Struk</a>
</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer_petugas.php'; ?>