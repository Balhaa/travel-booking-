<?php
use App\Classes\Layanan;
use App\Classes\Auth;

// Cek Role Admin
$user = Auth::user();
if ($user['role'] !== 'admin') {
    die("Akses Ditolak");
}

$layananModel = new Layanan();

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    if ($layananModel->destroy($_GET['id'])) {
        echo "<script>alert('Data berhasil dihapus'); window.location='index.php?page=layanan';</script>";
    }
}

$dataLayanan = $layananModel->index();
?>

<div class="container">
    <h2>Kelola Layanan Travel</h2>
    <div style="margin-bottom: 20px;">
        <a href="index.php?page=dashboard" class="button-link">Kembali ke Dashboard</a>
        <a href="index.php?page=layanan_create" class="button-link primary">Tambah Layanan</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Layanan</th>
                <th>Kendaraan</th>
                <th>Rute</th>
                <th>Tanggal</th>
                <th>Jam Berangkat</th>
                <th>Biaya</th>
                <th>Stok/Kapasitas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($dataLayanan as $item): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($item->nama) ?></td>
                <td>
                    <?= htmlspecialchars($item->kendaraan) ?>
                    <?php if ($item->kendaraan_status != 'tersedia'): ?>
                        <br><span style="color: red; font-size: 12px;">(Kendaraan Tidak Tersedia)</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($item->rute) ?></td>
                <td><?= htmlspecialchars($item->tanggal) ?></td>
                <td><?= htmlspecialchars($item->jam) ?></td>
                <td>Rp <?= number_format($item->harga, 0, ',', '.') ?></td>
                <td><?= $item->stok_kursi ?></td>
                <td>
                    <a href="index.php?page=layanan_edit&id=<?= $item->id ?>">Edit</a> | 
                    <a href="index.php?page=layanan&action=delete&id=<?= $item->id ?>" onclick="return confirm('Yakin hapus?')" style="color: red;">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
