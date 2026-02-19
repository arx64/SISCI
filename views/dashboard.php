<?php
/**
 * ============================================================
 * FILE: views/dashboard.php
 * ------------------------------------------------------------
 * Halaman dashboard utama sistem
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

$pageTitle = 'Dashboard';

// Include header
include __DIR__ . '/layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include __DIR__ . '/layouts/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content p-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </h2>
                    <p class="text-muted mb-0">Selamat datang, <?php echo htmlspecialchars($currentUser['nama']); ?></p>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-dark border">
                        <i class="bi bi-calendar me-1"></i>
                        <?php echo formatDateIndo(date('Y-m-d')); ?>
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
            
            <!-- Statistik Cards -->
            <div class="row g-4 mb-4">
                <!-- Total Domain -->
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small">Total Domain ISO</p>
                                    <h3 class="fw-bold mb-0"><?php echo $stats['total_domain']; ?></h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-2 rounded">
                                    <i class="bi bi-folder text-primary fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Total Kontrol -->
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card info h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small">Total Kontrol ISO</p>
                                    <h3 class="fw-bold mb-0"><?php echo $stats['total_kontrol']; ?></h3>
                                </div>
                                <div class="bg-info bg-opacity-10 p-2 rounded">
                                    <i class="bi bi-list-check text-info fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Persentase Kepatuhan -->
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small">Persentase Kepatuhan</p>
                                    <h3 class="fw-bold mb-0"><?php echo $persentaseKepatuhan; ?>%</h3>
                                    <span class="badge <?php echo $kategoriKepatuhan['badge']; ?> mt-1">
                                        <?php echo $kategoriKepatuhan['label']; ?>
                                    </span>
                                </div>
                                <div class="bg-success bg-opacity-10 p-2 rounded">
                                    <i class="bi bi-check-circle text-success fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gap Analysis -->
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small">Kontrol Perlu Perbaikan</p>
                                    <h3 class="fw-bold mb-0"><?php echo $totalGap; ?></h3>
                                    <small class="text-muted">Dari total evaluasi</small>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-2 rounded">
                                    <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Section -->
            <div class="row g-4 mb-4">
                <!-- Grafik Persentase Kepatuhan per Domain -->
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-bar-chart me-2"></i>
                                Persentase Kepatuhan per Domain
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="domainChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Ringkasan Kepatuhan -->
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-pie-chart me-2"></i>
                                Ringkasan
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Progress Kepatuhan -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-semibold">Tingkat Kepatuhan</span>
                                    <span class="fw-bold text-primary"><?php echo $persentaseKepatuhan; ?>%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-<?php echo $kategoriKepatuhan['class']; ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo $persentaseKepatuhan; ?>%"
                                         aria-valuenow="<?php echo $persentaseKepatuhan; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Statistik Lainnya -->
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="bi bi-check2-circle me-2 text-success"></i>Skor Diperoleh</span>
                                    <span class="fw-bold"><?php echo $rekapEvaluasi['total_skor_diperoleh']; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="bi bi-award me-2 text-primary"></i>Skor Maksimal</span>
                                    <span class="fw-bold"><?php echo $rekapEvaluasi['total_skor_maksimal']; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="bi bi-calculator me-2 text-info"></i>Rata-rata Skor</span>
                                    <span class="fw-bold"><?php echo round($rekapEvaluasi['rata_rata_skor'], 2); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="bi bi-clipboard-data me-2 text-warning"></i>Total Dievaluasi</span>
                                    <span class="fw-bold"><?php echo $rekapEvaluasi['total_dievaluasi']; ?> / <?php echo $rekapEvaluasi['total_kontrol']; ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabel Domain Detail -->
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-table me-2"></i>
                        Detail Kepatuhan per Domain
                    </h5>
                    <a href="<?php echo BASE_URL; ?>index.php?page=laporan" class="btn btn-sm btn-primary">
                        <i class="bi bi-file-earmark-text me-1"></i>
                        Lihat Laporan Lengkap
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4">Kode</th>
                                    <th>Nama Domain</th>
                                    <th class="text-center">Total Kontrol</th>
                                    <th class="text-center">Dievaluasi</th>
                                    <th class="text-center">Rata-rata Skor</th>
                                    <th class="text-center">Persentase</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($domainStats as $stat): 
                                    $status = getKategoriKepatuhan($stat['persentase']);
                                ?>
                                <tr>
                                    <td class="px-4 fw-semibold"><?php echo $stat['kode']; ?></td>
                                    <td><?php echo $stat['nama']; ?></td>
                                    <td class="text-center"><?php echo $stat['total_kontrol']; ?></td>
                                    <td class="text-center"><?php echo $stat['total_dievaluasi']; ?></td>
                                    <td class="text-center"><?php echo $stat['rata_rata']; ?></td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px; width: 60px;">
                                                <div class="progress-bar bg-<?php echo $status['class']; ?>" 
                                                     style="width: <?php echo $stat['persentase']; ?>%"></div>
                                            </div>
                                            <small class="fw-semibold"><?php echo $stat['persentase']; ?>%</small>
                                        </div>
                                    </td>
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
        </div>
    </div>
</div>

<!-- Chart.js Configuration -->
<script>
    // Data untuk grafik
    const domainLabels = <?php echo json_encode(array_column($domainStats, 'kode')); ?>;
    const domainData = <?php echo json_encode(array_column($domainStats, 'persentase')); ?>;
    
    // Konfigurasi grafik
    const ctx = document.getElementById('domainChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: domainLabels,
            datasets: [{
                label: 'Persentase Kepatuhan (%)',
                data: domainData,
                backgroundColor: [
                    'rgba(44, 82, 130, 0.8)',
                    'rgba(49, 130, 206, 0.8)',
                    'rgba(56, 161, 105, 0.8)',
                    'rgba(214, 158, 46, 0.8)',
                    'rgba(229, 62, 62, 0.8)',
                    'rgba(128, 90, 213, 0.8)',
                    'rgba(236, 112, 99, 0.8)',
                    'rgba(26, 54, 93, 0.8)',
                    'rgba(72, 187, 120, 0.8)',
                    'rgba(237, 137, 54, 0.8)',
                    'rgba(66, 153, 225, 0.8)',
                    'rgba(160, 174, 192, 0.8)',
                    'rgba(113, 128, 150, 0.8)',
                    'rgba(74, 85, 104, 0.8)'
                ],
                borderColor: [
                    'rgba(44, 82, 130, 1)',
                    'rgba(49, 130, 206, 1)',
                    'rgba(56, 161, 105, 1)',
                    'rgba(214, 158, 46, 1)',
                    'rgba(229, 62, 62, 1)',
                    'rgba(128, 90, 213, 1)',
                    'rgba(236, 112, 99, 1)',
                    'rgba(26, 54, 93, 1)',
                    'rgba(72, 187, 120, 1)',
                    'rgba(237, 137, 54, 1)',
                    'rgba(66, 153, 225, 1)',
                    'rgba(160, 174, 192, 1)',
                    'rgba(113, 128, 150, 1)',
                    'rgba(74, 85, 104, 1)'
                ],
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
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + '% Kepatuhan';
                        }
                    }
                }
            }
        }
    });
</script>

<?php
// Include footer
include __DIR__ . '/layouts/footer.php';
