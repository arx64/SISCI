-- ============================================================
-- FILE: config/schema.sql
-- ------------------------------------------------------------
-- Skema Database Sistem Informasi Evaluasi Kepatuhan
-- ISO/IEC 27001 untuk SMKN 1 Galang
-- ------------------------------------------------------------
-- @author  : M. Arifin Ilham
-- @project : Sistem Evaluasi Kepatuhan Keamanan Informasi
-- @version : 1.0
-- ============================================================

-- Buat database (jika belum ada)
CREATE DATABASE IF NOT EXISTS sisci_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sisci_db;

-- ------------------------------------------------------------
-- Tabel: users
-- Deskripsi: Menyimpan data pengguna sistem (Admin, Auditor, Manajemen)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID unik pengguna',
    nama VARCHAR(100) NOT NULL COMMENT 'Nama lengkap pengguna',
    email VARCHAR(100) NOT NULL UNIQUE COMMENT 'Email pengguna (username login)',
    password VARCHAR(255) NOT NULL COMMENT 'Password yang sudah di-hash',
    role ENUM('admin', 'auditor', 'manajemen') NOT NULL DEFAULT 'auditor' COMMENT 'Role pengguna',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu pembuatan akun',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Waktu terakhir update',
    
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabel data pengguna sistem';

-- ------------------------------------------------------------
-- Tabel: domain_iso
-- Deskripsi: Menyimpan data domain/kategori ISO/IEC 27001
-- ------------------------------------------------------------
DROP TABLE IF EXISTS domain_iso;
CREATE TABLE domain_iso (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID unik domain',
    kode_domain VARCHAR(20) NOT NULL UNIQUE COMMENT 'Kode domain ISO (contoh: A.5, A.6)',
    nama_domain VARCHAR(200) NOT NULL COMMENT 'Nama domain/kategori',
    deskripsi TEXT COMMENT 'Deskripsi detail domain',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu pembuatan',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Waktu terakhir update',
    
    INDEX idx_kode (kode_domain)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabel domain ISO/IEC 27001';

-- ------------------------------------------------------------
-- Tabel: kontrol_iso
-- Deskripsi: Menyimpan data kontrol/control ISO/IEC 27001
-- ------------------------------------------------------------
DROP TABLE IF EXISTS kontrol_iso;
CREATE TABLE kontrol_iso (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID unik kontrol',
    domain_id INT NOT NULL COMMENT 'ID domain (FK ke domain_iso)',
    kode_kontrol VARCHAR(30) NOT NULL COMMENT 'Kode kontrol (contoh: A.5.1, A.5.2)',
    nama_kontrol VARCHAR(255) NOT NULL COMMENT 'Nama/judul kontrol',
    deskripsi TEXT COMMENT 'Deskripsi detail kontrol',
    level_maksimal INT DEFAULT 5 COMMENT 'Level maksimal penilaian (default 5)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu pembuatan',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Waktu terakhir update',
    
    FOREIGN KEY (domain_id) REFERENCES domain_iso(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_domain (domain_id),
    INDEX idx_kode_kontrol (kode_kontrol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabel kontrol ISO/IEC 27001';

-- ------------------------------------------------------------
-- Tabel: evaluasi
-- Deskripsi: Menyimpan data hasil evaluasi/penilaian kontrol
-- ------------------------------------------------------------
DROP TABLE IF EXISTS evaluasi;
CREATE TABLE evaluasi (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID unik evaluasi',
    kontrol_id INT NOT NULL COMMENT 'ID kontrol yang dievaluasi (FK ke kontrol_iso)',
    auditor_id INT NOT NULL COMMENT 'ID auditor yang melakukan evaluasi (FK ke users)',
    skor INT NOT NULL COMMENT 'Skor penilaian 0-5',
    catatan TEXT COMMENT 'Catatan/temuan auditor',
    status VARCHAR(50) DEFAULT 'Perlu Perbaikan' COMMENT 'Status evaluasi',
    tanggal DATE NOT NULL COMMENT 'Tanggal evaluasi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu pembuatan',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Waktu terakhir update',
    
    FOREIGN KEY (kontrol_id) REFERENCES kontrol_iso(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (auditor_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_evaluasi (kontrol_id, auditor_id, tanggal),
    INDEX idx_kontrol (kontrol_id),
    INDEX idx_auditor (auditor_id),
    INDEX idx_tanggal (tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabel hasil evaluasi kontrol';

-- ------------------------------------------------------------
-- Tabel: hasil_rekap
-- Deskripsi: Menyimpan data hasil rekap/evaluasi keseluruhan
-- ------------------------------------------------------------
DROP TABLE IF EXISTS hasil_rekap;
CREATE TABLE hasil_rekap (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID unik rekap',
    rata_rata DECIMAL(5,2) NOT NULL COMMENT 'Rata-rata skor keseluruhan',
    persentase_kepatuhan DECIMAL(5,2) NOT NULL COMMENT 'Persentase kepatuhan (0-100)',
    tingkat_kematangan VARCHAR(50) NOT NULL COMMENT 'Tingkat kematangan/kepatuhan',
    total_kontrol INT NOT NULL COMMENT 'Total kontrol yang dievaluasi',
    total_skor_diperoleh INT NOT NULL COMMENT 'Total skor yang diperoleh',
    total_skor_maksimal INT NOT NULL COMMENT 'Total skor maksimal',
    tanggal_generate DATE NOT NULL COMMENT 'Tanggal generate laporan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu pembuatan',
    
    INDEX idx_tanggal (tanggal_generate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabel hasil rekap evaluasi';

-- ------------------------------------------------------------
-- Insert Data Default: Admin User
-- Password: admin123 (dihash dengan bcrypt)
-- ------------------------------------------------------------
INSERT INTO users (nama, email, password, role) VALUES 
('Administrator', 'admin@sisci.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Note: Hash di atas adalah untuk password 'admin123'

-- ------------------------------------------------------------
-- Insert Data Default: Domain ISO/IEC 27001:2013 (14 Domain Utama)
-- ------------------------------------------------------------
INSERT INTO domain_iso (kode_domain, nama_domain, deskripsi) VALUES
('A.5', 'Kebijakan Keamanan Informasi', 'Kebijakan keamanan informasi memberikan arahan dan dukungan manajemen untuk keamanan informasi sesuai dengan persyaratan bisnis dan hukum yang relevan.'),
('A.6', 'Organisasi Keamanan Informasi', 'Kerangka kerja internal organisasi untuk mengelola dan mengimplementasikan keamanan informasi.'),
('A.7', 'Keamanan Sumber Daya Manusia', 'Mastikan bahwa karyawan dan kontraktor memahami tanggung jawab mereka dan sesuai untuk peran yang mereka jalani.'),
('A.8', 'Manajemen Aset', 'Mengidentifikasi dan melindungi aset organisasi secara tepat.'),
('A.9', 'Kontrol Akses', 'Membatasi akses ke informasi dan fasilitas pengolahan informasi.'),
('A.10', 'Kriptografi', 'Mastikan penggunaan kriptografi yang tepat dan efektif untuk melindungi kerahasiaan, keautentikan, dan integritas informasi.'),
('A.11', 'Keamanan Fisik dan Lingkungan', 'Mencegah akses fisik yang tidak sah, kerusakan, dan gangguan terhadap informasi dan fasilitas pengolahan informasi.'),
('A.12', 'Keamanan Operasional', 'Mastikan operasi yang benar dari fasilitas pengolahan informasi.'),
('A.13', 'Keamanan Komunikasi', 'Mastikan perlindungan informasi dalam jaringan dan fasilitas pengolahan informasi.'),
('A.14', 'Pengadaan, Pengembangan, dan Pemeliharaan Sistem', 'Mastikan keamanan informasi adalah bagian integral dari siklus hidup sistem informasi.'),
('A.15', 'Hubungan dengan Pemasok', 'Mastikan perlindungan aset organisasi yang dapat diakses oleh pemasok.'),
('A.16', 'Manajemen Insiden Keamanan Informasi', 'Mastikan pendekatan yang konsisten dan efektif terhadap manajemen insiden keamanan informasi.'),
('A.17', 'Aspek Keamanan Informasi dalam Manajemen Kontinuitas Bisnis', 'Keamanan informasi harus menjadi bagian integral dari proses manajemen kontinuitas bisnis organisasi.'),
('A.18', 'Kepatuhan', 'Mencegah pelanggaran persyaratan keamanan informasi hukum, statutori, regulasi, atau kontrak.');

-- ------------------------------------------------------------
-- Insert Data Default: Beberapa Kontrol ISO/IEC 27001 (Contoh)
-- ------------------------------------------------------------
-- Domain A.5: Kebijakan Keamanan Informasi
INSERT INTO kontrol_iso (domain_id, kode_kontrol, nama_kontrol, deskripsi, level_maksimal) VALUES
(1, 'A.5.1', 'Kebijakan untuk Keamanan Informasi', 'Kebijakan keamanan informasi harus ditetapkan, ditinjau secara berkala, dan dikomunikasikan kepada karyawan dan kontraktor yang relevan.', 5),
(1, 'A.5.2', 'Tinjauan Kebijakan Keamanan Informasi', 'Kebijakan keamanan informasi harus ditinjau secara teratur untuk memastikan kesesuaian dengan organisasi.', 5);

-- Domain A.6: Organisasi Keamanan Informasi
INSERT INTO kontrol_iso (domain_id, kode_kontrol, nama_kontrol, deskripsi, level_maksimal) VALUES
(2, 'A.6.1', 'Organisasi Internal', 'Kerangka kerja manajemen harus ditetapkan untuk menginisiasi dan mengendalikan implementasi keamanan informasi.', 5),
(2, 'A.6.2', 'Perangkat Seluler dan Telekerja', 'Kebijakan dan prosedur keamanan informasi harus ditetapkan untuk melindungi informasi yang diakses, diproses, atau disimpan di fasilitas telekerja.', 5);

-- Domain A.7: Keamanan Sumber Daya Manusia
INSERT INTO kontrol_iso (domain_id, kode_kontrol, nama_kontrol, deskripsi, level_maksimal) VALUES
(3, 'A.7.1', 'Sebelum Pekerjaan', 'Tinjauan latar belakang verifikasi harus dilakukan untuk semua kandidat untuk pekerjaan.', 5),
(3, 'A.7.2', 'Selama Pekerjaan', 'Semua karyawan dan kontraktor harus mengetahui dan mematuhi ketentuan keamanan informasi.', 5),
(3, 'A.7.3', 'Pemutusan dan Perubahan Pekerjaan', 'Tanggung jawab keamanan harus didefinisikan dan diberlakukan saat pemutusan atau perubahan pekerjaan.', 5);

-- Domain A.8: Manajemen Aset
INSERT INTO kontrol_iso (domain_id, kode_kontrol, nama_kontrol, deskripsi, level_maksimal) VALUES
(4, 'A.8.1', 'Tanggung Jawab atas Aset', 'Aset yang terkait dengan informasi dan fasilitas pengolahan informasi harus diidentifikasi dan daftar inventaris harus dikembangkan dan dipelihara.', 5),
(4, 'A.8.2', 'Klasifikasi Informasi', 'Informasi harus diklasifikasikan sesuai dengan kebutuhan keamanan organisasi.', 5),
(4, 'A.8.3', 'Penanganan Media', 'Media harus dikelola sesuai dengan klasifikasi keamanan informasi.', 5);

-- Domain A.9: Kontrol Akses
INSERT INTO kontrol_iso (domain_id, kode_kontrol, nama_kontrol, deskripsi, level_maksimal) VALUES
(5, 'A.9.1', 'Kebijakan Kontrol Akses Bisnis', 'Kebijakan kontrol akses harus ditetapkan, didokumentasikan, dan ditinjau berdasarkan kebutuhan bisnis.', 5),
(5, 'A.9.2', 'Manajemen Akses Pengguna', 'Pengguna harus didaftarkan dan dideregistrasi secara formal.', 5),
(5, 'A.9.3', 'Tanggung Jawab Pengguna', 'Pengguna harus bertanggung jawab untuk menjaga kerahasiaan informasi otentikasi mereka.', 5),
(5, 'A.9.4', 'Kontrol Akses Sistem dan Aplikasi', 'Akses ke sistem dan aplikasi harus dikendalikan.', 5);

-- Domain A.11: Keamanan Fisik dan Lingkungan
INSERT INTO kontrol_iso (domain_id, kode_kontrol, nama_kontrol, deskripsi, level_maksimal) VALUES
(7, 'A.11.1', 'Area Aman', 'Area aman harus ditetapkan untuk melindungi fasilitas pengolahan informasi.', 5),
(7, 'A.11.2', 'Keamanan Peralatan', 'Peralatan harus ditempatkan dan dilindungi untuk mengurangi risiko dari ancaman dan bahaya.', 5);

-- Domain A.12: Keamanan Operasional
INSERT INTO kontrol_iso (domain_id, kode_kontrol, nama_kontrol, deskripsi, level_maksimal) VALUES
(8, 'A.12.1', 'Prosedur Operasional dan Tanggung Jawab', 'Prosedur operasional harus dikembangkan dan didokumentasikan.', 5),
(8, 'A.12.2', 'Perlindungan dari Malware', 'Deteksi, pencegahan, dan pemulihan harus diimplementasikan untuk melindungi dari malware.', 5),
(8, 'A.12.3', 'Backup', 'Backup informasi, perangkat lunak, dan sistem harus dilakukan secara teratur.', 5);

-- Domain A.16: Manajemen Insiden Keamanan Informasi
INSERT INTO kontrol_iso (domain_id, kode_kontrol, nama_kontrol, deskripsi, level_maksimal) VALUES
(12, 'A.16.1', 'Manajemen Insiden dan Perbaikan', 'Prosedur dan tanggung jawab manajemen insiden harus ditetapkan.', 5);

-- ============================================================
-- SELESAI - Skema Database SISCI
-- ============================================================
