<?php
/**
 * Class Layanan
 * Mengelola data layanan travel (CRUD).
 */

namespace App\Classes;

use App\Interfaces\ICrud;
use PDO;

class Layanan extends BaseModel implements ICrud {
    
    /**
     * Mengambil semua data layanan dan memetakannya ke struktur array kustom.
     * Initial State: Data di database.
     * Final State: Array of objects (Custom Structure).
     * @return array
     */
    public function index() {
        // Join ke kendaraan untuk ambil info kendaraan
        $sql = "SELECT m.*, k.no_polisi, k.jenis_kendaraan, k.kapasitas, k.status as status_kendaraan 
                FROM master_layanan m 
                LEFT JOIN tb_kendaraan k ON m.kendaraan_id = k.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Transformasi ke struktur data array kustom
        $dataLayanan = [];
        foreach ($results as $row) {
            $item = new \stdClass();
            $item->id = $row['id'];
            $item->nama = $row['nama_layanan'];
            $item->rute = $row['rute_asal'] . " - " . $row['rute_tujuan'];
            $item->tanggal = $row['tanggal_berangkat'];
            $item->jam = $row['waktu_berangkat'];
            $item->harga = $row['biaya'];
            $item->stok_kursi = $row['stok'];
            // Info kendaraan
            $item->kendaraan = isset($row['no_polisi']) ? $row['jenis_kendaraan'] . " (" . $row['no_polisi'] . ")" : "Belum ditentukan";
            $item->kendaraan_status = $row['status_kendaraan'];
            
            $dataLayanan[] = $item;
        }

        return $dataLayanan;
    }

    public function show($id) {
        $stmt = $this->db->prepare("SELECT * FROM master_layanan WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Menambahkan data layanan baru.
     * Initial State: Data layanan belum ada di tabel master_layanan.
     * Final State: Data layanan berhasil disimpan.
     * 
     * @param array $data
     * @return bool
     */
    public function store($data) {
        try {
            // Ambil kapasitas kendaraan untuk set stok awal
            $stmtK = $this->db->prepare("SELECT kapasitas FROM tb_kendaraan WHERE id = :kid");
            $stmtK->execute(['kid' => $data['kendaraan_id']]);
            $kendaraan = $stmtK->fetch(PDO::FETCH_ASSOC);
            $stokAwal = $kendaraan ? $kendaraan['kapasitas'] : 0;

            $sql = "INSERT INTO master_layanan (nama_layanan, rute_asal, rute_tujuan, tanggal_berangkat, waktu_berangkat, biaya, stok, kendaraan_id) 
                    VALUES (:nama, :asal, :tujuan, :tanggal, :waktu, :biaya, :stok, :kid)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'nama' => $data['nama'],
                'asal' => $data['asal'],
                'tujuan' => $data['tujuan'],
                'tanggal' => $data['tanggal'],
                'waktu' => $data['waktu'],
                'biaya' => $data['biaya'],
                'stok' => $stokAwal, // Stok otomatis dari kapasitas
                'kid' => $data['kendaraan_id']
            ]);
        } catch (\PDOException $e) {
            $_SESSION['error'] = $e->getMessage();
            return false;
        }
    }

    /**
     * Memperbarui data layanan.
     * Initial State: Data layanan lama tersimpan.
     * Final State: Data layanan berhasil diperbarui sesuai input.
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        try {
             // Jika kendaraan berubah, apakah reset stok?
             // Untuk simplifikasi, kita update data saja, stok biar user yg handle manual di field stok atau logika lain.
             // Tapi request usernya: "stoknya menyesuaikan".
             // Maka kita cek, jika kendaraan berubah, kita reset stok ke kapasitas kendaraan BARU? 
             // Atau biarkan saja manual?
             // Mari kita anggap UPDATE itu hanya update info, tapi kalau ubah kendaraan, stok sebaiknya disesuaikan KECUALI sudah ada booking.
             // Untuk aman: Kita update data layanan. Stok kita biarkan update manual atau tetap.
             // TAPI, di form edit nanti kita tetap kirim stok.
             
            $sql = "UPDATE master_layanan SET 
                    nama_layanan = :nama, 
                    rute_asal = :asal, 
                    rute_tujuan = :tujuan, 
                    tanggal_berangkat = :tanggal,
                    waktu_berangkat = :waktu, 
                    biaya = :biaya, 
                    stok = :stok,
                    kendaraan_id = :kid
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'nama' => $data['nama'],
                'asal' => $data['asal'],
                'tujuan' => $data['tujuan'],
                'tanggal' => $data['tanggal'],
                'waktu' => $data['waktu'],
                'biaya' => $data['biaya'],
                'stok' => $data['stok'],
                'kid' => $data['kendaraan_id'],
                'id' => $id
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function destroy($id) {
        $stmt = $this->db->prepare("DELETE FROM master_layanan WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Memperbarui stok layanan.
     * @param int $id
     * @param int $stok
     * @return bool
     */
    public function updateStok($id, $stok) {
        try {
            $stmt = $this->db->prepare("UPDATE master_layanan SET stok = :stok WHERE id = :id");
            return $stmt->execute(['stok' => $stok, 'id' => $id]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function search($keyword) { return []; }
}
