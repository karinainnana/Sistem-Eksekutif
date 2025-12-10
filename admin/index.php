<?php
/**
 * Admin Dashboard
 * Halaman dashboard untuk admin
 */

$page_title = 'Admin Dashboard';
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/spab_functions.php';
require_once dirname(__DIR__) . '/includes/destana_functions.php';

// Check admin role
if (!isset($_SESSION['log']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Get statistics
$total_spab = countTotalSPAB();
$total_destana = countTotalDESTANA();

// Get user count
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengguna");
$row = mysqli_fetch_assoc($result);
$total_users = (int)$row['total'];

// Get admin count
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengguna WHERE role = 'admin'");
$row = mysqli_fetch_assoc($result);
$total_admins = (int)$row['total'];

$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=0" />
    <meta name="description" content="Admin Dashboard - Sistem Informasi Eksekutif BPBD DIY" />
    <title><?php echo $page_title; ?> - BPBD PKRR DIY</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a1c2c 0%, #2d3748 100%);
            min-height: 100vh;
            padding: 15px;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .nav-header {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        
        .nav-header .brand { font-size: 1.2rem; font-weight: 700; }
        
        .nav-header .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-size: 0.9rem;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .nav-header .nav-links a:hover,
        .nav-header .nav-links a.active { opacity: 1; }
        
        .page-title {
            color: white;
            margin-bottom: 25px;
        }
        
        .page-title h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .page-title p { opacity: 0.8; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--card-color) 0%, var(--card-color-dark) 100%);
            border-radius: 16px;
            padding: 25px;
            color: white;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            display: block;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
            color: white;
        }
        
        .stat-card.primary { --card-color: #3b82f6; --card-color-dark: #2563eb; }
        .stat-card.success { --card-color: #10b981; --card-color-dark: #059669; }
        .stat-card.warning { --card-color: #f59e0b; --card-color-dark: #d97706; }
        .stat-card.danger { --card-color: #ef4444; --card-color-dark: #dc2626; }
        
        .stat-card .stat-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 4rem;
            opacity: 0.2;
        }
        
        .stat-card .stat-content { position: relative; z-index: 1; }
        .stat-card .stat-label { font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px; }
        .stat-card .stat-number { font-size: 2.5rem; font-weight: 700; line-height: 1; }
        .stat-card .stat-action { margin-top: 15px; font-size: 0.85rem; opacity: 0.9; }
        
        .content-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            margin-bottom: 25px;
        }
        
        .content-header {
            background: linear-gradient(135deg, #1a1c2c 0%, #2d3748 100%);
            color: white;
            padding: 20px;
            font-weight: 600;
        }
        
        .content-header i { margin-right: 10px; }
        
        .content-body { padding: 25px; }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .action-btn {
            background: #f8f9fa;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            color: #1a1c2c;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .action-btn:hover {
            border-color: #3b82f6;
            background: #f0f7ff;
            transform: translateY(-3px);
            color: #3b82f6;
        }
        
        .action-btn i { font-size: 2rem; margin-bottom: 10px; display: block; }
        .action-btn span { font-weight: 500; }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .info-card h5 {
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .info-item:last-child { border-bottom: none; }
        
        .badge-role {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-admin { background: #fee2e2; color: #dc2626; }
        .badge-eksekutif { background: #dbeafe; color: #2563eb; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Navigation Header -->
        <div class="nav-header">
            <div class="brand">
                <i class="fas fa-user-shield me-2"></i>Admin Panel
            </div>
            <div class="nav-links">
                <a href="../index.php">Dashboard</a>
                <a href="../pages/spab.php">SPAB</a>
                <a href="../pages/destana.php">DESTANA</a>
                <a href="index.php" class="active">Admin</a>
                <a href="users.php">Pengguna</a>
                <a href="../auth/logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
            </div>
        </div>
        
        <!-- Page Title -->
        <div class="page-title">
            <h1><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h1>
            <p>Selamat datang, <?php echo htmlspecialchars($user_email); ?></p>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <a href="../pages/tabel-spab.php" class="stat-card primary">
                <i class="fas fa-school stat-icon"></i>
                <div class="stat-content">
                    <div class="stat-label">Total SPAB</div>
                    <div class="stat-number"><?php echo number_format($total_spab); ?></div>
                    <div class="stat-action"><i class="fas fa-arrow-right me-1"></i>Kelola Data</div>
                </div>
            </a>
            
            <a href="../pages/tabel-destana.php" class="stat-card success">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-content">
                    <div class="stat-label">Total DESTANA</div>
                    <div class="stat-number"><?php echo number_format($total_destana); ?></div>
                    <div class="stat-action"><i class="fas fa-arrow-right me-1"></i>Kelola Data</div>
                </div>
            </a>
            
            <a href="users.php" class="stat-card warning">
                <i class="fas fa-user-friends stat-icon"></i>
                <div class="stat-content">
                    <div class="stat-label">Total Pengguna</div>
                    <div class="stat-number"><?php echo number_format($total_users); ?></div>
                    <div class="stat-action"><i class="fas fa-arrow-right me-1"></i>Kelola Pengguna</div>
                </div>
            </a>
            
            <a href="users.php" class="stat-card danger">
                <i class="fas fa-user-shield stat-icon"></i>
                <div class="stat-content">
                    <div class="stat-label">Total Admin</div>
                    <div class="stat-number"><?php echo number_format($total_admins); ?></div>
                    <div class="stat-action"><i class="fas fa-arrow-right me-1"></i>Kelola Admin</div>
                </div>
            </a>
        </div>
        
        <!-- Quick Actions -->
        <div class="content-card">
            <div class="content-header">
                <i class="fas fa-bolt"></i>Quick Actions
            </div>
            <div class="content-body">
                <div class="quick-actions">
                    <a href="../pages/tabel-spab.php" class="action-btn">
                        <i class="fas fa-plus-circle text-primary"></i>
                        <span>Tambah SPAB</span>
                    </a>
                    <a href="../pages/tabel-destana.php" class="action-btn">
                        <i class="fas fa-plus-circle text-success"></i>
                        <span>Tambah DESTANA</span>
                    </a>
                    <a href="users.php" class="action-btn">
                        <i class="fas fa-user-plus text-warning"></i>
                        <span>Tambah Pengguna</span>
                    </a>
                    <a href="../index.php" class="action-btn">
                        <i class="fas fa-chart-line text-info"></i>
                        <span>Dashboard Eksekutif</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Info Section -->
        <div class="content-card">
            <div class="content-header">
                <i class="fas fa-info-circle"></i>Informasi Sistem
            </div>
            <div class="content-body">
                <div class="info-grid">
                    <div class="info-card">
                        <h5><i class="fas fa-chart-bar me-2"></i>Statistik Data</h5>
                        <div class="info-item">
                            <span>Total SPAB</span>
                            <strong><?php echo number_format($total_spab); ?></strong>
                        </div>
                        <div class="info-item">
                            <span>Total DESTANA</span>
                            <strong><?php echo number_format($total_destana); ?></strong>
                        </div>
                        <div class="info-item">
                            <span>Total Pengguna</span>
                            <strong><?php echo number_format($total_users); ?></strong>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <h5><i class="fas fa-user me-2"></i>Informasi Login</h5>
                        <div class="info-item">
                            <span>Email</span>
                            <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong>
                        </div>
                        <div class="info-item">
                            <span>Role</span>
                            <span class="badge-role badge-admin"><?php echo ucfirst($_SESSION['role']); ?></span>
                        </div>
                        <div class="info-item">
                            <span>Waktu Server</span>
                            <strong><?php echo date('d M Y H:i'); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
