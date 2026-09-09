<?php
/**
 * Middleware Autentikasi & Otorisasi Hak Akses (Role-Based Access Control)
 * 
 * Modul ini berfungsi sebagai middleware pengaman aplikasi yang mengontrol
 * inisialisasi sesi, penampungan buffer output (`ob_start()`), serta pembatasan
 * hak akses halaman berdasarkan role (`admin`, `petugas`, `owner`).
 * 
 * @package Parkeer\Includes
 * @author Alwan Lutfi Maulida
 */

// Inisialisasi Output Buffering & Sesi PHP sebelum ada output HTML yang terkirim ke browser
if (session_status() === PHP_SESSION_NONE) {
    ob_start();
    session_start();
}

/**
 * Memverifikasi status login pengguna aktif.
 * Jika session `id_user` belum terdaftar, pengguna akan dialihkan ke halaman Login.
 * 
 * @return void
 */
function cekLogin(): void
{
    if (!isset($_SESSION['id_user'])) {
        header("Location: /parkeer/auth/login.php");
        exit;
    }
}

/**
 * Memverifikasi apakah role pengguna saat ini sesuai dengan daftar role yang diizinkan.
 * Contoh penggunaan: cekRole(['admin']) atau cekRole(['petugas', 'admin'])
 *
 * @param array $rolesDiizinkan Array daftar role yang memiliki wewenang mengakes halaman
 * @return void
 */
function cekRole(array $rolesDiizinkan): void
{
    // 1. Verifikasi terlebih dahulu apakah pengguna telah ber-autentikasi
    cekLogin();

    // 2. Cek kecocokan role pengguna dalam daftar role yang diizinkan
    if (!in_array($_SESSION['role'], $rolesDiizinkan)) {
        http_response_code(403);
        die("Akses ditolak. Halaman ini khusus untuk pengguna dengan wewenang: " . implode(', ', $rolesDiizinkan));
    }
}