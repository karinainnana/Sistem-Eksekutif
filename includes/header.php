<?php
/**
 * Header Component
 * Komponen header yang akan digunakan di semua halaman
 */

// Cek apakah user sudah login
if (!isset($_SESSION['log']) || $_SESSION['log'] !== true) {
    header('Location: ' . APP_URL . '/auth/login.php');
    exit;
}

// Get user info
$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$user_name = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User';

// Determine base path based on current directory
$base_path = '';
$current_dir = dirname($_SERVER['PHP_SELF']);
if (strpos($current_dir, '/admin') !== false || strpos($current_dir, '/eksekutif') !== false) {
    $base_path = '../';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistem Informasi Eksekutif BPBD DIY" />
    <meta name="author" content="BPBD DIY" />
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo APP_NAME; ?></title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="<?php echo $base_path; ?>assets/css/styles.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <!-- FusionCharts -->
    <script src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
    <script src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
        }
        .sb-sidenav-dark {
            background: linear-gradient(180deg, #1a1c2c 0%, #2d3748 100%);
        }
        .sb-topnav {
            background: linear-gradient(90deg, #1a1c2c 0%, #2d3748 100%) !important;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }
        .stats-card {
            transition: transform 0.2s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="sb-nav-fixed">

<!-- NAVBAR TOP -->
<nav class="sb-topnav navbar navbar-expand navbar-dark">
    <a class="navbar-brand ps-3" href="<?php echo $base_path; ?>index.php">
        <i class="fas fa-shield-alt me-2"></i><?php echo APP_NAME; ?>
    </a>
    
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <!-- Search dapat ditambahkan di sini -->
    </form>
    
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle fa-fw me-1"></i>
                <span class="d-none d-md-inline"><?php echo htmlspecialchars($user_name); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                <li><span class="dropdown-item-text text-muted small"><?php echo htmlspecialchars($user_email); ?></span></li>
                <li><span class="dropdown-item-text"><span class="badge bg-primary"><?php echo ucfirst($user_role); ?></span></span></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="<?php echo $base_path; ?>auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>

<div id="layoutSidenav">
