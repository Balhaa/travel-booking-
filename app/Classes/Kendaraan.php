<?php
/**
 * Class Kendaraan
 * Mengelola data armada kendaraan.
 */

namespace App\Classes;

use App\Interfaces\ICrud;
use PDO;

class Kendaraan extends BaseModel implements ICrud {
    
    /**
     * Mengambil semua data kendaraan.
     */
    public function index() {
        $stmt = $this->db->prepare("SELECT * FROM tb_kendaraan");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($results as $row) {
            $obj = new \stdClass();
            $obj->id = $row['id'];
            $obj->nopol = $row['no_polisi'];
            $obj->jenis = $row['jenis_kendaraan'];
            $obj->kapasitas = $row['kapasitas'];
            $obj->status = $row['status'];
            $data[] = $obj;
        }
        return $data;
    }

    public function show($id) {
        $stmt = $this->db->prepare("SELECT * FROM tb_kendaraan WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Menambahkan data kendaraan baru kr db.  
    public function store($data) {
        try {
            $sql = "INSERT INTO tb_kendaraan (no_polisi, jenis_kendaraan, kapasitas, status) VALUES (:nopol, :jenis, :kap, :sts)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'nopol' => $data['nopol'],
                'jenis' => $data['jenis'],
                'kap' => $data['kapasitas'],
                'sts' => $data['status']
            ]);
        } catch (\PDOException $e) { return false; }
    }

    public function update($id, $data) {
        try {
            $sql = "UPDATE tb_kendaraan SET no_polisi=:nopol, jenis_kendaraan=:jenis, kapasitas=:kap, status=:sts WHERE id=:id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'nopol' => $data['nopol'],
                'jenis' => $data['jenis'],
                'kap' => $data['kapasitas'],
                'sts' => $data['status'],
                'id' => $id
            ]);
        } catch (\PDOException $e) { return false; }
    }

    public function destroy($id) {
        $stmt = $this->db->prepare("DELETE FROM tb_kendaraan WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function search($keyword) { return []; }

    public function getUnassignedVehicles() {
        // Ambil kendaraan yg tersedia AND (tidak ada di master_layanan OR master_layanan.id is null) logic
        // Tapi "tidak muncul lagi" artinya 1 kendaraan = 1 layanan? 
        // Asumsikan 1 kendaraan hanya boleh 1 layanan aktif.
        
        $sql = "SELECT k.* FROM tb_kendaraan k 
                WHERE k.status = 'tersedia' 
                AND k.id NOT IN (SELECT kendaraan_id FROM master_layanan)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        foreach ($results as $row) {
            $obj = new \stdClass();
            $obj->id = $row['id'];
            $obj->nopol = $row['no_polisi'];
            $obj->jenis = $row['jenis_kendaraan'];
            $obj->kapasitas = $row['kapasitas'];
            $obj->status = $row['status'];
            $data[] = $obj;
        }
        return $data;
    }
}
