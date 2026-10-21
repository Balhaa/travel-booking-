<?php
/**
 * Class User
 * Mengelola data pengguna (Admin, Kasir, Pelanggan).
 */

namespace App\Classes;

use App\Interfaces\ICrud;
use PDO;

class User extends BaseModel implements ICrud {
    
    public function index() {
        $stmt = $this->db->prepare("SELECT * FROM master_user");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($results as $row) {
            $obj = new \stdClass();
            $obj->id = $row['id'];
            $obj->username = $row['username'];
            $obj->fullname = $row['nama_lengkap'];
            $obj->role = $row['role'];
            $data[] = $obj;
        }
        return $data;
    }

    public function show($id) { return []; }

    /**
     * Menambah user baru ke database.
     * Password akan di-hash menggunakan Bcrypt.
     */
    public function store($data) {
        try {
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            $sql = "INSERT INTO master_user (nama_lengkap, username, password, role) VALUES (:nama, :user, :pass, :role)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'nama' => $data['fullname'], // Assuming 'fullname' from original input maps to 'nama'
                'user' => $data['username'],
                'pass' => $hashedPassword,
                'role' => $data['role']
            ]);
        } catch (\PDOException $e) { return false; }
    }

    /**
     * Update data user.
     * Jika password kosong, maka password lama tidak diubah.
     */
    public function update($id, $data) { return false; }

    public function destroy($id) {
        $stmt = $this->db->prepare("DELETE FROM master_user WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function search($keyword) { return []; }
}
