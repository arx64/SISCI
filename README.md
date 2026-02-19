# SISCI - Sistem Evaluasi Kepatuhan Keamanan Informasi

## 📋 Informasi Proyek

**Judul Penelitian:** Evaluasi Kepatuhan Keamanan Informasi Menggunakan Kerangka Kerja ISO/IEC 27001 Pada SMKN 1 Galang

**Penulis:** M. Arifin Ilham

**Versi:** 1.0

---

## 🎯 Deskripsi Sistem

SISCI adalah sistem informasi berbasis web yang digunakan untuk melakukan evaluasi tingkat kepatuhan keamanan informasi berdasarkan standar **ISO/IEC 27001:2013** pada SMKN 1 Galang. Sistem ini mampu:

- Melakukan pengisian checklist kontrol ISO/IEC 27001
- Menghitung tingkat kepatuhan otomatis
- Melakukan gap analysis
- Menghasilkan laporan evaluasi

---

## 🏗️ Arsitektur Sistem

Sistem ini dibangun menggunakan arsitektur **MVC (Model-View-Controller)** sederhana dengan:

- **Backend:** PHP Native (tanpa framework)
- **Database:** MySQL dengan PDO
- **Frontend:** Bootstrap 5, Chart.js
- **Keamanan:** CSRF Token, Password Hashing, Session Management

---

## 📁 Struktur Folder

```
SISCI/
├── config/                 # Konfigurasi sistem
│   ├── config.php         # Konfigurasi umum dan helper functions
│   ├── database.php       # Konfigurasi koneksi database
│   └── schema.sql         # Skema database
├── controllers/           # Controller MVC
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── DomainController.php
│   ├── EvaluasiController.php
│   ├── KontrolController.php
│   ├── LaporanController.php
│   └── UserController.php
├── models/                # Model MVC
│   ├── DomainModel.php
│   ├── EvaluasiModel.php
│   ├── KontrolModel.php
│   └── UserModel.php
├── views/                 # View MVC
│   ├── auth/             # Halaman autentikasi
│   ├── domain/           # Halaman manajemen domain
│   ├── evaluasi/         # Halaman evaluasi
│   ├── kontrol/          # Halaman manajemen kontrol
│   ├── laporan/          # Halaman laporan
│   ├── layouts/          # Layout template
│   └── users/            # Halaman manajemen user
├── assets/               # Asset statis
│   ├── css/
│   ├── js/
│   └── images/
├── index.php            # Entry point aplikasi
└── README.md            # Dokumentasi ini
```

---

## 🚀 Cara Instalasi

### 1. Persyaratan Sistem

- PHP >= 7.4
- MySQL >= 5.7 atau MariaDB >= 10.2
- Web Server (Apache/Nginx)
- Browser modern

### 2. Langkah Instalasi

#### Step 1: Clone/Download Proyek
```bash
# Copy folder proyek ke htdocs (XAMPP) atau www (WAMP)
# Path: C:\xampp\htdocs\SISCI\
```

#### Step 2: Buat Database
```bash
# Buka phpMyAdmin
# Buat database baru dengan nama: sisci_db
# Import file: config/schema.sql
```

#### Step 3: Konfigurasi Database
Edit file `config/database.php`:
```php
define('DB_HOST', 'localhost');      // Host database
define('DB_NAME', 'sisci_db');       // Nama database
define('DB_USER', 'root');           // Username MySQL
define('DB_PASS', '');               // Password MySQL (kosong untuk XAMPP default)
```

#### Step 4: Konfigurasi Base URL
Edit file `config/config.php`:
```php
define('BASE_URL', 'http://localhost/SISCI/');  // Sesuaikan dengan URL Anda
```

#### Step 5: Akses Aplikasi
```
URL: http://localhost/SISCI/
```

---

## 👤 Akun Default

| Role      | Email             | Password  |
|-----------|-------------------|-----------|
| Admin     | admin@sisci.com   | admin123  |

> **Catatan:** Silakan ganti password default setelah login pertama kali untuk keamanan.

---

## 🎭 Role Pengguna

### 1. Admin
- ✅ Mengelola pengguna (CRUD)
- ✅ Mengelola domain ISO (CRUD)
- ✅ Mengelola kontrol ISO (CRUD)
- ✅ Melakukan evaluasi
- ✅ Melihat laporan dan gap analysis

### 2. Auditor
- ✅ Melakukan evaluasi kontrol
- ✅ Melihat laporan dan gap analysis
- ❌ Tidak bisa mengelola master data

### 3. Manajemen
- ✅ Melihat laporan dan statistik
- ✅ Generate hasil rekap
- ❌ Tidak bisa melakukan evaluasi
- ❌ Tidak bisa mengelola master data

---

## 📊 Perhitungan Kepatuhan

### Rumus Persentase Kepatuhan
```
Persentase Kepatuhan = (Total skor diperoleh / Total skor maksimal) × 100%
```

### Kategori Tingkat Kepatuhan

| Persentase      | Kategori              | Badge       |
|-----------------|-----------------------|-------------|
| 81% - 100%      | Sangat Patuh          | 🟢 Hijau    |
| 61% - 80%       | Patuh                 | 🔵 Biru     |
| 41% - 60%       | Cukup Patuh           | 🟡 Kuning   |
| 21% - 40%       | Tidak Patuh           | 🔴 Merah    |
| 0% - 20%        | Sangat Tidak Patuh    | ⚫ Hitam    |

### Level Penilaian Kontrol

| Level | Deskripsi               | Status            |
|-------|-------------------------|-------------------|
| 0     | Belum diimplementasikan | Perlu Perbaikan   |
| 1     | Partial (25%)           | Perlu Perbaikan   |
| 2     | Partial (50%)           | Perlu Perbaikan   |
| 3     | Moderate (75%)          | Memenuhi          |
| 4     | Substantial (90%)       | Memenuhi          |
| 5     | Optimized (100%)        | Memenuhi          |

---

## 🔒 Fitur Keamanan

1. **Password Hashing**: Menggunakan `password_hash()` dengan algoritma bcrypt
2. **Prepared Statements**: PDO untuk mencegah SQL Injection
3. **CSRF Protection**: Token CSRF pada setiap form
4. **Session Security**: Session regeneration dan secure cookie settings
5. **XSS Protection**: Output escaping dengan `htmlspecialchars()`
6. **Role-Based Access Control**: Proteksi akses berdasarkan role pengguna

---

## 🖼️ Tampilan Sistem

Sistem menggunakan **Bootstrap 5** dengan desain:
- ✅ Responsive (mobile-friendly)
- ✅ Professional dan modern
- ✅ Chart.js untuk visualisasi data
- ✅ Icon Bootstrap Icons

---

## 📝 Struktur Database

### Tabel `users`
- Menyimpan data pengguna sistem
- Field: id, nama, email, password, role, created_at

### Tabel `domain_iso`
- Menyimpan domain ISO/IEC 27001
- Field: id, kode_domain, nama_domain, deskripsi

### Tabel `kontrol_iso`
- Menyimpan kontrol/control ISO/IEC 27001
- Field: id, domain_id, kode_kontrol, nama_kontrol, deskripsi, level_maksimal

### Tabel `evaluasi`
- Menyimpan hasil evaluasi/penilaian
- Field: id, kontrol_id, auditor_id, skor, catatan, status, tanggal

### Tabel `hasil_rekap`
- Menyimpan hasil rekap keseluruhan
- Field: id, rata_rata, persentase_kepatuhan, tingkat_kematangan, tanggal_generate

---

## 🛠️ Troubleshooting

### 1. Error Koneksi Database
- Cek konfigurasi di `config/database.php`
- Pastikan MySQL sudah running
- Cek nama database sudah benar

### 2. Error "Page Not Found"
- Cek konfigurasi `BASE_URL` di `config/config.php`
- Pastikan mod_rewrite aktif (Apache)

### 3. CSS/JS Tidak Load
- Cek path `BASE_URL` sudah benar
- Cek koneksi internet (CDN Bootstrap & Chart.js)

### 4. Session Error
- Pastikan folder session PHP writable
- Cek pengaturan `session.save_path` di php.ini

---

## 📚 Referensi

- [ISO/IEC 27001:2013](https://www.iso.org/standard/54534.html) - Information Security Management Systems
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)
- [Chart.js Documentation](https://www.chartjs.org/docs/)
- [PHP PDO Documentation](https://www.php.net/manual/en/book.pdo.php)

---

## 👨‍💻 Pengembang

**M. Arifin Ilham**

Untuk keperluan akademik/Skripsi

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademik dan penelitian.

---

## 🙏 Catatan

Sistem ini dikembangkan sebagai bagian dari penelitian skripsi dengan judul:
> "Evaluasi Kepatuhan Keamanan Informasi Menggunakan Kerangka Kerja ISO/IEC 27001 Pada SMKN 1 Galang"

Semoga sistem ini bermanfaat untuk meningkatkan keamanan informasi di SMKN 1 Galang.

---

**Terima Kasih!** 🎉
