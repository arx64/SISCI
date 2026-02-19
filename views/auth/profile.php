<?php
/**
 * ============================================================
 * FILE: views/auth/profile.php
 * ------------------------------------------------------------
 * Halaman profil pengguna
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

$pageTitle = 'Profil Pengguna';

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
                <a href="<?php echo BASE_URL; ?>index.php?page=dashboard" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-person-circle me-2"></i>Profil Pengguna
                    </h2>
                    <p class="text-muted mb-0">Kelola informasi profil Anda</p>
                </div>
            </div>
            
            <!-- Alert Messages -->
            <?php if ($flash = getFlashMessage()): ?>
                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                    <i class="bi bi-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                    <?php echo $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error = getError()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($success = getSuccess()): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Profil Card -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 100px; height: 100px;">
                                <i class="bi bi-person text-primary" style="font-size: 48px;"></i>
                            </div>
                            <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($user['nama']); ?></h4>
                            <p class="text-muted mb-2"><?php echo $user['email']; ?></p>
                            <span class="badge bg-primary text-capitalize"><?php echo $user['role']; ?></span>
                            <hr>
                            <p class="text-muted small mb-0">
                                <i class="bi bi-calendar me-1"></i>
                                Bergabung: <?php echo date('d M Y', strtotime($user['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Edit Form -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0">Edit Profil</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo BASE_URL; ?>index.php?page=profile&action=update" method="POST">
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
                                               value="<?php echo htmlspecialchars($user['nama']); ?>"
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
                                               value="<?php echo $user['email']; ?>"
                                               required>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <h6 class="fw-bold mb-3">Ubah Password (Opsional)</h6>
                                
                                <div class="row">
                                    <!-- Password Lama -->
                                    <div class="col-md-6 mb-3">
                                        <label for="password_lama" class="form-label fw-semibold">Password Lama</label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_lama" 
                                               name="password_lama"
                                               placeholder="Isi jika ingin mengubah password">
                                    </div>
                                    
                                    <!-- Password Baru -->
                                    <div class="col-md-6 mb-3">
                                        <label for="password_baru" class="form-label fw-semibold">Password Baru</label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_baru" 
                                               name="password_baru"
                                               placeholder="Minimal 6 karakter"
                                               minlength="6">
                                    </div>
                                </div>
                                
                                <!-- Buttons -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/../layouts/footer.php';
