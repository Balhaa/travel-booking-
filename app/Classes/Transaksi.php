<?php
/**
 * Class Transaksi
 * Mengelola pembayaran konfirmasi transaksi.
 */

namespace App\Classes;

use App\Interfaces\ICrud;
use PDO;

class Transaksi extends BaseModel implements ICrud {
    
    /**
     * Menampilkan daftar transaksi.
     * Jika Admin/Kasir: Lihat semua.
     * Jika Pelanggan: Hanya lihat milik sendiri.
     */
    public function index() {
        // Ambil semua pemesanan yang statusnya 'pending' atau semua untuk laporan
        $sql = "SELECT p.*, l.nama_layanan, u.nama_lengkap 
                FROM tb_pemesanan p 
                JOIN master_layanan l ON p.layanan_id = l.id
                JOIN master_user u ON p.user_id = u.id
                ORDER BY p.tanggal_pemesanan DESC"; // Tampilkan semua
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($results as $row) {
            $obj = new \stdClass();
            $obj->id = $row['id'];
            $obj->pemesan = $row['nama_lengkap'];
            $obj->layanan = $row['nama_layanan'];
            $obj->total = $row['total_harga'];
            $obj->status = $row['status_pemesanan'];
            $obj->status = $row['status_pemesanan'];
            // Cek ada bukti bayar gak di transaksi
            $sqlT = "SELECT bukti_pembayaran FROM tb_transaksi WHERE pemesanan_id = :pid ORDER BY id DESC LIMIT 1";
            $stmtT = $this->db->prepare($sqlT);
            $stmtT->execute(['pid' => $row['id']]);
            $trans = $stmtT->fetch(PDO::FETCH_ASSOC);
            $obj->bukti = $trans ? $trans['bukti_pembayaran'] : null;
            
            $obj->tanggal = $row['tanggal_pemesanan'];
            $data[] = $obj;
        }
        return $data;
    }

    public function show($id) {
        // Ambil detail transaksi untuk invoice
        $sql = "SELECT p.*, p.tanggal_jadwal, l.nama_layanan, l.rute_asal, l.rute_tujuan, l.waktu_berangkat, u.nama_lengkap 
                FROM tb_pemesanan p 
                JOIN master_layanan l ON p.layanan_id = l.id
                JOIN master_user u ON p.user_id = u.id
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function store($data) {
        // Digunakan untuk pembayaran manual/cash atau inisialisasi pembayaran
        // $data: pemesanan_id, jumlah_bayar, metode, bukti (optional)
        try {
            $this->db->beginTransaction();

            $sqlTrans = "INSERT INTO tb_transaksi (pemesanan_id, jumlah_bayar, metode_pembayaran, bukti_pembayaran, status_pembayaran) 
                         VALUES (:pid, :jml, :metode, :bukti, :status)";
            $stmt = $this->db->prepare($sqlTrans);
            
            $statusBayar = ($data['metode'] == 'cash') ? 'pending' : 'pending'; // Cash pun pending sampai admin confirm terima uang
            $bukti = isset($data['bukti']) ? $data['bukti'] : null;

            $stmt->execute([
                'pid' => $data['pemesanan_id'],
                'jml' => $data['jumlah_bayar'],
                'metode' => $data['metode'],
                'bukti' => $bukti,
                'status' => $statusBayar
            ]);

            // Update status pemesanan
            $sqlUpdate = "UPDATE tb_pemesanan SET status_pemesanan = 'menunggu_konfirmasi' WHERE id = :pid";
            $stmtUp = $this->db->prepare($sqlUpdate);
            $stmtUp->execute(['pid' => $data['pemesanan_id']]);

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            $_SESSION['error'] = "Gagal: " . $e->getMessage();
            return false;
        }
    }

    public function konfirmasi($pemesananId) {
        try {
            $this->db->beginTransaction();

            // Update Pemesanan
            $stmt = $this->db->prepare("UPDATE tb_pemesanan SET status_pemesanan = 'dibayar' WHERE id = :id");
            $stmt->execute(['id' => $pemesananId]);

            // Update Transaksi terakhir
            $stmt2 = $this->db->prepare("UPDATE tb_transaksi SET status_pembayaran = 'sukses' WHERE pemesanan_id = :id AND status_pembayaran = 'pending'");
            $stmt2->execute(['id' => $pemesananId]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Unused methods for this specific flow but required by Interface
    public function update($id, $data) { return false; }
    public function destroy($id) { return false; }
    public function search($keyword) { return []; }
}
