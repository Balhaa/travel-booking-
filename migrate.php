<?php
require_once 'app/Config/Database.php';

use App\Config\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // Cek apakah kolom kendaraan_id sudah ada di master_layanan
$check = $db->query("SHOW COLUMNS FROM master_layanan LIKE 'kendaraan_id'");
if ($check->rowCount() == 0) {
    echo "Menambahkan kolom kendaraan_id ke master_layanan...<br>";
    $db->exec("ALTER TABLE master_layanan ADD COLUMN kendaraan_id INT DEFAULT NULL"); // Allow NULL first
    $db->exec("UPDATE master_layanan SET kendaraan_id = (SELECT id FROM tb_kendaraan LIMIT 1) WHERE kendaraan_id IS NULL"); // Fill with default if exists
    // Note: Foreign key constraint is skipped here to avoid errors if tb_kendaraan is empty or mismatch, 
    // but ideally should be added.
    $db->exec("ALTER TABLE master_layanan ADD CONSTRAINT fk_ml_kendaraan FOREIGN KEY (kendaraan_id) REFERENCES tb_kendaraan(id)");
    echo "Kolom kendaraan_id berhasil ditambahkan.<br>";
} else {
    echo "Kolom kendaraan_id sudah ada.<br>";
}

// Cek apakah kolom tanggal_jadwal sudah ada di tb_pemesanan
$checkJadwal = $db->query("SHOW COLUMNS FROM tb_pemesanan LIKE 'tanggal_jadwal'");
if ($checkJadwal->rowCount() == 0) {
    echo "Menambahkan kolom tanggal_jadwal ke tb_pemesanan...<br>";
    $db->exec("ALTER TABLE tb_pemesanan ADD COLUMN tanggal_jadwal DATE DEFAULT NULL AFTER layanan_id");
    echo "Kolom tanggal_jadwal berhasil ditambahkan.<br>";
} else {
    echo "Kolom tanggal_jadwal sudah ada.<br>";
}

// Update Status Kendaraan
// 1. Ubah data lama 'terpakai'/'perbaikan' jadi 'tidak_tersedia'
// 2. ALTER TABLE modify column
$db->exec("UPDATE tb_kendaraan SET status = 'tidak_tersedia' WHERE status IN ('terpakai', 'perbaikan')");
try {
    // Modify column to new ENUM
    // Note: 'tidak_tersedia' matches new value. 'tersedia' is same.
    $db->exec("ALTER TABLE tb_kendaraan MODIFY COLUMN status ENUM('tersedia', 'tidak_tersedia') DEFAULT 'tersedia'");
    echo "Update Status Kendaraan ENUM Berhasil.<br>";
} catch (PDOException $e) {
     echo "Gagal Alter Enum: " . $e->getMessage() . "<br>";
}            
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
