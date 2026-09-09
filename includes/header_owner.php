<?php
require_once __DIR__ . '/auth.php';
cekRole(['owner']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Owner - Aplikasi Parkeer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    brand: {
                        50: '#FFF7EA',
                        100: '#FEECC7',
                        200: '#FDD68C',
                        400: '#F8BA4E',
                        500: '#F5A623',
                        600: '#DB8E12',
                        700: '#B0700D',
                        800: '#7A4E09'
                    }
                }
            }
        }
    }
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-slate-100 min-h-screen">
<div class="flex min-h-screen">
    <aside class="w-64 bg-slate-800 text-slate-200 flex-shrink-0">
        <div class="p-5 text-xl font-bold text-white border-b border-slate-700">🅿️ Parkeer Owner</div>
        <nav class="mt-4 flex flex-col gap-1 px-3">
            <a href="/parkeer/owner/dashboard.php" class="px-3 py-2 rounded-lg hover:bg-slate-700">Dashboard</a>
            <a href="/parkeer/owner/rekap/index.php" class="px-3 py-2 rounded-lg hover:bg-slate-700">Rekap Transaksi</a>
            <a href="/parkeer/auth/logout.php" class="px-3 py-2 rounded-lg hover:bg-red-600 mt-4">Logout</a>
        </nav>
    </aside>
    <main class="flex-1 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800"><?= $judulHalaman ?? 'Dashboard' ?></h1>
            <span class="text-sm text-slate-500">Halo, <b><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></b> (Owner)</span>
        </div>