<?php
/**
 * ============================================================
 * FILE: index.php
 * ------------------------------------------------------------
 * Entry point utama aplikasi
 * Sistem Informasi Evaluasi Kepatuhan ISO/IEC 27001
 * ------------------------------------------------------------
 * @author  : M. Arifin Ilham
 * @project : Sistem Evaluasi Kepatuhan Keamanan Informasi
 * @version : 1.0
 * ============================================================
 */

// Load konfigurasi
require_once __DIR__ . '/config/config.php';

// Routing - Ambil parameter dari URL
$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'index';

// Router
switch ($page) {
    // ==================== AUTHENTICATION ====================
    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;
        
    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case 'profile':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        
        if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->updateProfile();
        } else {
            $controller->profile();
        }
        break;
    
    // ==================== DASHBOARD ====================
    case 'dashboard':
        require_once __DIR__ . '/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;
    
    // ==================== DOMAIN ISO ====================
    case 'domain':
        require_once __DIR__ . '/controllers/DomainController.php';
        $controller = new DomainController();
        
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                $controller->index();
        }
        break;
    
    // ==================== KONTROL ISO ====================
    case 'kontrol':
        require_once __DIR__ . '/controllers/KontrolController.php';
        $controller = new KontrolController();
        
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                $controller->index();
        }
        break;
    
    // ==================== EVALUASI ====================
    case 'evaluasi':
        require_once __DIR__ . '/controllers/EvaluasiController.php';
        $controller = new EvaluasiController();
        
        switch ($action) {
            case 'pilih':
                $controller->pilihDomain();
                break;
            case 'form':
                $controller->formEvaluasi();
                break;
            case 'simpan':
                $controller->simpan();
                break;
            case 'gap':
                $controller->gapAnalysis();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                $controller->pilihDomain();
        }
        break;
    
    // ==================== LAPORAN ====================
    case 'laporan':
        require_once __DIR__ . '/controllers/LaporanController.php';
        $controller = new LaporanController();
        
        switch ($action) {
            case 'generate':
                $controller->generate();
                break;
            case 'riwayat':
                $controller->riwayat();
                break;
            case 'export':
                $controller->export();
                break;
            default:
                $controller->index();
        }
        break;
    
    // ==================== USER MANAGEMENT ====================
    case 'users':
        require_once __DIR__ . '/controllers/UserController.php';
        $controller = new UserController();
        
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                $controller->index();
        }
        break;
    
    // ==================== DEFAULT ====================
    default:
        // Redirect ke login jika halaman tidak ditemukan
        redirectTo('index.php?page=login');
}
