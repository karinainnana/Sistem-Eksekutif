<?php
/**
 * Tabel DESTANA Page
 * Halaman tabel data DESTANA lengkap
 */

$page_title = 'Tabel DESTANA';
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/destana_functions.php';

// Check login
if (!isset($_SESSION['log']) || $_SESSION['log'] !== true) {
    header('Location: ../auth/login.php');
    exit;
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addnewdestana'])) {
        addDESTANA($_POST);
        header('Location: tabel-destana.php?success=tambah');
        exit;
    }
    
    if (isset($_POST['updatedestana'])) {
        updateDESTANA($_POST['id_destana'], $_POST);
        header('Location: tabel-destana.php?success=update');
        exit;
    }
    
    if (isset($_POST['hapusdestana'])) {
        deleteDESTANA($_POST['id_destana']);
        header('Location: tabel-destana.php?success=hapus');
        exit;
    }
}

// Get all DESTANA data
$allDestana = getAllDESTANA([], 'ASC');
$total_destana = countTotalDESTANA();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=0" />
    <meta name="description" content="Tabel DESTANA - Sistem Informasi Eksekutif BPBD DIY" />
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
            background: #2d6a4f;
            min-height: 100vh;
            padding: 15px;
        }
        
        .nav-header {
            background: #1b4332;
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
            background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%);
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
            background: #40916c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-add:hover {
            background: #357a5a;
            transform: translateY(-2px);
            color: white;
        }
        
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .badge-tingkat {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-pratama { background: #f8d7da; color: #721c24; }
        .badge-madya { background: #fff3cd; color: #856404; }
        .badge-utama { background: #d4edda; color: #155724; }
        
        .btn-group .btn { padding: 5px 10px; }
        
        .modal-header.bg-primary {
            background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%) !important;
        }
        
        .modal-header.bg-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #2d6a4f;
            box-shadow: 0 0 0 3px rgba(45, 106, 79, 0.2);
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
            <a href="tabel-destana.php" class="active">Tabel DESTANA</a>
            <a href="../auth/logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
        </div>
    </div>

    <!-- Content Card -->
    <div class="content-card">
        <div class="content-header">
            <h1><i class="fas fa-table me-2"></i>Tabel Data DESTANA</h1>
            <div>
                <span class="me-3">Total: <?php echo number_format($total_destana); ?> data</span>
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
                    case 'tambah': echo 'Data DESTANA berhasil ditambahkan!'; break;
                    case 'update': echo 'Data DESTANA berhasil diperbarui!'; break;
                    case 'hapus': echo 'Data DESTANA berhasil dihapus!'; break;
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <table id="datatablesSimple" class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Desa</th>
                        <th>Kecamatan</th>
                        <th>Kabupaten</th>
                        <th>Tahun</th>
                        <th>Pendanaan</th>
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
                        <td><?php echo htmlspecialchars($data['tahun_pembentukan']); ?></td>
                        <td><?php echo htmlspecialchars($data['sumber_pendanaan']); ?></td>
                        <td><?php echo number_format($data['indeks'], 2); ?></td>
                        <td>
                            <span class="badge-tingkat badge-<?php echo $tingkatClass; ?>">
                                <?php echo htmlspecialchars($data['tingkat']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" 
                                        data-bs-target="#editModal<?php echo $data['id_destana']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal<?php echo $data['id_destana']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?php echo $data['id_destana']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Data DESTANA</h5>
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
                                                    <option value="Kota Yogyakarta" <?php echo ($data['kabupaten'] == 'Kota Yogyakarta') ? 'selected' : ''; ?>>Kota Yogyakarta</option>
                                                    <option value="Sleman" <?php echo ($data['kabupaten'] == 'Sleman') ? 'selected' : ''; ?>>Sleman</option>
                                                    <option value="Bantul" <?php echo ($data['kabupaten'] == 'Bantul') ? 'selected' : ''; ?>>Bantul</option>
                                                    <option value="Kulon Progo" <?php echo ($data['kabupaten'] == 'Kulon Progo') ? 'selected' : ''; ?>>Kulon Progo</option>
                                                    <option value="Gunungkidul" <?php echo ($data['kabupaten'] == 'Gunungkidul') ? 'selected' : ''; ?>>Gunungkidul</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Tahun Pembentukan</label>
                                                <input name="tahun_pembentukan" type="number" class="form-control" value="<?php echo htmlspecialchars($data['tahun_pembentukan']); ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Sumber Pendanaan</label>
                                                <input name="sumber_pendanaan" class="form-control" value="<?php echo htmlspecialchars($data['sumber_pendanaan']); ?>" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Indeks</label>
                                                <input name="indeks" type="number" step="0.01" class="form-control" value="<?php echo htmlspecialchars($data['indeks']); ?>" required>
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
                                        <button type="submit" name="updatedestana" class="btn btn-success">Simpan</button>
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
                                        <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Hapus Data</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id_destana" value="<?php echo $data['id_destana']; ?>">
                                        <p>Apakah Anda yakin ingin menghapus data desa <strong><?php echo htmlspecialchars($data['desa']); ?></strong>?</p>
                                        <p class="text-danger small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Data DESTANA</h5>
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
                                    <option value="Kota Yogyakarta">Kota Yogyakarta</option>
                                    <option value="Sleman">Sleman</option>
                                    <option value="Bantul">Bantul</option>
                                    <option value="Kulon Progo">Kulon Progo</option>
                                    <option value="Gunungkidul">Gunungkidul</option>
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
                        <button type="submit" name="addnewdestana" class="btn btn-success">Simpan</button>
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
