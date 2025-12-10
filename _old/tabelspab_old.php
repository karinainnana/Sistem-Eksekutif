<?php 
require 'function.php';
require 'cek.php';

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Tabel SPAB</title>

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">

<!-- NAVIGASI ATAS -->
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">PKRR BPBD DIY</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
       <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" data-bs-toggle="dropdown">
                <i class="fas fa-user fa-fw"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
        </li>
    </ul>
    </form>
    
</nav>

<!-- SIDEBAR + CONTENT -->
<div id="layoutSidenav">

    <!-- SIDEBAR -->
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">

                    <div class="sb-sidenav-menu-heading">Dashboard</div>

                    <a class="nav-link" href="index.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard BPBD DIY
                    </a>

                    <a class="nav-link" href="masuk.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-school"></i></div>
                        SPAB
                    </a>

                    <a class="nav-link" href="keluar.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        DESTANA
                    </a>

                    <div class="sb-sidenav-menu-heading">Tabel</div>

                    <a class="nav-link active" href="charts.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                        Tabel SPAB
                    </a>        
                    <a class="nav-link" href="tables.html">
                        <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                        Tabel DESTANA
                    </a>

                </div>
            </div>

            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                ADMIN BPBD
            </div>
        </nav>
    </div>

    <!-- CONTENT -->
    <div id="layoutSidenav_content">
        <main>

            <div class="container-fluid px-4">
                <h1 class="mt-4">Tabel Satuan Pendidikan Aman Bencana</h1>


                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Berikut ini merupakan tabel yang menampilkan SPAB </li>
                </ol>

                    <?php if (isset($_GET['success']) && $_GET['success'] == 'tambah') { ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Data SPAB berhasil ditambahkan!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php } ?>

                    <?php 
                        // NOTIFIKASI UPDATE
                        if (isset($_GET['success']) && $_GET['success'] == 'update') { ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Data SPAB berhasil diubah!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                    <?php } ?>



                <!-- ===== TABLE ===== -->
                <div class="card mb-4">
                    <div class="card-header">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
                            Tambah Data SPAB
                        </button>
                    </div>

                    <div class="card-body">
                        <table id="datatablesSimple" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID SPAB</th>
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
                                // URUT DARI ID TERKECIL KE TERBESAR supaya mulai dari 1,2,3...
                                $get = mysqli_query($conn, "SELECT * FROM spabb ORDER BY id_spab ASC");
                                while($data = mysqli_fetch_assoc($get)){ ?>
                                    <tr>
                                        <td><?= htmlspecialchars($data['id_spab']) ?></td>
                                        <td><?= htmlspecialchars($data['nama_sekolah']) ?></td>
                                        <td><?= htmlspecialchars($data['kabupaten']) ?></td>
                                        <td><?= htmlspecialchars($data['tahun']) ?></td>
                                        <td><?= htmlspecialchars($data['sumber_pendanaan']) ?></td>
                                        <td><?= htmlspecialchars($data['tingkatan']) ?></td>
                                        <td>
                                            <div class="btn-group" role="group" aria-label="Aksi">
                                                <button class="btn btn-warning btn-sm me-1" data-bs-toggle="modal" 
                                                    data-bs-target="#edit<?= $data['id_spab'] ?>">
                                                    Ubah
                                                </button>

                                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" 
                                                    data-bs-target="#hapus<?= $data['id_spab'] ?>">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- MODAL EDIT -->
                                    <div class="modal fade" id="edit<?= $data['id_spab'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="post">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Ubah Data SPAB</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <input type="hidden" name="id_spab" value="<?= $data['id_spab'] ?>">

                                                        <label>Nama Sekolah</label>
                                                        <input name="nama_sekolah" class="form-control" value="<?= htmlspecialchars($data['nama_sekolah']) ?>" required><br>

                                                        <label>Kabupaten</label>
                                                        <input name="kabupaten" class="form-control" value="<?= htmlspecialchars($data['kabupaten']) ?>" required><br>

                                                        <label>Tahun</label>
                                                        <input name="tahun" class="form-control" value="<?= htmlspecialchars($data['tahun']) ?>" required><br>

                                                        <label>Sumber Pendanaan</label>
                                                        <input name="sumber_pendanaan" class="form-control" value="<?= htmlspecialchars($data['sumber_pendanaan']) ?>" required><br>

                                                        <label>Tingkatan</label>
                                                        <input name="tingkatan" class="form-control" value="<?= htmlspecialchars($data['tingkatan']) ?>" required><br>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="submit" name="updatespab" class="btn btn-primary">Simpan</button>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- MODAL HAPUS -->
                                    <div class="modal fade" id="hapus<?= $data['id_spab'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="post">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Hapus Data SPAB</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <p>Yakin ingin menghapus data <b><?= htmlspecialchars($data['nama_sekolah']) ?></b>?</p>
                                                        <input type="hidden" name="id_spab" value="<?= $data['id_spab'] ?>">
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="submit" name="hapusspab" class="btn btn-danger">Hapus</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </main>

        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright © BPBD</div>
                </div>
            </div>
        </footer>

    </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="post">
        <div class="modal-header">
          <h4 class="modal-title">Tambah Data SPAB</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <label>Nama Sekolah</label>
          <input name="nama_sekolah" placeholder="Nama Sekolah" class="form-control" required><br>

          <label>Kabupaten</label>
          <input name="kabupaten" placeholder="Kabupaten" class="form-control" required><br>

          <label>Tahun</label>
          <input name="tahun" placeholder="Tahun" class="form-control" required><br>

          <label>Sumber Pendanaan</label>
          <input name="sumber_pendanaan" placeholder="Sumber Pendanaan" class="form-control" required><br>

          <label>Tingkatan</label>
          <input name="tingkatan" placeholder="Tingkatan (TK/SD/SMP/SMA/SLB)" class="form-control" required><br>
        </div>

        <div class="modal-footer">
          <button type="submit" name="addnewspab" class="btn btn-primary">Submit</button>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
<script src="js/datatables-simple-demo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



</body>
</html>
