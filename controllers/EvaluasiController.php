<?php
/**
 * ============================================================
 * FILE: controllers/EvaluasiController.php
 * ------------------------------------------------------------
 * Controller untuk manajemen Evaluasi/Penilaian Kontrol ISO
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ------------------------------------------------------------
 * @author  : M. Arifin Ilham
 * @project : Sistem Evaluasi Kepatuhan Keamanan Informasi
 * @version : 1.0
 * ============================================================
 */

require_once __DIR__ . '/../models/EvaluasiModel.php';
require_once __DIR__ . '/../models/DomainModel.php';
require_once __DIR__ . '/../models/KontrolModel.php';

/**
 * Class EvaluasiController
 * Menangani proses evaluasi/penilaian kontrol ISO
 */
class EvaluasiController {
    /** @var EvaluasiModel Instance model Evaluasi */
    private $evaluasiModel;
    
    /** @var DomainModel Instance model Domain */
    private $domainModel;
    
    /** @var KontrolModel Instance model Kontrol */
    private $kontrolModel;
    
    /**
     * Constructor - Inisialisasi model
     */
    public function __construct() {
        $this->evaluasiModel = new EvaluasiModel();
        $this->domainModel = new DomainModel();
        $this->kontrolModel = new KontrolModel();
    }
    
    /**
     * Menampilkan daftar evaluasi
     * 
     * @return void
     */
    public function index() {
        requireLogin();
        
        $evaluasis = $this->evaluasiModel->getAll();
        include __DIR__ . '/../views/evaluasi/index.php';
    }
    
    /**
     * Menampilkan form pilih domain untuk evaluasi
     * 
     * @return void
     */
    public function pilihDomain() {
        // Hanya auditor dan admin yang bisa evaluasi
        requireRole(['admin', 'auditor']);
        
        // Ambil semua domain
        $domains = $this->domainModel->getWithStats();
        
        include __DIR__ . '/../views/evaluasi/pilih_domain.php';
    }
    
    /**
     * Menampilkan form evaluasi untuk domain tertentu
     * 
     * @return void
     */
    public function formEvaluasi() {
        requireRole(['admin', 'auditor']);
        
        $domainId = intval($_GET['domain_id'] ?? 0);
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        
        if ($domainId <= 0) {
            $_SESSION['error'] = 'Pilih domain terlebih dahulu.';
            redirectTo('index.php?page=evaluasi&action=pilih');
        }
        
        // Ambil data domain
        $domain = $this->domainModel->getById($domainId);
        
        if (!$domain) {
            $_SESSION['error'] = 'Domain tidak ditemukan.';
            redirectTo('index.php?page=evaluasi&action=pilih');
        }
        
        // Ambil kontrol beserta evaluasi yang sudah ada (jika ada)
        $kontrols = $this->kontrolModel->getWithEvaluasi($domainId, $tanggal);
        
        include __DIR__ . '/../views/evaluasi/form.php';
    }
    
    /**
     * Simpan hasil evaluasi
     * 
     * @return void
     */
    public function simpan() {
        requireRole(['admin', 'auditor']);
        
        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['error'] = 'Token keamanan tidak valid.';
            redirectTo('index.php?page=evaluasi');
        }
        
        $domainId = intval($_POST['domain_id'] ?? 0);
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $skorList = $_POST['skor'] ?? [];
        $catatanList = $_POST['catatan'] ?? [];
        
        if (empty($skorList)) {
            $_SESSION['error'] = 'Tidak ada data yang dievaluasi.';
            redirectTo('index.php?page=evaluasi&action=form&domain_id=' . $domainId . '&tanggal=' . $tanggal);
        }
        
        $auditorId = $_SESSION['user_id'];
        $dataList = [];
        
        // Persiapkan data untuk disimpan
        foreach ($skorList as $kontrolId => $skor) {
            $skor = intval($skor);
            
            // Validasi skor (0-5)
            if ($skor < 0 || $skor > 5) {
                continue;
            }
            
            $dataList[] = [
                'kontrol_id' => intval($kontrolId),
                'auditor_id' => $auditorId,
                'skor' => $skor,
                'catatan' => sanitize($catatanList[$kontrolId] ?? ''),
                'tanggal' => $tanggal
            ];
        }
        
        // Simpan batch evaluasi
        if ($this->evaluasiModel->saveBatch($dataList)) {
            $_SESSION['success'] = 'Evaluasi berhasil disimpan.';
        } else {
            $_SESSION['error'] = 'Gagal menyimpan evaluasi.';
        }
        
        redirectTo('index.php?page=evaluasi&action=form&domain_id=' . $domainId . '&tanggal=' . $tanggal);
    }
    
    /**
     * Menampilkan gap analysis
     * 
     * @return void
     */
    public function gapAnalysis() {
        requireLogin();
        
        // Ambil data gap (kontrol dengan skor < 3)
        $gaps = $this->evaluasiModel->getGapAnalysis();
        
        // Hitung statistik
        $totalGap = count($gaps);
        $totalPerDomain = [];
        
        foreach ($gaps as $gap) {
            $kodeDomain = $gap['kode_domain'];
            if (!isset($totalPerDomain[$kodeDomain])) {
                $totalPerDomain[$kodeDomain] = 0;
            }
            $totalPerDomain[$kodeDomain]++;
        }
        
        include __DIR__ . '/../views/evaluasi/gap_analysis.php';
    }
    
    /**
     * Hapus evaluasi
     * 
     * @return void
     */
    public function delete() {
        requireRole(['admin', 'auditor']);
        
        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['error'] = 'Token keamanan tidak valid.';
            redirectTo('index.php?page=evaluasi');
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $_SESSION['error'] = 'ID evaluasi tidak valid.';
            redirectTo('index.php?page=evaluasi');
        }
        
        // Cek apakah auditor yang menghapus adalah pemilik evaluasi atau admin
        $evaluasi = $this->evaluasiModel->getById($id);
        
        if (!$evaluasi) {
            $_SESSION['error'] = 'Evaluasi tidak ditemukan.';
            redirectTo('index.php?page=evaluasi');
        }
        
        // Hanya admin atau pemilik evaluasi yang bisa menghapus
        if ($_SESSION['user_role'] !== 'admin' && $evaluasi['auditor_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Anda tidak memiliki izin untuk menghapus evaluasi ini.';
            redirectTo('index.php?page=evaluasi');
        }
        
        if ($this->evaluasiModel->delete($id)) {
            $_SESSION['success'] = 'Evaluasi berhasil dihapus.';
        } else {
            $_SESSION['error'] = 'Gagal menghapus evaluasi.';
        }
        
        redirectTo('index.php?page=evaluasi');
    }
}
