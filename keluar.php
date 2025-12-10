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
    <title>DESTANA</title>

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">

<!-- NAVBAR -->
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">PKRRR BPBD DIY</a>
    <button class="btn btn-link btn-sm" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <ul class="navbar-nav ms-auto me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                <i class="fas fa-user fa-fw"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>

<!-- SIDEBAR + CONTENT -->
<div id="layoutSidenav">
    
    <!-- SIDEBAR -->
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark">
            <div class="sb-sidenav-menu">
                <div class="nav">

                    <div class="sb-sidenav-menu-heading">Core</div>

                    <a class="nav-link" href="index.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard BPBD DIY
                    </a>

                    <a class="nav-link" href="masuk.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-school"></i></div>
                        SPAB
                    </a>

                    <a class="nav-link active" href="keluar.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        DESTANA
                    </a>

                    <div class="sb-sidenav-menu-heading">Addons</div>

                    <a class="nav-link" href="charts.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                        Charts
                    </a>

                    <a class="nav-link" href="tables.html">
                        <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                        Tables
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
                <h1 class="mt-4">DESTANA</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Dashboard DESTANA</li>
                </ol>

                <!-- CARDS -->
                <div class="row">
                    <div class="col-xl-2 col-md-4">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                Total DESTANA: 
                                <?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM destanaa")); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                TANGGUH UTAMA:
                                <?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM destanaa WHERE tingkat='Tangguh Utama'")); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                TANGGUH MADYA:
                                <?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM destanaa WHERE tingkat='Tangguh Madya'")); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4">
                        <div class="card bg-danger text-white mb-4">
                            <div class="card-body">
                                TANGGUH PRATAMA:
                                <?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM destanaa WHERE tingkat='Tangguh Pratama'")); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CHARTS -->
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-area me-1"></i>
                                Area Chart: Tahun Pembentukan
                            </div>
                            <div class="card-body">
                                <canvas id="areaChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-bar me-1"></i>
                                Bar Chart: Kabupaten
                            </div>
                            <div class="card-body">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="card mb-4">
                    <div class="card-header">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
                            Tambah Data DESTANA
                        </button>
                    </div>

                    <div class="card-body">
                        <table id="datatablesSimple">
                            <thead>
                                <tr>
                                    <th>ID</th>
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
                                $get = mysqli_query($conn, "SELECT * FROM destanaa");
                                while($d = mysqli_fetch_assoc($get)){
                                ?>
                                <tr>
                                    <td><?= $d['id_destana'] ?></td>
                                    <td><?= $d['desa'] ?></td>
                                    <td><?= $d['kecamatan'] ?></td>
                                    <td><?= $d['kabupaten'] ?></td>
                                    <td><?= $d['tahun_pembentukan'] ?></td>
                                    <td><?= $d['sumber_pendanaan'] ?></td>
                                    <td><?= $d['indeks'] ?></td>
                                    <td><?= $d['tingkat'] ?></td>
                                    <td>

                                        <!-- BTN EDIT -->
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" 
                                            data-bs-target="#edit<?= $d['id_destana'] ?>">
                                            Ubah
                                        </button>

                                        <!-- BTN DELETE -->
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" 
                                            data-bs-target="#hapus<?= $d['id_destana'] ?>">
                                            Hapus
                                        </button>

                                    </td>
                                </tr>

                                <!-- MODAL EDIT -->
                                <div class="modal fade" id="edit<?= $d['id_destana'] ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Ubah Data</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <input type="hidden" name="id_destana" value="<?= $d['id_destana'] ?>">

                                                    <input name="desa" class="form-control" value="<?= $d['desa'] ?>" required><br>
                                                    <input name="kecamatan" class="form-control" value="<?= $d['kecamatan'] ?>" required><br>
                                                    <input name="kabupaten" class="form-control" value="<?= $d['kabupaten'] ?>" required><br>
                                                    <input name="tahun_pembentukan" class="form-control" value="<?= $d['tahun_pembentukan'] ?>" required><br>
                                                    <input name="sumber_pendanaan" class="form-control" value="<?= $d['sumber_pendanaan'] ?>" required><br>
                                                    <input name="indeks" class="form-control" value="<?= $d['indeks'] ?>" required><br>
                                                    <input name="tingkat" class="form-control" value="<?= $d['tingkat'] ?>" required><br>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="submit" name="updatedestana" class="btn btn-primary">Simpan</button>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- MODAL HAPUS -->
                                <div class="modal fade" id="hapus<?= $d['id_destana'] ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Hapus Data</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <p>Yakin ingin menghapus data <b><?= $d['desa'] ?></b>?</p>
                                                    <input type="hidden" name="id_destana" value="<?= $d['id_destana'] ?>">
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="submit" name="hapusdestana" class="btn btn-danger">Hapus</button>
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
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Data DESTANA</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input name="desa" class="form-control" placeholder="Desa" required><br>
                    <input name="kecamatan" class="form-control" placeholder="Kecamatan" required><br>
                    <input name="kabupaten" class="form-control" placeholder="Kabupaten" required><br>
                    <input name="tahun_pembentukan" class="form-control" placeholder="Tahun Pembentukan" required><br>
                    <input name="sumber_pendanaan" class="form-control" placeholder="Sumber Pendanaan" required><br>
                    <input name="indeks" class="form-control" placeholder="Indeks" required><br>
                    <input name="tingkat" class="form-control" placeholder="Tingkat" required><br>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="addnewdestana" class="btn btn-primary">Submit</button>
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

<!-- AREA CHART -->
<?php
$area_labels = [];
$area_values = [];

$q1 = mysqli_query($conn, "
    SELECT tahun_pembentukan, COUNT(*) AS total 
    FROM destanaa 
    GROUP BY tahun_pembentukan 
    ORDER BY tahun_pembentukan ASC
");

while($row = mysqli_fetch_assoc($q1)){
    $area_labels[] = $row['tahun_pembentukan'];
    $area_values[] = $row['total'];
}
?>

<script>
new Chart(document.getElementById("areaChart"), {
    type: "line",
    data: {
        labels: <?= json_encode($area_labels) ?>,
        datasets: [{
            label: "DESTANA per Tahun",
            data: <?= json_encode($area_values) ?>,
            borderColor: "blue",
            backgroundColor: "rgba(0,0,255,0.2)",
            borderWidth: 2,
            fill: true
        }]
    }
});
</script>

<!-- BAR CHART -->
<?php
$bar_labels = [];
$bar_values = [];

$q2 = mysqli_query($conn, "
    SELECT kabupaten, COUNT(*) AS total 
    FROM destanaa 
    GROUP BY kabupaten 
    ORDER BY kabupaten ASC
");

while($row = mysqli_fetch_assoc($q2)){
    $bar_labels[] = $row['kabupaten'];
    $bar_values[] = $row['total'];
}
?>

<script>
new Chart(document.getElementById("barChart"), {
    type: "bar",
    data: {
        labels: <?= json_encode($bar_labels) ?>,
        datasets: [{
            label: "DESTANA per Kabupaten",
            data: <?= json_encode($bar_values) ?>,
            backgroundColor: "rgba(0,123,255,0.5)",
            borderColor: "blue",
            borderWidth: 1
        }]
    }
});
</script>

</body>
</html>
