<?php
/**
 * Konfigurasi Database & Koneksi PDO (PHP Data Objects)
 * 
 * Modul ini menangani koneksi terpusat ke basis data MySQL menggunakan PDO
 * dengan konfigurasi error handling berstandar industri (PDO::ERRMODE_EXCEPTION).
 * 
 * @package Parkeer\Config
 * @author Alwan Lutfi Maulida
 */

class Database
{
    /** @var string Host server database MySQL */
    private $host = "localhost";

    /** @var string Nama basis data aplikasi */
    private $db_name = "db_parkeer";

    /** @var string Username autentikasi MySQL */
    private $username = "root";

    /** @var string Password autentikasi MySQL */
    private $password = "";

    /** @var PDO|null Instance koneksi PDO aktif */
    public $conn;

    /**
     * Membuat dan mengembalikan koneksi PDO ke database MySQL.
     * 
     * @return PDO Instance koneksi PDO yang siap digunakan untuk prepared statement
     */
    public function connect(): PDO
    {
        $this->conn = null;

        try {
            // Inisialisasi koneksi PDO dengan skema karakter utf8mb4
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            
            // Konfigurasi Error Mode: Lempar Exception untuk keamanan dan kemudahan debugging
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Konfigurasi Default Fetch: Selalu mengembalikan Associative Array
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // Hentikan eksekusi jika terjadi kendala koneksi ke server MySQL
            die("Koneksi database gagal: " . $e->getMessage());
        }

        return $this->conn;
    }
}