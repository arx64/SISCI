<?php
/**
 * ============================================================
 * FILE: views/evaluasi/pilih_domain.php
 * ------------------------------------------------------------
 * Halaman pilih domain untuk evaluasi
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

$pageTitle = 'Pilih Domain Evaluasi';

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
            <div class="mb-4">
                <h2 class="fw-bold text-primary mb-1">
                    <i class="bi bi-clipboard-check me-2"></i>Evaluasi Kontrol ISO
                </h2>
                <p class="text-muted mb-0">Pilih domain untuk melakukan evaluasi</p>
            </div>
            
            <!-- Alert Messages -->
            <?php if ($flash = getFlashMessage()): ?>
                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                    <i class="bi bi-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                    <?php echo $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Form Pilih Domain -->
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Pilih Domain dan Tanggal Evaluasi</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>index.php?page=evaluasi&action=form" method="GET">
                        <input type="hidden" name="page" value="evaluasi">
                        <input type="hidden" name="action" value="form">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="domain_id" class="form-label fw-semibold">Domain</label>
                                <select class="form-select" id="domain_id" name="domain_id" required>
                                    <option value="">Pilih Domain</option>
                                    <?php foreach ($domains as $domain): ?>
                                    <option value="<?php echo $domain['id']; ?>">
                                        <?php echo $domain['kode_domain']; ?> - <?php echo $domain['nama_domain']; ?> 
                                        (<?php echo $domain['total_kontrol']; ?> kontrol)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label fw-semibold">Tanggal Evaluasi</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-arrow-right me-1"></i>
                            Lanjutkan
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Domain Cards -->
            <h5 class="fw-bold mb-3">Daftar Domain ISO/IEC 27001</h5>
            <div class="row g-3">
                <?php foreach ($domains as $domain): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary"><?php echo $domain['kode_domain']; ?></span>
                                <span class="badge bg-light text-dark border">
                                    <?php echo $domain['total_kontrol']; ?> Kontrol
                                </span>
                            </div>
                            <h6 class="card-title fw-bold"><?php echo $domain['nama_domain']; ?></h6>
                            <p class="card-text small text-muted">
                                <?php echo substr(strip_tags($domain['deskripsi']), 0, 100) . '...'; ?>
                            </p>
                            <a href="<?php echo BASE_URL; ?>index.php?page=evaluasi&action=form&domain_id=<?php echo $domain['id']; ?>&tanggal=<?php echo date('Y-m-d'); ?>" 
                               class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-clipboard-check me-1"></i>
                                Evaluasi Domain Ini
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/../layouts/footer.php';
