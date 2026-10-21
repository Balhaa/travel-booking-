<?php
/**
 * Class Pemesanan
 * Mengelola logika pemesanan tiket oleh pelanggan.
 */

namespace App\Classes;

use App\Interfaces\ICrud;
use PDO;

class Pemesanan extends BaseModel implements ICrud {
    
    /**
     * Menambil riwayat pemesanan.
     */
    public function index() {
        $user = Auth::user();
        if ($user['role'] == 'pelanggan') {
            $sql = "SELECT p.*, l.nama_layanan, l.rute_asal, l.rute_tujuan, u.nama_lengkap as pemesan 
                    FROM tb_pemesanan p 
                    JOIN master_layanan l ON p.layanan_id = l.id
                    JOIN master_user u ON p.user_id = u.id
                    WHERE p.user_id = :uid ORDER BY p.tanggal_pemesanan DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['uid' => $user['id']]);
        } else {
            // Admin/Kasir lihat semua
            $sql = "SELECT p.*, l.nama_layanan, l.rute_asal, l.rute_tujuan, u.nama_lengkap as pemesan 
                    FROM tb_pemesanan p 
                    JOIN master_layanan l ON p.layanan_id = l.id
                    JOIN master_user u ON p.user_id = u.id
                    ORDER BY p.tanggal_pemesanan DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map to custom object structure if needed, or simple array is fine for join results usually.
        // But adhering to rule "Buat struktur data array...":
        $bookings = [];
        foreach ($results as $row) {
             $item = new \stdClass();
             $item->id = $row['id'];
             $item->layanan = $row['nama_layanan'] . " (" . $row['rute_asal'] . "-" . $row['rute_tujuan'] . ")";
             $item->pemesan = isset($row['pemesan']) ? $row['pemesan'] : 'Saya';
             $item->pemesan = isset($row['pemesan']) ? $row['pemesan'] : 'Saya';
             $item->tanggal_pesan = $row['tanggal_pemesanan'];
             $item->tanggal_jadwal = isset($row['tanggal_jadwal']) ? $row['tanggal_jadwal'] : '-';
             $item->jumlah = $row['jumlah_tiket'];
             $item->total = $row['total_harga'];
             $item->status = $row['status_pemesanan'];
             $bookings[] = $item;
        }
        return $bookings;
    }

    public function show($id) {
         // Not implemented yet
    }

    public function store($data) {
        // Transactional: Kurangi stok, Buat pemesanan
        try {
            $this->db->beginTransaction();

            // Cek Stok dulu
            $stmtLayanan = $this->db->prepare("SELECT stok, biaya FROM master_layanan WHERE id = :lid");
            $stmtLayanan->execute(['lid' => $data['layanan_id']]);
            $layanan = $stmtLayanan->fetch(PDO::FETCH_ASSOC);

            if ($layanan['stok'] < $data['jumlah']) {
                $this->db->rollBack();
                $_SESSION['error'] = "Stok tiket tidak mencukupi!";
                return false;
            }

            // Hitung Total
            $totalHarga = $layanan['biaya'] * $data['jumlah'];

            // Kurangi Stok
            $stmtUpdate = $this->db->prepare("UPDATE master_layanan SET stok = stok - :jml WHERE id = :lid");
            $stmtUpdate->execute(['jml' => $data['jumlah'], 'lid' => $data['layanan_id']]);

            // Insert Booking
            $sql = "INSERT INTO tb_pemesanan (user_id, layanan_id, jumlah_tiket, total_harga, status_pemesanan, tanggal_pemesanan, tanggal_jadwal) 
                    VALUES (:uid, :lid, :jml, :total, 'pending', NOW(), :tgl)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'uid' => $data['user_id'],
                'lid' => $data['layanan_id'],
                'jml' => $data['jumlah'],
                'total' => $totalHarga,
                'tgl' => $data['tanggal']
            ]);

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            $_SESSION['error'] = "Booking Error: " . $e->getMessage();
            return false;
        }
    }

    public function update($id, $data) {
        // Update status pemesanan
    }

    public function destroy($id) {
        // Cancel logic
    }

    public function search($keyword) {
        // Search logic
    }
}
