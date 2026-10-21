<?php
/**
 * File Utama (Entry Point)
 * Mengatur routing halaman berdasarkan parameter URL '?page='.
 * 
 * Author: Iqbal Habibi
 * Tanggal Pembuatan: 21 Januari 2026
 */

session_start();

// Autoloader Sederhana untuk Namespace
spl_autoload_register(function ($class) {
    // Prefix namespace "App\"
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Routing Sederhana
$page = isset($_GET['page']) ? $_GET['page'] : 'login';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Cek Pesan Error/Sukses
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
$success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
unset($_SESSION['error']);
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Booking App</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php
    // Global Action Handler
    if ($action == 'logout') {
        $auth = new App\Classes\Auth();
        $auth->logout();
    }

    // Routing Logic
    switch ($page) {
        case 'login':
            include 'app/Views/login.php';
            break;
        case 'dashboard':
            include 'app/Views/dashboard.php';
            break;
        case 'layanan':
            include 'app/Views/layanan/index.php';
            break;
        case 'layanan_stok':
            include 'app/Views/layanan/kelola_stok.php';
            break;
        case 'layanan_create':
            include 'app/Views/layanan/create.php';
            break;
        case 'layanan_edit':
            include 'app/Views/layanan/edit.php';
            break;
        case 'booking':
            include 'app/Views/booking/index.php';
            break;
        case 'riwayat':
            include 'app/Views/booking/history.php';
            break;
        case 'transaksi':
            include 'app/Views/transaksi/index.php';
            break;
        case 'invoice':
            require 'app/Lib/Invoice.php';
            break;
        case 'laporan_print':
            require 'app/Lib/LaporanPrint.php';
            break;
        case 'kendaraan':
            include 'app/Views/kendaraan/index.php';
            break;
        case 'kendaraan_edit':
            include 'app/Views/kendaraan/edit.php';
            break;
        case 'user':
            include 'app/Views/user/index.php';
            break;
        case 'laporan':
            include 'app/Views/transaksi/index.php'; // Reuse transaction summary as report for now
            break;
        // Tambahkan case lain sesuai kebutuhan
        default:
            echo "<h1>404 Not Found</h1>";
            break;
    }
    ?>
</body>
</html>
