<?php
/**
 * ============================================================
 * FILE: views/users/index.php
 * ------------------------------------------------------------
 * Halaman daftar pengguna
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

$pageTitle = 'Manajemen Pengguna';

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
                        <i class="bi bi-people me-2"></i>Manajemen Pengguna
                    </h2>
                    <p class="text-muted mb-0">Kelola pengguna sistem</p>
                </div>
                <a href="<?php echo BASE_URL; ?>index.php?page=users&action=create" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Pengguna
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
            
            <!-- Users Table -->
            <div class="card table-container">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Daftar Pengguna</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">Nama</th>
                                <th>Email</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Tanggal Daftar</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Tidak ada data pengguna
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): 
                                    // Badge class berdasarkan role
                                    $roleBadgeClass = [
                                        'admin' => 'bg-danger',
                                        'auditor' => 'bg-primary',
                                        'manajemen' => 'bg-success'
                                    ][$user['role']] ?? 'bg-secondary';
                                ?>
                                <tr>
                                    <td class="px-4 fw-semibold"><?php echo htmlspecialchars($user['nama']); ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td class="text-center">
                                        <span class="badge <?php echo $roleBadgeClass; ?> text-capitalize">
                                            <?php echo $user['role']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo BASE_URL; ?>index.php?page=users&action=edit&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <form action="<?php echo BASE_URL; ?>index.php?page=users&action=delete" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                            <?php echo csrfField(); ?>
                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Info Role -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card bg-danger bg-opacity-10 border-danger">
                        <div class="card-body">
                            <h6 class="fw-bold text-danger mb-2">
                                <i class="bi bi-shield-fill me-2"></i>Admin
                            </h6>
                            <p class="text-muted small mb-0">
                                Memiliki akses penuh ke sistem termasuk mengelola pengguna, domain, kontrol, dan melihat semua laporan.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary bg-opacity-10 border-primary">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-2">
                                <i class="bi bi-clipboard-check me-2"></i>Auditor
                            </h6>
                            <p class="text-muted small mb-0">
                                Bertugas melakukan evaluasi dan penilaian terhadap kontrol ISO/IEC 27001.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success bg-opacity-10 border-success">
                        <div class="card-body">
                            <h6 class="fw-bold text-success mb-2">
                                <i class="bi bi-graph-up me-2"></i>Manajemen
                            </h6>
                            <p class="text-muted small mb-0">
                                Dapat melihat hasil evaluasi, laporan, dan statistik kepatuhan sistem.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/../layouts/footer.php';
