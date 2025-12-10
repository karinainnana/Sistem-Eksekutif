<?php
/**
 * SPAB Dashboard Page
 * Dashboard SPAB dengan style seperti Looker Studio
 */

$page_title = 'SPAB';
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/spab_functions.php';

// Check login
if (!isset($_SESSION['log']) || $_SESSION['log'] !== true) {
    header('Location: ' . dirname($_SERVER['PHP_SELF']) . '/../auth/login.php');
    exit;
}

// Get filter values from request
$filters = [];
if (!empty($_GET['kabupaten'])) $filters['kabupaten'] = $_GET['kabupaten'];
if (!empty($_GET['tingkatan'])) $filters['tingkatan'] = $_GET['tingkatan'];
if (!empty($_GET['tahun'])) $filters['tahun'] = $_GET['tahun'];
if (!empty($_GET['sumber_pendanaan'])) $filters['sumber_pendanaan'] = $_GET['sumber_pendanaan'];

// Get statistics
$total_spab = countTotalSPAB($filters);

// Get chart data
$spab_by_kabupaten = getSPABByKabupaten();
$spab_by_pendanaan = getSPABByPendanaan();
$spab_by_tahun = getSPABByYear();
$spab_apbd_kab = getSPABAPBDByKabupaten();

// Get filter lists
$kabupatenList = getKabupatenList();
$pendanaanList = getPendanaanList();
$tingkatanList = getTingkatanList();
$tahunList = getTahunList();

// Get all SPAB data with filters
$allSpab = getAllSPAB($filters, 'DESC');

$user_name = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=0" />
    <meta name="description" content="SPAB - Sistem Informasi Eksekutif BPBD DIY" />
    <title><?php echo $page_title; ?> - BPBD PKRR DIY</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
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
            background: #e85d04;
            min-height: 100vh;
            padding: 15px;
        }
        
        .dashboard-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 15px;
            min-height: calc(100vh - 30px);
        }
        
        /* Sidebar Styles */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .total-card {
            background: #1a3a5c;
            border-radius: 12px;
            padding: 20px;
            color: white;
            text-align: center;
        }
        
        .total-card .icon {
            font-size: 3rem;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .total-card .number {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1;
        }
        
        .total-card .label {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-top: 5px;
        }
        
        .filter-card {
            background: #1a3a5c;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .filter-header {
            background: #e85d04;
            color: white;
            padding: 12px 15px;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }
        
        .filter-header i {
            transition: transform 0.3s;
        }
        
        .filter-header.collapsed i {
            transform: rotate(-90deg);
        }
        
        .filter-body {
            background: #1a3a5c;
            padding: 10px 15px;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .filter-body.collapsed {
            display: none;
        }
        
        .filter-search {
            background: rgba(255,255,255,0.1);
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            color: white;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .filter-search::placeholder {
            color: rgba(255,255,255,0.5);
        }
        
        .filter-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 0;
            color: white;
            font-size: 0.85rem;
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
            accent-color: #e85d04;
        }
        
        .filter-item .count {
            background: rgba(255,255,255,0.2);
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
        }
        
        /* Main Content Styles */
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
            background: linear-gradient(135deg, #1a3a5c 0%, #2d5a87 100%);
            color: white;
            padding: 12px 15px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .chart-card .chart-body {
            padding: 15px;
            background: white;
        }
        
        /* Table Styles */
        .table-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            flex: 1;
        }
        
        .table-card .table-header {
            background: linear-gradient(135deg, #1a3a5c 0%, #2d5a87 100%);
            color: white;
            padding: 12px 15px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-card .table-body {
            padding: 0;
            max-height: 400px;
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
            font-size: 0.85rem;
            color: #1a3a5c;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .data-table td {
            padding: 10px 15px;
            font-size: 0.85rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .data-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .badge-tingkatan {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-tk { background: #d4edda; color: #155724; }
        .badge-sd { background: #cce5ff; color: #004085; }
        .badge-smp { background: #fff3cd; color: #856404; }
        .badge-sma { background: #f8d7da; color: #721c24; }
        .badge-slb { background: #e2e3e5; color: #383d41; }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .table-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .table-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
        }
        
        /* Footer */
        .dashboard-footer {
            text-align: right;
            color: white;
            font-size: 0.8rem;
            padding: 10px 0;
            opacity: 0.8;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .charts-row {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 992px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }
        
        /* Nav header */
        .nav-header {
            background: #1a3a5c;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        
        .nav-header .brand {
            font-size: 1.2rem;
            font-weight: 700;
        }
        
        .nav-header .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-size: 0.9rem;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .nav-header .nav-links a:hover,
        .nav-header .nav-links a.active {
            opacity: 1;
        }
        
        .btn-action {
            background: #e85d04;
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-action:hover {
            background: #d45a04;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <div class="nav-header">
        <div class="brand">
            <i class="fas fa-shield-alt me-2"></i>BPBD PKRR DIY
        </div>
        <div class="nav-links">
            <a href="../index.php">Dashboard</a>
            <a href="spab.php" class="active">SPAB</a>
            <a href="destana.php">DESTANA</a>
            <?php if ($_SESSION['role'] == 'admin'): ?>
            <a href="../admin/index.php">Admin</a>
            <?php endif; ?>
            <a href="../auth/logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Total Card -->
            <div class="total-card">
                <div class="icon"><i class="fas fa-school"></i></div>
                <div class="label">Jumlah SPAB</div>
                <div class="number"><?php echo number_format($total_spab); ?></div>
            </div>
            
            <!-- Filter: Kabupaten -->
            <div class="filter-card">
                <div class="filter-header" onclick="toggleFilter(this)">
                    <span><i class="fas fa-map-marker-alt me-2"></i>Kabupaten</span>
                    <span>Record Count <i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="filter-body">
                    <input type="text" class="filter-search" placeholder="🔍 Type to search" onkeyup="filterList(this, 'kabupaten-list')">
                    <div id="kabupaten-list">
                        <?php foreach ($kabupatenList as $item): ?>
                        <div class="filter-item">
                            <label>
                                <input type="checkbox" name="kabupaten[]" value="<?php echo htmlspecialchars($item['nama']); ?>" 
                                    <?php echo (isset($filters['kabupaten']) && $filters['kabupaten'] == $item['nama']) ? 'checked' : ''; ?>
                                    onchange="applyFilter()">
                                <?php echo htmlspecialchars($item['nama']); ?>
                            </label>
                            <span class="count"><?php echo $item['total']; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Filter: Pendanaan -->
            <div class="filter-card">
                <div class="filter-header" onclick="toggleFilter(this)">
                    <span><i class="fas fa-money-bill-wave me-2"></i>Pendanaan</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="filter-body">
                    <?php foreach ($pendanaanList as $item): ?>
                    <div class="filter-item">
                        <label>
                            <input type="checkbox" name="pendanaan[]" value="<?php echo htmlspecialchars($item['nama']); ?>"
                                <?php echo (isset($filters['sumber_pendanaan']) && $filters['sumber_pendanaan'] == $item['nama']) ? 'checked' : ''; ?>
                                onchange="applyFilter()">
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
                                onchange="applyFilter()">
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
                                onchange="applyFilter()">
                            <?php echo htmlspecialchars($item['nama']); ?>
                        </label>
                        <span class="count"><?php echo $item['total']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Filter: Nama Sekolah -->
            <div class="filter-card">
                <div class="filter-header" onclick="toggleFilter(this)">
                    <span><i class="fas fa-search me-2"></i>Nama Sekolah</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="filter-body collapsed">
                    <input type="text" class="filter-search" id="searchNama" placeholder="🔍 Cari nama sekolah..." onkeyup="searchTable()">
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Charts Row 1 -->
            <div class="charts-row">
                <div class="chart-card">
                    <div class="chart-header">
                        <i class="fas fa-chart-bar me-2"></i>Jumlah SPAB per Kabupaten 2014-2025
                    </div>
                    <div class="chart-body">
                        <div id="chartKabupaten" style="width:100%; height:280px;"></div>
                    </div>
                </div>
                
                <div class="chart-card">
                    <div class="chart-header">
                        <i class="fas fa-chart-pie me-2"></i>Persentase Sumber Dana Pembiayaan SPAB DIY 2014-2025
                    </div>
                    <div class="chart-body">
                        <div id="chartPendanaan" style="width:100%; height:280px;"></div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Row 2 -->
            <div class="charts-row">
                <div class="chart-card">
                    <div class="chart-header">
                        <i class="fas fa-chart-bar me-2"></i>Jumlah SPAB yang Dibiayai Dana APBD Pemda DIY per Kabupaten 2014-2025
                    </div>
                    <div class="chart-body">
                        <div id="chartAPBD" style="width:100%; height:280px;"></div>
                    </div>
                </div>
                
                <div class="chart-card">
                    <div class="chart-header">
                        <i class="fas fa-chart-pie me-2"></i>Persentase Pembentukan SPAB Per Tahun
                    </div>
                    <div class="chart-body">
                        <div id="chartTahun" style="width:100%; height:280px;"></div>
                    </div>
                </div>
            </div>
            
            <!-- Data Table -->
            <div class="table-card">
                <div class="table-header">
                    <span><i class="fas fa-table me-2"></i>Data SPAB</span>
                    <div>
                        <button class="btn-action" onclick="location.href='tabel-spab.php'">
                            <i class="fas fa-external-link-alt me-1"></i>Lihat Semua
                        </button>
                    </div>
                </div>
                <div class="table-body">
                    <table class="data-table" id="spabTable">
                        <thead>
                            <tr>
                                <th>Kabupaten</th>
                                <th>Nama Sekolah</th>
                                <th>Tingkatan</th>
                                <th>Tahun</th>
                                <th>Pendanaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($data = mysqli_fetch_assoc($allSpab)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['kabupaten']); ?></td>
                                <td><?php echo htmlspecialchars($data['nama_sekolah']); ?></td>
                                <td>
                                    <span class="badge-tingkatan badge-<?php echo strtolower($data['tingkatan']); ?>">
                                        <?php echo htmlspecialchars($data['tingkatan']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($data['tahun']); ?></td>
                                <td><?php echo htmlspecialchars($data['sumber_pendanaan']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="dashboard-footer">
                1 - 100 / <?php echo $total_spab; ?> &nbsp; | &nbsp; Privacy &nbsp; | &nbsp; BPBD PKRR DIY
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Toggle filter collapse
    function toggleFilter(header) {
        header.classList.toggle('collapsed');
        const body = header.nextElementSibling;
        body.classList.toggle('collapsed');
    }
    
    // Filter list search
    function filterList(input, listId) {
        const filter = input.value.toLowerCase();
        const list = document.getElementById(listId);
        const items = list.getElementsByClassName('filter-item');
        
        for (let item of items) {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(filter) ? '' : 'none';
        }
    }
    
    // Apply filter
    function applyFilter() {
        const params = new URLSearchParams();
        
        document.querySelectorAll('input[name="kabupaten[]"]:checked').forEach(cb => {
            params.set('kabupaten', cb.value);
        });
        
        document.querySelectorAll('input[name="tingkatan[]"]:checked').forEach(cb => {
            params.set('tingkatan', cb.value);
        });
        
        document.querySelectorAll('input[name="tahun[]"]:checked').forEach(cb => {
            params.set('tahun', cb.value);
        });
        
        document.querySelectorAll('input[name="pendanaan[]"]:checked').forEach(cb => {
            params.set('sumber_pendanaan', cb.value);
        });
        
        window.location.href = 'spab.php?' + params.toString();
    }
    
    // Search table
    function searchTable() {
        const input = document.getElementById('searchNama');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('spabTable');
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let found = false;
            for (let cell of cells) {
                if (cell.textContent.toLowerCase().includes(filter)) {
                    found = true;
                    break;
                }
            }
            rows[i].style.display = found ? '' : 'none';
        }
    }
    
    // FusionCharts
    FusionCharts.ready(function() {
        // Chart 1: SPAB per Kabupaten (Bar)
        var chartKabupaten = new FusionCharts({
            type: 'column2d',
            renderAt: 'chartKabupaten',
            width: '100%',
            height: '280',
            dataFormat: 'json',
            dataSource: {
                chart: {
                    caption: "",
                    xAxisName: "",
                    yAxisName: "Jumlah Sekolah",
                    theme: "fusion",
                    paletteColors: "#1a3a5c",
                    showValues: "1",
                    valueFontColor: "#333",
                    valueFontSize: "11",
                    bgColor: "#ffffff",
                    canvasBgColor: "#ffffff",
                    showBorder: "0",
                    showCanvasBorder: "0"
                },
                data: <?php echo json_encode($spab_by_kabupaten); ?>
            }
        });
        chartKabupaten.render();
        
        // Chart 2: Persentase Pendanaan (Pie)
        var chartPendanaan = new FusionCharts({
            type: 'pie2d',
            renderAt: 'chartPendanaan',
            width: '100%',
            height: '280',
            dataFormat: 'json',
            dataSource: {
                chart: {
                    caption: "",
                    theme: "fusion",
                    showPercentValues: "1",
                    showLabels: "1",
                    showLegend: "1",
                    legendPosition: "right",
                    enableSmartLabels: "1",
                    decimals: "1",
                    bgColor: "#ffffff",
                    showBorder: "0"
                },
                data: <?php echo json_encode($spab_by_pendanaan); ?>
            }
        });
        chartPendanaan.render();
        
        // Chart 3: SPAB APBD per Kabupaten
        var chartAPBD = new FusionCharts({
            type: 'column2d',
            renderAt: 'chartAPBD',
            width: '100%',
            height: '280',
            dataFormat: 'json',
            dataSource: {
                chart: {
                    caption: "",
                    subCaption: "Dana APBD Pemda DIY per Kab",
                    xAxisName: "",
                    yAxisName: "",
                    theme: "fusion",
                    paletteColors: "#1a3a5c",
                    showValues: "1",
                    valueFontColor: "#333",
                    bgColor: "#ffffff",
                    canvasBgColor: "#ffffff",
                    showBorder: "0",
                    showCanvasBorder: "0"
                },
                data: <?php echo json_encode($spab_apbd_kab); ?>
            }
        });
        chartAPBD.render();
        
        // Chart 4: Persentase per Tahun (Pie)
        var chartTahun = new FusionCharts({
            type: 'pie2d',
            renderAt: 'chartTahun',
            width: '100%',
            height: '280',
            dataFormat: 'json',
            dataSource: {
                chart: {
                    caption: "",
                    theme: "fusion",
                    showPercentValues: "1",
                    showLabels: "1",
                    showLegend: "1",
                    legendPosition: "right",
                    enableSmartLabels: "1",
                    decimals: "1",
                    bgColor: "#ffffff",
                    showBorder: "0"
                },
                data: <?php echo json_encode($spab_by_tahun); ?>
            }
        });
        chartTahun.render();
    });
    </script>
</body>
</html>
