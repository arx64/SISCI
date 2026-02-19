<?php
/**
 * ============================================================
 * FILE: views/laporan/index.php
 * ------------------------------------------------------------
 * Halaman laporan evaluasi
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

$pageTitle = 'Laporan Evaluasi';

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
                        <i class="bi bi-file-earmark-text me-2"></i>Laporan Evaluasi
                    </h2>
                    <p class="text-muted mb-0">Laporan hasil evaluasi kepatuhan ISO/IEC 27001</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo BASE_URL; ?>index.php?page=laporan&action=export" class="btn btn-success" target="_blank">
                        <i class="bi bi-printer me-1"></i>
                        Cetak
                    </a>
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
            
            <!-- Ringkasan Evaluasi -->
            <div class="row g-4 mb-4">
                <!-- Persentase Kepatuhan -->
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card primary h-100 text-center">
                        <div class="card-body">
                            <div class="display-4 fw-bold text-primary mb-2">
                                <?php echo $persentaseKepatuhan; ?>
                                <small class="fs-5">%</small>
                            </div>
                            <p class="text-muted mb-0">Persentase Kepatuhan</p>
                            <span class="badge <?php echo $kategoriKepatuhan['badge']; ?> mt-2">
                                <?php echo $kategoriKepatuhan['label']; ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Rata-rata Skor -->
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card info h-100 text-center">
                        <div class="card-body">
                            <div class="display-4 fw-bold text-info mb-2">
                                <?php echo round($rekap['rata_rata_skor'], 2); ?>
                                <small class="fs-5">/5</small>
                            </div>
                            <p class="text-muted mb-0">Rata-rata Skor</p>
                            <small class="text-muted">dari level maksimal 5</small>
                        </div>
                    </div>
                </div>
                
                <!-- Total Dievaluasi -->
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card success h-100 text-center">
                        <div class="card-body">
                            <div class="display-4 fw-bold text-success mb-2">
                                <?php echo $rekap['total_dievaluasi']; ?>
                                <small class="fs-5">/<?php echo $rekap['total_kontrol']; ?></small>
                            </div>
                            <p class="text-muted mb-0">Kontrol Dievaluasi</p>
                            <small class="text-muted">dari total kontrol</small>
                        </div>
                    </div>
                </div>
                
                <!-- Gap Analysis -->
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card warning h-100 text-center">
                        <div class="card-body">
                            <div class="display-4 fw-bold text-warning mb-2">
                                <?php echo count($gapAnalysis); ?>
                            </div>
                            <p class="text-muted mb-0">Kontrol Perlu Perbaikan</p>
                            <small class="text-muted">skor < 3 (belum memenuhi)</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Grafik -->
            <div class="row g-4 mb-4">
                <!-- Bar Chart -->
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-bar-chart me-2"></i>
                                Persentase Kepatuhan per Domain
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="laporanChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Skor Detail -->
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-list-ol me-2"></i>
                                Detail Skor
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Total Skor Diperoleh</span>
                                    <strong><?php echo $rekap['total_skor_diperoleh']; ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Total Skor Maksimal</span>
                                    <strong><?php echo $rekap['total_skor_maksimal']; ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Total Kontrol</span>
                                    <strong><?php echo $rekap['total_kontrol']; ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Sudah Dievaluasi</span>
                                    <strong><?php echo $rekap['total_dievaluasi']; ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Belum Dievaluasi</span>
                                    <strong><?php echo $rekap['total_kontrol'] - $rekap['total_dievaluasi']; ?></strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabel Detail Domain -->
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-table me-2"></i>
                        Detail Kepatuhan per Domain
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4">Kode</th>
                                    <th>Nama Domain</th>
                                    <th class="text-center">Persentase</th>
                                    <th class="text-center">Skor</th>
                                    <th class="text-center">Rata-rata</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($domainStats as $stat): 
                                    $status = getKategoriKepatuhan($stat['persentase']);
                                ?>
                                <tr>
                                    <td class="px-4 fw-bold"><?php echo $stat['kode']; ?></td>
                                    <td><?php echo $stat['nama']; ?></td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-<?php echo $status['class']; ?>" 
                                                 style="width: <?php echo $stat['persentase']; ?>%">
                                                <?php echo $stat['persentase']; ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $stat['skor']; ?>/<?php echo $stat['maksimal']; ?>
                                    </td>
                                    <td class="text-center"><?php echo $stat['rata_rata']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $status['badge']; ?>">
                                            <?php echo $status['label']; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Simpan Rekap Button (Admin & Manajemen only) -->
            <?php if (hasRole(['admin', 'manajemen'])): ?>
            <div class="text-center">
                <form action="<?php echo BASE_URL; ?>index.php?page=laporan&action=generate" method="POST" class="d-inline">
                    <?php echo csrfField(); ?>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save me-2"></i>
                        Simpan Hasil Rekap
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script>
    const ctx = document.getElementById('laporanChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($domainStats, 'kode')); ?>,
            datasets: [{
                label: 'Persentase Kepatuhan (%)',
                data: <?php echo json_encode(array_column($domainStats, 'persentase')); ?>,
                backgroundColor: 'rgba(44, 82, 130, 0.8)',
                borderColor: 'rgba(44, 82, 130, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>

<?php
// Include footer
include __DIR__ . '/../layouts/footer.php';
