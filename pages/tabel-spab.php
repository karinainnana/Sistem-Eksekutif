<?php
/**
 * Tabel SPAB Page
 * Halaman tabel data SPAB lengkap
 */

$page_title = 'Tabel SPAB';
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/spab_functions.php';

// Check login
if (!isset($_SESSION['log']) || $_SESSION['log'] !== true) {
    header('Location: ../auth/login.php');
    exit;
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addnewspab'])) {
        addSPAB($_POST);
        header('Location: tabel-spab.php?success=tambah');
        exit;
    }
    
    if (isset($_POST['updatespab'])) {
        updateSPAB($_POST['id_spab'], $_POST);
        header('Location: tabel-spab.php?success=update');
        exit;
    }
    
    if (isset($_POST['hapusspab'])) {
        deleteSPAB($_POST['id_spab']);
        header('Location: tabel-spab.php?success=hapus');
        exit;
    }
}

// Get all SPAB data
$allSpab = getAllSPAB([], 'ASC');
$total_spab = countTotalSPAB();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=0" />
    <meta name="description" content="Tabel SPAB - Sistem Informasi Eksekutif BPBD DIY" />
    <title><?php echo $page_title; ?> - BPBD PKRR DIY</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #e85d04;
            min-height: 100vh;
            padding: 15px;
        }
        
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
        
        .content-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        .content-header {
            background: linear-gradient(135deg, #1a3a5c 0%, #2d5a87 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .content-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        
        .content-body { padding: 20px; }
        
        .btn-add {
            background: #e85d04;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-add:hover {
            background: #d45a04;
            transform: translateY(-2px);
            color: white;
        }
        
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .badge-tingkatan {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-tk { background: #d4edda; color: #155724; }
        .badge-sd { background: #cce5ff; color: #004085; }
        .badge-smp { background: #fff3cd; color: #856404; }
        .badge-sma { background: #f8d7da; color: #721c24; }
        .badge-slb { background: #e2e3e5; color: #383d41; }
        
        .btn-group .btn { padding: 5px 10px; }
        
        .modal-header.bg-primary {
            background: linear-gradient(135deg, #1a3a5c 0%, #2d5a87 100%) !important;
        }
        
        .modal-header.bg-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #e85d04;
            box-shadow: 0 0 0 3px rgba(232, 93, 4, 0.2);
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
            <a href="spab.php">SPAB</a>
            <a href="destana.php">DESTANA</a>
            <a href="tabel-spab.php" class="active">Tabel SPAB</a>
            <a href="../auth/logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
        </div>
    </div>

    <!-- Content Card -->
    <div class="content-card">
        <div class="content-header">
            <h1><i class="fas fa-table me-2"></i>Tabel Data SPAB</h1>
            <div>
                <span class="me-3">Total: <?php echo number_format($total_spab); ?> data</span>
                <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus me-2"></i>Tambah Data
                </button>
            </div>
        </div>
        
        <div class="content-body">
            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php 
                switch ($_GET['success']) {
                    case 'tambah': echo 'Data SPAB berhasil ditambahkan!'; break;
                    case 'update': echo 'Data SPAB berhasil diperbarui!'; break;
                    case 'hapus': echo 'Data SPAB berhasil dihapus!'; break;
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <table id="datatablesSimple" class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Sekolah</th>
                        <th>Kabupaten</th>
                        <th>Tahun</th>
                        <th>Sumber Pendanaan</th>
                        <th>Tingkatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($data = mysqli_fetch_assoc($allSpab)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($data['nama_sekolah']); ?></td>
                        <td><?php echo htmlspecialchars($data['kabupaten']); ?></td>
                        <td><?php echo htmlspecialchars($data['tahun']); ?></td>
                        <td><?php echo htmlspecialchars($data['sumber_pendanaan']); ?></td>
                        <td>
                            <span class="badge-tingkatan badge-<?php echo strtolower($data['tingkatan']); ?>">
                                <?php echo htmlspecialchars($data['tingkatan']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" 
                                        data-bs-target="#editModal<?php echo $data['id_spab']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal<?php echo $data['id_spab']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?php echo $data['id_spab']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Data SPAB</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id_spab" value="<?php echo $data['id_spab']; ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Nama Sekolah</label>
                                            <input name="nama_sekolah" class="form-control" value="<?php echo htmlspecialchars($data['nama_sekolah']); ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Kabupaten</label>
                                            <select name="kabupaten" class="form-select" required>
                                                <option value="Kota Yogyakarta" <?php echo ($data['kabupaten'] == 'Kota Yogyakarta') ? 'selected' : ''; ?>>Kota Yogyakarta</option>
                                                <option value="Sleman" <?php echo ($data['kabupaten'] == 'Sleman') ? 'selected' : ''; ?>>Sleman</option>
                                                <option value="Bantul" <?php echo ($data['kabupaten'] == 'Bantul') ? 'selected' : ''; ?>>Bantul</option>
                                                <option value="Kulon Progo" <?php echo ($data['kabupaten'] == 'Kulon Progo') ? 'selected' : ''; ?>>Kulon Progo</option>
                                                <option value="Gunungkidul" <?php echo ($data['kabupaten'] == 'Gunungkidul') ? 'selected' : ''; ?>>Gunungkidul</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Tahun</label>
                                            <input name="tahun" type="number" class="form-control" value="<?php echo htmlspecialchars($data['tahun']); ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Sumber Pendanaan</label>
                                            <input name="sumber_pendanaan" class="form-control" value="<?php echo htmlspecialchars($data['sumber_pendanaan']); ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Tingkatan</label>
                                            <select name="tingkatan" class="form-select" required>
                                                <option value="TK" <?php echo ($data['tingkatan'] == 'TK') ? 'selected' : ''; ?>>TK</option>
                                                <option value="SD" <?php echo ($data['tingkatan'] == 'SD') ? 'selected' : ''; ?>>SD</option>
                                                <option value="SMP" <?php echo ($data['tingkatan'] == 'SMP') ? 'selected' : ''; ?>>SMP</option>
                                                <option value="SMA" <?php echo ($data['tingkatan'] == 'SMA') ? 'selected' : ''; ?>>SMA</option>
                                                <option value="SLB" <?php echo ($data['tingkatan'] == 'SLB') ? 'selected' : ''; ?>>SLB</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="updatespab" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal<?php echo $data['id_spab']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Hapus Data</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id_spab" value="<?php echo $data['id_spab']; ?>">
                                        <p>Apakah Anda yakin ingin menghapus data <strong><?php echo htmlspecialchars($data['nama_sekolah']); ?></strong>?</p>
                                        <p class="text-danger small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="hapusspab" class="btn btn-danger">Hapus</button>
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
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Data SPAB</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Sekolah</label>
                            <input name="nama_sekolah" class="form-control" placeholder="Masukkan nama sekolah" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kabupaten</label>
                            <select name="kabupaten" class="form-select" required>
                                <option value="">Pilih Kabupaten</option>
                                <option value="Kota Yogyakarta">Kota Yogyakarta</option>
                                <option value="Sleman">Sleman</option>
                                <option value="Bantul">Bantul</option>
                                <option value="Kulon Progo">Kulon Progo</option>
                                <option value="Gunungkidul">Gunungkidul</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tahun</label>
                            <input name="tahun" type="number" class="form-control" placeholder="Contoh: 2024" value="<?php echo date('Y'); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Sumber Pendanaan</label>
                            <input name="sumber_pendanaan" class="form-control" placeholder="Contoh: APBD PROVINSI" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tingkatan</label>
                            <select name="tingkatan" class="form-select" required>
                                <option value="">Pilih Tingkatan</option>
                                <option value="TK">TK</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA</option>
                                <option value="SLB">SLB</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="addnewspab" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            const datatablesSimple = document.getElementById('datatablesSimple');
            if (datatablesSimple) {
                new simpleDatatables.DataTable(datatablesSimple, {
                    perPage: 25,
                    perPageSelect: [10, 25, 50, 100],
                    labels: {
                        placeholder: "Cari...",
                        perPage: "{select} data per halaman",
                        noRows: "Tidak ada data",
                        info: "Menampilkan {start} sampai {end} dari {rows} data",
                    }
                });
            }
        });
    </script>
</body>
</html>
