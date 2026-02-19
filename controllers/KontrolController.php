<?php
/**
 * ============================================================
 * FILE: controllers/KontrolController.php
 * ------------------------------------------------------------
 * Controller untuk manajemen Kontrol ISO/IEC 27001 (CRUD)
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ------------------------------------------------------------
 * @author  : M. Arifin Ilham
 * @project : Sistem Evaluasi Kepatuhan Keamanan Informasi
 * @version : 1.0
 * ============================================================
 */

require_once __DIR__ . '/../models/KontrolModel.php';
require_once __DIR__ . '/../models/DomainModel.php';

/**
 * Class KontrolController
 * Menangani CRUD Kontrol ISO/IEC 27001
 */
class KontrolController {
    /** @var KontrolModel Instance model Kontrol */
    private $kontrolModel;
    
    /** @var DomainModel Instance model Domain */
    private $domainModel;
    
    /**
     * Constructor - Inisialisasi model
     */
    public function __construct() {
        $this->kontrolModel = new KontrolModel();
        $this->domainModel = new DomainModel();
    }
    
    /**
     * Menampilkan daftar semua kontrol
     * 
     * @return void
     */
    public function index() {
        // Hanya admin yang bisa akses full CRUD
        requireRole('admin');
        
        $kontrols = $this->kontrolModel->getAll();
        include __DIR__ . '/../views/kontrol/index.php';
    }
    
    /**
     * Menampilkan form tambah kontrol
     * 
     * @return void
     */
    public function create() {
        requireRole('admin');
        
        // Ambil daftar domain untuk dropdown
        $domains = $this->domainModel->getForDropdown();
        
        include __DIR__ . '/../views/kontrol/create.php';
    }
    
    /**
     * Menyimpan kontrol baru ke database
     * 
     * @return void
     */
    public function store() {
        requireRole('admin');
        
        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['error'] = 'Token keamanan tidak valid.';
            redirectTo('index.php?page=kontrol&action=create');
        }
        
        // Ambil dan sanitasi data
        $domain_id = intval($_POST['domain_id'] ?? 0);
        $kode_kontrol = strtoupper(sanitize($_POST['kode_kontrol'] ?? ''));
        $nama_kontrol = sanitize($_POST['nama_kontrol'] ?? '');
        $deskripsi = sanitize($_POST['deskripsi'] ?? '');
        $level_maksimal = intval($_POST['level_maksimal'] ?? 5);
        
        // Validasi input
        if ($domain_id <= 0 || empty($kode_kontrol) || empty($nama_kontrol)) {
            $_SESSION['error'] = 'Domain, kode kontrol, dan nama kontrol harus diisi.';
            redirectTo('index.php?page=kontrol&action=create');
        }
        
        // Validasi level maksimal (harus 1-5)
        if ($level_maksimal < 1 || $level_maksimal > 5) {
            $level_maksimal = 5;
        }
        
        // Validasi format kode kontrol (harus A.X.Y)
        if (!preg_match('/^A\.\d+\.\d+$/', $kode_kontrol)) {
            $_SESSION['error'] = 'Format kode kontrol tidak valid. Contoh: A.5.1, A.6.2';
            redirectTo('index.php?page=kontrol&action=create');
        }
        
        // Cek apakah kode kontrol sudah ada dalam domain yang sama
        if ($this->kontrolModel->isKodeExists($kode_kontrol, $domain_id)) {
            $_SESSION['error'] = 'Kode kontrol sudah digunakan dalam domain ini.';
            redirectTo('index.php?page=kontrol&action=create');
        }
        
        // Simpan data
        $data = [
            'domain_id' => $domain_id,
            'kode_kontrol' => $kode_kontrol,
            'nama_kontrol' => $nama_kontrol,
            'deskripsi' => $deskripsi,
            'level_maksimal' => $level_maksimal
        ];
        
        if ($this->kontrolModel->create($data)) {
            $_SESSION['success'] = 'Kontrol berhasil ditambahkan.';
            redirectTo('index.php?page=kontrol');
        } else {
            $_SESSION['error'] = 'Gagal menambahkan kontrol.';
            redirectTo('index.php?page=kontrol&action=create');
        }
    }
    
    /**
     * Menampilkan form edit kontrol
     * 
     * @return void
     */
    public function edit() {
        requireRole('admin');
        
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            $_SESSION['error'] = 'ID kontrol tidak valid.';
            redirectTo('index.php?page=kontrol');
        }
        
        $kontrol = $this->kontrolModel->getById($id);
        
        if (!$kontrol) {
            $_SESSION['error'] = 'Kontrol tidak ditemukan.';
            redirectTo('index.php?page=kontrol');
        }
        
        // Ambil daftar domain untuk dropdown
        $domains = $this->domainModel->getForDropdown();
        
        include __DIR__ . '/../views/kontrol/edit.php';
    }
    
    /**
     * Update kontrol di database
     * 
     * @return void
     */
    public function update() {
        requireRole('admin');
        
        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['error'] = 'Token keamanan tidak valid.';
            redirectTo('index.php?page=kontrol');
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $_SESSION['error'] = 'ID kontrol tidak valid.';
            redirectTo('index.php?page=kontrol');
        }
        
        // Ambil dan sanitasi data
        $domain_id = intval($_POST['domain_id'] ?? 0);
        $kode_kontrol = strtoupper(sanitize($_POST['kode_kontrol'] ?? ''));
        $nama_kontrol = sanitize($_POST['nama_kontrol'] ?? '');
        $deskripsi = sanitize($_POST['deskripsi'] ?? '');
        $level_maksimal = intval($_POST['level_maksimal'] ?? 5);
        
        // Validasi input
        if ($domain_id <= 0 || empty($kode_kontrol) || empty($nama_kontrol)) {
            $_SESSION['error'] = 'Domain, kode kontrol, dan nama kontrol harus diisi.';
            redirectTo('index.php?page=kontrol&action=edit&id=' . $id);
        }
        
        // Validasi level maksimal
        if ($level_maksimal < 1 || $level_maksimal > 5) {
            $level_maksimal = 5;
        }
        
        // Validasi format kode kontrol
        if (!preg_match('/^A\.\d+\.\d+$/', $kode_kontrol)) {
            $_SESSION['error'] = 'Format kode kontrol tidak valid. Contoh: A.5.1, A.6.2';
            redirectTo('index.php?page=kontrol&action=edit&id=' . $id);
        }
        
        // Cek apakah kode kontrol sudah ada (kecuali untuk kontrol ini sendiri)
        if ($this->kontrolModel->isKodeExists($kode_kontrol, $domain_id, $id)) {
            $_SESSION['error'] = 'Kode kontrol sudah digunakan dalam domain ini.';
            redirectTo('index.php?page=kontrol&action=edit&id=' . $id);
        }
        
        // Update data
        $data = [
            'domain_id' => $domain_id,
            'kode_kontrol' => $kode_kontrol,
            'nama_kontrol' => $nama_kontrol,
            'deskripsi' => $deskripsi,
            'level_maksimal' => $level_maksimal
        ];
        
        if ($this->kontrolModel->update($id, $data)) {
            $_SESSION['success'] = 'Kontrol berhasil diperbarui.';
            redirectTo('index.php?page=kontrol');
        } else {
            $_SESSION['error'] = 'Gagal memperbarui kontrol.';
            redirectTo('index.php?page=kontrol&action=edit&id=' . $id);
        }
    }
    
    /**
     * Hapus kontrol dari database
     * 
     * @return void
     */
    public function delete() {
        requireRole('admin');
        
        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['error'] = 'Token keamanan tidak valid.';
            redirectTo('index.php?page=kontrol');
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $_SESSION['error'] = 'ID kontrol tidak valid.';
            redirectTo('index.php?page=kontrol');
        }
        
        if ($this->kontrolModel->delete($id)) {
            $_SESSION['success'] = 'Kontrol berhasil dihapus.';
        } else {
            $_SESSION['error'] = 'Gagal menghapus kontrol.';
        }
        
        redirectTo('index.php?page=kontrol');
    }
}
