<?php
/**
 * ============================================================
 * FILE: models/UserModel.php
 * ------------------------------------------------------------
 * Model untuk manajemen data pengguna (users)
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ------------------------------------------------------------
 * @author  : M. Arifin Ilham
 * @project : Sistem Evaluasi Kepatuhan Keamanan Informasi
 * @version : 1.0
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Class UserModel
 * Menangani semua operasi database terkait pengguna
 */
class UserModel {
    /** @var PDO Instance koneksi database */
    private $db;
    
    /**
     * Constructor - Inisialisasi koneksi database
     */
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Mengambil semua data pengguna
     * 
     * @return array Array berisi semua data pengguna
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT id, nama, email, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Mengambil data pengguna berdasarkan ID
     * 
     * @param int $id ID pengguna
     * @return array|false Data pengguna atau false jika tidak ditemukan
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT id, nama, email, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Mengambil data pengguna berdasarkan email
     * Digunakan untuk proses login
     * 
     * @param string $email Email pengguna
     * @return array|false Data pengguna atau false jika tidak ditemukan
     */
    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    /**
     * Membuat pengguna baru
     * 
     * @param array $data Data pengguna (nama, email, password, role)
     * @return int|false ID pengguna yang baru dibuat atau false jika gagal
     */
    public function create($data) {
        // Hash password menggunakan algoritma bcrypt
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $stmt = $this->db->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([
            $data['nama'],
            $data['email'],
            $hashedPassword,
            $data['role']
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Update data pengguna
     * 
     * @param int $id ID pengguna
     * @param array $data Data yang akan diupdate
     * @return bool True jika berhasil, false jika gagal
     */
    public function update($id, $data) {
        // Jika password diupdate, hash terlebih dahulu
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("UPDATE users SET nama = ?, email = ?, password = ?, role = ? WHERE id = ?");
            return $stmt->execute([
                $data['nama'],
                $data['email'],
                $data['password'],
                $data['role'],
                $id
            ]);
        } else {
            // Update tanpa password
            $stmt = $this->db->prepare("UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?");
            return $stmt->execute([
                $data['nama'],
                $data['email'],
                $data['role'],
                $id
            ]);
        }
    }
    
    /**
     * Hapus pengguna berdasarkan ID
     * 
     * @param int $id ID pengguna
     * @return bool True jika berhasil, false jika gagal
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Verifikasi login pengguna
     * 
     * @param string $email Email pengguna
     * @param string $password Password plain text
     * @return array|false Data pengguna jika berhasil login, false jika gagal
     */
    public function verifyLogin($email, $password) {
        $user = $this->getByEmail($email);
        
        // Cek apakah user ditemukan dan password cocok
        if ($user && password_verify($password, $user['password'])) {
            // Hapus password dari return data untuk keamanan
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Update password pengguna
     * 
     * @param int $id ID pengguna
     * @param string $newPassword Password baru (plain text)
     * @return bool True jika berhasil, false jika gagal
     */
    public function updatePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }
    
    /**
     * Cek apakah email sudah terdaftar
     * 
     * @param string $email Email yang akan dicek
     * @param int|null $excludeId ID pengguna yang dikecualikan (untuk update)
     * @return bool True jika email sudah ada, false jika belum
     */
    public function isEmailExists($email, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
        }
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Menghitung total pengguna berdasarkan role
     * 
     * @param string|null $role Role pengguna (null untuk semua)
     * @return int Total pengguna
     */
    public function countUsers($role = null) {
        if ($role) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
            $stmt->execute([$role]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) FROM users");
        }
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Mengambil daftar auditor (untuk dropdown evaluasi)
     * 
     * @return array Array berisi data auditor
     */
    public function getAuditors() {
        $stmt = $this->db->query("SELECT id, nama FROM users WHERE role = 'auditor' ORDER BY nama");
        return $stmt->fetchAll();
    }
}
