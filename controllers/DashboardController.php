<?php
/**
 * ============================================================
 * FILE: controllers/DashboardController.php
 * ------------------------------------------------------------
 * Controller untuk menampilkan dashboard dan statistik sistem
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ------------------------------------------------------------
 * @author  : M. Arifin Ilham
 * @project : Sistem Evaluasi Kepatuhan Keamanan Informasi
 * @version : 1.0
 * ============================================================
 */

require_once __DIR__ . '/../models/DomainModel.php';
require_once __DIR__ . '/../models/KontrolModel.php';
require_once __DIR__ . '/../models/EvaluasiModel.php';
require_once __DIR__ . '/../models/UserModel.php';

/**
 * Class DashboardController
 * Menangani tampilan dashboard dan statistik sistem
 */
class DashboardController {
    /** @var DomainModel Instance model Domain */
    private $domainModel;
    
    /** @var KontrolModel Instance model Kontrol */
    private $kontrolModel;
    
    /** @var EvaluasiModel Instance model Evaluasi */
    private $evaluasiModel;
    
    /** @var UserModel Instance model User */
    private $userModel;
    
    /**
     * Constructor - Inisialisasi semua model
     */
    public function __construct() {
        $this->domainModel = new DomainModel();
        $this->kontrolModel = new KontrolModel();
        $this->evaluasiModel = new EvaluasiModel();
        $this->userModel = new UserModel();
    }
    
    /**
     * Menampilkan halaman dashboard dengan statistik
     * 
     * @return void
     */
    public function index() {
        // Pastikan user sudah login
        requireLogin();
        
        // Ambil statistik untuk dashboard
        $stats = [
            'total_domain' => $this->domainModel->countAll(),
            'total_kontrol' => $this->kontrolModel->countAll(),
            'total_users' => $this->userModel->countUsers(),
            'total_auditor' => $this->userModel->countUsers('auditor')
        ];
        
        // Ambil data evaluasi untuk perhitungan kepatuhan
        $rekapEvaluasi = $this->evaluasiModel->getRekapEvaluasi();
        
        // Hitung persentase kepatuhan keseluruhan
        $persentaseKepatuhan = 0;
        if ($rekapEvaluasi['total_skor_maksimal'] > 0) {
            $persentaseKepatuhan = hitungPersentaseKepatuhan(
                $rekapEvaluasi['total_skor_diperoleh'],
                $rekapEvaluasi['total_skor_maksimal']
            );
        }
        
        // Dapatkan kategori kepatuhan
        $kategoriKepatuhan = getKategoriKepatuhan($persentaseKepatuhan);
        
        // Ambil statistik per domain untuk grafik
        $statistikDomain = $this->evaluasiModel->getStatistikPerDomain();
        
        // Hitung persentase per domain
        $domainStats = [];
        foreach ($statistikDomain as $domain) {
            $persentase = 0;
            if ($domain['total_skor_maksimal'] > 0) {
                $persentase = hitungPersentaseKepatuhan(
                    $domain['total_skor'],
                    $domain['total_skor_maksimal']
                );
            }
            
            $domainStats[] = [
                'kode' => $domain['kode_domain'],
                'nama' => $domain['nama_domain'],
                'persentase' => $persentase,
                'total_kontrol' => $domain['total_kontrol'],
                'total_dievaluasi' => $domain['total_dievaluasi'],
                'rata_rata' => round($domain['rata_rata_skor'], 2)
            ];
        }
        
        // Ambil gap analysis (kontrol dengan skor < 3)
        $gapAnalysis = $this->evaluasiModel->getGapAnalysis();
        $totalGap = count($gapAnalysis);
        
        // Load view dashboard
        include __DIR__ . '/../views/dashboard.php';
    }
}
