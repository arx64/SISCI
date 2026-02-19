<?php
/**
 * ============================================================
 * FILE: views/evaluasi/gap_analysis.php
 * ------------------------------------------------------------
 * Halaman gap analysis - kontrol yang perlu perbaikan
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

$pageTitle = 'Gap Analysis';

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
                        <i class="bi bi-exclamation-triangle me-2"></i>Gap Analysis
                    </h2>
                    <p class="text-muted mb-0">Analisis kesenjangan kontrol ISO/IEC 27001</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-danger fs-6">
                        <?php echo $totalGap; ?> Kontrol Perlu Perbaikan
                    </span>
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
            
            <!-- Statistik per Domain -->
            <?php if (!empty($totalPerDomain)): ?>
            <div class="row mb-4">
                <?php foreach ($totalPerDomain as $kode => $count): ?>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-warning bg-opacity-10 border-warning">
                        <div class="card-body text-center">
                            <h4 class="fw-bold text-warning mb-1"><?php echo $count; ?></h4>
                            <small class="text-muted">Domain <?php echo $kode; ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Gap Table -->
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Daftar Kontrol yang Perlu Perbaikan</h5>
                    <span class="text-muted small">Skor &lt; 3 (Belum Memenuhi)</span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($gaps)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle fs-1 text-success d-block mb-3"></i>
                            <h5>Tidak ada gap yang ditemukan!</h5>
                            <p>Semua kontrol telah memenuhi standar (skor ≥ 3).</p>
                            <a href="<?php echo BASE_URL; ?>index.php?page=evaluasi&action=pilih" class="btn btn-primary">
                                <i class="bi bi-clipboard-check me-1"></i>Lakukan Evaluasi
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4">Kode</th>
                                        <th>Nama Kontrol</th>
                                        <th>Domain</th>
                                        <th class="text-center">Skor</th>
                                        <th class="text-center">Gap</th>
                                        <th>Catatan/Temuan</th>
                                        <th class="text-center">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($gaps as $gap): ?>
                                    <tr>
                                        <td class="px-4 fw-bold"><?php echo $gap['kode_kontrol']; ?></td>
                                        <td class="fw-semibold"><?php echo $gap['nama_kontrol']; ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?php echo $gap['kode_domain']; ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger"><?php echo $gap['skor']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-dark"><?php echo $gap['gap']; ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($gap['catatan'])): ?>
                                                <span class="text-muted"><?php echo $gap['catatan']; ?></span>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic">Tidak ada catatan</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php echo date('d/m/Y', strtotime($gap['tanggal'])); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Rekomendasi -->
 <?php if (!empty($gaps)): ?>
            <div class="card mt-4 bg-light border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-lightbulb me-2 text-warning"></i>Rekomendasi Perbaikan
                    </h6>
                    <ul class="list-group list-group-flush bg-transparent">
                        <li class="list-group-item bg-transparent px-0">
                            <i class="bi bi-check2 me-2 text-success"></i>
                            Prioritaskan perbaikan pada kontrol dengan gap tertinggi
                        </li>
                        <li class="list-group-item bg-transparent px-0">
                            <i class="bi bi-check2 me-2 text-success"></i>
                            Buat rencana tindak lanjut dengan timeline yang jelas
                        </li>
                        <li class="list-group-item bg-transparent px-0">
                            <i class="bi bi-check2 me-2 text-success"></i>
                            Lakukan evaluasi ulang setelah perbaikan dilakukan
                        </li>
                        <li class="list-group-item bg-transparent px-0">
                            <i class="bi bi-check2 me-2 text-success"></i>
                            Dokumentasikan setiap perbaikan yang telah dilakukan
                        </li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/../layouts/footer.php';
