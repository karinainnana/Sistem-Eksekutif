<?php
/**
 * Admin - Kelola DESTANA dengan Sidebar
 * Tema: Biru #043e80, Orange #e64a19
 */

$page_title = 'Kelola DESTANA';
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/destana_functions.php';

// Check admin role
if (!isset($_SESSION['log']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addnewdestana'])) {
        addDESTANA($_POST);
        header('Location: destana.php?success=tambah');
        exit;
    }
    if (isset($_POST['updatedestana'])) {
        updateDESTANA($_POST['id_destana'], $_POST);
        header('Location: destana.php?success=update');
        exit;
    }
    if (isset($_POST['hapusdestana'])) {
        deleteDESTANA($_POST['id_destana']);
        header('Location: destana.php?success=hapus');
        exit;
    }
}

$allDestana = getAllDESTANA([], 'ASC');
$total_destana = countTotalDESTANA();
$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $page_title; ?> - BPBD PKRR DIY</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <style>
        :root { --primary: #043e80; --secondary: #e64a19; --sidebar-width: 260px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f4f6f9; }
        
        .sidebar { position: fixed; left: 0; top: 0; width: var(--sidebar-width); height: 100vh; background: var(--primary); color: white; z-index: 1000; overflow-y: auto; }
        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h4 { font-weight: 700; margin: 0; font-size: 1.1rem; }
        .sidebar-header small { opacity: 0.7; font-size: 0.8rem; }
        .sidebar-menu { padding: 15px 0; }
        .menu-label { padding: 10px 20px 5px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.5; }
        .sidebar-menu a { display: flex; align-items: center; padding: 12px 20px; color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.3s; border-left: 3px solid transparent; }
        .sidebar-menu a:hover { background: rgba(255,255,255,0.1); color: white; }
        .sidebar-menu a.active { background: rgba(255,255,255,0.15); color: white; border-left-color: var(--secondary); }
        .sidebar-menu a i { width: 20px; min-width: 20px; margin-right: 10px; text-align: center; font-size: 0.95rem; }
        .sidebar-menu a span { flex: 1; }
        
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .topbar { background: white; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .topbar h1 { font-size: 1.3rem; font-weight: 600; color: var(--primary); margin: 0; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-info .badge { background: var(--secondary); }
        .content-wrapper { padding: 25px; }
        
        .card { background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: none; }
        .card-header { background: var(--primary); color: white; padding: 15px 20px; font-weight: 600; border-radius: 12px 12px 0 0 !important; border: none; display: flex; justify-content: space-between; align-items: center; }
        .card-body { padding: 20px; }
        
        .btn-add { background: var(--secondary); color: white; border: none; padding: 8px 18px; border-radius: 8px; font-weight: 500; cursor: pointer; }
        .btn-add:hover { background: #c43e15; color: white; }
        
        .badge-tingkat { padding: 5px 10px; border-radius: 15px; font-size: 0.75rem; font-weight: 500; }
        .badge-pratama { background: #fce7f3; color: #9d174d; }
        .badge-madya { background: #fef3c7; color: #92400e; }
        .badge-utama { background: #d1fae5; color: #065f46; }
        
        .btn-group .btn { padding: 5px 10px; }
        .modal-header.bg-primary { background: var(--primary) !important; }
        .modal-header.bg-danger { background: var(--secondary) !important; }
        .form-control, .form-select { border-radius: 8px; padding: 10px 15px; }
        .table thead { background: #f8f9fa; }
        .table th { font-weight: 600; color: var(--primary); font-size: 0.85rem; }
        .table td { font-size: 0.85rem; }
        
        /* Mobile Toggle */
        .mobile-toggle { display: none; background: var(--primary); color: white; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-size: 1.2rem; }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999; }
        .sidebar-overlay.show { display: block; }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); width: 260px; transition: transform 0.3s; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .mobile-toggle { display: block; }
            .topbar { padding: 12px 15px; flex-wrap: wrap; gap: 10px; }
            .topbar h1 { font-size: 1.1rem; }
        }
        
        @media (max-width: 768px) {
            .content-wrapper { padding: 15px; }
            .card-header { flex-direction: column; gap: 10px; align-items: flex-start !important; }
            .table-responsive { font-size: 0.85rem; }
            .btn-group { display: flex; flex-direction: column; gap: 5px; }
            .btn-group .btn { width: 100%; }
        }
        
        @media (max-width: 576px) {
            .topbar { flex-direction: column; align-items: flex-start; }
            .user-info { width: 100%; justify-content: space-between; margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee; }
        }
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
            <a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a href="users.php"><i class="fas fa-users-cog"></i><span>Kelola Pengguna</span></a>
            
            <div class="menu-label">Data Master</div>
            <a href="spab.php"><i class="fas fa-school"></i><span>Kelola SPAB</span></a>
            <a href="destana.php" class="active"><i class="fas fa-house-user"></i><span>Kelola DESTANA</span></a>
            
            <a href="#" onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
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
            <div style="display: flex; align-items: center; gap: 15px;">
                <button class="mobile-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <h1><i class="fas fa-house-user me-2"></i>Kelola Data DESTANA</h1>
            </div>
            <div class="user-info">
                <span><?php echo htmlspecialchars($user_email); ?></span>
                <span class="badge">Admin</span>
            </div>
        </div>
        
        <div class="content-wrapper">
            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php 
                switch ($_GET['success']) {
                    case 'tambah': echo 'Data DESTANA berhasil ditambahkan!'; break;
                    case 'update': echo 'Data DESTANA berhasil diperbarui!'; break;
                    case 'hapus': echo 'Data DESTANA berhasil dihapus!'; break;
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <span><i class="fas fa-table me-2"></i>Data DESTANA (<?php echo number_format($total_destana); ?>)</span>
                    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus me-1"></i> Tambah Data
                    </button>
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Desa</th>
                                <th>Kecamatan</th>
                                <th>Kabupaten</th>
                                <th>Tahun</th>
                                <th>Indeks</th>
                                <th>Tingkat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            while ($data = mysqli_fetch_assoc($allDestana)): 
                            $tingkatClass = 'pratama';
                            if (strpos($data['tingkat'], 'Utama') !== false) $tingkatClass = 'utama';
                            elseif (strpos($data['tingkat'], 'Madya') !== false) $tingkatClass = 'madya';
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($data['desa']); ?></td>
                                <td><?php echo htmlspecialchars($data['kecamatan']); ?></td>
                                <td><?php echo htmlspecialchars($data['kabupaten']); ?></td>
                                <td><?php echo $data['tahun_pembentukan']; ?></td>
                                <td><?php echo number_format($data['indeks'], 2); ?></td>
                                <td><span class="badge-tingkat badge-<?php echo $tingkatClass; ?>"><?php echo $data['tingkat']; ?></span></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $data['id_destana']; ?>"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $data['id_destana']; ?>"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo $data['id_destana']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form method="post">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit DESTANA</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id_destana" value="<?php echo $data['id_destana']; ?>">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Desa</label>
                                                        <input name="desa" class="form-control" value="<?php echo htmlspecialchars($data['desa']); ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Kecamatan</label>
                                                        <input name="kecamatan" class="form-control" value="<?php echo htmlspecialchars($data['kecamatan']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Kabupaten</label>
                                                        <select name="kabupaten" class="form-select" required>
                                                            <?php foreach (['Sleman','Bantul','Kulon Progo','Gunungkidul','Kota Yogyakarta'] as $kab): ?>
                                                            <option value="<?php echo $kab; ?>" <?php echo ($data['kabupaten'] == $kab) ? 'selected' : ''; ?>><?php echo $kab; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Tahun Pembentukan</label>
                                                        <input name="tahun_pembentukan" type="number" class="form-control" value="<?php echo $data['tahun_pembentukan']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Sumber Pendanaan</label>
                                                        <input name="sumber_pendanaan" class="form-control" value="<?php echo htmlspecialchars($data['sumber_pendanaan']); ?>" required>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Indeks</label>
                                                        <input name="indeks" type="number" step="0.01" class="form-control" value="<?php echo $data['indeks']; ?>" required>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Tingkat</label>
                                                        <select name="tingkat" class="form-select" required>
                                                            <option value="Tangguh Pratama" <?php echo ($data['tingkat'] == 'Tangguh Pratama') ? 'selected' : ''; ?>>Tangguh Pratama</option>
                                                            <option value="Tangguh Madya" <?php echo ($data['tingkat'] == 'Tangguh Madya') ? 'selected' : ''; ?>>Tangguh Madya</option>
                                                            <option value="Tangguh Utama" <?php echo ($data['tingkat'] == 'Tangguh Utama') ? 'selected' : ''; ?>>Tangguh Utama</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="updatedestana" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal<?php echo $data['id_destana']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="post">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Hapus DESTANA</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id_destana" value="<?php echo $data['id_destana']; ?>">
                                                <p>Hapus desa <strong><?php echo htmlspecialchars($data['desa']); ?></strong>?</p>
                                                <p class="text-danger small mb-0"><i class="fas fa-exclamation-triangle me-1"></i>Tidak dapat dibatalkan!</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="hapusdestana" class="btn btn-danger">Hapus</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah DESTANA</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Desa</label>
                                <input name="desa" class="form-control" placeholder="Nama desa" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kecamatan</label>
                                <input name="kecamatan" class="form-control" placeholder="Nama kecamatan" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kabupaten</label>
                                <select name="kabupaten" class="form-select" required>
                                    <option value="">Pilih Kabupaten</option>
                                    <option value="Sleman">Sleman</option>
                                    <option value="Bantul">Bantul</option>
                                    <option value="Kulon Progo">Kulon Progo</option>
                                    <option value="Gunungkidul">Gunungkidul</option>
                                    <option value="Kota Yogyakarta">Kota Yogyakarta</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun Pembentukan</label>
                                <input name="tahun_pembentukan" type="number" class="form-control" value="<?php echo date('Y'); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Sumber Pendanaan</label>
                                <input name="sumber_pendanaan" class="form-control" placeholder="Contoh: APBD" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Indeks</label>
                                <input name="indeks" type="number" step="0.01" class="form-control" placeholder="Contoh: 75.5" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tingkat</label>
                                <select name="tingkat" class="form-select" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="Tangguh Pratama">Tangguh Pratama</option>
                                    <option value="Tangguh Madya">Tangguh Madya</option>
                                    <option value="Tangguh Utama">Tangguh Utama</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="addnewdestana" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            const dt = document.getElementById('datatablesSimple');
            if (dt) new simpleDatatables.DataTable(dt, { perPage: 25, labels: { placeholder: "Cari...", perPage: "{select} per halaman", noRows: "Tidak ada data", info: "{start}-{end} dari {rows}" } });
        });
    </script>
    
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    <script>
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('show');
        document.querySelector('.sidebar-overlay').classList.toggle('show');
    }
    </script>
</body>
</html>
