<?php
use App\Classes\Layanan;
use App\Classes\Kendaraan;
use App\Classes\Auth;

if (Auth::user()['role'] !== 'admin') die("Akses Ditolak");

// Ambil list kendaraan
$kendaraanModel = new Kendaraan();
$listKendaraan = $kendaraanModel->index();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $layananModel = new Layanan();
    $data = [
        'nama' => $_POST['nama'],
        'asal' => $_POST['asal'],
        'tujuan' => $_POST['tujuan'],
        'tanggal' => $_POST['tanggal'],
        'waktu' => $_POST['waktu'],
        'biaya' => $_POST['biaya'],
        'kendaraan_id' => $_POST['kendaraan_id']
        // Stok tidak dikirim dr form, tapi diambil dari helper di Model berdasarkan kendaraan
    ];

    if ($layananModel->store($data)) {
        header("Location: index.php?page=layanan");
        exit;
    } else {
        $error = "Gagal menyimpan data: " . ($_SESSION['error'] ?? '');
    }
}
?>

<div class="container">
    <h2>Tambah Layanan Baru</h2>
    <?php if (isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>

    <?php 
    // List kendaraan yang tersedia dan belum di-assign
    $availableVehicles = $kendaraanModel->getUnassignedVehicles();
    
    if (empty($availableVehicles)): 
    ?>
        <div class="alert alert-error">
            <strong>Peringatan:</strong> Tidak ada kendaraan yang tersedia (Status: Tersedia). 
            Silakan <a href="index.php?page=kendaraan">Tambah/Update Status Kendaraan</a> terlebih dahulu sebelum membuat Layanan.
        </div>
    <?php else: ?>

    <form method="POST" action="" style="max-width: 600px;">
        <div class="form-group">
            <label>Nama Layanan</label>
            <input type="text" name="nama" required>
        </div>
        
        <div class="form-group">
            <label>Pilih Kendaraan (Armada)</label>
            <select name="kendaraan_id" required>
                <option value="">-- Pilih Kendaraan --</option>
                <?php foreach ($availableVehicles as $k): ?>
                    <option value="<?= $k->id ?>">
                        <?= $k->jenis ?> - <?= $k->nopol ?> (Kap: <?= $k->kapasitas ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <small>Stok tiket akan otomatis diset sesuai kapasitas kendaraan.</small>
        </div>

        <div class="form-group">
            <label>Rute Asal</label>
            <input type="text" name="asal" required>
        </div>
        <div class="form-group">
            <label>Rute Tujuan</label>
            <input type="text" name="tujuan" required>
        </div>
        <div class="form-group">
            <label>Tanggal Berangkat</label>
            <input type="date" name="tanggal" required>
        </div>
        <div class="form-group">
            <label>Waktu Berangkat</label>
            <input type="time" name="waktu" required>
        </div>
        <div class="form-group">
            <label>Biaya (Rp)</label>
            <input type="number" name="biaya" required>
        </div>
        
        <button type="submit">Simpan</button>
        <a href="index.php?page=layanan" style="margin-left: 10px;">Batal</a>
    </form>
    <?php endif; ?>
</div>
