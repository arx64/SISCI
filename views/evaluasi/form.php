<?php
/**
 * ============================================================
 * FILE: views/evaluasi/form.php
 * ------------------------------------------------------------
 * Halaman form evaluasi kontrol
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

$pageTitle = 'Form Evaluasi - ' . $domain['kode_domain'];

// Include header
include __DIR__ . '/../layouts/header.php';

// Level descriptions
$levelDescriptions = [
    0 => 'Belum diimplementasikan',
    1 => 'Partial (25%)',
    2 => 'Partial (50%)',
    3 => 'Moderate (75%)',
    4 => 'Substantial (90%)',
    5 => 'Optimized (100%)'
];
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content p-4">
            <!-- Page Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="<?php echo BASE_URL; ?>index.php?page=evaluasi&action=pilih" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-clipboard-check me-2"></i>Form Evaluasi
                    </h2>
                    <p class="text-muted mb-0">
                        <?php echo $domain['kode_domain']; ?> - <?php echo $domain['nama_domain']; ?>
                    </p>
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
            
            <!-- Evaluasi Form -->
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0">Penilaian Kontrol</h5>
                        <small class="text-muted">Tanggal: <?php echo formatDateIndo($tanggal); ?></small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Total Kontrol: <?php echo count($kontrols); ?></small>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($kontrols)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                            <p>Belum ada kontrol untuk domain ini.</p>
                            <a href="<?php echo BASE_URL; ?>index.php?page=kontrol&action=create" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i>Tambah Kontrol
                            </a>
                        </div>
                    <?php else: ?>
                        <form action="<?php echo BASE_URL; ?>index.php?page=evaluasi&action=simpan" method="POST">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="domain_id" value="<?php echo $domainId; ?>">
                            <input type="hidden" name="tanggal" value="<?php echo $tanggal; ?>">
                            
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-4" style="width: 80px;">Kode</th>
                                            <th>Kontrol</th>
                                            <th class="text-center" style="width: 180px;">Skor (0-5)</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($kontrols as $index => $kontrol): 
                                            $existingSkor = $kontrol['skor'] ?? '';
                                            $existingCatatan = $kontrol['catatan'] ?? '';
                                        ?>
                                        <tr>
                                            <td class="px-4 fw-bold"><?php echo $kontrol['kode_kontrol']; ?></td>
                                            <td>
                                                <strong><?php echo $kontrol['nama_kontrol']; ?></strong>
                                                <?php if (!empty($kontrol['deskripsi'])): ?>
                                                    <small class="d-block text-muted mt-1">
                                                        <?php echo substr(strip_tags($kontrol['deskripsi']), 0, 100) . '...'; ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <select class="form-select form-select-sm skor-select" 
                                                        name="skor[<?php echo $kontrol['id']; ?>]" 
                                                        data-kontrol="<?php echo $kontrol['kode_kontrol']; ?>">
                                                    <option value="">Pilih</option>
                                                    <?php for ($i = 0; $i <= 5; $i++): ?>
                                                    <option value="<?php echo $i; ?>" 
                                                            title="<?php echo $levelDescriptions[$i]; ?>"
                                                            <?php echo ($existingSkor !== '' && $existingSkor == $i) ? 'selected' : ''; ?>>
                                                        <?php echo $i; ?> - <?php echo $levelDescriptions[$i]; ?>
                                                    </option>
                                                    <?php endfor; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" 
                                                       class="form-control form-control-sm" 
                                                       name="catatan[<?php echo $kontrol['id']; ?>]"
                                                       value="<?php echo htmlspecialchars($existingCatatan); ?>"
                                                       placeholder="Catatan/temuan...">
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Legend -->
                            <div class="p-3 bg-light border-top">
                                <h6 class="fw-bold mb-2">Keterangan Level Penilaian:</h6>
                                <div class="row g-2 small">
                                    <div class="col-md-4"><strong>0</strong> - Belum diimplementasikan</div>
                                    <div class="col-md-4"><strong>1-2</strong> - Partial (25-50%)</div>
                                    <div class="col-md-4"><strong>3</strong> - Moderate (75%)</div>
                                    <div class="col-md-4"><strong>4</strong> - Substantial (90%)</div>
                                    <div class="col-md-4"><strong>5</strong> - Optimized (100%)</div>
                                    <div class="col-md-4 text-danger"><strong>&lt; 3</strong> - Perlu Perbaikan</div>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="p-3 border-top">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>
                                    Simpan Evaluasi
                                </button>
                                <a href="<?php echo BASE_URL; ?>index.php?page=evaluasi&action=pilih" class="btn btn-secondary">
                                    <i class="bi bi-x-lg me-1"></i>
                                    Batal
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/../layouts/footer.php';
