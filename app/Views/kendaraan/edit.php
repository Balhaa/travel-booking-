<?php
use App\Classes\Kendaraan;
use App\Classes\Auth;

//admin only yg bisa
if (Auth::user()['role'] !== 'admin') die("Akses Ditolak");

$model = new Kendaraan();
$id = $_GET['id'] ?? null;
if (!$id) die("ID tidak ditemukan");

$item = $model->show($id);
if (!$item) die("Data tidak ditemukan");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'nopol' => $_POST['nopol'],
        'jenis' => $_POST['jenis'],
        'kapasitas' => $_POST['kapasitas'],
        'status' => $_POST['status']
    ];

    if ($model->update($id, $data)) {
        echo "<script>alert('Data berhasil diupdate'); window.location='index.php?page=kendaraan';</script>";
    } else {
        $error = "Gagal mengupdate data";
    }
}
?>

<div class="container">
    <h2>Edit Kendaraan</h2>
    <form method="POST" action="" style="max-width: 600px;">
        <div class="form-group">
            <label>No Polisi</label>
            <input type="text" name="nopol" value="<?= htmlspecialchars($item['no_polisi']) ?>" required>
        </div>
        <div class="form-group">
            <label>Jenis Kendaraan</label>
            <input type="text" name="jenis" value="<?= htmlspecialchars($item['jenis_kendaraan']) ?>" required>
        </div>
        <div class="form-group">
            <label>Kapasitas</label>
            <input type="number" name="kapasitas" value="<?= htmlspecialchars($item['kapasitas']) ?>" required>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="tersedia" <?= $item['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                <option value="tidak_tersedia" <?= $item['status'] == 'tidak_tersedia' ? 'selected' : '' ?>>Tidak Tersedia</option>
            </select>
        </div>
        <button type="submit">Update</button>
        <a href="index.php?page=kendaraan" class="button-link" style="margin-top: 10px; display:inline-block; text-align:center;">Batal</a>
    </form>
</div>
