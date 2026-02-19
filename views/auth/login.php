<?php
/**
 * ============================================================
 * FILE: views/auth/login.php
 * ------------------------------------------------------------
 * Halaman login sistem
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

// Tandai ini sebagai halaman login
$isLoginPage = true;

// Include header
include __DIR__ . '/../layouts/header.php';
?>

<div class="login-container d-flex align-items-center justify-content-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <!-- Login Card -->
                <div class="login-card p-4">
                    <!-- Logo -->
                    <div class="text-center mb-4">
                        <div class="brand-logo mx-auto mb-3">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="fw-bold text-primary">SISCI</h4>
                        <p class="text-muted small mb-0">Sistem Evaluasi Kepatuhan Keamanan Informasi</p>
                        <p class="text-muted small">ISO/IEC 27001 - SMKN 1 Galang</p>
                    </div>
                    
                    <!-- Alert Messages -->
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
                    
                    <!-- Login Form -->
                    <form action="<?php echo BASE_URL; ?>index.php?page=login" method="POST">
                        <?php echo csrfField(); ?>
                        
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-envelope text-muted"></i>
                                </span>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       placeholder="Masukkan email"
                                       required 
                                       autofocus>
                            </div>
                        </div>
                        
                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Masukkan password"
                                       required>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Login
                        </button>
                    </form>
                    
                    <!-- Info -->
                    <div class="mt-4 pt-3 border-top text-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Default login: admin@sisci.com / admin123
                        </small>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="text-center mt-4 text-white">
                    <small>
                        &copy; <?php echo date('Y'); ?> SISCI - SMKN 1 Galang<br>
                        Sistem Evaluasi Kepatuhan ISO/IEC 27001
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/../layouts/footer.php';
