<?php
// Autoloader Sederhana (Copied from index.php)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

use App\Config\Database;

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to database.\n";
    
    $sql = "ALTER TABLE tb_kendaraan MODIFY COLUMN status ENUM('tersedia', 'terpakai', 'perbaikan', 'tidak_tersedia') DEFAULT 'tersedia'";
    $db->exec($sql);
    
    echo "Migration successful: 'tidak_tersedia' added to enum.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
