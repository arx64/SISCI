<?php
/**
 * ============================================================
 * FILE: views/layouts/footer.php
 * ------------------------------------------------------------
 * Template footer utama sistem
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */
?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Auto-hide alert setelah 5 detik
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const closeBtn = alert.querySelector('.btn-close');
                    if (closeBtn) {
                        closeBtn.click();
                    }
                }, 5000);
            });
        });
        
        // Konfirmasi delete
        function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
            return confirm(message);
        }
        
        // Print functionality
        function printReport() {
            window.print();
        }
    </script>
    
    <?php if (isset($extraJS)): ?>
        <?php echo $extraJS; ?>
    <?php endif; ?>
    
</body>
</html>
