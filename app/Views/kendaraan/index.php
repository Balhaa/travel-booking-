<?php
use App\Classes\Kendaraan;
use App\Classes\Auth;

if (Auth::user()['role'] !== 'admin') die("Akses Ditolak");

$model = new Kendaraan();

// Handle Add/view/Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $data = [
        'nopol' => $_POST['nopol'],
        'jenis' => $_POST['jenis'],
        'kapasitas' => $_POST['kapasitas'],
        'status' => $_POST['status']
    ];
    if ($model->store($data)) echo "<script>alert('Berhasil!');</script>";
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $model->destroy($_GET['id']);
    echo "<script>window.location='index.php?page=kendaraan';</script>";
}

$list = $model->index();
?>

<div class="container">
    <h2>Kelola Kendaraan</h2>
    <a href="index.php?page=dashboard" class="button-link">Kembali</a>
    
    <div style="margin-top: 20px; padding: 15px; border: 1px solid #ddd; background: white;">
        <h3>Tambah Kendaraan</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            <input type="text" name="nopol" placeholder="No Polisi" required>
            <input type="text" name="jenis" placeholder="Jenis (e.g. Elf, Bus)" required>
            <input type="number" name="kapasitas" placeholder="Kapasitas" required>
            <select name="status">
                <option value="tersedia">Tersedia</option>
                <option value="tidak_tersedia">Tidak Tersedia</option>
            </select>
            <button type="submit">Tambah</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>No Polisi</th>
                <th>Jenis</th>
                <th>Kapasitas</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $k): ?>
            <tr>
                <td><?= htmlspecialchars($k->nopol) ?></td>
                <td><?= htmlspecialchars($k->jenis) ?></td>
                <td><?= $k->kapasitas ?></td>
                <td><?= ucwords(str_replace('_', ' ', $k->status)) ?></td>
                <td>
                    <a href="index.php?page=kendaraan_edit&id=<?= $k->id ?>">Edit</a> | 
                    <a href="index.php?page=kendaraan&action=delete&id=<?= $k->id ?>" onclick="return confirm('Hapus?')" style="color:red;">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
