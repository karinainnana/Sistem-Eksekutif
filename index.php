<?php
/**
 * Dashboard Eksekutif Utama
 * Tema: Biru #043e80, Orange #e64a19
 * Chart: Chart.js (tanpa watermark)
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
$spab_by_kabupaten = getSPABByKabupaten();
$spab_by_tahun = getSPABByYear();
$destana_by_kabupaten = getDESTANAByKabupaten();
$destana_by_tahun = getDESTANAByYear();
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
        
        .nav-header {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 15px 25px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        
        .nav-header .brand { font-size: 1.3rem; font-weight: 700; }
        
        .nav-header .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 25px;
            font-size: 0.9rem;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .nav-header .nav-links a:hover,
        .nav-header .nav-links a.active { opacity: 1; }
        
        .welcome-section {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .welcome-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .welcome-section p {
            font-size: 1.1rem;
            opacity: 0.8;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s;
        }
        
        .stat-card:hover { transform: translateY(-5px); }
        
        .stat-card .icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
        }
        
        .stat-card .icon.primary { background: linear-gradient(135deg, var(--primary) 0%, #032d5e 100%); }
        .stat-card .icon.secondary { background: linear-gradient(135deg, var(--secondary) 0%, #c43e15 100%); }
        
        .stat-card .info h3 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin: 0;
            line-height: 1;
        }
        
        .stat-card .info p {
            margin: 5px 0 0;
            color: #666;
            font-size: 0.95rem;
        }
        
        .stat-card .info small {
            color: #999;
            font-size: 0.8rem;
        }
        
        /* Quick Links */
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .quick-link {
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 12px;
            padding: 20px;
            color: white;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
        }
        
        .quick-link:hover {
            background: rgba(255,255,255,0.2);
            border-color: var(--secondary);
            transform: translateY(-3px);
            color: white;
        }
        
        .quick-link i { font-size: 2rem; margin-bottom: 10px; display: block; }
        .quick-link span { font-weight: 500; }
        
        /* Charts */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .chart-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        .chart-card .chart-header {
            padding: 15px 20px;
            font-weight: 600;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chart-card .chart-header.primary { background: var(--primary); }
        .chart-card .chart-header.secondary { background: var(--secondary); }
        
        .chart-card .chart-body {
            padding: 20px;
        }
        
        /* Breakdown Cards */
        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .breakdown-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        .breakdown-card .breakdown-header {
            padding: 15px 20px;
            font-weight: 600;
            color: white;
        }
        
        .breakdown-card .breakdown-header.primary { background: var(--primary); }
        .breakdown-card .breakdown-header.secondary { background: var(--secondary); }
        
        .breakdown-card .breakdown-body { padding: 0; }
        
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .breakdown-item:last-child { border-bottom: none; }
        
        .breakdown-item .label {
            font-weight: 500;
            color: #333;
        }
        
        .breakdown-item .value {
            font-weight: 700;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        .breakdown-item .value.primary { background: rgba(4, 62, 128, 0.1); color: var(--primary); }
        .breakdown-item .value.secondary { background: rgba(230, 74, 25, 0.1); color: var(--secondary); }
        
        .footer {
            text-align: center;
            color: white;
            padding: 20px;
            opacity: 0.7;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .charts-grid { grid-template-columns: 1fr; }
            .welcome-section h1 { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <div class="nav-header">
        <div class="brand"><i class="fas fa-shield-alt me-2"></i>BPBD PKRR DIY</div>
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
        <h1>Sistem Informasi Eksekutif</h1>
        <p>Selamat datang, <?php echo htmlspecialchars($user_email); ?> | <?php echo date('l, d F Y'); ?></p>
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
            <i class="fas fa-edit"></i>
            <span>Kelola DESTANA</span>
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Charts Grid -->
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-header primary"><i class="fas fa-chart-bar"></i>SPAB per Kabupaten</div>
            <div class="chart-body"><canvas id="chartSpabKab" height="250"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-header secondary"><i class="fas fa-chart-bar"></i>DESTANA per Kabupaten</div>
            <div class="chart-body"><canvas id="chartDestanaKab" height="250"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-header primary"><i class="fas fa-chart-line"></i>Trend SPAB per Tahun</div>
            <div class="chart-body"><canvas id="chartSpabTahun" height="250"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-header secondary"><i class="fas fa-chart-line"></i>Trend DESTANA per Tahun</div>
            <div class="chart-body"><canvas id="chartDestanaTahun" height="250"></canvas></div>
        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    const primaryColor = '#043e80';
    const secondaryColor = '#e64a19';
    
    // Chart 1: SPAB per Kabupaten
    new Chart(document.getElementById('chartSpabKab'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($spab_by_kabupaten, 'label')); ?>,
            datasets: [{
                label: 'Jumlah SPAB',
                data: <?php echo json_encode(array_column($spab_by_kabupaten, 'value')); ?>,
                backgroundColor: primaryColor,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
    
    // Chart 2: DESTANA per Kabupaten
    new Chart(document.getElementById('chartDestanaKab'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($destana_by_kabupaten, 'label')); ?>,
            datasets: [{
                label: 'Jumlah DESTANA',
                data: <?php echo json_encode(array_column($destana_by_kabupaten, 'value')); ?>,
                backgroundColor: secondaryColor,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
    
    // Chart 3: SPAB per Tahun
    new Chart(document.getElementById('chartSpabTahun'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($spab_by_tahun, 'label')); ?>,
            datasets: [{
                label: 'Jumlah SPAB',
                data: <?php echo json_encode(array_column($spab_by_tahun, 'value')); ?>,
                borderColor: primaryColor,
                backgroundColor: 'rgba(4, 62, 128, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
    
    // Chart 4: DESTANA per Tahun
    new Chart(document.getElementById('chartDestanaTahun'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($destana_by_tahun, 'label')); ?>,
            datasets: [{
                label: 'Jumlah DESTANA',
                data: <?php echo json_encode(array_column($destana_by_tahun, 'value')); ?>,
                borderColor: secondaryColor,
                backgroundColor: 'rgba(230, 74, 25, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
    
    function confirmLogout() {
        if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
            window.location.href = 'auth/logout.php';
        }
    }
    </script>
</body>
</html>
