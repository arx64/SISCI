<?php
/**
 * ============================================================
 * FILE: views/layouts/sidebar.php
 * ------------------------------------------------------------
 * Template sidebar navigasi sistem
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

// Dapatkan halaman aktif
$currentPage = $_GET['page'] ?? 'dashboard';
$currentAction = $_GET['action'] ?? 'index';

// Fungsi helper untuk cek menu aktif
function isActive($page, $action = null) {
    global $currentPage, $currentAction;
    if ($action === null) {
        return $currentPage === $page ? 'active' : '';
    }
    return ($currentPage === $page) ? 'active' : '';
}
?>
<!-- Sidebar -->
<div class="col-md-3 col-lg-2 sidebar p-0">
    <div class="d-flex flex-column">
        <!-- Brand -->
        <div class="text-center p-4 border-bottom border-light">
            <div class="brand-logo mx-auto">
                <i class="bi bi-shield-check"></i>
            </div>
            <h5 class="text-white mb-0">SISCI</h5>
            <small class="text-white-50">SMKN 1 Galang</small>
        </div>
        
        <!-- Navigation -->
        <nav class="nav flex-column py-3">
            <!-- Dashboard - Semua Role -->
            <a href="<?php echo BASE_URL; ?>index.php?page=dashboard" 
               class="nav-link <?php echo isActive('dashboard'); ?>">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
            
            <?php if (hasRole('admin')): ?>
            <!-- Manajemen User - Admin Only -->
            <a href="<?php echo BASE_URL; ?>index.php?page=users" 
               class="nav-link <?php echo isActive('users'); ?>">
                <i class="bi bi-people"></i>
                Manajemen User
            </a>
            <?php endif; ?>
            
            <?php if (hasRole('admin')): ?>
            <!-- Domain ISO - Admin Only -->
            <a href="<?php echo BASE_URL; ?>index.php?page=domain" 
               class="nav-link <?php echo isActive('domain'); ?>">
                <i class="bi bi-folder"></i>
                Domain ISO
            </a>
            
            <!-- Kontrol ISO - Admin Only -->
            <a href="<?php echo BASE_URL; ?>index.php?page=kontrol" 
               class="nav-link <?php echo isActive('kontrol'); ?>">
                <i class="bi bi-list-check"></i>
                Kontrol ISO
            </a>
            <?php endif; ?>
            
            <?php if (hasRole(['admin', 'auditor'])): ?>
            <!-- Evaluasi - Admin & Auditor -->
            <a href="<?php echo BASE_URL; ?>index.php?page=evaluasi" 
               class="nav-link <?php echo isActive('evaluasi'); ?>">
                <i class="bi bi-clipboard-check"></i>
                Evaluasi
            </a>
            <?php endif; ?>
            
            <!-- Gap Analysis - Semua Role -->
            <a href="<?php echo BASE_URL; ?>index.php?page=evaluasi&action=gap" 
               class="nav-link <?php echo ($currentPage === 'evaluasi' && $currentAction === 'gap') ? 'active' : ''; ?>">
                <i class="bi bi-exclamation-triangle"></i>
                Gap Analysis
            </a>
            
            <!-- Laporan - Semua Role -->
            <a href="<?php echo BASE_URL; ?>index.php?page=laporan" 
               class="nav-link <?php echo isActive('laporan'); ?>">
                <i class="bi bi-file-earmark-text"></i>
                Laporan
            </a>
        </nav>
        
        <!-- User Info & Logout -->
        <div class="mt-auto p-3 border-top border-light">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" 
                   data-bs-toggle="dropdown">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" 
                         style="width: 32px; height: 32px;">
                        <i class="bi bi-person text-dark"></i>
                    </div>
                    <div>
                        <small class="d-block text-white"><?php echo htmlspecialchars($currentUser['nama']); ?></small>
                        <small class="text-white-50 text-capitalize"><?php echo $currentUser['role']; ?></small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <li>
                        <a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?page=profile">
                            <i class="bi bi-person-circle me-2"></i>Profil
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="<?php echo BASE_URL; ?>index.php?page=logout" method="POST" class="d-inline">
                            <?php echo csrfField(); ?>
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
