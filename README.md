# 🚗 PARKEER — Sistem Manajemen & Otomasi Gate Parkir Terpadu
**Aplikasi Pengelolaan Parkir Berbasis Web (3 Level Pengguna: Admin, Petugas, Owner)**  
*Dibuat untuk Seleksi PKL Perusahaan Software Developer CV Creative Gama*  
*Pengembang / Programmer: Alwan Lutfi Maulida*

---

## 📌 Kredensial Akun Login Demo

Aplikasi ini menyediakan 3 akun pengguna bawaan untuk masing-masing role dengan password yang sama yaitu `password123`:

| Role / Wewenang | Username | Password | Fitur Utama |
|---|---|---|---|
| 👑 **Administrator** | `admin` | `password123` | Kelola User, Tarif, Area Parkir, Kendaraan, & Audit Log |
| 💳 **Petugas Gate** | `petugas` | `password123` | Direct Express Check-In, Kasir Check-Out, & Cetak Struk Thermal |
| 📊 **Executive Owner** | `owner` | `password123` | Dashboard Finansial, Grafik Tren Chart.js, & Ekspor CSV Excel |

---

## 🛠️ Persyaratan Sistem & Instalasi

### 1. Prasyarat Software
- Web Server: **Apache** / **Nginx** (Laragon / XAMPP / WAMP)
- Bahasa Pemrograman: **PHP version 8.0+** (disarankan PHP 8.2 / 8.3 / 8.4)
- Database Server: **MySQL 8.0+** / **MariaDB 10.4+**

### 2. Langkah-Langkah Instalasi Database & Aplikasi
1. **Clone / Salin Folder Proyek**:
   Pastikan folder proyek berada di direktori web server Anda (contoh Laragon: `d:/laragon/www/Parkeer`).
2. **Impor Database**:
   - Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
   - Buat database baru bernama `db_parkeer`.
   - Pilih database `db_parkeer`, lalu klik tab **Import**.
   - Pilih berkas SQL yang berada di `database/db_parkeer.sql`, lalu klik **Go / Kirim**.
3. **Konfigurasi Koneksi Database**:
   - Buka file `config/database.php`.
   - Sesuaikan `$host`, `$db_name`, `$username`, dan `$password` dengan konfigurasi MySQL lokal Anda:
     ```php
     private $host = "localhost";
     private $db_name = "db_parkeer";
     private $username = "root";
     private $password = ""; // Isi jika MySQL Anda menggunakan password
     ```
4. **Jalankan Aplikasi**:
   Akses melalui browser di URL: `http://localhost/parkeer/`

---

## 📖 Panduan Lengkap Penggunaan Aplikasi Per Role

### 1. 👑 Panduan Penggunaan Administrator (`username: admin`)
Setelah login sebagai **Admin**, Anda diarahkan ke Admin Dashboard dengan akses menu di Sidebar kiri:
- **Dashboard**: Menampilkan ringkasan statistik total user, area parkir, tarif, dan log aktivitas real-time.
- **Manajemen User (`/admin/user/`)**:
  - Menambah akun baru untuk Admin, Petugas, atau Owner.
  - Mengubah data user (nama, username, password, role) atau mengonfigurasi status **Aktif/Nonaktif** akun.
  - Menghapus akun user (terproteksi: tidak dapat menghapus akun diri sendiri saat sedang login).
- **Tarif Parkir (`/admin/tarif/`)**:
  - Mengatur tarif harga parkir per jam untuk masing-masing jenis kendaraan (`Motor`, `Mobil`, `Lainnya/Truk`).
- **Area Parkir (`/admin/area/`)**:
  - Mengatur area lokasi parkir (contoh: `Skansanesia`, `Masjid Ash Shidiq`) beserta **kapasitas maksimal slot**.
  - Sistem otomatis menghitung jumlah slot terisi dan slot sisa secara real-time.
- **Data Kendaraan (`/admin/kendaraan/`)**:
  - Melihat & mengelola master data kendaraan terdaftar (Plat Nomor, Jenis, Warna, Pemilik).
- **Log Aktivitas (`/admin/log_aktivitas.php`)**:
  - Memantau rekam jejak audit log seluruh tindakan pengguna di dalam sistem lengkap dengan timestamp.

---

### 2. 💳 Panduan Penggunaan Petugas Gate (`username: petugas`)
Setelah login sebagai **Petugas Gate**, Anda akan mengoperasikan pintu masuk/keluar parkir:
- **Direct Express Check-In (`/petugas/transaksi/masuk.php`)**:
  - Cukup masukkan **Plat Nomor Kendaraan** (misal: `B 1234 ABC`), pilih **Jenis Kendaraan** & **Area Parkir Tujuan**, lalu klik **"Proses Kendaraan Masuk (Buka Gate Barrier)"**.
  - *Sistem otomatis*: Jika plat nomor belum pernah terdaftar, sistem akan mendaftarkannya secara otomatis dalam 1 kali klik tanpa perlu kerja 2 kali.
- **Pencatatan Keluar & Kasir (`/petugas/transaksi/keluar.php`)**:
  - Pada halaman daftar kendaraan parkir (`/petugas/transaksi/index.php`), klik tombol **"Proses Keluar"** pada kendaraan yang hendak keluar.
  - Sistem otomatis menghitung durasi jam parkir (pembulatan ke atas per jam) dan mengalikan dengan tarif per jam berlaku.
  - Klik **"Konfirmasi Pembayaran & Cetak Struk"** untuk menyelesaikan transaksi dan mengosongkan slot area.
- **Cetak Struk Thermal Ticket (`/petugas/struk/cetak.php`)**:
  - Tampilan struk bergaya tiket kertas thermal (`JetBrains Mono` font + Barcode).
  - Klik tombol kuning **"Cetak Struk Sekarang"** untuk mencetak ke printer thermal/PDF.

---

### 3. 📊 Panduan Penggunaan Executive Owner (`username: owner`)
Setelah login sebagai **Owner**, Anda fokus pada analisis laporan dan performa bisnis:
- **Dashboard Executive (`/owner/dashboard.php`)**:
  - Ringkasan KPI Finansial: Total Pendapatan, Total Transaksi, Rata-rata per Tiket, dan Okupansi Area Parkir.
- **Rekap Transaksi & Filter Tanggal (`/owner/rekap/index.php`)**:
  - Tentukan **Dari Tanggal** dan **Sampai Tanggal**, lalu klik **"Tampilkan Laporan"**.
  - **Grafik Tren Pendapatan**: Visualisasi grafik bar interaktif (Chart.js) omzet harian.
  - **Breakdown per Jenis Kendaraan**: Persentase kontribusi omzet dari Motor, Mobil, dan Truk.
  - **Detail Log Transaksi**: Tabel rincian 100 transaksi terbaru.
- **Export Data CSV (`/owner/rekap/export.php`)**:
  - Klik tombol hijau **"Export CSV Data"** untuk mengunduh laporan keuangan dalam format CSV murni ber-enkoding UTF-8 BOM yang langsung rapi terpisah kolom saat dibuka di **Microsoft Excel**.

---

## 📂 Struktur Direktori Proyek

```
Parkeer/
├── admin/                  # Modul Khusus Role Administrator
│   ├── area/               # CRUD Area Parkir & Slot Kapasitas
│   ├── kendaraan/          # CRUD Master Data Kendaraan
│   ├── tarif/              # CRUD Pengaturan Tarif Per Jam
│   ├── user/               # CRUD Pengguna & Role Management
│   ├── dashboard.php       # Dashboard Statistik Admin
│   └── log_aktivitas.php   # Audit Log Aktivitas Sistem
├── assets/                 # Asset Gambar & Script
│   └── img/                # Logo Resmi (logo.png)
├── auth/                   # Modul Autentikasi
│   ├── login.php           # Halaman Login Multi-Role & Animasi Gate
│   └── logout.php          # Proses Logout Sesi
├── config/                 # Konfigurasi Database
│   └── database.php        # Class PDO Singleton Database Connection
├── database/               # Berkas Dump SQL Database
│   └── db_parkeer.sql      # Dump Database MySQL lengkap dengan Data Demo
├── functions/              # Controller Logic & Helper Functions
│   ├── area_functions.php  # Fungsi Pengelolaan Area Parkir
│   ├── kendaraan_functions.php # Fungsi Pengelolaan Kendaraan
│   ├── log_functions.php   # Fungsi Logger Aktivitas
│   ├── rekap_functions.php # Fungsi Agregasi Finansial Owner
│   ├── tarif_functions.php # Fungsi Pengelolaan Tarif
│   ├── transaksi_functions.php # Fungsi Core Check-In/Out & Billing
│   └── user_functions.php  # Fungsi Pengelolaan User
├── includes/               # Master Header, Footer, & Middleware
│   ├── auth.php            # Middleware RBAC & Session Initializer
│   ├── header_admin.php    # Header & Fixed Sidebar Layout Admin
│   ├── header_petugas.php  # Header & Fixed Sidebar Layout Petugas
│   ├── header_owner.php    # Header & Fixed Sidebar Layout Owner
│   ├── footer_admin.php    # Real-time Live Clock Ticker Admin
│   ├── footer_petugas.php  # Real-time Live Clock Ticker Petugas
│   └── footer_owner.php    # Real-time Live Clock Ticker Owner
├── owner/                  # Modul Khusus Role Executive Owner
│   ├── rekap/              # Rekap Transaksi, Grafik Chart.js, & Export CSV
│   └── dashboard.php       # Executive Financial Dashboard
├── petugas/                # Modul Khusus Role Petugas Gate
│   ├── struk/              # Modul Cetak Struk Thermal Ticket
│   ├── transaksi/          # Direct Express Check-In & Check-Out Kasir
│   └── dashboard.php       # Gate Operator Station Dashboard
├── index.php               # Router Halaman Utama (Redirect Auth)
├── LAPORAN_EVALUASI.md     # Berkas Laporan Evaluasi Singkat Seleksi PKL
└── README.md               # Berkas Dokumentasi & Panduan Penggunaan Ini
```

---

## 🛡️ Fitur Keamanan & Best Practices

1. **Proteksi Injeksi SQL**: Seluruh query menggunakan *PDO Prepared Statements* dengan *parameter binding*.
2. **Enkripsi Kredensial**: Password disimpan menggunakan algoritma standar bcrypt (`password_hash()`).
3. **Pemberhentian Output Buffering**: Penggunaan `ob_start()` memastikan pengalihan halaman (`header("Location: ...")`) berjalan lancar tanpa peringatan *headers already sent*.
4. **Respon Cepat & Ringan**: Query agregasi finansial dihitung langsung oleh server MySQL (`SUM`, `COUNT`, `AVG`) dengan pembatasan `LIMIT`.

---

&copy; 2026 **PARKEER** — Alwan Lutfi Maulida. All Rights Reserved.  
*Dibuat untuk Seleksi PKL Perusahaan Software Developer CV Creative Gama.*
