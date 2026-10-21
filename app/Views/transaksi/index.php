<?php
use App\Classes\Transaksi;
use App\Classes\Auth;

$user = Auth::user();
if (!in_array($user['role'], ['admin', 'kasir'])) die("Akses Ditolak");

$model = new Transaksi();

// Handle Konfirmasi Pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_id'])) {
    if ($model->konfirmasi($_POST['confirm_id'])) {
        echo "<script>alert('Pembayaran Dikonfirmasi!'); window.location='index.php?page=transaksi';</script>";
    } else {
        echo "<script>alert('Gagal konfirmasi.');</script>";
    }
}

$list = $model->index();
?>

<div class="container">
    <h2>Kelola Transaksi & Pembayaran</h2>
    <div style="margin-bottom: 15px;">
        <a href="index.php?page=dashboard" class="button-link">Kembali ke Dashboard</a>
        <a href="index.php?page=laporan_print" target="_blank" class="button-link primary">Cetak Laporan Lengkap</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Pemesan</th>
                <th>Layanan</th>
                <th>Total Tagihan</th>
                <th>Metode / Bukti</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $t): ?>
            <tr>
                <td>#<?= $t->id ?></td>
                <td><?= htmlspecialchars($t->pemesan) ?></td>
                <td><?= htmlspecialchars($t->layanan) ?></td>
                <td>Rp <?= number_format($t->total, 0, ',', '.') ?></td>
                <td>
                    <?php if ($t->status == 'menunggu_konfirmasi'): ?>
                        <?php if ($t->bukti): ?>
                            <a href="public/uploads/bukti/<?= $t->bukti ?>" target="_blank">Lihat Bukti</a>
                        <?php else: ?>
                            Transfer (Tanpa Bukti)
                        <?php endif; ?>
                    <?php elseif ($t->status == 'pending'): ?>
                        -
                    <?php else: ?>
                        Selesai
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($t->status == 'pending'): ?>
                        <span style="color: red;">Belum Dibayar</span>
                    <?php elseif ($t->status == 'menunggu_konfirmasi'): ?>
                        <span style="color: blue;">Menunggu Konfirmasi</span>
                    <?php else: ?>
                        <span style="color: green;">Lunas</span>
                    <?php endif; ?>
                </td>
                <td>
                <td>
                    <?php if ($t->status == 'pending' || $t->status == 'menunggu_konfirmasi'): ?>
                        <form method="POST" onsubmit="return confirm('Konfirmasi lunas?');" style="display:inline;">
                            <input type="hidden" name="confirm_id" value="<?= $t->id ?>">
                            <button type="submit" style="background: green; padding: 5px 10px; font-size: 14px;">
                                <?= $t->status == 'pending' ? 'Bayar Manual' : 'Konfirmasi Lunas' ?>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="index.php?page=invoice&id=<?= $t->id ?>" target="_blank" class="button-link primary" style="font-size: 14px;">Cetak Invoice</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
