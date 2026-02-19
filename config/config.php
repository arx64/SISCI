<?php
/**
 * ============================================================
 * FILE: config/config.php
 * ------------------------------------------------------------
 * Konfigurasi umum sistem dan session management
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ------------------------------------------------------------
 * @author  : M. Arifin Ilham
 * @project : Sistem Evaluasi Kepatuhan Keamanan Informasi
 * @version : 1.0
 * ============================================================
 */

// Mulai session dengan pengaturan keamanan yang ketat
session_start();

// Set timezone (sesuaikan dengan zona waktu Indonesia)
date_default_timezone_set('Asia/Jakarta');

// Definisi konstanta aplikasi
define('APP_NAME', 'SISCI - Sistem Evaluasi Kepatuhan');
define('APP_VERSION', '1.0');
define('BASE_URL', 'http://localhost/SISCI/'); // Sesuaikan dengan URL aplikasi
define('ASSETS_URL', BASE_URL . 'assets/');

// Fungsi untuk redirect
define('BASE_PATH', dirname(__DIR__) . '/');

/**
 * ============================================================
 * FUNGSI UTILITY DAN HELPER
 * ============================================================
 */

/**
 * Fungsi untuk melakukan redirect ke URL tertentu
 * 
 * @param string $url URL tujuan redirect
 * @return void
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Fungsi untuk melakukan redirect ke halaman base URL
 * 
 * @param string $path Path setelah base URL
 * @return void
 */
function redirectTo($path = '') {
    redirect(BASE_URL . $path);
}

/**
 * Fungsi untuk mengecek apakah user sudah login
 * 
 * @return bool True jika sudah login, false jika belum
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Fungsi untuk mengecek role user yang sedang login
 * 
 * @param string|array $roles Role yang diizinkan (string atau array)
 * @return bool True jika role sesuai, false jika tidak
 */
function hasRole($roles) {
    if (!isLoggedIn()) {
        return false;
    }
    
    // Jika roles adalah string, ubah menjadi array
    if (is_string($roles)) {
        $roles = [$roles];
    }
    
    return in_array($_SESSION['user_role'], $roles);
}

/**
 * Fungsi untuk mendapatkan data user yang sedang login
 * 
 * @return array|null Data user jika login, null jika belum
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'nama' => $_SESSION['user_nama'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role']
    ];
}

/**
 * Fungsi untuk memastikan user sudah login
 * Redirect ke halaman login jika belum login
 * 
 * @return void
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'Silakan login terlebih dahulu.';
        redirectTo('index.php?page=login');
    }
}

/**
 * Fungsi untuk memastikan user memiliki role tertentu
 * Redirect ke dashboard jika tidak memiliki akses
 * 
 * @param string|array $roles Role yang diizinkan
 * @return void
 */
function requireRole($roles) {
    requireLogin();
    
    if (!hasRole($roles)) {
        $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman tersebut.';
        redirectTo('index.php?page=dashboard');
    }
}

/**
 * Fungsi untuk sanitasi input string
 * Mencegah XSS (Cross-Site Scripting)
 * 
 * @param string $data Data yang akan disanitasi
 * @return string Data yang sudah disanitasi
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Fungsi untuk menampilkan pesan flash (sekali tampil)
 * 
 * @param string $type Tipe pesan (success, error, warning, info)
 * @param string $message Pesan yang akan ditampilkan
 * @return void
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Fungsi untuk mengambil dan menghapus pesan flash
 * 
 * @return array|null Data pesan flash atau null jika tidak ada
 */
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Fungsi untuk mengecek dan menampilkan pesan error dari session
 * 
 * @return string|null Pesan error atau null
 */
function getError() {
    if (isset($_SESSION['error'])) {
        $error = $_SESSION['error'];
        unset($_SESSION['error']);
        return $error;
    }
    return null;
}

/**
 * Fungsi untuk mengecek dan menampilkan pesan sukses dari session
 * 
 * @return string|null Pesan sukses atau null
 */
function getSuccess() {
    if (isset($_SESSION['success'])) {
        $success = $_SESSION['success'];
        unset($_SESSION['success']);
        return $success;
    }
    return null;
}

/**
 * Fungsi untuk generate CSRF token
 * 
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Fungsi untuk validasi CSRF token
 * 
 * @param string $token Token yang akan divalidasi
 * @return bool True jika valid, false jika tidak
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Fungsi untuk mendapatkan CSRF token input field
 * 
 * @return string HTML input hidden dengan CSRF token
 */
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

/**
 * Fungsi untuk format tanggal Indonesia
 * 
 * @param string $date Tanggal dalam format Y-m-d
 * @return string Tanggal dalam format Indonesia
 */
function formatDateIndo($date) {
    $bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    
    $d = date('d', strtotime($date));
    $m = date('m', strtotime($date));
    $y = date('Y', strtotime($date));
    
    return $d . ' ' . $bulan[$m] . ' ' . $y;
}

/**
 * Fungsi untuk menentukan kategori kepatuhan berdasarkan persentase
 * 
 * @param float $persentase Persentase kepatuhan (0-100)
 * @return array Data kategori dengan label dan class Bootstrap
 */
function getKategoriKepatuhan($persentase) {
    if ($persentase >= 81 && $persentase <= 100) {
        return [
            'label' => 'Sangat Patuh',
            'class' => 'success',
            'badge' => 'bg-success'
        ];
    } elseif ($persentase >= 61 && $persentase <= 80) {
        return [
            'label' => 'Patuh',
            'class' => 'info',
            'badge' => 'bg-info'
        ];
    } elseif ($persentase >= 41 && $persentase <= 60) {
        return [
            'label' => 'Cukup Patuh',
            'class' => 'warning',
            'badge' => 'bg-warning'
        ];
    } elseif ($persentase >= 21 && $persentase <= 40) {
        return [
            'label' => 'Tidak Patuh',
            'class' => 'danger',
            'badge' => 'bg-danger'
        ];
    } else {
        return [
            'label' => 'Sangat Tidak Patuh',
            'class' => 'dark',
            'badge' => 'bg-dark'
        ];
    }
}

/**
 * Fungsi untuk menghitung persentase kepatuhan
 * 
 * @param int $totalSkorDiperoleh Total skor yang diperoleh
 * @param int $totalSkorMaksimal Total skor maksimal
 * @return float Persentase kepatuhan (0-100)
 */
function hitungPersentaseKepatuhan($totalSkorDiperoleh, $totalSkorMaksimal) {
    if ($totalSkorMaksimal == 0) {
        return 0;
    }
    return round(($totalSkorDiperoleh / $totalSkorMaksimal) * 100, 2);
}
