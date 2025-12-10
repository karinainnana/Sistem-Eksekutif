<?php
/**
 * Dashboard Eksekutif SPAB
 * Tema: Biru #043e80, Orange #e64a19
 * Chart: Chart.js (tanpa watermark)
 */

$page_title = 'Dashboard SPAB';
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/spab_functions.php';

if (!isset($_SESSION['log']) || $_SESSION['log'] !== true) {
    header('Location: ../auth/login.php');
    exit;
}

// Get filter values
$filters = [];
if (!empty($_GET['kabupaten'])) $filters['kabupaten'] = $_GET['kabupaten'];
if (!empty($_GET['pendanaan'])) $filters['pendanaan'] = $_GET['pendanaan'];
if (!empty($_GET['tingkatan'])) $filters['tingkatan'] = $_GET['tingkatan'];
if (!empty($_GET['tahun'])) $filters['tahun'] = $_GET['tahun'];

// Get statistics - with filters
$total_spab = countTotalSPAB($filters);
$spab_by_kabupaten = getSPABByKabupaten($filters);
$spab_by_pendanaan = getSPABByPendanaan($filters);
$spab_by_tahun = getSPABByYear($filters);
$spab_by_tingkatan = getSPABByTingkatan($filters);

// Get filter lists
$kabupatenList = getKabupatenList();
$pendanaanList = getPendanaanList();
$tingkatanList = getTingkatanList();
$tahunList = getTahunList();

// Get all SPAB data
$allSpab = getAllSPAB($filters, 'ASC');

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
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
            background: var(--primary);
            min-height: 100vh;
            padding: 15px;
        }
        
        .dashboard-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 15px;
            min-height: calc(100vh - 30px);
        }
        
        /* Sidebar */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .total-card {
            background: linear-gradient(135deg, var(--secondary) 0%, #c43e15 100%);
            border-radius: 12px;
            padding: 20px;
            color: white;
            text-align: center;
        }
        
        .total-card .icon { font-size: 2.5rem; margin-bottom: 10px; opacity: 0.9; }
        .total-card .number { font-size: 3rem; font-weight: 700; line-height: 1; }
        .total-card .label { font-size: 1rem; opacity: 0.9; margin-top: 5px; }
        
        .filter-card {
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .filter-header {
            background: var(--secondary);
            color: white;
            padding: 12px 15px;
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }
        
        .filter-body {
            background: rgba(0,0,0,0.2);
            padding: 10px 15px;
            max-height: 180px;
            overflow-y: auto;
        }
        
        .filter-body.collapsed { display: none; }
        
        .filter-search {
            background: rgba(255,255,255,0.15);
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            color: white;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .filter-search::placeholder { color: rgba(255,255,255,0.5); }
        
        .filter-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 0;
            color: white;
            font-size: 0.8rem;
        }
        
        .filter-item label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            flex: 1;
        }
        
        .filter-item input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--secondary);
        }
        
        .filter-item .count {
            background: rgba(255,255,255,0.2);
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
        }
        
        /* Main Content */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .chart-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .chart-card .chart-header {
            background: var(--primary);
            color: white;
            padding: 12px 15px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .chart-card .chart-body {
            padding: 15px;
            background: white;
        }
        
        /* Table */
        .table-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            flex: 1;
        }
        
        .table-card .table-header {
            background: var(--primary);
            color: white;
            padding: 12px 15px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-card .table-body {
            padding: 0;
            max-height: 350px;
            overflow-y: auto;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table thead {
            background: #f8f9fa;
            position: sticky;
            top: 0;
        }
        
        .data-table th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--primary);
            border-bottom: 2px solid #e2e8f0;
        }
        
        .data-table td {
            padding: 10px 15px;
            font-size: 0.8rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .data-table tbody tr:hover { background: #f8f9fa; }
        
        .badge-tingkatan {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        
        .badge-tk { background: #d1fae5; color: #065f46; }
        .badge-sd { background: #dbeafe; color: #1e40af; }
        .badge-smp { background: #fef3c7; color: #92400e; }
        .badge-sma { background: #fce7f3; color: #9d174d; }
        .badge-slb { background: #e5e7eb; color: #374151; }
        
        /* Nav header */
        .nav-header {
            background: rgba(0,0,0,0.3);
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 15px;
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
        
        .btn-action {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
        }
        
        .btn-action:hover { background: #c43e15; color: white; }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,0.1); border-radius: 3px; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 3px; }
        .table-body::-webkit-scrollbar-track { background: #f1f1f1; }
        .table-body::-webkit-scrollbar-thumb { background: #c1c1c1; }
        
        .dashboard-footer {
            text-align: right;
            color: white;
            font-size: 0.8rem;
            padding: 10px 0;
            opacity: 0.8;
        }
        
        @media (max-width: 1200px) { .charts-row { grid-template-columns: 1fr; } }
        @media (max-width: 992px) {
            .dashboard-container { grid-template-columns: 1fr; }
            .sidebar { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <div class="nav-header">
        <div class="brand"><i class="fas fa-shield-alt me-2"></i>BPBD PKRR DIY</div>
        <div class="nav-links">
            <a href="../index.php">Dashboard</a>
            <a href="spab.php" class="active">SPAB</a>
            <a href="destana.php">DESTANA</a>
            <?php if ($user_role == 'admin'): ?>
            <a href="../admin/index.php">Admin</a>
            <?php endif; ?>
            <a href="#" onclick="confirmLogout()"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="total-card">
                <div class="icon"><i class="fas fa-school"></i></div>
                <div class="label">Jumlah SPAB</div>
                <div class="number"><?php echo number_format($total_spab); ?></div>
                <?php if (!empty($filters)): ?>
                <a href="spab.php" class="btn btn-sm btn-light mt-2" style="opacity: 0.9;"><i class="fas fa-times me-1"></i>Reset Filter</a>
                <?php endif; ?>
            </div>
            
            <!-- Filter: Kabupaten -->
            <div class="filter-card">
                <div class="filter-header" onclick="toggleFilter(this)">
                    <span><i class="fas fa-map-marker-alt me-2"></i>Kabupaten</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="filter-body">
                    <?php foreach ($kabupatenList as $item): ?>
                    <div class="filter-item">
                        <label>
                            <input type="checkbox" name="kabupaten[]" value="<?php echo htmlspecialchars($item['nama']); ?>" 
                                <?php echo (isset($filters['kabupaten']) && $filters['kabupaten'] == $item['nama']) ? 'checked' : ''; ?>
                                >
                            <?php echo htmlspecialchars($item['nama']); ?>
                        </label>
                        <span class="count"><?php echo $item['total']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Filter: Tingkatan -->
            <div class="filter-card">
                <div class="filter-header" onclick="toggleFilter(this)">
                    <span><i class="fas fa-layer-group me-2"></i>Tingkatan</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="filter-body">
                    <?php foreach ($tingkatanList as $item): ?>
                    <div class="filter-item">
                        <label>
                            <input type="checkbox" name="tingkatan[]" value="<?php echo htmlspecialchars($item['nama']); ?>"
                                <?php echo (isset($filters['tingkatan']) && $filters['tingkatan'] == $item['nama']) ? 'checked' : ''; ?>
                                >
                            <?php echo htmlspecialchars($item['nama']); ?>
                        </label>
                        <span class="count"><?php echo $item['total']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Filter: Tahun -->
            <div class="filter-card">
                <div class="filter-header" onclick="toggleFilter(this)">
                    <span><i class="fas fa-calendar-alt me-2"></i>Tahun</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="filter-body">
                    <?php foreach ($tahunList as $item): ?>
                    <div class="filter-item">
                        <label>
                            <input type="checkbox" name="tahun[]" value="<?php echo htmlspecialchars($item['nama']); ?>"
                                <?php echo (isset($filters['tahun']) && $filters['tahun'] == $item['nama']) ? 'checked' : ''; ?>
                                >
                            <?php echo htmlspecialchars($item['nama']); ?>
                        </label>
                        <span class="count"><?php echo $item['total']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Charts Row 1 -->
            <div class="charts-row">
                <div class="chart-card">
                    <div class="chart-header"><i class="fas fa-chart-bar me-2"></i>Jumlah SPAB per Kabupaten</div>
                    <div class="chart-body"><canvas id="chartKabupaten" height="250"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="chart-header"><i class="fas fa-chart-pie me-2"></i>Persentase Sumber Dana</div>
                    <div class="chart-body"><canvas id="chartPendanaan" height="250"></canvas></div>
                </div>
            </div>
            
            <!-- Charts Row 2 -->
            <div class="charts-row">
                <div class="chart-card">
                    <div class="chart-header"><i class="fas fa-chart-line me-2"></i>Trend SPAB per Tahun</div>
                    <div class="chart-body"><canvas id="chartTahun" height="250"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="chart-header"><i class="fas fa-chart-pie me-2"></i>Persentase Tingkatan</div>
                    <div class="chart-body"><canvas id="chartTingkatan" height="250"></canvas></div>
                </div>
            </div>
            

            
            <div class="dashboard-footer">BPBD PKRR DIY &copy; <?php echo date('Y'); ?></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function toggleFilter(header) {
        const body = header.nextElementSibling;
        body.classList.toggle('collapsed');
        const icon = header.querySelector('.fa-chevron-down');
        icon.style.transform = body.classList.contains('collapsed') ? 'rotate(-90deg)' : '';
    }
    
    function applyFilter() {
        const params = new URLSearchParams();
        
        // Get selected filters
        const kabupaten = document.querySelector('input[name="kabupaten[]"]:checked');
        const tingkatan = document.querySelector('input[name="tingkatan[]"]:checked');
        const tahun = document.querySelector('input[name="tahun[]"]:checked');
        
        if (kabupaten) params.set('kabupaten', kabupaten.value);
        if (tingkatan) params.set('tingkatan', tingkatan.value);
        if (tahun) params.set('tahun', tahun.value);
        
        window.location.href = 'spab.php?' + params.toString();
    }
    
    // Make checkboxes behave like radio buttons (single selection per category)
    document.querySelectorAll('.filter-item input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                // Uncheck other checkboxes in the same category
                const name = this.name;
                document.querySelectorAll('input[name="' + name + '"]').forEach(cb => {
                    if (cb !== this) cb.checked = false;
                });
            }
            applyFilter();
        });
    });
    
    // Chart.js - No watermark!
    const primaryColor = '#043e80';
    const secondaryColor = '#e64a19';
    const chartColors = [primaryColor, secondaryColor, '#10b981', '#f59e0b', '#8b5cf6'];
    
    // Chart 1: SPAB per Kabupaten
    new Chart(document.getElementById('chartKabupaten'), {
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
    
    // Chart 2: Pendanaan (Pie)
    new Chart(document.getElementById('chartPendanaan'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($spab_by_pendanaan, 'label')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($spab_by_pendanaan, 'value')); ?>,
                backgroundColor: chartColors
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'right' } }
        }
    });
    
    // Chart 3: Trend per Tahun
    new Chart(document.getElementById('chartTahun'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($spab_by_tahun, 'label')); ?>,
            datasets: [{
                label: 'Jumlah SPAB',
                data: <?php echo json_encode(array_column($spab_by_tahun, 'value')); ?>,
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
    
    // Chart 4: Tingkatan (Pie)
    new Chart(document.getElementById('chartTingkatan'), {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_column($spab_by_tingkatan, 'label')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($spab_by_tingkatan, 'value')); ?>,
                backgroundColor: chartColors
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'right' } }
        }
    });
    
    function confirmLogout() {
        if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
            window.location.href = '../auth/logout.php';
        }
    }
    </script>
</body>
</html>
