<?php
use App\Classes\Layanan;
use App\Classes\Pemesanan;
use App\Classes\Auth;

if (!Auth::check()) die("Login required");

$layananModel = new Layanan();
$layananList = $layananModel->index();

// Handle Booking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pemesananModel = new Pemesanan();
    $data = [
        'user_id' => Auth::user()['id'],
        'layanan_id' => $_POST['layanan_id'],
        'jumlah' => $_POST['jumlah'],
        'tanggal' => $_POST['tanggal_jadwal']
    ];

    if ($pemesananModel->store($data)) {
        $_SESSION['success'] = "Pemesanan berhasil! Silahkan lakukan pembayaran.";
        header("Location: index.php?page=riwayat"); // Redirect to history
        exit;
    }
}
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Pesan Tiket Travel</h2>
        <a href="index.php?page=dashboard" class="button-link">Kembali ke Dashboard</a>
    </div>
    <div class="service-list">
        <?php foreach ($layananList as $item): ?>
            <?php 
            // Skip jika kendaraan tidak tersedia
            if ($item->kendaraan_status != 'tersedia') continue; 
            ?>
            <div class="card service-card">
                <h3><?= htmlspecialchars($item->nama) ?></h3>
                <p><strong>Rute:</strong> <?= htmlspecialchars($item->rute) ?></p>
                <p><strong>Tanggal:</strong> <?= htmlspecialchars($item->tanggal) ?></p>
                <p><strong>Jam:</strong> <?= htmlspecialchars($item->jam) ?></p>
                <p><strong>Harga:</strong> Rp <?= number_format($item->harga, 0, ',', '.') ?></p>
                <p><strong>Sisa Kursi:</strong> <?= $item->stok_kursi ?></p>
                
                <?php if ($item->stok_kursi > 0): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="layanan_id" value="<?= $item->id ?>">
                        <input type="hidden" name="tanggal_jadwal" value="<?= $item->tanggal ?>">
                        <div style="margin-top: 10px;">
                            <label style="display:block; margin-bottom:5px;">Jumlah Tiket:</label>
                            <input type="number" name="jumlah" min="1" max="<?= $item->stok_kursi ?>" value="1" style="width: 80px; display: inline-block;">
                            <button type="submit">Pesan Sekarang</button>
                        </div>
                    </form>
                <?php else: ?>
                    <button disabled style="background: grey;">Habis</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .service-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }
    .service-card {
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 8px;
        background: white;
    }
</style>
