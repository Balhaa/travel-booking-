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

    // 1. Update status_pemesanan ENUM in tb_pemesanan
    $sql1 = "ALTER TABLE tb_pemesanan MODIFY COLUMN status_pemesanan ENUM('pending', 'menunggu_konfirmasi', 'dibayar', 'batal') DEFAULT 'pending'";
    $db->exec($sql1);
    echo "Updated tb_pemesanan status_pemesanan.\n";

    // 2. Add columns to tb_transaksi
    // Check if columns exist first or just try-catch individually if specific granular control needed, 
    // but here we assume they don't exist or we rely on SQL error handling.
    // However, existing tb_transaksi might hold data.
    
    // Check if metode_pembayaran exists
    try {
        $db->query("SELECT metode_pembayaran FROM tb_transaksi LIMIT 1");
    } catch (\PDOException $e) {
        $sql2 = "ALTER TABLE tb_transaksi ADD COLUMN metode_pembayaran ENUM('cash', 'transfer') NOT NULL DEFAULT 'cash' AFTER jumlah_bayar";
        $db->exec($sql2);
        echo "Added metode_pembayaran to tb_transaksi.\n";
    }

    try {
        $db->query("SELECT bukti_pembayaran FROM tb_transaksi LIMIT 1");
    } catch (\PDOException $e) {
        $sql3 = "ALTER TABLE tb_transaksi ADD COLUMN bukti_pembayaran VARCHAR(255) NULL AFTER metode_pembayaran";
        $db->exec($sql3);
        echo "Added bukti_pembayaran to tb_transaksi.\n";
    }

    // 3. Update status_pembayaran ENUM in tb_transaksi to include 'pending'
    $sql4 = "ALTER TABLE tb_transaksi MODIFY COLUMN status_pembayaran ENUM('pending', 'sukses', 'gagal') DEFAULT 'pending'";
    $db->exec($sql4);
    echo "Updated tb_transaksi status_pembayaran.\n";

    echo "Payment migration successful.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
