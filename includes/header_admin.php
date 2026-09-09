<?php
require_once __DIR__ . '/auth.php';
cekRole(['admin']);
$currentUri = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($judulHalaman ?? 'Admin') ?> - Parkeer</title>
    <!-- Favicon Chrome & Web Browser Tab -->
    <link rel="icon" type="image/png" href="/parkeer/assets/img/logo.png">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Plus Jakarta Sans', 'sans-serif'],
                },
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
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen antialiased selection:bg-brand-500 selection:text-white">

<div class="flex min-h-screen relative overflow-x-hidden">

    <!-- Backdrop Overlay Mobile -->
    <div id="mobileBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

    <!-- Sidebar -->
    <aside id="mainSidebar" class="w-64 bg-slate-950 border-r border-slate-800 text-slate-300 flex-shrink-0 fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col justify-between h-screen overflow-y-auto">
        
        <div>
            <!-- Branding Header -->
            <div class="p-5 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="/parkeer/assets/img/logo.png" alt="Parkeer Logo" class="w-10 h-10 object-contain rounded-xl bg-slate-900/80 border border-slate-800 p-1 shadow-md">
                    <div>
                        <h2 class="font-extrabold text-white tracking-wide text-lg">PARKEER</h2>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-brand-500/20 text-brand-400 border border-brand-500/30 font-semibold uppercase">Admin Panel</span>
                    </div>
                </div>
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1.5">
                <?php
                $navItems = [
                    ['url' => '/parkeer/admin/dashboard.php', 'label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'match' => 'dashboard.php'],
                    ['url' => '/parkeer/admin/user/index.php', 'label' => 'Manajemen User', 'icon' => 'fa-users-gear', 'match' => '/user/'],
                    ['url' => '/parkeer/admin/tarif/index.php', 'label' => 'Tarif Parkir', 'icon' => 'fa-money-bill-wave', 'match' => '/tarif/'],
                    ['url' => '/parkeer/admin/area/index.php', 'label' => 'Area Parkir', 'icon' => 'fa-square-parking', 'match' => '/area/'],
                    ['url' => '/parkeer/admin/kendaraan/index.php', 'label' => 'Kendaraan', 'icon' => 'fa-car', 'match' => '/kendaraan/'],
                    ['url' => '/parkeer/admin/log_aktivitas.php', 'label' => 'Log Aktivitas', 'icon' => 'fa-clock-rotate-left', 'match' => 'log_aktivitas.php'],
                ];

                foreach ($navItems as $item):
                    $isActive = strpos($currentUri, $item['match']) !== false;
                ?>
                    <a href="<?= $item['url'] ?>" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= $isActive ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-slate-950 font-bold shadow-lg shadow-brand-500/20' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' ?>">
                        <i class="fa-solid <?= $item['icon'] ?> text-base w-5 text-center <?= $isActive ? 'text-slate-950' : 'text-slate-400' ?>"></i>
                        <span><?= $item['label'] ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- User Info & Logout Footer -->
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-300 text-xs font-bold">
                        <i class="fa-solid fa-user-shield text-brand-400"></i>
                    </div>
                    <div class="truncate max-w-[110px]">
                        <p class="text-xs font-bold text-white truncate"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></p>
                        <p class="text-[10px] text-emerald-400 font-medium">● Online</p>
                    </div>
                </div>
            </div>
            <a href="/parkeer/auth/logout.php" 
               class="flex items-center justify-center gap-2 w-full px-3 py-2 rounded-xl text-xs font-bold bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/20 transition-all duration-200">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Keluar (Logout)</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 bg-slate-900 lg:pl-64">
        
        <!-- Top Header Bar (Fixed) -->
        <header class="bg-slate-950/90 backdrop-blur-md border-b border-slate-800 fixed top-0 right-0 left-0 lg:left-64 z-30 px-4 sm:px-6 py-3.5 flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg bg-slate-800 text-slate-300 hover:text-white">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight flex items-center gap-2">
                        <?= htmlspecialchars($judulHalaman ?? 'Dashboard') ?>
                    </h1>
                </div>
            </div>

            <!-- Right Widget: Real-time Live Clock -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-semibold text-slate-300">
                    <i class="fa-regular fa-clock text-brand-400"></i>
                    <span id="liveClockDisplay">--:--:-- WIB</span>
                </div>
            </div>
        </header>

        <!-- Dynamic Main Body Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 mt-16">