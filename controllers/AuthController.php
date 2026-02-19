<?php
/**
 * ============================================================
 * FILE: controllers/AuthController.php
 * ------------------------------------------------------------
 * Controller untuk manajemen autentikasi pengguna
 * (Login, Logout, dan proses autentikasi)
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ------------------------------------------------------------
 * @author  : M. Arifin Ilham
 * @project : Sistem Evaluasi Kepatuhan Keamanan Informasi
 * @version : 1.0
 * ============================================================
 */

require_once __DIR__ . '/../models/UserModel.php';

/**
 * Class AuthController
 * Menangani semua proses autentikasi pengguna
 */
class AuthController {
    /** @var UserModel Instance model User */
    private $userModel;
    
    /**
     * Constructor - Inisialisasi model
     */
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    /**
     * Menampilkan halaman login
     * 
     * @return void
     */
    public function showLogin() {
        // Jika sudah login, redirect ke dashboard
        if (isLoggedIn()) {
            redirectTo('index.php?page=dashboard');
        }
        
        // Load view halaman login
        include __DIR__ . '/../views/auth/login.php';
    }
    
    /**
     * Proses login pengguna
     * 
     * @return void
     */
    public function login() {
        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['error'] = 'Token keamanan tidak valid.';
            redirectTo('index.php?page=login');
        }
        
        // Ambil data dari form dan sanitasi
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validasi input
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email dan password harus diisi.';
            redirectTo('index.php?page=login');
        }
        
        // Validasi format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Format email tidak valid.';
            redirectTo('index.php?page=login');
        }
        
        // Verifikasi login
        $user = $this->userModel->verifyLogin($email, $password);
        
        if ($user) {
            // Login berhasil - Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Regenerate session ID untuk keamanan
            session_regenerate_id(true);
            
            // Redirect ke dashboard
            $_SESSION['success'] = 'Selamat datang, ' . $user['nama'] . '!';
            redirectTo('index.php?page=dashboard');
        } else {
            // Login gagal
            $_SESSION['error'] = 'Email atau password salah.';
            redirectTo('index.php?page=login');
        }
    }
    
    /**
     * Proses logout pengguna
     * 
     * @return void
     */
    public function logout() {
        // Hapus semua data session
        $_SESSION = [];
        
        // Hapus cookie session jika ada
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }
        
        // Hancurkan session
        session_destroy();
        
        // Redirect ke halaman login
        redirectTo('index.php?page=login');
    }
    
    /**
     * Menampilkan halaman profil pengguna
     * 
     * @return void
     */
    public function profile() {
        requireLogin();
        
        $user = $this->userModel->getById($_SESSION['user_id']);
        include __DIR__ . '/../views/auth/profile.php';
    }
    
    /**
     * Update profil pengguna
     * 
     * @return void
     */
    public function updateProfile() {
        requireLogin();
        
        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['error'] = 'Token keamanan tidak valid.';
            redirectTo('index.php?page=profile');
        }
        
        $id = $_SESSION['user_id'];
        $nama = sanitize($_POST['nama'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password_lama = $_POST['password_lama'] ?? '';
        $password_baru = $_POST['password_baru'] ?? '';
        
        // Validasi input
        if (empty($nama) || empty($email)) {
            $_SESSION['error'] = 'Nama dan email harus diisi.';
            redirectTo('index.php?page=profile');
        }
        
        // Cek apakah email sudah digunakan user lain
        if ($this->userModel->isEmailExists($email, $id)) {
            $_SESSION['error'] = 'Email sudah digunakan oleh pengguna lain.';
            redirectTo('index.php?page=profile');
        }
        
        $data = [
            'nama' => $nama,
            'email' => $email,
            'role' => $_SESSION['user_role'] // Role tidak bisa diubah
        ];
        
        // Jika ada perubahan password
        if (!empty($password_baru)) {
            // Verifikasi password lama
            $user = $this->userModel->getById($id);
            $userWithPass = $this->userModel->getByEmail($user['email']);
            
            if (!password_verify($password_lama, $userWithPass['password'])) {
                $_SESSION['error'] = 'Password lama salah.';
                redirectTo('index.php?page=profile');
            }
            
            $data['password'] = $password_baru;
        }
        
        // Update data
        if ($this->userModel->update($id, $data)) {
            // Update session
            $_SESSION['user_nama'] = $nama;
            $_SESSION['user_email'] = $email;
            
            $_SESSION['success'] = 'Profil berhasil diperbarui.';
        } else {
            $_SESSION['error'] = 'Gagal memperbarui profil.';
        }
        
        redirectTo('index.php?page=profile');
    }
}
