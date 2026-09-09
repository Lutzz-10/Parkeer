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
    <title>Login - Aplikasi Parkeer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; }

        /* ===== Panel kiri: scene animasi gerbang parkir ===== */
        .gate-panel {
            background: radial-gradient(circle at 20% 20%, #1e293b 0%, #0f172a 60%, #020617 100%);
            background-image:
                radial-gradient(circle at 20% 20%, #1e293b 0%, #0f172a 60%, #020617 100%),
                radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: auto, 22px 22px;
        }

        .gate-scene {
            position: relative;
            width: 420px;
            max-width: 90%;
            height: 260px;
            margin: 0 auto;
        }

        .road {
            position: absolute;
            left: 0; right: 0; bottom: 55px;
            height: 4px;
            background: repeating-linear-gradient(90deg, #fbbf24 0 22px, transparent 22px 46px);
            opacity: 0.9;
        }

        .barrier-post {
            position: absolute;
            left: 95px; bottom: 55px;
            width: 16px; height: 78px;
            background: linear-gradient(180deg, #e2e8f0, #94a3b8);
            border-radius: 4px;
            box-shadow: 0 0 0 2px #0f172a inset;
        }

        .barrier-lamp {
            position: absolute;
            left: 99px; bottom: 128px;
            width: 8px; height: 8px;
            border-radius: 50%;
            animation: lampColor 6s ease-in-out infinite;
        }

        .barrier-arm {
            position: absolute;
            left: 103px; bottom: 128px;
            width: 170px; height: 10px;
            background: repeating-linear-gradient(90deg, #f87171 0 18px, #f8fafc 18px 36px);
            border-radius: 5px;
            transform-origin: left center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.4);
            animation: barrierSwing 6s ease-in-out infinite;
        }

        @keyframes barrierSwing {
            0%, 18%   { transform: rotate(0deg); }
            33%, 68%  { transform: rotate(-82deg); }
            83%, 100% { transform: rotate(0deg); }
        }

        @keyframes lampColor {
            0%, 18%   { background: #ef4444; box-shadow: 0 0 10px 2px #ef4444; }
            33%, 68%  { background: #22c55e; box-shadow: 0 0 10px 2px #22c55e; }
            83%, 100% { background: #ef4444; box-shadow: 0 0 10px 2px #ef4444; }
        }

        .car {
            position: absolute;
            bottom: 58px;
            left: -110px;
            width: 76px; height: 30px;
            animation: carDrive 6s ease-in-out infinite;
        }

        .car-body {
            position: absolute;
            bottom: 8px; left: 0;
            width: 76px; height: 20px;
            background: linear-gradient(180deg, #38bdf8, #0284c7);
            border-radius: 10px 10px 6px 6px;
        }
        .car-cabin {
            position: absolute;
            bottom: 20px; left: 16px;
            width: 40px; height: 14px;
            background: linear-gradient(180deg, #7dd3fc, #38bdf8);
            border-radius: 8px 8px 2px 2px;
        }
        .car-wheel {
            position: absolute;
            bottom: 0;
            width: 14px; height: 14px;
            background: #1e293b;
            border-radius: 50%;
            border: 2px solid #64748b;
        }
        .car-wheel.front { left: 10px; }
        .car-wheel.back { left: 52px; }

        @keyframes carDrive {
            0%, 30%   { left: -110px; opacity: 0; }
            34%       { opacity: 1; }
            38%, 63%  { left: 155px; opacity: 1; }
            78%       { left: 480px; opacity: 1; }
            82%, 100% { left: 480px; opacity: 0; }
        }

        .gate-branding {
            position: absolute;
            top: 40px; left: 40px;
            color: white;
        }
    </style>
</head>
<body class="min-h-screen flex">

    <!-- Panel Kiri: Animasi Gerbang Parkir -->
    <div class="gate-panel hidden lg:flex w-3/5 relative items-center justify-center overflow-hidden">

        <div class="gate-branding">
            <p class="text-2xl font-bold tracking-tight">🅿️ Aplikasi Parkeer</p>
            <p class="text-slate-400 text-sm mt-1">Kelola parkir jadi lebih mudah &amp; efisien</p>
        </div>

        <div class="gate-scene">
            <div class="road"></div>
            <div class="barrier-post"></div>
            <div class="barrier-lamp"></div>
            <div class="barrier-arm"></div>
            <div class="car">
                <div class="car-body"></div>
                <div class="car-cabin"></div>
                <div class="car-wheel front"></div>
                <div class="car-wheel back"></div>
            </div>
        </div>

        <p class="absolute bottom-8 left-1/2 -translate-x-1/2 text-slate-500 text-xs">
            &copy; <?= date('Y') ?> Aplikasi Parkeer — CV Creative Gama
        </p>
    </div>

    <!-- Panel Kanan: Form Login -->
    <div class="w-full lg:w-2/5 flex items-center justify-center bg-white p-8">
        <div class="w-full max-w-sm">

            <div class="mb-8 lg:hidden text-center">
                <p class="text-2xl font-bold text-slate-800">🅿️ Aplikasi Parkeer</p>
            </div>

            <h1 class="text-2xl font-bold text-slate-800 mb-1">Selamat Datang</h1>
            <p class="text-slate-500 text-sm mb-6">Silakan login untuk melanjutkan</p>

            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 text-sm rounded-lg px-4 py-2 mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                    <input type="text" name="username" required autofocus
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg py-2.5 transition mt-2">
                    Masuk
                </button>
            </form>
        </div>
    </div>

</body>
</html>