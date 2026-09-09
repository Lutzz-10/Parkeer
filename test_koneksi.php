<?php
require_once 'config/database.php';

$database = new Database();
$koneksi = $database->connect();

if ($koneksi) {
    echo "Koneksi ke db_parkeer berhasil!";
}