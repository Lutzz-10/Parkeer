<?php
require_once __DIR__ . '/auth.php';
cekRole(['admin']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - Aplikasi Parkeer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-800 text-slate-200 flex-shrink-0">
        <div class="p-5 text-xl font-bold text-white border-b border-slate-700">
            🅿️ Parkeer Admin
        </div>
        <nav class="mt-4 flex flex-col gap-1 px-3">
            <a href="/parkeer/admin/dashboard.php" class="px-3 py-2 rounded-lg hover:bg-slate-700">Dashboard</a>
            <a href="/parkeer/admin/user/index.php" class="px-3 py-2 rounded-lg hover:bg-slate-700">Manajemen User</a>
            <a href="/parkeer/admin/tarif/index.php" class="px-3 py-2 rounded-lg hover:bg-slate-700">Tarif Parkir</a>
            <a href="/parkeer/admin/area/index.php" class="px-3 py-2 rounded-lg hover:bg-slate-700">Area Parkir</a>
            <a href="/parkeer/admin/kendaraan/index.php" class="px-3 py-2 rounded-lg hover:bg-slate-700">Kendaraan</a>
            <a href="/parkeer/admin/log_aktivitas.php" class="px-3 py-2 rounded-lg hover:bg-slate-700">Log Aktivitas</a>
            <a href="/parkeer/auth/logout.php" class="px-3 py-2 rounded-lg hover:bg-red-600 mt-4">Logout</a>
        </nav>
    </aside>

    <!-- Konten -->
    <main class="flex-1 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800"><?= $judulHalaman ?? 'Dashboard' ?></h1>
            <span class="text-sm text-slate-500">Halo, <b><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></b> (Admin)</span>
        </div>