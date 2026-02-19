<?php
/**
 * ============================================================
 * FILE: views/kontrol/edit.php
 * ------------------------------------------------------------
 * Halaman edit kontrol ISO
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

$pageTitle = 'Edit Kontrol ISO';

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
                <a href="<?php echo BASE_URL; ?>index.php?page=kontrol" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-pencil-square me-2"></i>Edit Kontrol ISO
                    </h2>
                    <p class="text-muted mb-0">Edit kontrol ISO/IEC 27001</p>
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
                    <h5 class="fw-bold mb-0">Edit Kontrol: <?php echo $kontrol['kode_kontrol']; ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>index.php?page=kontrol&action=update" method="POST">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="id" value="<?php echo $kontrol['id']; ?>">
                        
                        <div class="row">
                            <!-- Domain -->
                            <div class="col-md-6 mb-3">
                                <label for="domain_id" class="form-label fw-semibold">
                                    Domain <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="domain_id" name="domain_id" required>
                                    <option value="">Pilih Domain</option>
                                    <?php foreach ($domains as $domain): ?>
                                    <option value="<?php echo $domain['id']; ?>" 
                                            <?php echo ($domain['id'] == $kontrol['domain_id']) ? 'selected' : ''; ?>>
                                        <?php echo $domain['nama']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">
                                    Pilih domain tempat kontrol ini berada
                                </div>
                            </div>
                            
                            <!-- Kode Kontrol -->
                            <div class="col-md-6 mb-3">
                                <label for="kode_kontrol" class="form-label fw-semibold">
                                    Kode Kontrol <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="kode_kontrol" 
                                       name="kode_kontrol" 
                                       value="<?php echo $kontrol['kode_kontrol']; ?>"
                                       placeholder="Contoh: A.5.1, A.5.2"
                                       required
                                       pattern="A\.\d+\.\d+"
                                       title="Format: A.X.Y (contoh: A.5.1)">
                                <div class="form-text">
                                    Format: A.5.1, A.6.2, dst. (Domain.Sub.Kontrol)
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Nama Kontrol -->
                            <div class="col-md-8 mb-3">
                                <label for="nama_kontrol" class="form-label fw-semibold">
                                    Nama Kontrol <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nama_kontrol" 
                                       name="nama_kontrol" 
                                       value="<?php echo htmlspecialchars($kontrol['nama_kontrol']); ?>"
                                       placeholder="Masukkan nama kontrol"
                                       required>
                            </div>
                            
                            <!-- Level Maksimal -->
                            <div class="col-md-4 mb-3">
                                <label for="level_maksimal" class="form-label fw-semibold">
                                    Level Maksimal
                                </label>
                                <select class="form-select" id="level_maksimal" name="level_maksimal">
                                    <option value="5" <?php echo ($kontrol['level_maksimal'] == 5) ? 'selected' : ''; ?>>5 (Maksimal)</option>
                                    <option value="4" <?php echo ($kontrol['level_maksimal'] == 4) ? 'selected' : ''; ?>>4</option>
                                    <option value="3" <?php echo ($kontrol['level_maksimal'] == 3) ? 'selected' : ''; ?>>3</option>
                                    <option value="2" <?php echo ($kontrol['level_maksimal'] == 2) ? 'selected' : ''; ?>>2</option>
                                    <option value="1" <?php echo ($kontrol['level_maksimal'] == 1) ? 'selected' : ''; ?>>1 (Minimal)</option>
                                </select>
                                <div class="form-text">
                                    Level penilaian maksimal (default: 5)
                                </div>
                            </div>
                        </div>
                        
                        <!-- Deskripsi -->
                        <div class="mb-4">
                            <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                            <textarea class="form-control" 
                                      id="deskripsi" 
                                      name="deskripsi" 
                                      rows="5"
                                      placeholder="Masukkan deskripsi kontrol..."><?php echo htmlspecialchars($kontrol['deskripsi']); ?></textarea>
                            <div class="form-text">
                                Jelaskan detail tentang kontrol ini dan persyaratan implementasinya
                            </div>
                        </div>
                        
                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>
                                Simpan Perubahan
                            </button>
                            <a href="<?php echo BASE_URL; ?>index.php?page=kontrol" class="btn btn-secondary">
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
