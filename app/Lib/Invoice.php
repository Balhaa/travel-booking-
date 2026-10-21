<?php
/**
 * File Invoice (Library)
 * Menampilkan struk pembayaran siap cetak.
 * 
 * Author: Iqbal Habibi
 * Tanggal Pembuatan: 21 Januari 2026
 */

use App\Classes\Transaksi;
use App\Classes\Auth;

if (!Auth::check()) die("Login required");

$id = $_GET['id'] ?? null;
if (!$id) die("ID Transaksi tidak ditemukan");

$model = new Transaksi();
$data = $model->show($id);

if (!$data) die("Data tidak ditemukan");

// Security Check: Pelanggan hanya boleh lihat punya sendiri
if (Auth::user()['role'] == 'pelanggan' && $data['user_id'] != Auth::user()['id']) {
    die("Akses Ditolak: Ini bukan invoice Anda.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $data['id'] ?></title>
    <style>
        body { font-family: sans-serif; padding: 40px; }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .total { text-align: right; font-weight: bold; font-size: 1.2em; margin-top: 20px; }
        @media print {
            .no-print { display: none; }
            .invoice-box { box-shadow: none; border: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="invoice-box">
        <div class="header">
            <div>
                <h1>INVOICE</h1>
                <p>Travel Booking App</p>
            </div>
            <div style="text-align: right;">
                <p>No Order: #<?= $data['id'] ?></p>
                <p>Tanggal Order: <?= $data['tanggal_pemesanan'] ?></p>
                <p><strong>Jadwal Berangkat: <?= isset($data['tanggal_jadwal']) ? $data['tanggal_jadwal'] . ' ' . $data['waktu_berangkat'] : '-' ?></strong></p>
            </div>
        </div>

        <p><strong>Pelanggan:</strong> <?= $data['nama_lengkap'] ?></p>

        <table>
            <thead>
                <tr>
                    <th>Layanan</th>
                    <th>Rute</th>
                    <th>Jumlah Tiket</th>
                    <th>Harga Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $data['nama_layanan'] ?></td>
                    <td><?= $data['rute_asal'] ?> - <?= $data['rute_tujuan'] ?></td>
                    <td><?= $data['jumlah_tiket'] ?></td>
                    <td>Rp <?= number_format($data['total_harga'], 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>

        <div class="total">
            Total Bayar: Rp <?= number_format($data['total_harga'], 0, ',', '.') ?>
        </div>

        <div style="margin-top: 50px; text-align: center;">
            <p>Terima Kasih atas kepercayaan Anda!</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #333; color: white; cursor: pointer;">Cetak PDF</button>
    </div>
</body>
</html>
