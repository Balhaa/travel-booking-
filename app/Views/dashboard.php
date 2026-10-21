<?php
use App\Classes\Auth;

if (!Auth::check()) {
    header("Location: index.php?page=login");
    exit;
}

$user = Auth::user();
?>

<div class="container">
    <header style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 20px;">
        <h1>Dashboard <?= ucfirst($user['role']) ?></h1>
        <div>
            <span>Halo, <b><?= $user['nama_lengkap'] ?></b></span> | 
            <a href="index.php?action=logout" style="color: red; text-decoration: none;">Logout</a>
        </div>
    </header>

    <div class="content">
        <?php if ($user['role'] == 'admin'): ?>
            <!-- Menu Admin -->
            <div class="menu-grid">
                <a href="index.php?page=layanan" class="card">Kelola Layanan</a>
                <a href="index.php?page=kendaraan" class="card">Kelola Kendaraan</a>
                <a href="index.php?page=user" class="card">Kelola User</a>
                <a href="index.php?page=laporan" class="card">Laporan & Invoice</a>
            </div>
        <?php elseif ($user['role'] == 'kasir'): ?>
            <!-- Menu Kasir -->
             <div class="menu-grid">
                <a href="index.php?page=transaksi" class="card">Kelola Pemesanan & Pembayaran</a>
                <a href="index.php?page=layanan_stok" class="card">Kelola Stok Layanan</a>
                <a href="index.php?page=laporan" class="card">Laporan</a>
            </div>
        <?php elseif ($user['role'] == 'pelanggan'): ?>
            <!-- Menu Pelanggan -->
             <div class="menu-grid">
                <a href="index.php?page=booking" class="card">Pesan Tiket</a>
                <a href="index.php?page=riwayat" class="card">Riwayat Transaksi & Invoice</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Simple Logout Script Helper -->
<?php
// We can't include a file from href directly if it's a class file without routing.
// So let's handle logout via query param in index or a small script.
// For now, I'll update the link above to index.php?action=logout
?>
<script>
    document.querySelector('a[href="app/Classes/Logout.php"]').setAttribute('href', 'index.php?action=logout');
</script>
