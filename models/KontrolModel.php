<?php
/**
 * ============================================================
 * FILE: models/KontrolModel.php
 * ------------------------------------------------------------
 * Model untuk manajemen data Kontrol ISO/IEC 27001
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ------------------------------------------------------------
 * @author  : M. Arifin Ilham
 * @project : Sistem Evaluasi Kepatuhan Keamanan Informasi
 * @version : 1.0
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Class KontrolModel
 * Menangani semua operasi database terkait Kontrol ISO/IEC 27001
 */
class KontrolModel {
    /** @var PDO Instance koneksi database */
    private $db;
    
    /**
     * Constructor - Inisialisasi koneksi database
     */
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Mengambil semua data kontrol ISO dengan informasi domain
     * 
     * @return array Array berisi semua data kontrol dengan domain
     */
    public function getAll() {
        $stmt = $this->db->query("
            SELECT k.*, d.kode_domain, d.nama_domain
            FROM kontrol_iso k
            JOIN domain_iso d ON k.domain_id = d.id
            ORDER BY d.kode_domain ASC, k.kode_kontrol ASC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Mengambil data kontrol berdasarkan ID
     * 
     * @param int $id ID kontrol
     * @return array|false Data kontrol atau false jika tidak ditemukan
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT k.*, d.kode_domain, d.nama_domain
            FROM kontrol_iso k
            JOIN domain_iso d ON k.domain_id = d.id
            WHERE k.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Mengambil kontrol berdasarkan domain
     * 
     * @param int $domainId ID domain
     * @return array Array berisi kontrol dari domain tersebut
     */
    public function getByDomain($domainId) {
        $stmt = $this->db->prepare("
            SELECT k.*, d.kode_domain, d.nama_domain
            FROM kontrol_iso k
            JOIN domain_iso d ON k.domain_id = d.id
            WHERE k.domain_id = ?
            ORDER BY k.kode_kontrol ASC
        ");
        $stmt->execute([$domainId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Membuat kontrol baru
     * 
     * @param array $data Data kontrol (domain_id, kode_kontrol, nama_kontrol, deskripsi, level_maksimal)
     * @return int|false ID kontrol yang baru dibuat atau false jika gagal
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO kontrol_iso (domain_id, kode_kontrol, nama_kontrol, deskripsi, level_maksimal) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $result = $stmt->execute([
            $data['domain_id'],
            $data['kode_kontrol'],
            $data['nama_kontrol'],
            $data['deskripsi'] ?? null,
            $data['level_maksimal'] ?? 5
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Update data kontrol
     * 
     * @param int $id ID kontrol
     * @param array $data Data yang akan diupdate
     * @return bool True jika berhasil, false jika gagal
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE kontrol_iso 
            SET domain_id = ?, kode_kontrol = ?, nama_kontrol = ?, deskripsi = ?, level_maksimal = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['domain_id'],
            $data['kode_kontrol'],
            $data['nama_kontrol'],
            $data['deskripsi'] ?? null,
            $data['level_maksimal'] ?? 5,
            $id
        ]);
    }
    
    /**
     * Hapus kontrol berdasarkan ID
     * Note: Evaluasi terkait akan otomatis terhapus karena ON DELETE CASCADE
     * 
     * @param int $id ID kontrol
     * @return bool True jika berhasil, false jika gagal
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM kontrol_iso WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Cek apakah kode kontrol sudah ada dalam domain yang sama
     * 
     * @param string $kode Kode kontrol yang akan dicek
     * @param int $domainId ID domain
     * @param int|null $excludeId ID kontrol yang dikecualikan (untuk update)
     * @return bool True jika kode sudah ada, false jika belum
     */
    public function isKodeExists($kode, $domainId, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM kontrol_iso 
                WHERE kode_kontrol = ? AND domain_id = ? AND id != ?
            ");
            $stmt->execute([$kode, $domainId, $excludeId]);
        } else {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM kontrol_iso 
                WHERE kode_kontrol = ? AND domain_id = ?
            ");
            $stmt->execute([$kode, $domainId]);
        }
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Menghitung total kontrol ISO
     * 
     * @param int|null $domainId ID domain (null untuk semua)
     * @return int Total kontrol
     */
    public function countAll($domainId = null) {
        if ($domainId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM kontrol_iso WHERE domain_id = ?");
            $stmt->execute([$domainId]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) FROM kontrol_iso");
        }
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Mengambil kontrol dengan hasil evaluasi terbaru
     * 
     * @param int $domainId ID domain
     * @param string $tanggal Tanggal evaluasi (format Y-m-d)
     * @return array Array berisi kontrol dengan skor evaluasi
     */
    public function getWithEvaluasi($domainId, $tanggal = null) {
        $sql = "
            SELECT k.*, d.kode_domain, d.nama_domain,
                   e.skor, e.catatan, e.status, e.auditor_id, e.tanggal,
                   u.nama as nama_auditor
            FROM kontrol_iso k
            JOIN domain_iso d ON k.domain_id = d.id
            LEFT JOIN evaluasi e ON k.id = e.kontrol_id
            LEFT JOIN users u ON e.auditor_id = u.id
            WHERE k.domain_id = ?
        ";
        
        $params = [$domainId];
        
        if ($tanggal) {
            $sql .= " AND (e.tanggal = ? OR e.tanggal IS NULL)";
            $params[] = $tanggal;
        }
        
        $sql .= " ORDER BY k.kode_kontrol ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Mengambil kontrol dengan skor rendah (< 3) untuk gap analysis
     * 
     * @return array Array berisi kontrol yang perlu perbaikan
     */
    public function getKontrolRendah() {
        $stmt = $this->db->query("
            SELECT k.*, d.kode_domain, d.nama_domain,
                   e.skor, e.catatan, e.tanggal
            FROM kontrol_iso k
            JOIN domain_iso d ON k.domain_id = d.id
            JOIN evaluasi e ON k.id = e.kontrol_id
            WHERE e.skor < 3
            ORDER BY d.kode_domain ASC, k.kode_kontrol ASC
        ");
        return $stmt->fetchAll();
    }
}
