<?php
/**
 * ============================================================
 * FILE: views/domain/index.php
 * ------------------------------------------------------------
 * Halaman daftar domain ISO
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

$pageTitle = 'Manajemen Domain ISO';

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
                        <i class="bi bi-folder me-2"></i>Manajemen Domain ISO
                    </h2>
                    <p class="text-muted mb-0">Kelola domain ISO/IEC 27001</p>
                </div>
                <a href="<?php echo BASE_URL; ?>index.php?page=domain&action=create" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Domain
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
            
            <!-- Domain Table -->
            <div class="card table-container">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Daftar Domain ISO/IEC 27001</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4" style="width: 100px;">Kode</th>
                                <th>Nama Domain</th>
                                <th>Deskripsi</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($domains)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Tidak ada data domain
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($domains as $domain): ?>
                                <tr>
                                    <td class="px-4 fw-bold"><?php echo $domain['kode_domain']; ?></td>
                                    <td class="fw-semibold"><?php echo $domain['nama_domain']; ?></td>
                                    <td>
                                        <?php 
                                        $deskripsi = strip_tags($domain['deskripsi']);
                                        echo strlen($deskripsi) > 100 ? substr($deskripsi, 0, 100) . '...' : $deskripsi;
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo BASE_URL; ?>index.php?page=domain&action=edit&id=<?php echo $domain['id']; ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?php echo BASE_URL; ?>index.php?page=domain&action=delete" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus domain ini? Kontrol terkait juga akan terhapus.')">
                                            <?php echo csrfField(); ?>
                                            <input type="hidden" name="id" value="<?php echo $domain['id']; ?>">
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
                        <i class="bi bi-info-circle me-2"></i>Informasi
                    </h6>
                    <p class="text-muted mb-0 small">
                        ISO/IEC 27001:2013 memiliki 14 domain kontrol keamanan informasi. 
                        Domain A.5 sampai A.18 mencakup berbagai aspek keamanan informasi mulai dari kebijakan, organisasi, SDM, aset, akses, kriptografi, fisik, operasional, komunikasi, pengadaan sistem, hubungan pemasok, manajemen insiden, kontinuitas bisnis, dan kepatuhan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/../layouts/footer.php';
