<?php
// config/database.php

class Database
{
    private $host = "localhost";
    private $db_name = "db_parkeer";
    private $username = "root";
    private $password = ""; // sesuaikan jika MySQL kamu pakai password
    public $conn;

    public function connect()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            // Supaya error PDO memunculkan Exception, bukan diam-diam gagal
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Supaya hasil query berupa associative array
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }

        return $this->conn;
    }
}