<?php
/**
 * Class Database
 * Menangani koneksi ke database menggunakan pola Singleton.
 * 
 * Author: iqbal habibi
 * Version: 1.0
 */

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private $host = 'localhost';
    private $db_name = 'db_travel_booking';
    private $username = 'root';
    private $password = ''; // Sesuaikan dengan password database Anda
    private $conn;
    
    // Instance tunggal untuk Singleton Pattern
    private static $instance = null;

    /**
     * Constructor private untuk mencegah instansiasi langsung.
     * Initial State: Koneksi belum terbentuk.
     * Final State: Koneksi terbentuk atau error ditangkap.
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Connection Error: " . $e->getMessage());
        }
    }

    /**
     * Mendapatkan instance koneksi database.
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Mengembalikan objek PDO connection.
     * @return PDO
     */
    public function getConnection() {
        return $this->conn;
    }
}
