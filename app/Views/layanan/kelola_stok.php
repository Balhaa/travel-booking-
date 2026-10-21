<?php
use App\Classes\Layanan;
use App\Classes\Auth;

// Cek Role (Admin & Kasir boleh akses, tapi utamanya Kasir)
$user = Auth::user();
if (!in_array($user['role'], ['admin', 'kasir'])) {
    die("Akses Ditolak");
}

$layananModel = new Layanan();

// Handle Update Stok
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stok'])) {
    $id = $_POST['id'];
    $stok = $_POST['stok'];
    
    // Validasi sederhana
    if (is_numeric($stok) && $stok >= 0) {
        if ($layananModel->updateStok($id, $stok)) {
            $success_msg = "Stok berhasil diperbarui.";
        } else {
            $error_msg = "Gagal memperbarui stok.";
        }
    } else {
        $error_msg = "Stok harus berupa angka positif.";
    }
}

$dataLayanan = $layananModel->index();
?>

<div class="container">
    <h2>Kelola Stok Layanan</h2>
    
    <?php if (isset($success_msg)): ?>
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb; border-radius: 4px;">
            <?= $success_msg ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error_msg)): ?>
        <div class="alert alert-error" style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 4px;">
            <?= $error_msg ?>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 20px;">
        <a href="index.php?page=dashboard" class="button-link">Kembali ke Dashboard</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Layanan</th>
                <th>Rute</th>
                <th>Waktu</th>
                <th>Stok Saat Ini</th>
                <th>Update Stok</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($dataLayanan as $item): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($item->nama) ?></td>
                <td><?= htmlspecialchars($item->rute) ?></td>
                <td><?= htmlspecialchars($item->tanggal) . ' ' . htmlspecialchars($item->jam) ?></td>
                <td style="font-weight: bold; font-size: 1.1em;"><?= $item->stok_kursi ?></td>
                <td>
                    <form method="POST" action="" style="display: flex; gap: 5px; align-items: center;">
                        <input type="hidden" name="id" value="<?= $item->id ?>">
                        <input type="number" name="stok" value="<?= $item->stok_kursi ?>" min="0" style="width: 70px; padding: 5px;">
                        <button type="submit" name="update_stok" class="button-link primary" style="font-size: 12px; padding: 6px 10px;">Simpan</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
