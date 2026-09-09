<?php
// includes/auth.php

// Selalu mulai output buffering & session di paling atas sebelum ada output apapun
if (session_status() === PHP_SESSION_NONE) {
    ob_start();
    session_start();
}

/**
 * Mengecek apakah user sudah login.
 * Jika belum, redirect ke halaman login.
 */
function cekLogin(): void
{
    if (!isset($_SESSION['id_user'])) {
        header("Location: /parkeer/auth/login.php");
        exit;
    }
}

/**
 * Mengecek apakah role user yang login sesuai dengan yang diizinkan.
 * Contoh pemakaian: cekRole(['admin']) atau cekRole(['admin', 'owner'])
 *
 * @param array $rolesDiizinkan
 */
function cekRole(array $rolesDiizinkan): void
{
    cekLogin(); // pastikan sudah login dulu

    if (!in_array($_SESSION['role'], $rolesDiizinkan)) {
        // Role tidak sesuai, tolak akses
        http_response_code(403);
        die("Akses ditolak. Halaman ini khusus untuk: " . implode(', ', $rolesDiizinkan));
    }
}