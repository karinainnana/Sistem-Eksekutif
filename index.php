<?php
/**
 * Dashboard Utama
 * Halaman utama sistem informasi eksekutif
 */

$page_title = 'Dashboard';
require_once 'config/config.php';
require_once 'includes/spab_functions.php';
require_once 'includes/destana_functions.php';

// Check login
if (!isset($_SESSION['log']) || $_SESSION['log'] !== true) {
    header('Location: auth/login.php');
    exit;
}

// Get statistics
$total_spab = countTotalSPAB();
$total_destana = countTotalDESTANA();

// Count by tingkatan SPAB
$spab_tk = countSPABByTingkatan('TK');
$spab_sd = countSPABByTingkatan('SD');
$spab_smp = countSPABByTingkatan('SMP');
$spab_sma = countSPABByTingkatan('SMA');
$spab_slb = countSPABByTingkatan('SLB');

// Count by tingkat DESTANA
$destana_utama = countDESTANAByTingkat('Tangguh Utama');
$destana_madya = countDESTANAByTingkat('Tangguh Madya');
$destana_pratama = countDESTANAByTingkat('Tangguh Pratama');

// Get chart data
$spab_by_kabupaten = getSPABByKabupaten();
$spab_by_tahun = getSPABByYear();
$destana_by_kabupaten = getDESTANAByKabupaten();
$destana_by_tahun = getDESTANAByYear();

$user_name = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User';
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=0" />
    <meta name="description" content="Dashboard - Sistem Informasi Eksekutif BPBD DIY" />
    <title><?php echo $page_title; ?> - BPBD PKRR DIY</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <!-- FusionCharts -->
    <script src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
    <script src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a1c2c 0%, #2d3748 100%);
            min-height: 100vh;
            padding: 15px;
        }
        
        .dashboard-container {
            max-width: 1600px;
            margin: 0 auto;
        }
        
        /* Nav header */
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
        
        .nav-header .brand {
            font-size: 1.3rem;
            font-weight: 700;
        }
        
        .nav-header .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 25px;
            font-size: 0.95rem;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .nav-header .nav-links a:hover,
        .nav-header .nav-links a.active {
            opacity: 1;
        }
        
        .nav-header .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .nav-header .user-info .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        /* Welcome Section */
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
            opacity: 0.8;
            font-size: 1.1rem;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--card-color) 0%, var(--card-color-dark) 100%);
            border-radius: 16px;
            padding: 25px;
            color: white;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }
        
        .stat-card.spab {
            --card-color: #e85d04;
            --card-color-dark: #c44d02;
        }
        
        .stat-card.destana {
            --card-color: #2d6a4f;
            --card-color-dark: #1b4332;
        }
        
        .stat-card.info {
            --card-color: #3b82f6;
            --card-color-dark: #2563eb;
        }
        
        .stat-card.warning {
            --card-color: #f59e0b;
            --card-color-dark: #d97706;
        }
        
        .stat-card .stat-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 4rem;
            opacity: 0.2;
        }
        
        .stat-card .stat-content {
            position: relative;
            z-index: 1;
        }
        
        .stat-card .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        .stat-card .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
        }
        
        .stat-card .stat-detail {
            margin-top: 15px;
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .stat-card .stat-link {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            color: white;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        
        .stat-card .stat-link:hover {
            background: rgba(255,255,255,0.3);
        }
        
        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
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
            font-size: 1rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chart-card .chart-header.spab {
            background: linear-gradient(135deg, #e85d04 0%, #c44d02 100%);
            color: white;
        }
        
        .chart-card .chart-header.destana {
            background: linear-gradient(135deg, #2d6a4f 0%, #1b4332 100%);
            color: white;
        }
        
        .chart-card .chart-body {
            padding: 20px;
        }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .action-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .action-card:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-3px);
            color: white;
        }
        
        .action-card i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        
        .action-card span {
            font-weight: 500;
        }
        
        /* Footer */
        .dashboard-footer {
            text-align: center;
            color: white;
            opacity: 0.6;
            font-size: 0.85rem;
            padding: 20px 0;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .charts-section {
                grid-template-columns: 1fr;
            }
            
            .nav-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .nav-header .nav-links {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
            }
            
            .nav-header .nav-links a {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Navigation Header -->
        <div class="nav-header">
            <div class="brand">
                <i class="fas fa-shield-alt me-2"></i>BPBD PKRR DIY
            </div>
            <div class="nav-links">
                <a href="index.php" class="active">Dashboard</a>
                <a href="pages/spab.php">SPAB</a>
                <a href="pages/destana.php">DESTANA</a>
                <?php if ($user_role == 'admin'): ?>
                <a href="admin/index.php">Admin</a>
                <?php endif; ?>
            </div>
            <div class="user-info">
                <span><?php echo htmlspecialchars($user_name); ?></span>
                <span class="badge bg-<?php echo ($user_role == 'admin') ? 'danger' : 'info'; ?>">
                    <?php echo ucfirst($user_role); ?>
                </span>
                <a href="auth/logout.php" class="btn btn-sm btn-outline-light">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
        
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Selamat Datang, <?php echo htmlspecialchars($user_name); ?>!</h1>
            <p>Sistem Informasi Eksekutif BPBD DIY - Data SPAB & DESTANA</p>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card spab">
                <i class="fas fa-school stat-icon"></i>
                <div class="stat-content">
                    <div class="stat-label">Total SPAB</div>
                    <div class="stat-number"><?php echo number_format($total_spab); ?></div>
                    <div class="stat-detail">
                        TK: <?php echo $spab_tk; ?> | SD: <?php echo $spab_sd; ?> | SMP: <?php echo $spab_smp; ?> | SMA: <?php echo $spab_sma; ?> | SLB: <?php echo $spab_slb; ?>
                    </div>
                    <a href="pages/spab.php" class="stat-link">
                        <i class="fas fa-arrow-right me-2"></i>Lihat Detail
                    </a>
                </div>
            </div>
            
            <div class="stat-card destana">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-content">
                    <div class="stat-label">Total DESTANA</div>
                    <div class="stat-number"><?php echo number_format($total_destana); ?></div>
                    <div class="stat-detail">
                        Utama: <?php echo $destana_utama; ?> | Madya: <?php echo $destana_madya; ?> | Pratama: <?php echo $destana_pratama; ?>
                    </div>
                    <a href="pages/destana.php" class="stat-link">
                        <i class="fas fa-arrow-right me-2"></i>Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="pages/spab.php" class="action-card">
                <i class="fas fa-chart-bar"></i>
                <span>Dashboard SPAB</span>
            </a>
            <a href="pages/destana.php" class="action-card">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard DESTANA</span>
            </a>
            <a href="pages/tabel-spab.php" class="action-card">
                <i class="fas fa-table"></i>
                <span>Tabel SPAB</span>
            </a>
            <a href="pages/tabel-destana.php" class="action-card">
                <i class="fas fa-list"></i>
                <span>Tabel DESTANA</span>
            </a>
            <?php if ($user_role == 'admin'): ?>
            <a href="admin/users.php" class="action-card">
                <i class="fas fa-user-cog"></i>
                <span>Kelola Pengguna</span>
            </a>
            <?php endif; ?>
        </div>
        
        <!-- Charts Section -->
        <div class="charts-section">
            <div class="chart-card">
                <div class="chart-header spab">
                    <i class="fas fa-chart-bar"></i>
                    SPAB per Kabupaten
                </div>
                <div class="chart-body">
                    <div id="chartSpabKab" style="width:100%; height:300px;"></div>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header destana">
                    <i class="fas fa-chart-bar"></i>
                    DESTANA per Kabupaten
                </div>
                <div class="chart-body">
                    <div id="chartDestanaKab" style="width:100%; height:300px;"></div>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header spab">
                    <i class="fas fa-chart-line"></i>
                    Trend SPAB per Tahun
                </div>
                <div class="chart-body">
                    <div id="chartSpabTahun" style="width:100%; height:300px;"></div>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header destana">
                    <i class="fas fa-chart-line"></i>
                    Trend DESTANA per Tahun
                </div>
                <div class="chart-body">
                    <div id="chartDestanaTahun" style="width:100%; height:300px;"></div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="dashboard-footer">
            Copyright &copy; <?php echo date('Y'); ?> BPBD PKRR DIY. All rights reserved.
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    FusionCharts.ready(function() {
        // SPAB per Kabupaten
        var chartSpabKab = new FusionCharts({
            type: 'column2d',
            renderAt: 'chartSpabKab',
            width: '100%',
            height: '300',
            dataFormat: 'json',
            dataSource: {
                chart: {
                    xAxisName: "Kabupaten",
                    yAxisName: "Jumlah",
                    theme: "fusion",
                    paletteColors: "#e85d04",
                    showValues: "1",
                    valueFontColor: "#333"
                },
                data: <?php echo json_encode($spab_by_kabupaten); ?>
            }
        });
        chartSpabKab.render();
        
        // DESTANA per Kabupaten
        var chartDestanaKab = new FusionCharts({
            type: 'column2d',
            renderAt: 'chartDestanaKab',
            width: '100%',
            height: '300',
            dataFormat: 'json',
            dataSource: {
                chart: {
                    xAxisName: "Kabupaten",
                    yAxisName: "Jumlah",
                    theme: "fusion",
                    paletteColors: "#2d6a4f",
                    showValues: "1",
                    valueFontColor: "#333"
                },
                data: <?php echo json_encode($destana_by_kabupaten); ?>
            }
        });
        chartDestanaKab.render();
        
        // SPAB per Tahun
        var chartSpabTahun = new FusionCharts({
            type: 'line',
            renderAt: 'chartSpabTahun',
            width: '100%',
            height: '300',
            dataFormat: 'json',
            dataSource: {
                chart: {
                    xAxisName: "Tahun",
                    yAxisName: "Jumlah",
                    theme: "fusion",
                    lineThickness: "3",
                    anchorRadius: "5",
                    paletteColors: "#e85d04",
                    showValues: "1"
                },
                data: <?php echo json_encode($spab_by_tahun); ?>
            }
        });
        chartSpabTahun.render();
        
        // DESTANA per Tahun
        var chartDestanaTahun = new FusionCharts({
            type: 'line',
            renderAt: 'chartDestanaTahun',
            width: '100%',
            height: '300',
            dataFormat: 'json',
            dataSource: {
                chart: {
                    xAxisName: "Tahun",
                    yAxisName: "Jumlah",
                    theme: "fusion",
                    lineThickness: "3",
                    anchorRadius: "5",
                    paletteColors: "#2d6a4f",
                    showValues: "1"
                },
                data: <?php echo json_encode($destana_by_tahun); ?>
            }
        });
        chartDestanaTahun.render();
    });
    </script>
</body>
</html>
