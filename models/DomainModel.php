<?php
/**
 * ============================================================
 * FILE: models/DomainModel.php
 * ------------------------------------------------------------
 * Model untuk manajemen data Domain ISO/IEC 27001
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ------------------------------------------------------------
 * @author  : M. Arifin Ilham
 * @project : Sistem Evaluasi Kepatuhan Keamanan Informasi
 * @version : 1.0
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Class DomainModel
 * Menangani semua operasi database terkait Domain ISO/IEC 27001
 */
class DomainModel {
    /** @var PDO Instance koneksi database */
    private $db;
    
    /**
     * Constructor - Inisialisasi koneksi database
     */
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Mengambil semua data domain ISO
     * 
     * @return array Array berisi semua data domain
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM domain_iso ORDER BY kode_domain ASC");
        return $stmt->fetchAll();
    }
    
    /**
     * Mengambil data domain berdasarkan ID
     * 
     * @param int $id ID domain
     * @return array|false Data domain atau false jika tidak ditemukan
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM domain_iso WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Mengambil data domain berdasarkan kode domain
     * 
     * @param string $kode Kode domain (contoh: A.5, A.6)
     * @return array|false Data domain atau false jika tidak ditemukan
     */
    public function getByKode($kode) {
        $stmt = $this->db->prepare("SELECT * FROM domain_iso WHERE kode_domain = ?");
        $stmt->execute([$kode]);
        return $stmt->fetch();
    }
    
    /**
     * Membuat domain baru
     * 
     * @param array $data Data domain (kode_domain, nama_domain, deskripsi)
     * @return int|false ID domain yang baru dibuat atau false jika gagal
     */
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO domain_iso (kode_domain, nama_domain, deskripsi) VALUES (?, ?, ?)");
        $result = $stmt->execute([
            $data['kode_domain'],
            $data['nama_domain'],
            $data['deskripsi'] ?? null
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Update data domain
     * 
     * @param int $id ID domain
     * @param array $data Data yang akan diupdate
     * @return bool True jika berhasil, false jika gagal
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE domain_iso SET kode_domain = ?, nama_domain = ?, deskripsi = ? WHERE id = ?");
        return $stmt->execute([
            $data['kode_domain'],
            $data['nama_domain'],
            $data['deskripsi'] ?? null,
            $id
        ]);
    }
    
    /**
     * Hapus domain berdasarkan ID
     * Note: Kontrol terkait akan otomatis terhapus karena ON DELETE CASCADE
     * 
     * @param int $id ID domain
     * @return bool True jika berhasil, false jika gagal
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM domain_iso WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Cek apakah kode domain sudah ada
     * 
     * @param string $kode Kode domain yang akan dicek
     * @param int|null $excludeId ID domain yang dikecualikan (untuk update)
     * @return bool True jika kode sudah ada, false jika belum
     */
    public function isKodeExists($kode, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM domain_iso WHERE kode_domain = ? AND id != ?");
            $stmt->execute([$kode, $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM domain_iso WHERE kode_domain = ?");
            $stmt->execute([$kode]);
        }
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Menghitung total domain ISO
     * 
     * @return int Total domain
     */
    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM domain_iso");
        return $stmt->fetchColumn();
    }
    
    /**
     * Mengambil domain dengan statistik kontrol
     * Berguna untuk dashboard dan laporan
     * 
     * @return array Array berisi domain dengan jumlah kontrol
     */
    public function getWithStats() {
        $stmt = $this->db->query("
            SELECT d.*, COUNT(k.id) as total_kontrol
            FROM domain_iso d
            LEFT JOIN kontrol_iso k ON d.id = k.domain_id
            GROUP BY d.id
            ORDER BY d.kode_domain ASC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Mengambil daftar domain untuk dropdown/select
     * 
     * @return array Array berisi id dan nama domain
     */
    public function getForDropdown() {
        $stmt = $this->db->query("SELECT id, CONCAT(kode_domain, ' - ', nama_domain) as nama FROM domain_iso ORDER BY kode_domain ASC");
        return $stmt->fetchAll();
    }
}
