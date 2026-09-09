<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['id_user'])) {
    header("Location: /parkeer/" . $_SESSION['role'] . "/dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $database = new Database();
        $koneksi = $database->connect();

        $stmt = $koneksi->prepare("SELECT * FROM tb_user WHERE username = :username AND status_aktif = 1 LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            $stmtLog = $koneksi->prepare(
                "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) VALUES (:id_user, :aktivitas, NOW())"
            );
            $stmtLog->execute([
                ':id_user' => $user['id_user'],
                ':aktivitas' => 'Login ke sistem'
            ]);

            header("Location: /parkeer/" . $user['role'] . "/dashboard.php");
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Parkeer Smart System</title>
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

        /* ===== Gate Scene Background & Glow ===== */
        .gate-panel-bg {
            background: radial-gradient(circle at 30% 30%, #1e293b 0%, #0f172a 50%, #070a12 100%);
        }

        .ambient-grid {
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .gate-scene {
            position: relative;
            width: 480px;
            max-width: 95%;
            height: 290px;
            margin: 0 auto;
        }

        .asphalt {
            position: absolute;
            left: 0; right: 0; bottom: 40px;
            height: 48px;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            border-top: 2px solid #334155;
            border-bottom: 2px solid #334155;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .road-line {
            position: absolute;
            left: 0; right: 0; top: 50%;
            transform: translateY(-50%);
            height: 3px;
            background: repeating-linear-gradient(90deg, #f8ba4e 0 28px, transparent 28px 56px);
            opacity: 0.85;
        }

        /* RFID Scanner Column */
        .rfid-post {
            position: absolute;
            left: 70px; bottom: 85px;
            width: 14px; height: 50px;
            background: linear-gradient(180deg, #334155, #1e293b);
            border-radius: 4px;
            border: 1px solid #475569;
            box-shadow: 0 0 10px rgba(0,0,0,0.4);
        }

        .rfid-scanner-head {
            position: absolute;
            top: 4px; left: -3px;
            width: 20px; height: 16px;
            background: #0f172a;
            border: 1px solid #38bdf8;
            border-radius: 3px;
        }

        .rfid-beam {
            position: absolute;
            top: 8px; left: 18px;
            width: 70px; height: 30px;
            background: linear-gradient(90deg, rgba(56, 189, 248, 0.4), transparent);
            clip-path: polygon(0 0, 100% 40%, 100% 100%, 0 80%);
            animation: pulseBeam 2s infinite ease-in-out;
        }

        @keyframes pulseBeam {
            0%, 100% { opacity: 0.3; transform: scaleY(0.9); }
            50% { opacity: 0.9; transform: scaleY(1.1); }
        }

        /* Barrier Gate Post & Arm */
        .barrier-post {
            position: absolute;
            left: 140px; bottom: 85px;
            width: 20px; height: 95px;
            background: linear-gradient(180deg, #475569 0%, #1e293b 100%);
            border-radius: 6px;
            border: 1px solid #64748b;
            box-shadow: 0 4px 15px rgba(0,0,0,0.6);
            z-index: 10;
        }

        .barrier-display {
            position: absolute;
            top: 8px; left: 3px;
            width: 14px; height: 20px;
            background: #020617;
            border: 1px solid #334155;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .barrier-lamp {
            width: 8px; height: 8px;
            border-radius: 50%;
            animation: lampStatus 7s infinite ease-in-out;
        }

        .barrier-arm {
            position: absolute;
            left: 150px; bottom: 162px;
            width: 190px; height: 12px;
            background: repeating-linear-gradient(90deg, #ef4444 0 22px, #ffffff 22px 44px);
            border-radius: 6px;
            transform-origin: 5px 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            animation: barrierSwing 7s infinite ease-in-out;
            z-index: 12;
        }

        .barrier-arm::after {
            content: '';
            position: absolute;
            right: 4px; top: 3px;
            width: 6px; height: 6px;
            background: #f8ba4e;
            border-radius: 50%;
            box-shadow: 0 0 8px #f8ba4e;
        }

        @keyframes barrierSwing {
            0%, 20%   { transform: rotate(0deg); }
            35%, 68%  { transform: rotate(-84deg); }
            85%, 100% { transform: rotate(0deg); }
        }

        @keyframes lampStatus {
            0%, 20%   { background: #ef4444; box-shadow: 0 0 12px 3px rgba(239, 68, 68, 0.8); }
            35%, 68%  { background: #22c55e; box-shadow: 0 0 12px 3px rgba(34, 197, 94, 0.8); }
            85%, 100% { background: #ef4444; box-shadow: 0 0 12px 3px rgba(239, 68, 68, 0.8); }
        }

        /* Modern Car with Headlights Glow */
        .car-wrapper {
            position: absolute;
            bottom: 48px;
            left: -140px;
            width: 110px; height: 42px;
            animation: carDrive 7s infinite ease-in-out;
            z-index: 5;
        }

        .car-body {
            position: absolute;
            bottom: 8px; left: 0;
            width: 110px; height: 26px;
            background: linear-gradient(180deg, #f5a623 0%, #b0700d 100%);
            border-radius: 12px 14px 8px 8px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.4);
        }

        .car-cabin {
            position: absolute;
            bottom: 24px; left: 24px;
            width: 58px; height: 18px;
            background: linear-gradient(180deg, #334155, #0f172a);
            border-radius: 12px 10px 2px 2px;
            border: 1px solid #475569;
        }

        .car-headlight {
            position: absolute;
            right: 0; bottom: 12px;
            width: 6px; height: 10px;
            background: #fef08a;
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 10px #fef08a;
        }

        .headlight-beam {
            position: absolute;
            right: -80px; bottom: -10px;
            width: 85px; height: 40px;
            background: linear-gradient(90deg, rgba(254, 240, 138, 0.4) 0%, transparent 100%);
            clip-path: polygon(0 40%, 100% 0%, 100% 100%, 0 60%);
            pointer-events: none;
        }

        .car-wheel {
            position: absolute;
            bottom: 0;
            width: 18px; height: 18px;
            background: #0f172a;
            border-radius: 50%;
            border: 3px solid #64748b;
            box-shadow: inset 0 0 4px #000;
        }
        .car-wheel.front { right: 16px; }
        .car-wheel.back { left: 16px; }

        @keyframes carDrive {
            0%        { left: -140px; opacity: 0; }
            10%       { opacity: 1; }
            22%, 62%  { left: 35px; opacity: 1; } /* Stops right at the RFID/Barrier gate */
            78%       { left: 520px; opacity: 1; }
            82%, 100% { left: 520px; opacity: 0; }
        }

        /* Ambient Floating Glow Circles */
        .glow-orb-1 {
            position: absolute;
            top: 20%; left: 20%;
            width: 260px; height: 260px;
            background: radial-gradient(circle, rgba(245, 166, 35, 0.15) 0%, transparent 70%);
            filter: blur(40px);
            pointer-events: none;
        }

        .glow-orb-2 {
            position: absolute;
            bottom: 10%; right: 10%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.1) 0%, transparent 70%);
            filter: blur(50px);
            pointer-events: none;
        }
    </style>
</head>
<body class="min-h-screen flex bg-slate-950 text-slate-100 overflow-x-hidden selection:bg-brand-500 selection:text-white">

    <!-- Ambient Glow Orbs -->
    <div class="glow-orb-1"></div>
    <div class="glow-orb-2"></div>

    <!-- Panel Kiri: Scene Animasi Gate Barrier -->
    <div class="gate-panel-bg ambient-grid hidden lg:flex w-7/12 relative items-center justify-center p-12 overflow-hidden border-r border-slate-800/80">

        <!-- Header Branding -->
        <div class="absolute top-10 left-10 flex items-center gap-4 z-20">
            <img src="/parkeer/assets/img/logo.png" alt="Parkeer Logo" class="w-12 h-12 object-contain rounded-2xl bg-slate-900/80 border border-slate-800 p-1 shadow-lg shadow-brand-500/25 ring-2 ring-brand-400/30">
            <div>
                <p class="text-3xl font-extrabold tracking-tight text-white flex items-center gap-2">
                    PARKEER 
                </p>
                <p class="text-slate-400 text-xs mt-0.5">Sistem Manajemen &amp; Otomasi Gate Parkir Terpadu</p>
            </div>
        </div>

        <!-- Barrier Gate Live Scene Container -->
        <div class="relative z-10 w-full flex flex-col items-center">
            
            <!-- Live Indicator Badge -->
            <div class="mb-8 inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-slate-900/90 border border-slate-700/60 shadow-xl backdrop-blur-md">
                <span class="relative flex h-2.5 w-2.5">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-semibold text-slate-300 tracking-wide uppercase">Gate Sensor &amp; Auto-Barrier Active</span>
            </div>

            <!-- Gate Animation Scene -->
            <div class="gate-scene">
                <div class="asphalt">
                    <div class="road-line"></div>
                </div>

                <!-- RFID Scanner -->
                <div class="rfid-post">
                    <div class="rfid-scanner-head"></div>
                    <div class="rfid-beam"></div>
                </div>

                <!-- Gate Barrier -->
                <div class="barrier-post">
                    <div class="barrier-display">
                        <div class="barrier-lamp"></div>
                    </div>
                </div>
                <div class="barrier-arm"></div>

                <!-- Animated Car -->
                <div class="car-wrapper">
                    <div class="car-body"></div>
                    <div class="car-cabin"></div>
                    <div class="car-headlight"></div>
                    <div class="headlight-beam"></div>
                    <div class="car-wheel back"></div>
                    <div class="car-wheel front"></div>
                </div>
            </div>

            <!-- Features Highlights Grid -->
            <div class="grid grid-cols-3 gap-4 w-full max-w-lg mt-10">
                <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-3 text-center backdrop-blur-sm">
                    <i class="fa-solid fa-bolt text-brand-400 text-lg mb-1.5"></i>
                    <p class="text-xs font-bold text-slate-200">Respon Cepat</p>
                    <p class="text-[10px] text-slate-400">Pencatatan Real-time</p>
                </div>
                <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-3 text-center backdrop-blur-sm">
                    <i class="fa-solid fa-shield-halved text-emerald-400 text-lg mb-1.5"></i>
                    <p class="text-xs font-bold text-slate-200">Keamanan Tinggi</p>
                    <p class="text-[10px] text-slate-400">Log &amp; Otorisasi Role</p>
                </div>
                <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-3 text-center backdrop-blur-sm">
                    <i class="fa-solid fa-chart-pie text-sky-400 text-lg mb-1.5"></i>
                    <p class="text-xs font-bold text-slate-200">Rekap Presisi</p>
                    <p class="text-[10px] text-slate-400">Laporan Otomatis</p>
                </div>
            </div>
        </div>

        <p class="absolute bottom-8 left-1/2 -translate-x-1/2 text-slate-500 text-xs font-medium">
            &copy; <?= date('Y') ?> Parkeer — Alwan Lutfi Maulida. All Rights Reserved.
        </p>
    </div>

    <!-- Panel Kanan: Form Login -->
    <div class="w-full lg:w-5/12 flex items-center justify-center p-6 sm:p-12 relative z-20 bg-slate-950">
        <div class="w-full max-w-md bg-slate-900/90 rounded-3xl shadow-2xl shadow-brand-500/10 p-8 sm:p-10 border border-slate-800 backdrop-blur-xl relative">
            
            <!-- Mobile Branding -->
            <div class="flex flex-col items-center mb-8 lg:hidden">
                <img src="/parkeer/assets/img/logo.png" alt="Parkeer Logo" class="w-14 h-14 object-contain rounded-2xl bg-slate-900/80 border border-slate-800 p-1 shadow-lg shadow-brand-500/30 mb-3">
                <h1 class="text-2xl font-extrabold tracking-tight text-white">PARKEER</h1>
                <p class="text-xs text-brand-400 font-medium">Smart Parking System Management</p>
            </div>

            <!-- Form Header -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-white tracking-tight mb-2">Selamat datang kembali 👋</h2>
                <p class="text-slate-400 text-sm">Masukkan username dan password Anda untuk masuk ke sistem.</p>
            </div>

            <!-- Error Alert -->
            <?php if (!empty($error)): ?>
                <div class="flex items-center gap-3 bg-red-950/80 text-red-300 text-sm rounded-2xl px-4 py-3.5 mb-6 border border-red-800/60 shadow-lg shadow-red-950/40">
                    <div class="w-8 h-8 rounded-xl bg-red-900/60 flex items-center justify-center text-red-400 shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <span class="font-medium text-xs sm:text-sm"><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-user-circle text-base"></i>
                        </div>
                        <input type="text" name="username" required autofocus placeholder="Masukkan username"
                               class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl pl-11 pr-4 py-3.5 text-sm
                                      placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 
                                      transition-all duration-200">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">Password</label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-base"></i>
                        </div>
                        <input type="password" id="passwordInput" name="password" required placeholder="••••••••"
                               class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl pl-11 pr-11 py-3.5 text-sm
                                      placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 
                                      transition-all duration-200">
                        <button type="button" onclick="togglePasswordVisibility()" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-200 transition">
                            <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 
                                   text-slate-950 font-bold rounded-xl py-3.5 px-4 text-sm tracking-wide transition-all duration-200 
                                   shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 hover:scale-[1.01] active:scale-[0.99]
                                   flex items-center justify-center gap-2">
                        <span>Masuk Sistem</span>
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    </button>
                </div>
            </form>

            <!-- Role Badge Footer Info -->
            <div class="mt-8 pt-6 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-500">
                <span>Akses: Admin, Petugas, Owner</span>
                <span class="flex items-center gap-1.5 text-emerald-400 font-semibold">
                    <i class="fa-solid fa-circle-check text-[10px]"></i> System Ready
                </span>
            </div>

        </div>
    </div>

    <script>
    function togglePasswordVisibility() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('togglePasswordIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>