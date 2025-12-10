<?php
/**
 * Sidebar Component
 * Komponen sidebar navigasi
 */

// Determine active page
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                
                <!-- Dashboard Menu -->
                <div class="sb-sidenav-menu-heading">Dashboard</div>
                
                <a class="nav-link <?php echo ($current_page == 'index') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Halaman Utama
                </a>
                
                <!-- Data Menu -->
                <div class="sb-sidenav-menu-heading">Data</div>
                
                <a class="nav-link <?php echo ($current_page == 'spab') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>pages/spab.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-school"></i></div>
                    SPAB
                </a>
                
                <a class="nav-link <?php echo ($current_page == 'destana') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>pages/destana.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    DESTANA
                </a>
                
                <!-- Tabel Menu -->
                <div class="sb-sidenav-menu-heading">Tabel</div>
                
                <a class="nav-link <?php echo ($current_page == 'tabel-spab') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>pages/tabel-spab.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                    Tabel SPAB
                </a>
                
                <a class="nav-link <?php echo ($current_page == 'tabel-destana') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>pages/tabel-destana.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                    Tabel DESTANA
                </a>
                
                <?php if ($user_role == 'admin'): ?>
                <!-- Admin Menu -->
                <div class="sb-sidenav-menu-heading">Admin</div>
                
                <a class="nav-link <?php echo ($current_page == 'users') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>admin/users.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-user-cog"></i></div>
                    Kelola Pengguna
                </a>
                <?php endif; ?>
                
            </div>
        </div>
        
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            <strong><?php echo htmlspecialchars($user_name); ?></strong>
            <span class="badge bg-<?php echo ($user_role == 'admin') ? 'danger' : 'info'; ?> ms-2">
                <?php echo ucfirst($user_role); ?>
            </span>
        </div>
    </nav>
</div>

<div id="layoutSidenav_content">
