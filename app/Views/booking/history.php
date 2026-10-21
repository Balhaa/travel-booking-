<?php
use App\Classes\Pemesanan;
use App\Classes\Auth;

if (!Auth::check()) die("Login required");

use App\Classes\Transaksi;

$pemesananModel = new Pemesanan();
$transaksiModel = new Transaksi();

// Handle Upload Bukti / Bayar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $pemesananId = $_POST['pemesanan_id'];
    $total = $_POST['total']; 

    if ($action == 'upload') {
        // Handle File Upload
        if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['bukti']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                echo "<script>alert('Hanya file gambar (JPG, JPEG, PNG, GIF) yang diperbolehkan!'); window.location='index.php?page=riwayat';</script>";
                exit;
            }

            $targetDir = "public/uploads/bukti/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            
            $fileName = time() . '_' . basename($filename);
            $targetFilePath = $targetDir . $fileName;
            
            if (move_uploaded_file($_FILES['bukti']['tmp_name'], $targetFilePath)) {
                $data = [
                    'pemesanan_id' => $pemesananId,
                    'jumlah_bayar' => $total,
                    'metode' => 'transfer',
                    'bukti' => $fileName
                ];
                if ($transaksiModel->store($data)) {
                    echo "<script>alert('Bukti berhasil diupload. Tunggu konfirmasi admin.');</script>";
                }
            } else {
                echo "<script>alert('Gagal upload file.');</script>";
            }
        }
    } elseif ($action == 'cash') {
        $data = [
            'pemesanan_id' => $pemesananId,
            'jumlah_bayar' => $total,
            'metode' => 'cash'
        ];
        if ($transaksiModel->store($data)) {
             echo "<script>alert('Metode Cash dipilih. Silahkan bayar di lokasi.');</script>";
        }
    }
    // Refresh page
    echo "<script>window.location='index.php?page=riwayat';</script>";
}

$myBookings = $pemesananModel->index();
?>

<div class="container">
    <h2>Riwayat Pemesanan Anda</h2>
    <div style="margin-bottom: 20px;">
        <a href="index.php?page=dashboard" class="button-link">Kembali ke Dashboard</a>
        <a href="index.php?page=booking" class="button-link primary">Pesan Tiket Lagi</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Layanan/Rute</th>
                <th>Tanggal Order</th>
                <th>Tanggal Berangkat</th>
                <th>Jumlah</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($myBookings as $b): ?>
            <tr>
                <td><?= $b->layanan ?></td>
                <td><?= $b->tanggal_pesan ?></td>
                <td><span style="font-weight: bold; color: #007bff;"><?= $b->tanggal_jadwal ?></span></td>
                <td><?= $b->jumlah ?></td>
                <td>Rp <?= number_format($b->total, 0, ',', '.') ?></td>
                <td>
                    <?php if ($b->status == 'pending'): ?>
                        <span style="color: orange; font-weight: bold;">Belum Bayar</span>
                         <br><small>Pilih Metode Bayar dibawah</small>
                    <?php elseif ($b->status == 'menunggu_konfirmasi'): ?>
                        <span style="color: blue; font-weight: bold;">Menunggu Konfirmasi</span>
                    <?php elseif ($b->status == 'dibayar'): ?>
                        <span style="color: green; font-weight: bold;">Lunas</span>
                    <?php else: ?>
                        <span style="color: red; font-weight: bold;">Batal</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($b->status == 'pending'): ?>
                        <!-- Form Upload -->
                        <form method="POST" enctype="multipart/form-data" style="margin-bottom: 5px;">
                            <input type="hidden" name="action" value="upload">
                            <input type="hidden" name="pemesanan_id" value="<?= $b->id ?>">
                            <input type="hidden" name="total" value="<?= $b->total ?>">
                            <input type="file" name="bukti" required style="width: 150px;" accept="image/*">
                            <button type="submit" style="font-size: 10px;">Upload Bukti</button>
                        </form>
                        <!-- Form Cash -->
                        <form method="POST" onsubmit="return confirm('Bayar Cash di Lokasi?')">
                            <input type="hidden" name="action" value="cash">
                            <input type="hidden" name="pemesanan_id" value="<?= $b->id ?>">
                            <input type="hidden" name="total" value="<?= $b->total ?>">
                            <button type="submit" style="font-size: 10px; background: grey;">Bayar Cash</button>
                        </form>
                    <?php elseif ($b->status == 'dibayar'): ?>
                        <a href="index.php?page=invoice&id=<?= $b->id ?>" target="_blank">Cetak Invoice</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
