<?php
/**
 * ============================================================
 * FILE: controllers/LaporanController.php
 * ------------------------------------------------------------
 * Controller untuk manajemen Laporan Evaluasi
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ------------------------------------------------------------
 * @author  : M. Arifin Ilham
 * @project : Sistem Evaluasi Kepatuhan Keamanan Informasi
 * @version : 1.0
 * ============================================================
 */

require_once __DIR__ . '/../models/EvaluasiModel.php';
require_once __DIR__ . '/../models/DomainModel.php';

/**
 * Class LaporanController
 * Menangani generasi dan tampilan laporan evaluasi
 */
class LaporanController {
    /** @var EvaluasiModel Instance model Evaluasi */
    private $evaluasiModel;
    
    /** @var DomainModel Instance model Domain */
    private $domainModel;
    
    /**
     * Constructor - Inisialisasi model
     */
    public function __construct() {
        $this->evaluasiModel = new EvaluasiModel();
        $this->domainModel = new DomainModel();
    }
    
    /**
     * Menampilkan halaman laporan utama
     * 
     * @return void
     */
    public function index() {
        requireLogin();
        
        // Ambil data rekap keseluruhan
        $rekap = $this->evaluasiModel->getRekapEvaluasi();
        
        // Hitung persentase kepatuhan
        $persentaseKepatuhan = 0;
        if ($rekap['total_skor_maksimal'] > 0) {
            $persentaseKepatuhan = hitungPersentaseKepatuhan(
                $rekap['total_skor_diperoleh'],
                $rekap['total_skor_maksimal']
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
                'skor' => $domain['total_skor'],
                'maksimal' => $domain['total_skor_maksimal'],
                'rata_rata' => round($domain['rata_rata_skor'], 2)
            ];
        }
        
        // Ambil gap analysis
        $gapAnalysis = $this->evaluasiModel->getGapAnalysis();
        
        // Data untuk chart
        $chartLabels = [];
        $chartData = [];
        foreach ($domainStats as $stat) {
            $chartLabels[] = $stat['kode'];
            $chartData[] = $stat['persentase'];
        }
        
        include __DIR__ . '/../views/laporan/index.php';
    }
    
    /**
     * Generate dan simpan hasil rekap
     * 
     * @return void
     */
    public function generate() {
        requireRole(['admin', 'manajemen']);
        
        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['error'] = 'Token keamanan tidak valid.';
            redirectTo('index.php?page=laporan');
        }
        
        // Ambil data rekap
        $rekap = $this->evaluasiModel->getRekapEvaluasi();
        
        // Hitung persentase
        $persentase = 0;
        if ($rekap['total_skor_maksimal'] > 0) {
            $persentase = hitungPersentaseKepatuhan(
                $rekap['total_skor_diperoleh'],
                $rekap['total_skor_maksimal']
            );
        }
        
        // Dapatkan tingkat kematangan
        $kategori = getKategoriKepatuhan($persentase);
        
        // Simpan hasil rekap
        $dataRekap = [
            'rata_rata' => round($rekap['rata_rata_skor'], 2),
            'persentase_kepatuhan' => $persentase,
            'tingkat_kematangan' => $kategori['label'],
            'total_kontrol' => $rekap['total_kontrol'],
            'total_skor_diperoleh' => $rekap['total_skor_diperoleh'],
            'total_skor_maksimal' => $rekap['total_skor_maksimal'],
            'tanggal_generate' => date('Y-m-d')
        ];
        
        if ($this->evaluasiModel->simpanHasilRekap($dataRekap)) {
            $_SESSION['success'] = 'Hasil rekap berhasil disimpan.';
        } else {
            $_SESSION['error'] = 'Gagal menyimpan hasil rekap.';
        }
        
        redirectTo('index.php?page=laporan');
    }
    
    /**
     * Menampilkan riwayat hasil rekap
     * 
     * @return void
     */
    public function riwayat() {
        requireLogin();
        
        $riwayat = $this->evaluasiModel->getRiwayatRekap();
        
        include __DIR__ . '/../views/laporan/riwayat.php';
    }
    
    /**
     * Export laporan ke format yang bisa dicetak
     * 
     * @return void
     */
    public function export() {
        requireLogin();
        
        // Ambil data rekap
        $rekap = $this->evaluasiModel->getRekapEvaluasi();
        
        // Hitung persentase
        $persentaseKepatuhan = 0;
        if ($rekap['total_skor_maksimal'] > 0) {
            $persentaseKepatuhan = hitungPersentaseKepatuhan(
                $rekap['total_skor_diperoleh'],
                $rekap['total_skor_maksimal']
            );
        }
        
        $kategoriKepatuhan = getKategoriKepatuhan($persentaseKepatuhan);
        
        // Ambil statistik per domain
        $statistikDomain = $this->evaluasiModel->getStatistikPerDomain();
        
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
                'skor' => $domain['total_skor'],
                'maksimal' => $domain['total_skor_maksimal'],
                'rata_rata' => round($domain['rata_rata_skor'], 2)
            ];
        }
        
        // Ambil gap analysis
        $gapAnalysis = $this->evaluasiModel->getGapAnalysis();
        
        // Data untuk chart
        $chartLabels = [];
        $chartData = [];
        foreach ($domainStats as $stat) {
            $chartLabels[] = $stat['kode'];
            $chartData[] = $stat['persentase'];
        }
        
        // Load view export (layout khusus untuk print)
        include __DIR__ . '/../views/laporan/export.php';
    }
}
