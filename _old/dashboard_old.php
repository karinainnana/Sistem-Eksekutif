<?php
session_start();
include 'function.php'; // pakai file koneksi milik kamu

// ambil total spab
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM spabb"))['jml'];
$sd = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM spabb WHERE tingkatan='SD'"))['jml'];
$smp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM spabb WHERE tingkatan='SMP'"))['jml'];
$sma = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM spabb WHERE tingkatan='SMA'"))['jml'];
$slb = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM spabb WHERE tingkatan='SLB'"))['jml'];

// area chart: tahun & sumber pendanaan
$areaQuery = mysqli_query($conn, "SELECT tahun, sumber_pendaan, COUNT(*) AS total FROM spabb GROUP BY tahun, sumber_pendaan ORDER BY tahun ASC");
$areaData = [];
while($row = mysqli_fetch_assoc($areaQuery)){
    $areaData[] = $row;
}

// bar chart: kabupaten & tingkatan
$barQuery = mysqli_query($conn, "SELECT kabupaten, tingkatan, COUNT(*) AS total FROM spabb GROUP BY kabupaten, tingkatan ORDER BY kabupaten ASC");
$barData = [];
while($row = mysqli_fetch_assoc($barQuery)){
    $barData[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard SPAB</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="p-4">

<!-- CARD -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card bg-primary text-white p-3">Total SPAB: <?= $total ?></div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white p-3">SPAB SD: <?= $sd ?></div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-dark p-3">SPAB SMP: <?= $smp ?></div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white p-3">SPAB SMA: <?= $sma ?></div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white p-3">SPAB SLB: <?= $slb ?></div>
    </div>
</div>

<!-- CHART AREA -->
<div class="row">
    <div class="col-md-6">
        <h5>Area Chart: Sumber Pendanaan per Tahun</h5>
        <canvas id="areaChart"></canvas>
    </div>

    <div class="col-md-6">
        <h5>Bar Chart: Tingkatan per Kabupaten</h5>
        <canvas id="barChart"></canvas>
    </div>
</div>

<script>
// DATA AREA CHART
const areaLabels = [
    <?php foreach($areaData as $d){ echo "'".$d['tahun']." - ".$d['sumber_pendaan']."',"; } ?>
];
const areaValues = [
    <?php foreach($areaData as $d){ echo $d['total'].","; } ?>
];

// DATA BAR CHART
const barLabels = [
    <?php foreach($barData as $d){ echo "'".$d['kabupaten']." (".$d['tingkatan'].")',"; } ?>
];
const barValues = [
    <?php foreach($barData as $d){ echo $d['total'].","; } ?>
];

// AREA CHART
new Chart(document.getElementById("areaChart"), {
    type: "line",
    data: {
        labels: areaLabels,
        datasets: [{
            label: "Total",
            data: areaValues,
            fill: true,
            borderWidth: 2
        }]
    }
});

// BAR CHART
new Chart(document.getElementById("barChart"), {
    type: "bar",
    data: {
        labels: barLabels,
        datasets: [{
            label: "Jumlah",
            data: barValues,
            borderWidth: 1
        }]
    }
});
</script>

</body>
</html>
