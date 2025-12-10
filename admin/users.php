<?php
/**
 * User Management Page
 * Halaman untuk mengelola pengguna (khusus admin)
 * Schema: id_pengguna, email, password, role
 */

$page_title = 'Kelola Pengguna';
require_once dirname(__DIR__) . '/config/config.php';

// Check admin role
if (!isset($_SESSION['log']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new user
    if (isset($_POST['adduser'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);
        
        // Check if email exists
        $check = mysqli_query($conn, "SELECT * FROM pengguna WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            header('Location: users.php?error=email_exists');
            exit;
        }
        
        mysqli_query($conn, "INSERT INTO pengguna (email, password, role) VALUES ('$email', '$password', '$role')");
        header('Location: users.php?success=tambah');
        exit;
    }
    
    // Update user
    if (isset($_POST['updateuser'])) {
        $id = (int)$_POST['id_pengguna'];
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);
        
        $query = "UPDATE pengguna SET email='$email', role='$role'";
        
        // Update password if provided
        if (!empty($_POST['password'])) {
            $password = mysqli_real_escape_string($conn, $_POST['password']);
            $query .= ", password='$password'";
        }
        
        $query .= " WHERE id_pengguna=$id";
        mysqli_query($conn, $query);
        header('Location: users.php?success=update');
        exit;
    }
    
    // Delete user
    if (isset($_POST['deleteuser'])) {
        $id = (int)$_POST['id_pengguna'];
        mysqli_query($conn, "DELETE FROM pengguna WHERE id_pengguna=$id");
        header('Location: users.php?success=hapus');
        exit;
    }
}

// Get all users
$allUsers = mysqli_query($conn, "SELECT * FROM pengguna ORDER BY id_pengguna ASC");
$total_users = mysqli_num_rows($allUsers);
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=0" />
    <title><?php echo $page_title; ?> - BPBD PKRR DIY</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #1a1c2c 0%, #2d3748 100%); min-height: 100vh; padding: 15px; }
        .nav-header { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 12px; padding: 15px 20px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .nav-header .brand { font-size: 1.2rem; font-weight: 700; }
        .nav-header .nav-links a { color: white; text-decoration: none; margin-left: 20px; font-size: 0.9rem; opacity: 0.8; transition: opacity 0.3s; }
        .nav-header .nav-links a:hover, .nav-header .nav-links a.active { opacity: 1; }
        .content-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.2); }
        .content-header { background: linear-gradient(135deg, #1a1c2c 0%, #2d3748 100%); color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        .content-header h1 { font-size: 1.5rem; font-weight: 600; margin: 0; }
        .content-body { padding: 20px; }
        .btn-add { background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s; }
        .btn-add:hover { background: #2563eb; color: white; }
        .badge-role { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; }
        .badge-admin { background: #fee2e2; color: #dc2626; }
        .badge-eksekutif { background: #dbeafe; color: #2563eb; }
        .btn-group .btn { padding: 5px 10px; }
        .modal-header.bg-primary { background: linear-gradient(135deg, #1a1c2c 0%, #2d3748 100%) !important; }
        .modal-header.bg-danger { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important; }
        .form-control, .form-select { border-radius: 8px; padding: 10px 15px; }
        .table thead { background: #f8f9fa; }
        .table th { font-weight: 600; color: #1a1c2c; }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <div class="nav-header">
        <div class="brand"><i class="fas fa-user-shield me-2"></i>Admin Panel</div>
        <div class="nav-links">
            <a href="../index.php">Dashboard</a>
            <a href="../pages/spab.php">SPAB</a>
            <a href="../pages/destana.php">DESTANA</a>
            <a href="index.php">Admin</a>
            <a href="users.php" class="active">Pengguna</a>
            <a href="../auth/logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
        </div>
    </div>

    <!-- Content Card -->
    <div class="content-card">
        <div class="content-header">
            <h1><i class="fas fa-users-cog me-2"></i>Kelola Pengguna</h1>
            <div>
                <span class="me-3">Total: <?php echo $total_users; ?> pengguna</span>
                <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-user-plus me-2"></i>Tambah Pengguna
                </button>
            </div>
        </div>
        
        <div class="content-body">
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
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php if ($_GET['error'] == 'email_exists') echo 'Email sudah terdaftar!'; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
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
                    <?php 
                    $no = 1;
                    while ($user = mysqli_fetch_assoc($allUsers)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="badge-role badge-<?php echo $user['role']; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $user['id_pengguna']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($user['id_pengguna'] != $user_id): ?>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $user['id_pengguna']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
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
                                            <input name="password" type="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
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
                                        <h5 class="modal-title"><i class="fas fa-user-times me-2"></i>Hapus Pengguna</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id_pengguna" value="<?php echo $user['id_pengguna']; ?>">
                                        <p>Apakah Anda yakin ingin menghapus <strong><?php echo htmlspecialchars($user['email']); ?></strong>?</p>
                                        <p class="text-danger small mb-0"><i class="fas fa-exclamation-triangle me-1"></i>Tindakan ini tidak dapat dibatalkan.</p>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Tambah Pengguna Baru</h5>
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
            if (dt) {
                new simpleDatatables.DataTable(dt, {
                    perPage: 10,
                    labels: { placeholder: "Cari...", perPage: "{select} per halaman", noRows: "Tidak ada data", info: "Menampilkan {start}-{end} dari {rows}" }
                });
            }
        });
    </script>
</body>
</html>
