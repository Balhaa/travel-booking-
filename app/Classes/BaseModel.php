<?php
/**
 * Class BaseModel
 * Parent class untuk semua Model. Bertugas menyediakan properti koneksi database ($this->db) 
 */

namespace App\Classes;

use App\Config\Database;

class BaseModel {
    protected $db;

    /**
     * Constructor
     * Otomatis dijalankan saat class ini (atau anaknya) dipanggil.
     * Menginisialisasi koneksi database.
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
}
