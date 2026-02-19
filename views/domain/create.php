<?php
/**
 * ============================================================
 * FILE: views/domain/create.php
 * ------------------------------------------------------------
 * Halaman tambah domain ISO baru
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

$pageTitle = 'Tambah Domain ISO';

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
                <a href="<?php echo BASE_URL; ?>index.php?page=domain" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-folder-plus me-2"></i>Tambah Domain ISO
                    </h2>
                    <p class="text-muted mb-0">Tambah domain baru ISO/IEC 27001</p>
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
                    <h5 class="fw-bold mb-0">Form Domain Baru</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>index.php?page=domain&action=store" method="POST">
                        <?php echo csrfField(); ?>
                        
                        <div class="row">
                            <!-- Kode Domain -->
                            <div class="col-md-6 mb-3">
                                <label for="kode_domain" class="form-label fw-semibold">
                                    Kode Domain <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="kode_domain" 
                                       name="kode_domain" 
                                       placeholder="Contoh: A.5, A.6"
                                       required
                                       pattern="A\.\d+"
                                       title="Format: A.angka (contoh: A.5)">
                                <div class="form-text">
                                    Format: A.5, A.6, A.7, dst. (A. diikuti angka)
                                </div>
                            </div>
                            
                            <!-- Nama Domain -->
                            <div class="col-md-6 mb-3">
                                <label for="nama_domain" class="form-label fw-semibold">
                                    Nama Domain <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nama_domain" 
                                       name="nama_domain" 
                                       placeholder="Masukkan nama domain"
                                       required>
                            </div>
                        </div>
                        
                        <!-- Deskripsi -->
                        <div class="mb-4">
                            <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                            <textarea class="form-control" 
                                      id="deskripsi" 
                                      name="deskripsi" 
                                      rows="4"
                                      placeholder="Masukkan deskripsi domain..."></textarea>
                            <div class="form-text">
                                Jelaskan tentang domain ini secara singkat
                            </div>
                        </div>
                        
                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>
                                Simpan
                            </button>
                            <a href="<?php echo BASE_URL; ?>index.php?page=domain" class="btn btn-secondary">
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
