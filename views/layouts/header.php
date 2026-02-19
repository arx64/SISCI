<?php
/**
 * ============================================================
 * FILE: views/layouts/header.php
 * ------------------------------------------------------------
 * Template header utama sistem
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ============================================================
 */

// Pastikan user sudah login untuk halaman yang memerlukan autentikasi
if (!isset($isLoginPage) && !isLoggedIn()) {
    redirectTo('index.php?page=login');
}

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Evaluasi Kepatuhan Keamanan Informasi ISO/IEC 27001 - SMKN 1 Galang">
    <meta name="author" content="M. Arifin Ilham">
    
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Chart.js untuk grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2c5282;
            --secondary-color: #4a5568;
            --success-color: #38a169;
            --info-color: #3182ce;
            --warning-color: #d69e2e;
            --danger-color: #e53e3e;
            --light-bg: #f7fafc;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
        }
        
        /* Sidebar Styles */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1a365d 0%, #2c5282 100%);
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.2);
            border-left: 4px solid #63b3ed;
        }
        
        .sidebar .nav-link i {
            width: 24px;
            margin-right: 8px;
        }
        
        /* Navbar Styles */
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        
        .stat-card {
            border-left: 4px solid;
        }
        
        .stat-card.primary { border-left-color: var(--primary-color); }
        .stat-card.success { border-left-color: var(--success-color); }
        .stat-card.info { border-left-color: var(--info-color); }
        .stat-card.warning { border-left-color: var(--warning-color); }
        .stat-card.danger { border-left-color: var(--danger-color); }
        
        /* Table Styles */
        .table-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        /* Button Styles */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #1a365d;
            border-color: #1a365d;
        }
        
        /* Login Page Styles */
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a365d 0%, #2c5282 50%, #3182ce 100%);
        }
        
        .login-card {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        
        /* Logo and Branding */
        .brand-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            margin: 0 auto 20px;
        }
        
        /* Progress and Badge */
        .progress {
            border-radius: 10px;
            height: 20px;
        }
        
        .badge-status {
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        /* Print Styles */
        @media print {
            .sidebar, .navbar, .no-print {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
        }
    </style>
    
    <?php if (isset($extraCSS)): ?>
        <?php echo $extraCSS; ?>
    <?php endif; ?>
</head>
<body>
