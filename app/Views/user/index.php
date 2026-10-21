<?php
use App\Classes\User;
use App\Classes\Auth;

if (Auth::user()['role'] !== 'admin') die("Akses Ditolak");

$model = new User();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $data = [
        'username' => $_POST['username'],
        'password' => $_POST['password'],
        'fullname' => $_POST['fullname'],
        'role' => $_POST['role']
    ];
    if ($model->store($data)) echo "<script>alert('User berhasil dibuat!');</script>";
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $model->destroy($_GET['id']);
    echo "<script>window.location='index.php?page=user';</script>";
}

$list = $model->index();
?>

<div class="container">
    <h2>Kelola User</h2>
    <a href="index.php?page=dashboard" class="button-link">Kembali</a>

    <div style="margin-top: 20px; padding: 15px; border: 1px solid #ddd; background: white;">
        <h3>Tambah User Baru</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="text" name="fullname" placeholder="Nama Lengkap" required>
                <select name="role">
                    <option value="admin">Admin</option>
                    <option value="kasir">Kasir</option>
                    <option value="pelanggan">Pelanggan</option>
                </select>
            </div>
            <button type="submit" style="margin-top: 10px;">Tambah User</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u->username) ?></td>
                <td><?= htmlspecialchars($u->fullname) ?></td>
                <td><?= ucfirst($u->role) ?></td>
                <td>
                    <a href="index.php?page=user&action=delete&id=<?= $u->id ?>" onclick="return confirm('Hapus user ini?')" style="color:red;">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
