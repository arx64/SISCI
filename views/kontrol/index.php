<?php
/**
 * ============================================================
 * FILE: views/kontrol/index.php
 * ------------------------------------------------------------
 * Halaman daftar kontrol ISO
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

$pageTitle = 'Manajemen Kontrol ISO';

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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-list-check me-2"></i>Manajemen Kontrol ISO
                    </h2>
                    <p class="text-muted mb-0">Kelola kontrol ISO/IEC 27001</p>
                </div>
                <a href="<?php echo BASE_URL; ?>index.php?page=kontrol&action=create" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Kontrol
                </a>
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
            
            <!-- Kontrol Table -->
            <div class="card table-container">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Daftar Kontrol ISO/IEC 27001</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4" style="width: 100px;">Kode</th>
                                <th>Nama Kontrol</th>
                                <th>Domain</th>
                                <th class="text-center" style="width: 120px;">Level Maks</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($kontrols)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Tidak ada data kontrol
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($kontrols as $kontrol): ?>
                                <tr>
                                    <td class="px-4 fw-bold"><?php echo $kontrol['kode_kontrol']; ?></td>
                                    <td class="fw-semibold">
                                        <?php echo $kontrol['nama_kontrol']; ?>
                                        <?php if (!empty($kontrol['deskripsi'])): ?>
                                            <small class="d-block text-muted mt-1">
                                                <?php echo substr(strip_tags($kontrol['deskripsi']), 0, 80) . '...'; ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?php echo $kontrol['kode_domain']; ?>
                                        </span>
                                        <small class="d-block text-muted"><?php echo $kontrol['nama_domain']; ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info"><?php echo $kontrol['level_maksimal']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo BASE_URL; ?>index.php?page=kontrol&action=edit&id=<?php echo $kontrol['id']; ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?php echo BASE_URL; ?>index.php?page=kontrol&action=delete" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus kontrol ini? Evaluasi terkait juga akan terhapus.')">
                                            <?php echo csrfField(); ?>
                                            <input type="hidden" name="id" value="<?php echo $kontrol['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Info Card -->
            <div class="card mt-4 bg-light border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">
                        <i class="bi bi-info-circle me-2"></i>Informasi Kontrol ISO/IEC 27001
                    </h6>
                    <p class="text-muted mb-0 small">
                        Setiap kontrol ISO/IEC 27001 memiliki level maksimal penilaian 1-5. 
                        Level 0 = Belum diimplementasikan, Level 1-2 = Partial, Level 3 = Moderate, 
                        Level 4 = Substantial, Level 5 = Optimized. 
                        Skor di bawah 3 menunjukkan kontrol perlu perbaikan (Gap Analysis).
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/../layouts/footer.php';
