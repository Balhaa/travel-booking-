<?php
// Autoloader Sederhana
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
    
    // Check if column exists to avoid error on re-run (rudimentary check)
    // Or just try add and catch exception
    
    $sql = "ALTER TABLE master_layanan ADD COLUMN tanggal_berangkat DATE NOT NULL DEFAULT CURRENT_DATE AFTER rute_tujuan";
    $db->exec($sql);
    
    echo "Migration successful: 'tanggal_berangkat' added to master_layanan.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    // Usually error 42S21 is 'Column already exists'
}
