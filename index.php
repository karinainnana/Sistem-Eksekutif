<?php
/**
 * Dashboard Eksekutif Utama
 * Tema: Biru #043e80, Orange #e64a19
 */

$page_title = 'Dashboard Eksekutif';
require_once 'config/config.php';
require_once 'includes/spab_functions.php';
require_once 'includes/destana_functions.php';

if (!isset($_SESSION['log']) || $_SESSION['log'] !== true) {
    header('Location: auth/login.php');
    exit;
}

// Get statistics
$total_spab = countTotalSPAB();
$total_destana = countTotalDESTANA();
$spab_by_tingkatan = getSPABByTingkatan();
$destana_by_tingkat = getDESTANAByTingkat();

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
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
    
    <style>
        :root {
            --primary: #043e80;
            --secondary: #e64a19;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, #021d3d 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container-main {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .nav-header {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 15px 25px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .nav-header .brand { 
            font-size: 1.3rem; 
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .nav-header .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 25px;
            font-size: 0.9rem;
            opacity: 0.8;
            transition: all 0.3s;
            padding: 8px 15px;
            border-radius: 8px;
        }
        
        .nav-header .nav-links a:hover,
        .nav-header .nav-links a.active { 
            opacity: 1;
            background: rgba(255,255,255,0.15);
        }
        
        .welcome-section {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            padding: 20px;
        }
        
        .welcome-section h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .welcome-section p {
            font-size: 1.1rem;
            opacity: 0.85;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-bottom: 35px;
        }
        
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 25px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover { 
            transform: translateY(-8px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.25);
        }
        
        .stat-card .icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            flex-shrink: 0;
        }
        
        .stat-card .icon.primary { background: linear-gradient(135deg, var(--primary) 0%, #032d5e 100%); }
        .stat-card .icon.secondary { background: linear-gradient(135deg, var(--secondary) 0%, #c43e15 100%); }
        
        .stat-card .info h3 {
            font-size: 3rem;
            font-weight: 700;
            color: #333;
            margin: 0;
            line-height: 1;
        }
        
        .stat-card .info p {
            margin: 8px 0 0;
            color: #555;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .stat-card .info small {
            color: #888;
            font-size: 0.85rem;
        }
        
        /* Quick Links */
        .quick-links {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }
        
        .quick-link {
            background: rgba(255,255,255,0.12);
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 16px;
            padding: 25px 20px;
            color: white;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
        }
        
        .quick-link:hover {
            background: rgba(255,255,255,0.25);
            border-color: var(--secondary);
            transform: translateY(-5px);
            color: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .quick-link i { 
            font-size: 2.2rem; 
            margin-bottom: 12px; 
            display: block;
            color: var(--secondary);
        }
        
        .quick-link span { 
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        /* Breakdown Cards */
        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .breakdown-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .breakdown-card .breakdown-header {
            padding: 18px 25px;
            font-weight: 600;
            font-size: 1.05rem;
            color: white;
            display: flex;
            align-items: center;
        }
        
        .breakdown-card .breakdown-header.primary { background: linear-gradient(135deg, var(--primary) 0%, #032d5e 100%); }
        .breakdown-card .breakdown-header.secondary { background: linear-gradient(135deg, var(--secondary) 0%, #c43e15 100%); }
        
        .breakdown-card .breakdown-body { padding: 10px 0; }
        
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }
        
        .breakdown-item:hover { background: #f8f9fa; }
        .breakdown-item:last-child { border-bottom: none; }
        
        .breakdown-item .label {
            font-weight: 500;
            color: #444;
            font-size: 0.95rem;
        }
        
        .breakdown-item .value {
            font-weight: 700;
            padding: 8px 18px;
            border-radius: 25px;
            font-size: 0.9rem;
        }
        
        .breakdown-item .value.primary { 
            background: linear-gradient(135deg, rgba(4, 62, 128, 0.1) 0%, rgba(4, 62, 128, 0.05) 100%);
            color: var(--primary); 
        }
        .breakdown-item .value.secondary { 
            background: linear-gradient(135deg, rgba(230, 74, 25, 0.1) 0%, rgba(230, 74, 25, 0.05) 100%);
            color: var(--secondary); 
        }
        
        .footer {
            text-align: center;
            color: white;
            padding: 25px;
            opacity: 0.7;
            font-size: 0.9rem;
        }
        
        @media (max-width: 992px) {
            .stats-grid { grid-template-columns: 1fr; }
            .breakdown-grid { grid-template-columns: 1fr; }
            .quick-links { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 576px) {
            .welcome-section h1 { font-size: 1.8rem; }
            .quick-links { grid-template-columns: 1fr; }
            .nav-header { flex-direction: column; gap: 15px; }
            .nav-header .nav-links a { margin: 5px; }
        }
    </style>
</head>
<body>
    <div class="container-main">
        <!-- Navigation -->
        <div class="nav-header">
            <div class="brand"><i class="fas fa-shield-alt"></i>BPBD PKRR DIY</div>
            <div class="nav-links">
                <a href="index.php" class="active">Dashboard</a>
                <a href="pages/spab.php">SPAB</a>
                <a href="pages/destana.php">DESTANA</a>
                <?php if ($user_role == 'admin'): ?>
                <a href="admin/index.php">Admin</a>
                <?php endif; ?>
                <a href="#" onclick="confirmLogout()"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
            </div>
        </div>

        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1><i class="fas fa-chart-line me-3"></i>Sistem Informasi Eksekutif</h1>
            <p>Selamat datang, <strong><?php echo htmlspecialchars($user_email); ?></strong> | <?php echo date('l, d F Y'); ?></p>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon primary"><i class="fas fa-school"></i></div>
                <div class="info">
                    <h3><?php echo number_format($total_spab); ?></h3>
                    <p>Total SPAB</p>
                    <small>Sekolah/Madrasah Aman Bencana</small>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon secondary"><i class="fas fa-house-user"></i></div>
                <div class="info">
                    <h3><?php echo number_format($total_destana); ?></h3>
                    <p>Total DESTANA</p>
                    <small>Desa/Kelurahan Tangguh Bencana</small>
                </div>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="quick-links">
            <a href="pages/spab.php" class="quick-link">
                <i class="fas fa-chart-bar"></i>
                <span>Dashboard SPAB</span>
            </a>
            <a href="pages/destana.php" class="quick-link">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard DESTANA</span>
            </a>
            <?php if ($user_role == 'admin'): ?>
            <a href="admin/spab.php" class="quick-link">
                <i class="fas fa-edit"></i>
                <span>Kelola SPAB</span>
            </a>
            <a href="admin/destana.php" class="quick-link">
                <i class="fas fa-cog"></i>
                <span>Kelola DESTANA</span>
            </a>
            <?php endif; ?>
        </div>
        
        <!-- Breakdown Cards -->
        <div class="breakdown-grid">
            <div class="breakdown-card">
                <div class="breakdown-header primary"><i class="fas fa-layer-group me-2"></i>SPAB per Tingkatan</div>
                <div class="breakdown-body">
                    <?php foreach ($spab_by_tingkatan as $item): ?>
                    <div class="breakdown-item">
                        <span class="label"><?php echo htmlspecialchars($item['label']); ?></span>
                        <span class="value primary"><?php echo number_format($item['value']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="breakdown-card">
                <div class="breakdown-header secondary"><i class="fas fa-star me-2"></i>DESTANA per Tingkat</div>
                <div class="breakdown-body">
                    <?php foreach ($destana_by_tingkat as $item): ?>
                    <div class="breakdown-item">
                        <span class="label"><?php echo htmlspecialchars($item['label']); ?></span>
                        <span class="value secondary"><?php echo number_format($item['value']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="footer">
            BPBD PKRR DIY &copy; <?php echo date('Y'); ?> | Sistem Informasi Eksekutif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function confirmLogout() {
        if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
            window.location.href = 'auth/logout.php';
        }
    }
    </script>
</body>
</html>
