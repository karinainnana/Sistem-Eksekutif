<?php
/**
 * Admin - Kelola Pengguna dengan Sidebar
 * Tema: Biru #043e80, Orange #e64a19
 */

$page_title = 'Kelola Pengguna';
$active_menu = 'users';
require_once dirname(__DIR__) . '/config/config.php';

// Check admin role
if (!isset($_SESSION['log']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['adduser'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);
        
        $check = mysqli_query($conn, "SELECT * FROM pengguna WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            header('Location: users.php?error=email_exists');
            exit;
        }
        
        mysqli_query($conn, "INSERT INTO pengguna (email, password, role) VALUES ('$email', '$password', '$role')");
        header('Location: users.php?success=tambah');
        exit;
    }
    
    if (isset($_POST['updateuser'])) {
        $id = (int)$_POST['id_pengguna'];
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);
        
        $query = "UPDATE pengguna SET email='$email', role='$role'";
        if (!empty($_POST['password'])) {
            $password = mysqli_real_escape_string($conn, $_POST['password']);
            $query .= ", password='$password'";
        }
        $query .= " WHERE id_pengguna=$id";
        mysqli_query($conn, $query);
        header('Location: users.php?success=update');
        exit;
    }
    
    if (isset($_POST['deleteuser'])) {
        $id = (int)$_POST['id_pengguna'];
        mysqli_query($conn, "DELETE FROM pengguna WHERE id_pengguna=$id");
        header('Location: users.php?success=hapus');
        exit;
    }
}

$allUsers = mysqli_query($conn, "SELECT * FROM pengguna ORDER BY email ASC");
$total_users = mysqli_num_rows($allUsers);
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
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
        .sidebar-menu a i { width: 20px; margin-right: 12px; text-align: center; }
        
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .topbar { background: white; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .topbar h1 { font-size: 1.3rem; font-weight: 600; color: var(--primary); margin: 0; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-info .badge { background: var(--secondary); }
        .content-wrapper { padding: 25px; }
        
        .card { background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: none; }
        .card-header { background: var(--primary); color: white; padding: 15px 20px; font-weight: 600; border-radius: 12px 12px 0 0 !important; border: none; display: flex; justify-content: space-between; align-items: center; }
        .card-body { padding: 20px; }
        
        .btn-add { background: var(--secondary); color: white; border: none; padding: 8px 18px; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s; }
        .btn-add:hover { background: #c43e15; color: white; }
        
        .badge-role { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; }
        .badge-admin { background: #fee2e2; color: #dc2626; }
        .badge-eksekutif { background: #dbeafe; color: #2563eb; }
        
        .btn-group .btn { padding: 5px 10px; }
        .modal-header.bg-primary { background: var(--primary) !important; }
        .modal-header.bg-danger { background: var(--secondary) !important; }
        .form-control, .form-select { border-radius: 8px; padding: 10px 15px; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(4, 62, 128, 0.2); }
        .table thead { background: #f8f9fa; }
        .table th { font-weight: 600; color: var(--primary); }
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
            <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="users.php" class="active"><i class="fas fa-users-cog"></i> Kelola Pengguna</a>
            
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
            <h1><i class="fas fa-users-cog me-2"></i>Kelola Pengguna</h1>
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
                    case 'tambah': echo 'Pengguna berhasil ditambahkan!'; break;
                    case 'update': echo 'Pengguna berhasil diperbarui!'; break;
                    case 'hapus': echo 'Pengguna berhasil dihapus!'; break;
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>Email sudah terdaftar!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <span><i class="fas fa-users me-2"></i>Daftar Pengguna (<?php echo $total_users; ?>)</span>
                    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-user-plus me-1"></i> Tambah
                    </button>
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($user = mysqli_fetch_assoc($allUsers)): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><span class="badge-role badge-<?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $user['id_pengguna']; ?>"><i class="fas fa-edit"></i></button>
                                        <?php if ($user['id_pengguna'] != $user_id): ?>
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $user['id_pengguna']; ?>"><i class="fas fa-trash"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo $user['id_pengguna']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="post">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit Pengguna</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id_pengguna" value="<?php echo $user['id_pengguna']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input name="email" type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Password Baru <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                                                    <input name="password" type="password" class="form-control">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Role</label>
                                                    <select name="role" class="form-select" required>
                                                        <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                                        <option value="eksekutif" <?php echo ($user['role'] == 'eksekutif') ? 'selected' : ''; ?>>Eksekutif</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="updateuser" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal<?php echo $user['id_pengguna']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="post">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Hapus Pengguna</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id_pengguna" value="<?php echo $user['id_pengguna']; ?>">
                                                <p>Hapus <strong><?php echo htmlspecialchars($user['email']); ?></strong>?</p>
                                                <p class="text-danger small mb-0"><i class="fas fa-exclamation-triangle me-1"></i>Tidak dapat dibatalkan!</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="deleteuser" class="btn btn-danger">Hapus</button>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Tambah Pengguna</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input name="email" type="email" class="form-control" placeholder="email@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input name="password" type="password" class="form-control" placeholder="Minimal 6 karakter" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="">Pilih Role</option>
                                <option value="admin">Admin</option>
                                <option value="eksekutif">Eksekutif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="adduser" class="btn btn-primary">Simpan</button>
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
            if (dt) new simpleDatatables.DataTable(dt, { perPage: 10, labels: { placeholder: "Cari...", perPage: "{select} per halaman", noRows: "Tidak ada data", info: "{start}-{end} dari {rows}" } });
        });
    </script>
</body>
</html>
