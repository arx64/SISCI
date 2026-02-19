<?php
/**
 * ============================================================
 * FILE: models/EvaluasiModel.php
 * ------------------------------------------------------------
 * Model untuk manajemen data Evaluasi/Penilaian Kontrol
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ------------------------------------------------------------
 * @author  : M. Arifin Ilham
 * @project : Sistem Evaluasi Kepatuhan Keamanan Informasi
 * @version : 1.0
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Class EvaluasiModel
 * Menangani semua operasi database terkait Evaluasi Kontrol ISO/IEC 27001
 */
class EvaluasiModel {
    /** @var PDO Instance koneksi database */
    private $db;
    
    /**
     * Constructor - Inisialisasi koneksi database
     */
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Mengambil semua data evaluasi dengan detail lengkap
     * 
     * @return array Array berisi semua data evaluasi
     */
    public function getAll() {
        $stmt = $this->db->query("
            SELECT e.*, k.kode_kontrol, k.nama_kontrol, k.level_maksimal,
                   d.kode_domain, d.nama_domain,
                   u.nama as nama_auditor
            FROM evaluasi e
            JOIN kontrol_iso k ON e.kontrol_id = k.id
            JOIN domain_iso d ON k.domain_id = d.id
            JOIN users u ON e.auditor_id = u.id
            ORDER BY e.tanggal DESC, d.kode_domain ASC, k.kode_kontrol ASC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Mengambil data evaluasi berdasarkan ID
     * 
     * @param int $id ID evaluasi
     * @return array|false Data evaluasi atau false jika tidak ditemukan
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT e.*, k.kode_kontrol, k.nama_kontrol, k.level_maksimal,
                   d.kode_domain, d.nama_domain,
                   u.nama as nama_auditor
            FROM evaluasi e
            JOIN kontrol_iso k ON e.kontrol_id = k.id
            JOIN domain_iso d ON k.domain_id = d.id
            JOIN users u ON e.auditor_id = u.id
            WHERE e.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Mengambil evaluasi berdasarkan kontrol dan auditor
     * 
     * @param int $kontrolId ID kontrol
     * @param int $auditorId ID auditor
     * @param string $tanggal Tanggal evaluasi (Y-m-d)
     * @return array|false Data evaluasi atau false jika tidak ditemukan
     */
    public function getByKontrolAuditor($kontrolId, $auditorId, $tanggal) {
        $stmt = $this->db->prepare("
            SELECT * FROM evaluasi 
            WHERE kontrol_id = ? AND auditor_id = ? AND tanggal = ?
        ");
        $stmt->execute([$kontrolId, $auditorId, $tanggal]);
        return $stmt->fetch();
    }
    
    /**
     * Membuat evaluasi baru atau update jika sudah ada
     * 
     * @param array $data Data evaluasi (kontrol_id, auditor_id, skor, catatan, tanggal)
     * @return int|false ID evaluasi atau false jika gagal
     */
    public function save($data) {
        // Tentukan status berdasarkan skor
        $status = ($data['skor'] < 3) ? 'Perlu Perbaikan' : 'Memenuhi';
        
        // Cek apakah evaluasi sudah ada
        $existing = $this->getByKontrolAuditor(
            $data['kontrol_id'], 
            $data['auditor_id'], 
            $data['tanggal']
        );
        
        if ($existing) {
            // Update evaluasi yang sudah ada
            $stmt = $this->db->prepare("
                UPDATE evaluasi 
                SET skor = ?, catatan = ?, status = ?
                WHERE id = ?
            ");
            $result = $stmt->execute([
                $data['skor'],
                $data['catatan'] ?? null,
                $status,
                $existing['id']
            ]);
            return $result ? $existing['id'] : false;
        } else {
            // Insert evaluasi baru
            $stmt = $this->db->prepare("
                INSERT INTO evaluasi (kontrol_id, auditor_id, skor, catatan, status, tanggal) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $result = $stmt->execute([
                $data['kontrol_id'],
                $data['auditor_id'],
                $data['skor'],
                $data['catatan'] ?? null,
                $status,
                $data['tanggal']
            ]);
            return $result ? $this->db->lastInsertId() : false;
        }
    }
    
    /**
     * Simpan multiple evaluasi sekaligus (batch insert/update)
     * 
     * @param array $dataList Array berisi data evaluasi
     * @return bool True jika berhasil, false jika gagal
     */
    public function saveBatch($dataList) {
        try {
            $this->db->beginTransaction();
            
            foreach ($dataList as $data) {
                $result = $this->save($data);
                if (!$result) {
                    $this->db->rollBack();
                    return false;
                }
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error batch save evaluasi: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update data evaluasi
     * 
     * @param int $id ID evaluasi
     * @param array $data Data yang akan diupdate
     * @return bool True jika berhasil, false jika gagal
     */
    public function update($id, $data) {
        $status = ($data['skor'] < 3) ? 'Perlu Perbaikan' : 'Memenuhi';
        
        $stmt = $this->db->prepare("
            UPDATE evaluasi 
            SET skor = ?, catatan = ?, status = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['skor'],
            $data['catatan'] ?? null,
            $status,
            $id
        ]);
    }
    
    /**
     * Hapus evaluasi berdasarkan ID
     * 
     * @param int $id ID evaluasi
     * @return bool True jika berhasil, false jika gagal
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM evaluasi WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Mengambil hasil evaluasi per domain untuk laporan
     * 
     * @param string|null $tanggal Tanggal evaluasi (null untuk semua)
     * @return array Array berisi statistik per domain
     */
    public function getStatistikPerDomain($tanggal = null) {
        $sql = "
            SELECT 
                d.id,
                d.kode_domain,
                d.nama_domain,
                COUNT(DISTINCT k.id) as total_kontrol,
                COUNT(e.id) as total_dievaluasi,
                COALESCE(SUM(e.skor), 0) as total_skor,
                COALESCE(SUM(k.level_maksimal), 0) as total_skor_maksimal,
                COALESCE(AVG(e.skor), 0) as rata_rata_skor
            FROM domain_iso d
            LEFT JOIN kontrol_iso k ON d.id = k.domain_id
            LEFT JOIN evaluasi e ON k.id = e.kontrol_id
        ";
        
        $params = [];
        if ($tanggal) {
            $sql .= " WHERE e.tanggal = ? OR e.id IS NULL";
            $params[] = $tanggal;
        }
        
        $sql .= " GROUP BY d.id ORDER BY d.kode_domain ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Mengambil gap analysis - kontrol dengan skor < 3
     * 
     * @return array Array berisi kontrol yang perlu perbaikan
     */
    public function getGapAnalysis() {
        $stmt = $this->db->query("
            SELECT 
                e.id as evaluasi_id,
                e.skor,
                e.catatan,
                e.tanggal,
                k.kode_kontrol,
                k.nama_kontrol,
                k.level_maksimal,
                d.kode_domain,
                d.nama_domain,
                u.nama as nama_auditor,
                (k.level_maksimal - e.skor) as gap
            FROM evaluasi e
            JOIN kontrol_iso k ON e.kontrol_id = k.id
            JOIN domain_iso d ON k.domain_id = d.id
            JOIN users u ON e.auditor_id = u.id
            WHERE e.skor < 3
            ORDER BY d.kode_domain ASC, k.kode_kontrol ASC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Menghitung rekap keseluruhan evaluasi
     * 
     * @param string|null $tanggal Tanggal evaluasi (null untuk semua)
     * @return array Data rekap evaluasi
     */
    public function getRekapEvaluasi($tanggal = null) {
        $sql = "
            SELECT 
                COUNT(DISTINCT k.id) as total_kontrol,
                COUNT(e.id) as total_dievaluasi,
                COALESCE(SUM(e.skor), 0) as total_skor_diperoleh,
                COALESCE(SUM(k.level_maksimal), 0) as total_skor_maksimal,
                COALESCE(AVG(e.skor), 0) as rata_rata_skor
            FROM kontrol_iso k
            LEFT JOIN evaluasi e ON k.id = e.kontrol_id
        ";
        
        $params = [];
        if ($tanggal) {
            $sql .= " WHERE e.tanggal = ? OR e.id IS NULL";
            $params[] = $tanggal;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        // Hitung persentase kepatuhan
        if ($result['total_skor_maksimal'] > 0) {
            $result['persentase_kepatuhan'] = round(
                ($result['total_skor_diperoleh'] / $result['total_skor_maksimal']) * 100, 
                2
            );
        } else {
            $result['persentase_kepatuhan'] = 0;
        }
        
        return $result;
    }
    
    /**
     * Simpan hasil rekap ke database
     * 
     * @param array $data Data hasil rekap
     * @return int|false ID hasil rekap atau false jika gagal
     */
    public function simpanHasilRekap($data) {
        $stmt = $this->db->prepare("
            INSERT INTO hasil_rekap 
            (rata_rata, persentase_kepatuhan, tingkat_kematangan, total_kontrol, total_skor_diperoleh, total_skor_maksimal, tanggal_generate)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['rata_rata'],
            $data['persentase_kepatuhan'],
            $data['tingkat_kematangan'],
            $data['total_kontrol'],
            $data['total_skor_diperoleh'],
            $data['total_skor_maksimal'],
            $data['tanggal_generate']
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Mengambil riwayat hasil rekap
     * 
     * @return array Array berisi riwayat rekap
     */
    public function getRiwayatRekap() {
        $stmt = $this->db->query("
            SELECT * FROM hasil_rekap ORDER BY tanggal_generate DESC, created_at DESC
        ");
        return $stmt->fetchAll();
    }
}
