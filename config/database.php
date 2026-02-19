<?php
/**
 * ============================================================
 * FILE: config/database.php
 * ------------------------------------------------------------
 * Konfigurasi koneksi database MySQL menggunakan PDO
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ------------------------------------------------------------
 * @author  : M. Arifin Ilham
 * @project : Sistem Evaluasi Kepatuhan Keamanan Informasi
 * @version : 1.0
 * ============================================================
 */

// Definisi konstanta database
// Sesuaikan dengan konfigurasi server Anda
define('DB_HOST', 'localhost');      // Host database
define('DB_NAME', 'sisci_db');       // Nama database
define('DB_USER', 'root');           // Username database
define('DB_PASS', '');               // Password database (kosong untuk default XAMPP)
define('DB_CHARSET', 'utf8mb4');     // Charset untuk mendukung karakter UTF-8

/**
 * Class Database
 * Menggunakan Singleton Pattern untuk memastikan hanya ada satu koneksi database
 */
class Database {
    /** @var PDO|null Instance koneksi database */
    private static $instance = null;
    
    /**
     * Mendapatkan instance koneksi database (Singleton Pattern)
     * 
     * @return PDO Object koneksi database
     * @throws PDOException jika koneksi gagal
     */
    public static function getInstance() {
        // Buat instance baru jika belum ada
        if (self::$instance === null) {
            try {
                // Data Source Name (DSN) untuk koneksi PDO
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                
                // Opsi konfigurasi PDO
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // Throw exception pada error
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Fetch array associative
                    PDO::ATTR_EMULATE_PREPARES => false,                // Nonaktifkan prepared statement emulasi
                ];
                
                // Membuat koneksi PDO baru
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
                
            } catch (PDOException $e) {
                // Log error dan tampilkan pesan user-friendly
                error_log("Koneksi Database Gagal: " . $e->getMessage());
                die("Terjadi kesalahan koneksi database. Silakan coba lagi nanti.");
            }
        }
        
        return self::$instance;
    }
    
    /**
     * Mencegah cloning instance (Singleton Pattern)
     */
    private function __clone() {}
    
    /**
     * Mencegah unserialize instance (Singleton Pattern)
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Fungsi helper untuk mendapatkan koneksi database
 * 
 * @return PDO Object koneksi database
 */
function getDB() {
    return Database::getInstance();
}
