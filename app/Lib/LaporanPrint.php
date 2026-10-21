<?php
/**
 * File Laporan Print (Library)
 * Menampilkan laporan rekapitulasi transaksi siap cetak.
 */

use App\Classes\Transaksi;
use App\Classes\Auth;

if (!Auth::check()) die("Login required");
$user = Auth::user();

// Security: Hanya Admin/Kasir
if (!in_array($user['role'], ['admin', 'kasir'])) die("Akses Ditolak");

$model = new Transaksi();
// Ambil semua data transaksi (bisa dimodifikasi untuk filter tanggal jika ada parameter GET)
$data = $model->index();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        h1, h2 { text-align: center; margin: 5px 0; }
        .meta { text-align: center; margin-bottom: 20px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #eee; }
        @media print {
            .no-print { display: none; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Cetak/Simpan PDF</button>
        <button onclick="window.history.back()" style="padding: 10px 20px; cursor: pointer;">Kembali</button>
    </div>

    <h1>LAPORAN TRANSAKSI & PEMBAYARAN</h1>
    <h2>Travel Booking App</h2>
    <div class="meta">
        Dicetak oleh: <?= $user['nama_lengkap'] ?> (<?= ucfirst($user['role']) ?>)<br>
        Tanggal Cetak: <?= date('d-m-Y H:i:s') ?>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Tanggal Order</th>
                <th>Nama Pelanggan</th>
                <th>Layanan / Rute</th>
                <th>Metode Bayar</th>
                <th>Status</th>
                <th style="text-align: right;">Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $grandTotal = 0;
            $no = 1;
            foreach ($data as $row): 
                // Hitung grand total hanya untuk yang sudah lunas/sukses agar akurat secara keuangan
                // Atau tampilkan semua tapi bedakan statusnya. 
                // Di sini kita jumlahkan semua nilai transaksi yang tercatat.
                if ($row->status == 'dibayar') {
                    $grandTotal += $row->total; 
                }
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row->tanggal ?></td>
                <td><?= htmlspecialchars($row->pemesan) ?></td>
                <td><?= htmlspecialchars($row->layanan) ?></td>
                <td>
                    <?php 
                        // Deteksi metode bayar dari bukti atau logic lain, 
                        // karena di Transaksi::index saat ini belum join lengkap ke tb_transaksi untuk ambil metodenya secara eksplisit per row 
                        // (hanya ambil bukti). Kita bisa improve nanti, tapi sementara kita cek bukti.
                        if ($row->bukti) echo "Transfer";
                        elseif ($row->status == 'dibayar' && !$row->bukti) echo "Cash/Manual";
                        else echo "-";
                    ?>
                </td>
                <td>
                    <?php if ($row->status == 'pending'): ?>
                        Belum Bayar
                    <?php elseif ($row->status == 'menunggu_konfirmasi'): ?>
                        Menunggu Konfirmasi
                    <?php elseif ($row->status == 'dibayar'): ?>
                        LUNAS
                    <?php else: ?>
                        <?= ucfirst($row->status) ?>
                    <?php endif; ?>
                </td>
                <td style="text-align: right;">Rp <?= number_format($row->total, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" style="text-align: right;">Total Pemasukan (Status: Lunas):</td>
                <td style="text-align: right;">Rp <?= number_format($grandTotal, 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 50px; float: right; width: 200px; text-align: center;">
        <p>Purwokerto, <?= date('d F Y') ?></p>
        <p><?= ucfirst($user['role']) ?></p>
        <br><br><br>
        <p><strong>( <?= $user['nama_lengkap'] ?> )</strong></p>
    </div>
</body>
</html>
