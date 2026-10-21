<?php
/**
 * Class Auth
 * Menangani proses otentikasi pengguna (Login, Logout, Cek Sesi).
 */

namespace App\Classes;
use App\Config\Database;
use PDO;

class Auth {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Memverifikasi username dan password.
     * Jika benar, simpan data user ke SESSION.
     */
    public function login($username, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM master_user WHERE username = :uname");
            $stmt->execute(['uname' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Set Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                return true;
            }
            return false;
        } catch (\PDOException $e) {
            $_SESSION['error'] = "Database Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Menghapus sesi login (Logout).
     */
    public function logout() {
        session_destroy();
        header("Location: index.php?page=login");
        exit;
    }

    /**
     * Cek apakah user sudah login.
     * @return bool
     */
    public static function check() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Ambil data user yang sedang login.
     * @return array|null
     */
    public static function user() {
        if (self::check()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'] ?? '',
                'role' => $_SESSION['role'],
                'nama' => $_SESSION['nama_lengkap'] ?? ($_SESSION['nama'] ?? 'User'),
                'nama_lengkap' => $_SESSION['nama_lengkap'] ?? ($_SESSION['nama'] ?? 'User')
            ];
        }
        return null;
    }
}
