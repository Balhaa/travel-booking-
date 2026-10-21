<?php
use App\Classes\Layanan;
use App\Classes\Kendaraan;
use App\Classes\Auth;

if (Auth::user()['role'] !== 'admin') die("Akses Ditolak");

$layananModel = new Layanan();
$kendaraanModel = new Kendaraan();

$id = $_GET['id'] ?? null;
if (!$id) die("ID tidak ditemukan");

$item = $layananModel->show($id);
if (!$item) die("Data tidak ditemukan");

$listKendaraan = $kendaraanModel->index();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Logic: Kalau ganti kendaraan, stok mau direset atau tidak?
    // Asumsi: Admin manual adjust stok kalau perlu, atau kita set stok = stok inputan form.
    
    $data = [
        'nama' => $_POST['nama'],
        'asal' => $_POST['asal'],
        'tujuan' => $_POST['tujuan'],
        'tanggal' => $_POST['tanggal'],
        'waktu' => $_POST['waktu'],
        'biaya' => $_POST['biaya'],
        'stok' => $_POST['stok'],
        'kendaraan_id' => $_POST['kendaraan_id']
    ];

    if ($layananModel->update($id, $data)) {
        header("Location: index.php?page=layanan");
        exit;
    } else {
        $error = "Gagal mengupdate data";
    }
}
?>

<div class="container">
    <h2>Edit Layanan</h2>
    <form method="POST" action="" style="max-width: 600px;">
        <div class="form-group">
            <label>Nama Layanan</label>
            <input type="text" name="nama" value="<?= $item['nama_layanan'] ?>" required>
        </div>
        
        <div class="form-group">
            <label>Kendaraan</label>
            <select name="kendaraan_id" required>
                <?php foreach ($listKendaraan as $k): ?>
                    <option value="<?= $k->id ?>" <?= $item['kendaraan_id'] == $k->id ? 'selected' : '' ?>>
                        <?= $k->jenis ?> - <?= $k->nopol ?> (Kap: <?= $k->kapasitas ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Rute Asal</label>
            <input type="text" name="asal" value="<?= $item['rute_asal'] ?>" required>
        </div>
        <div class="form-group">
            <label>Rute Tujuan</label>
            <input type="text" name="tujuan" value="<?= $item['rute_tujuan'] ?>" required>
        </div>
        <div class="form-group">
            <label>Tanggal Berangkat</label>
            <input type="date" name="tanggal" value="<?= $item['tanggal_berangkat'] ?>" required>
        </div>
        <div class="form-group">
            <label>Waktu Berangkat</label>
            <input type="time" name="waktu" value="<?= $item['waktu_berangkat'] ?>" required>
        </div>
        <div class="form-group">
            <label>Biaya (Rp)</label>
            <input type="number" name="biaya" value="<?= $item['biaya'] ?>" required>
        </div>
        <div class="form-group">
            <label>Stok Kursi</label>
            <input type="number" name="stok" value="<?= $item['stok'] ?>" required>
            <small>Sesuaikan manual jika ganti kendaraan.</small>
        </div>
        <button type="submit">Update</button>
        <a href="index.php?page=layanan" style="margin-left: 10px;">Batal</a>
    </form>
</div>
