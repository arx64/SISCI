<?php
/**
 * ============================================================
 * FILE: controllers/UserController.php
 * ------------------------------------------------------------
 * Controller untuk manajemen Pengguna oleh Admin
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ------------------------------------------------------------
 * @author  : M. Arifin Ilham
 * @project : Sistem Evaluasi Kepatuhan Keamanan Informasi
 * @version : 1.0
 * ============================================================
 */

require_once __DIR__ . '/../models/UserModel.php';

/**
 * Class UserController
 * Menangani CRUD User (hanya untuk Admin)
 */
class UserController {
    /** @var UserModel Instance model User */
    private $userModel;
    
    /**
     * Constructor - Inisialisasi model
     */
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    /**
     * Menampilkan daftar semua pengguna
     * 
     * @return void
     */
    public function index() {
        // Hanya admin yang bisa akses
        requireRole('admin');
        
        $users = $this->userModel->getAll();
        include __DIR__ . '/../views/users/index.php';
    }
    
    /**
     * Menampilkan form tambah pengguna
     * 
     * @return void
     */
    public function create() {
        requireRole('admin');
        
        include __DIR__ . '/../views/users/create.php';
    }
    
    /**
     * Menyimpan pengguna baru ke database
     * 
     * @return void
     */
    public function store() {
        requireRole('admin');
        
        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['error'] = 'Token keamanan tidak valid.';
            redirectTo('index.php?page=users&action=create');
        }
        
        // Ambil dan sanitasi data
        $nama = sanitize($_POST['nama'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'auditor';
        
        // Validasi input
        if (empty($nama) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Nama, email, dan password harus diisi.';
            redirectTo('index.php?page=users&action=create');
        }
        
        // Validasi format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Format email tidak valid.';
            redirectTo('index.php?page=users&action=create');
        }
        
        // Validasi password minimal 6 karakter
        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password minimal 6 karakter.';
            redirectTo('index.php?page=users&action=create');
        }
        
        // Validasi role
        $allowedRoles = ['admin', 'auditor', 'manajemen'];
        if (!in_array($role, $allowedRoles)) {
            $role = 'auditor';
        }
        
        // Cek apakah email sudah ada
        if ($this->userModel->isEmailExists($email)) {
            $_SESSION['error'] = 'Email sudah digunakan.';
            redirectTo('index.php?page=users&action=create');
        }
        
        // Simpan data
        $data = [
            'nama' => $nama,
            'email' => $email,
            'password' => $password,
            'role' => $role
        ];
        
        if ($this->userModel->create($data)) {
            $_SESSION['success'] = 'Pengguna berhasil ditambahkan.';
            redirectTo('index.php?page=users');
        } else {
            $_SESSION['error'] = 'Gagal menambahkan pengguna.';
            redirectTo('index.php?page=users&action=create');
        }
    }
    
    /**
     * Menampilkan form edit pengguna
     * 
     * @return void
     */
    public function edit() {
        requireRole('admin');
        
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            $_SESSION['error'] = 'ID pengguna tidak valid.';
            redirectTo('index.php?page=users');
        }
        
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Pengguna tidak ditemukan.';
            redirectTo('index.php?page=users');
        }
        
        include __DIR__ . '/../views/users/edit.php';
    }
    
    /**
     * Update pengguna di database
     * 
     * @return void
     */
    public function update() {
        requireRole('admin');
        
        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['error'] = 'Token keamanan tidak valid.';
            redirectTo('index.php?page=users');
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $_SESSION['error'] = 'ID pengguna tidak valid.';
            redirectTo('index.php?page=users');
        }
        
        // Ambil dan sanitasi data
        $nama = sanitize($_POST['nama'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'auditor';
        
        // Validasi input
        if (empty($nama) || empty($email)) {
            $_SESSION['error'] = 'Nama dan email harus diisi.';
            redirectTo('index.php?page=users&action=edit&id=' . $id);
        }
        
        // Validasi format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Format email tidak valid.';
            redirectTo('index.php?page=users&action=edit&id=' . $id);
        }
        
        // Validasi role
        $allowedRoles = ['admin', 'auditor', 'manajemen'];
        if (!in_array($role, $allowedRoles)) {
            $role = 'auditor';
        }
        
        // Cek apakah email sudah ada (kecuali untuk user ini sendiri)
        if ($this->userModel->isEmailExists($email, $id)) {
            $_SESSION['error'] = 'Email sudah digunakan.';
            redirectTo('index.php?page=users&action=edit&id=' . $id);
        }
        
        // Update data
        $data = [
            'nama' => $nama,
            'email' => $email,
            'role' => $role
        ];
        
        // Tambahkan password jika diisi
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Password minimal 6 karakter.';
                redirectTo('index.php?page=users&action=edit&id=' . $id);
            }
            $data['password'] = $password;
        }
        
        if ($this->userModel->update($id, $data)) {
            $_SESSION['success'] = 'Pengguna berhasil diperbarui.';
            redirectTo('index.php?page=users');
        } else {
            $_SESSION['error'] = 'Gagal memperbarui pengguna.';
            redirectTo('index.php?page=users&action=edit&id=' . $id);
        }
    }
    
    /**
     * Hapus pengguna dari database
     * 
     * @return void
     */
    public function delete() {
        requireRole('admin');
        
        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['error'] = 'Token keamanan tidak valid.';
            redirectTo('index.php?page=users');
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $_SESSION['error'] = 'ID pengguna tidak valid.';
            redirectTo('index.php?page=users');
        }
        
        // Cegah admin menghapus dirinya sendiri
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'Anda tidak dapat menghapus akun sendiri.';
            redirectTo('index.php?page=users');
        }
        
        if ($this->userModel->delete($id)) {
            $_SESSION['success'] = 'Pengguna berhasil dihapus.';
        } else {
            $_SESSION['error'] = 'Gagal menghapus pengguna.';
        }
        
        redirectTo('index.php?page=users');
    }
}
