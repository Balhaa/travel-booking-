-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Jan 2026 pada 01.29
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_travel_booking`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_layanan`
--

CREATE TABLE `master_layanan` (
  `id` int(11) NOT NULL,
  `nama_layanan` varchar(100) NOT NULL,
  `rute_asal` varchar(100) NOT NULL,
  `rute_tujuan` varchar(100) NOT NULL,
  `tanggal_berangkat` date NOT NULL,
  `waktu_berangkat` time NOT NULL,
  `biaya` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `kendaraan_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_layanan`
--

INSERT INTO `master_layanan` (`id`, `nama_layanan`, `rute_asal`, `rute_tujuan`, `tanggal_berangkat`, `waktu_berangkat`, `biaya`, `stok`, `created_at`, `kendaraan_id`) VALUES
(1, 'reguler', 'purwokerto kota', 'bandung', '2026-01-23', '16:30:00', 200000.00, 60, '2026-01-21 02:33:50', 1),
(2, 'lintas pagi', 'purwokerto', 'sidareja', '2026-01-23', '06:30:00', 50000.00, 42, '2026-01-21 02:45:19', 2),
(3, 'Lintas sore', 'sidareja', 'kp. rambutan', '2026-01-23', '18:00:00', 180000.00, 65, '2026-01-21 04:01:32', 3),
(5, 'trans banyumas', 'kembaran', 'pwt kota', '2026-01-23', '06:30:00', 5000.00, 8, '2026-01-21 05:30:30', 6),
(7, 'ha', 'in', 'ala', '2026-01-22', '14:42:00', 30000.00, 11, '2026-01-21 07:42:57', 11),
(8, 'gapura raya', 'purwokerto', 'pengandaran', '2026-01-24', '15:30:00', 70000.00, 50, '2026-01-21 08:25:31', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_user`
--

CREATE TABLE `master_user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','kasir','pelanggan') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_user`
--

INSERT INTO `master_user` (`id`, `username`, `password`, `nama_lengkap`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', '2026-01-21 02:28:04'),
(2, 'kasir', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kasir Utama', 'kasir', '2026-01-21 02:28:04'),
(3, 'pelanggan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pelanggan Setia', 'pelanggan', '2026-01-21 02:28:04'),
(7, 'iqbalpelanggan', '$2y$10$b/9OZmi4MgfjOi04E4A7qO4TZm8aUUOxqod1WoMOlfwJEs2xeQkAq', 'iqbal', 'pelanggan', '2026-01-21 03:13:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_kendaraan`
--

CREATE TABLE `tb_kendaraan` (
  `id` int(11) NOT NULL,
  `no_polisi` varchar(20) NOT NULL,
  `jenis_kendaraan` varchar(50) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `status` enum('tersedia','terpakai','perbaikan','tidak_tersedia') DEFAULT 'tersedia',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_kendaraan`
--

INSERT INTO `tb_kendaraan` (`id`, `no_polisi`, `jenis_kendaraan`, `kapasitas`, `status`, `created_at`) VALUES
(1, 'R9183UB', 'Sinar Jayain', 65, 'tidak_tersedia', '2026-01-21 02:34:47'),
(2, 'R1234AB', 'ASLI', 45, 'tersedia', '2026-01-21 02:45:50'),
(3, 'R7474QI', 'Maju jaya', 70, 'tidak_tersedia', '2026-01-21 03:21:19'),
(4, 'R0283KS', 'Gapura R', 50, 'tersedia', '2026-01-21 03:21:49'),
(6, 'R6735', 'Elff', 10, 'tersedia', '2026-01-21 04:44:18'),
(9, 'R5481BN', 'avanza', 12, 'tersedia', '2026-01-21 07:11:08'),
(11, 'R56731MJ', 'odong', 12, 'tersedia', '2026-01-21 07:30:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_pemesanan`
--

CREATE TABLE `tb_pemesanan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `layanan_id` int(11) NOT NULL,
  `tanggal_jadwal` date DEFAULT NULL,
  `tanggal_pemesanan` datetime DEFAULT current_timestamp(),
  `jumlah_tiket` int(11) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status_pemesanan` enum('pending','menunggu_konfirmasi','dibayar','batal') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_pemesanan`
--

INSERT INTO `tb_pemesanan` (`id`, `user_id`, `layanan_id`, `tanggal_jadwal`, `tanggal_pemesanan`, `jumlah_tiket`, `total_harga`, `status_pemesanan`) VALUES
(1, 3, 1, NULL, '2026-01-21 09:43:14', 2, 400000.00, 'dibayar'),
(2, 3, 2, NULL, '2026-01-21 09:53:05', 1, 50000.00, 'dibayar'),
(3, 3, 2, NULL, '2026-01-21 09:53:14', 1, 50000.00, 'dibayar'),
(4, 3, 1, NULL, '2026-01-21 10:05:02', 10, 2000000.00, 'dibayar'),
(5, 7, 2, NULL, '2026-01-21 10:15:47', 1, 50000.00, 'dibayar'),
(6, 3, 2, NULL, '2026-01-21 10:38:47', 1, 50000.00, 'dibayar'),
(7, 7, 3, NULL, '2026-01-21 11:02:30', 1, 180000.00, 'dibayar'),
(8, 7, 3, NULL, '2026-01-21 11:46:34', 1, 180000.00, 'dibayar'),
(9, 3, 5, NULL, '2026-01-21 12:34:45', 1, 5000.00, 'dibayar'),
(10, 3, 3, NULL, '2026-01-21 12:40:37', 1, 180000.00, 'dibayar'),
(11, 3, 5, NULL, '2026-01-21 12:46:33', 9, 45000.00, 'dibayar'),
(12, 3, 3, NULL, '2026-01-21 12:59:51', 1, 180000.00, 'dibayar'),
(13, 3, 1, '2026-01-23', '2026-01-21 13:40:29', 1, 200000.00, 'dibayar'),
(14, 3, 1, '2026-01-21', '2026-01-21 13:47:30', 1, 200000.00, 'dibayar'),
(15, 3, 1, '2026-01-24', '2026-01-21 13:56:55', 1, 200000.00, 'dibayar'),
(16, 3, 7, '2026-01-22', '2026-01-21 14:44:08', 1, 30000.00, 'dibayar'),
(17, 3, 5, '2026-01-23', '2026-01-21 14:46:39', 1, 5000.00, 'dibayar'),
(18, 7, 3, '2026-01-23', '2026-01-21 15:02:17', 1, 180000.00, 'dibayar'),
(19, 7, 1, '2026-01-23', '2026-01-21 15:05:03', 1, 200000.00, 'dibayar'),
(20, 7, 1, '2026-01-23', '2026-01-21 15:05:45', 1, 200000.00, 'dibayar'),
(21, 7, 2, '2026-01-23', '2026-01-21 15:08:52', 1, 50000.00, 'dibayar'),
(22, 7, 2, '2026-01-23', '2026-01-21 15:10:22', 1, 50000.00, 'dibayar'),
(23, 7, 1, '2026-01-23', '2026-01-21 15:31:17', 1, 200000.00, 'dibayar'),
(24, 7, 1, '2026-01-23', '2026-01-21 15:32:33', 1, 200000.00, 'dibayar'),
(25, 7, 2, '2026-01-23', '2026-01-21 16:49:59', 1, 50000.00, 'dibayar'),
(26, 7, 5, '2026-01-23', '2026-01-22 08:51:44', 1, 5000.00, 'dibayar');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id` int(11) NOT NULL,
  `pemesanan_id` int(11) NOT NULL,
  `tanggal_pembayaran` datetime DEFAULT current_timestamp(),
  `jumlah_bayar` decimal(10,2) NOT NULL,
  `metode_pembayaran` enum('cash','transfer') NOT NULL DEFAULT 'cash',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status_pembayaran` enum('pending','sukses','gagal') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id`, `pemesanan_id`, `tanggal_pembayaran`, `jumlah_bayar`, `metode_pembayaran`, `bukti_pembayaran`, `status_pembayaran`) VALUES
(1, 1, '2026-01-21 09:46:23', 400000.00, 'cash', NULL, 'sukses'),
(2, 3, '2026-01-21 09:55:42', 50000.00, 'cash', NULL, 'sukses'),
(3, 2, '2026-01-21 09:55:57', 50000.00, 'cash', NULL, 'sukses'),
(4, 4, '2026-01-21 10:05:59', 2000000.00, 'cash', NULL, 'sukses'),
(5, 5, '2026-01-21 10:16:24', 50000.00, 'cash', NULL, 'sukses'),
(6, 6, '2026-01-21 10:39:22', 50000.00, 'cash', NULL, 'sukses'),
(7, 7, '2026-01-21 11:03:09', 180000.00, 'cash', NULL, 'sukses'),
(8, 8, '2026-01-21 11:47:12', 180000.00, 'cash', NULL, 'sukses'),
(9, 9, '2026-01-21 12:35:09', 5000.00, 'cash', NULL, 'sukses'),
(10, 10, '2026-01-21 12:44:24', 180000.00, 'cash', NULL, 'sukses'),
(11, 11, '2026-01-21 12:46:54', 45000.00, 'cash', NULL, 'sukses'),
(12, 12, '2026-01-21 13:03:27', 180000.00, 'cash', NULL, 'sukses'),
(13, 13, '2026-01-21 13:41:03', 200000.00, 'cash', NULL, 'sukses'),
(14, 14, '2026-01-21 13:57:22', 200000.00, 'cash', NULL, 'sukses'),
(15, 15, '2026-01-21 13:57:32', 200000.00, 'cash', NULL, 'sukses'),
(16, 16, '2026-01-21 14:44:30', 30000.00, 'cash', NULL, 'sukses'),
(17, 17, '2026-01-21 14:47:10', 5000.00, 'cash', NULL, 'sukses'),
(18, 18, '2026-01-21 15:02:55', 180000.00, 'transfer', '1768982575_invoice.pdf', 'sukses'),
(19, 19, '2026-01-21 15:05:17', 200000.00, 'transfer', '1768982717_actvty uji.png', 'sukses'),
(20, 20, '2026-01-21 15:05:49', 200000.00, 'cash', NULL, 'sukses'),
(21, 21, '2026-01-21 15:08:59', 50000.00, 'cash', NULL, 'sukses'),
(22, 22, '2026-01-21 15:10:25', 50000.00, 'cash', NULL, 'sukses'),
(23, 23, '2026-01-21 15:31:27', 200000.00, 'transfer', '1768984287_TLHDJm8n4BsJy1ycd3G1Ve0X18Le31o8mCt43BiZr2rTqtOnnlZVRSjkQRlhRVlcpSURDp1J6gMkJhpVu_IkGPSWsU6e8MUICirAqU_rUvZfKiBWFZynCG1KuBuylaJ5fCFTbyVNn54S3dZ9yIzRqHOtBqums32bwMJ29r8eKC2M5QHXA_5CV00_bWvGASfG6VggFDH3xEPlxf4GbiHBZ.png', 'sukses'),
(24, 24, '2026-01-21 15:32:42', 200000.00, 'transfer', '1768984362_TLHDJm8n4BsJy1ycd3G1Ve0X18Le31o8mCt43BiZr2rTqtOnnlZVRSjkQRlhRVlcpSURDp1J6gMkJhpVu_IkGPSWsU6e8MUICirAqU_rUvZfKiBWFZynCG1KuBuylaJ5fCFTbyVNn54S3dZ9yIzRqHOtBqums32bwMJ29r8eKC2M5QHXA_5CV00_bWvGASfG6VggFDH3xEPlxf4GbiHBZ.png', 'sukses'),
(25, 25, '2026-01-21 16:50:09', 50000.00, 'transfer', '1768989009_actvty uji.png', 'sukses'),
(26, 26, '2026-01-22 08:51:51', 5000.00, 'transfer', '1769046711_TLHDJm8n4BsJy1ycd3G1Ve0X18Le31o8mCt43BiZr2rTqtOnnlZVRSjkQRlhRVlcpSURDp1J6gMkJhpVu_IkGPSWsU6e8MUICirAqU_rUvZfKiBWFZynCG1KuBuylaJ5fCFTbyVNn54S3dZ9yIzRqHOtBqums32bwMJ29r8eKC2M5QHXA_5CV00_bWvGASfG6VggFDH3xEPlxf4GbiHBZ.png', 'sukses');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `master_layanan`
--
ALTER TABLE `master_layanan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_user`
--
ALTER TABLE `master_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_polisi` (`no_polisi`);

--
-- Indeks untuk tabel `tb_pemesanan`
--
ALTER TABLE `tb_pemesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `layanan_id` (`layanan_id`);

--
-- Indeks untuk tabel `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pemesanan_id` (`pemesanan_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `master_layanan`
--
ALTER TABLE `master_layanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `master_user`
--
ALTER TABLE `master_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `tb_pemesanan`
--
ALTER TABLE `tb_pemesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tb_pemesanan`
--
ALTER TABLE `tb_pemesanan`
  ADD CONSTRAINT `tb_pemesanan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `master_user` (`id`),
  ADD CONSTRAINT `tb_pemesanan_ibfk_2` FOREIGN KEY (`layanan_id`) REFERENCES `master_layanan` (`id`);

--
-- Ketidakleluasaan untuk tabel `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD CONSTRAINT `tb_transaksi_ibfk_1` FOREIGN KEY (`pemesanan_id`) REFERENCES `tb_pemesanan` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
