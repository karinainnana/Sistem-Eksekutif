<?php
/**
 * Admin Dashboard dengan Sidebar
 * Tema: Biru #043e80, Orange #e64a19
 */

$page_title = 'Admin Dashboard';
$active_menu = 'dashboard';
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

// Get eksekutif count
$total_eksekutif = $total_users - $total_admins;

$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $page_title; ?> - BPBD PKRR DIY</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary: #043e80;
            --secondary: #e64a19;
            --sidebar-width: 260px;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f4f6f9; }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--primary);
            color: white;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h4 {
            font-weight: 700;
            margin: 0;
            font-size: 1.1rem;
        }
        
        .sidebar-header small {
            opacity: 0.7;
            font-size: 0.8rem;
        }
        
        .sidebar-menu {
            padding: 15px 0;
        }
        
        .menu-label {
            padding: 10px 20px 5px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.5;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: var(--secondary);
        }
        
        .sidebar-menu a i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .topbar {
            background: white;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .topbar h1 {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info .badge {
            background: var(--secondary);
        }
        
        .content-wrapper {
            padding: 25px;
        }
        
        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
        }
        
        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .stat-card .icon.primary { background: var(--primary); }
        .stat-card .icon.secondary { background: var(--secondary); }
        .stat-card .icon.success { background: #10b981; }
        .stat-card .icon.warning { background: #f59e0b; }
        
        .stat-card .info h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin: 0;
        }
        
        .stat-card .info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: none;
            margin-bottom: 25px;
        }
        
        .card-header {
            background: var(--primary);
            color: white;
            padding: 15px 20px;
            font-weight: 600;
            border-radius: 12px 12px 0 0 !important;
            border: none;
        }
        
        .card-body { padding: 20px; }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
        }
        
        .action-btn {
            background: #f8f9fa;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            color: var(--primary);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .action-btn:hover {
            border-color: var(--secondary);
            background: #fff5f2;
            color: var(--secondary);
        }
        
        .action-btn i { font-size: 1.8rem; margin-bottom: 10px; display: block; }
        .action-btn span { font-weight: 500; font-size: 0.9rem; }
        
        /* Info List */
        .info-list { list-style: none; padding: 0; margin: 0; }
        .info-list li {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        .info-list li:last-child { border-bottom: none; }
        .info-list li strong { color: var(--primary); }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-shield-alt me-2"></i>BPBD PKRR DIY</h4>
            <small>Admin Panel</small>
        </div>
        <div class="sidebar-menu">
            <div class="menu-label">Menu Utama</div>
            <a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="users.php"><i class="fas fa-users-cog"></i> Kelola Pengguna</a>
            
            <div class="menu-label">Data Master</div>
            <a href="spab.php"><i class="fas fa-school"></i> Kelola SPAB</a>
            <a href="destana.php"><i class="fas fa-house-user"></i> Kelola DESTANA</a>
            
            <a href="#" onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <script>
    function confirmLogout() {
        if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
            window.location.href = '../auth/logout.php';
        }
    }
    </script>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <h1><i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin</h1>
            <div class="user-info">
                <span><?php echo htmlspecialchars($user_email); ?></span>
                <span class="badge">Admin</span>
            </div>
        </div>
        
        <div class="content-wrapper">
            <!-- Stats -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="icon primary"><i class="fas fa-school"></i></div>
                    <div class="info">
                        <h3><?php echo number_format($total_spab); ?></h3>
                        <p>Total SPAB</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon secondary"><i class="fas fa-house-user"></i></div>
                    <div class="info">
                        <h3><?php echo number_format($total_destana); ?></h3>
                        <p>Total DESTANA</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon success"><i class="fas fa-users"></i></div>
                    <div class="info">
                        <h3><?php echo number_format($total_users); ?></h3>
                        <p>Total Pengguna</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon warning"><i class="fas fa-user-shield"></i></div>
                    <div class="info">
                        <h3><?php echo number_format($total_admins); ?></h3>
                        <p>Total Admin</p>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="users.php" class="action-btn">
                            <i class="fas fa-user-plus"></i>
                            <span>Tambah Pengguna</span>
                        </a>
                        <a href="spab.php" class="action-btn">
                            <i class="fas fa-plus-circle"></i>
                            <span>Kelola SPAB</span>
                        </a>
                        <a href="destana.php" class="action-btn">
                            <i class="fas fa-plus-circle"></i>
                            <span>Kelola DESTANA</span>
                    </div>
                </div>
            </div>
            
            <!-- Info Section -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-2"></i>Statistik Data
                        </div>
                        <div class="card-body">
                            <ul class="info-list">
                                <li><span>Total SPAB</span><strong><?php echo number_format($total_spab); ?></strong></li>
                                <li><span>Total DESTANA</span><strong><?php echo number_format($total_destana); ?></strong></li>
                                <li><span>Total Pengguna</span><strong><?php echo number_format($total_users); ?></strong></li>
                                <li><span>Admin</span><strong><?php echo number_format($total_admins); ?></strong></li>
                                <li><span>Eksekutif</span><strong><?php echo number_format($total_eksekutif); ?></strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-user me-2"></i>Informasi Login
                        </div>
                        <div class="card-body">
                            <ul class="info-list">
                                <li><span>Email</span><strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong></li>
                                <li><span>Role</span><strong style="color: var(--secondary);"><?php echo ucfirst($_SESSION['role']); ?></strong></li>
                                <li><span>Waktu Server</span><strong><?php echo date('d M Y H:i'); ?></strong></li>
                                <li><span>Versi Sistem</span><strong>2.0.0</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
