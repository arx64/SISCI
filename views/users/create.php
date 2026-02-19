<?php
/**
 * ============================================================
 * FILE: views/users/create.php
 * ------------------------------------------------------------
 * Halaman tambah pengguna baru
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

$pageTitle = 'Tambah Pengguna';

// Include header
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content p-4">
            <!-- Page Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="<?php echo BASE_URL; ?>index.php?page=users" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-person-plus me-2"></i>Tambah Pengguna
                    </h2>
                    <p class="text-muted mb-0">Tambah pengguna baru ke sistem</p>
                </div>
            </div>
            
            <!-- Alert Messages -->
            <?php if ($error = getError()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Form Card -->
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Form Pengguna Baru</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>index.php?page=users&action=store" method="POST">
                        <?php echo csrfField(); ?>
                        
                        <div class="row">
                            <!-- Nama -->
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label fw-semibold">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nama" 
                                       name="nama" 
                                       placeholder="Masukkan nama lengkap"
                                       required>
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       placeholder="Masukkan email"
                                       required>
                                <div class="form-text">
                                    Email akan digunakan sebagai username untuk login
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Masukkan password"
                                       required
                                       minlength="6">
                                <div class="form-text">
                                    Minimal 6 karakter
                                </div>
                            </div>
                            
                            <!-- Role -->
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label fw-semibold">
                                    Role <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="auditor" selected>Auditor</option>
                                    <option value="manajemen">Manajemen</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <div class="form-text">
                                    Pilih role sesuai tugas dan tanggung jawab pengguna
                                </div>
                            </div>
                        </div>
                        
                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>
                                Simpan
                            </button>
                            <a href="<?php echo BASE_URL; ?>index.php?page=users" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i>
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/../layouts/footer.php';
