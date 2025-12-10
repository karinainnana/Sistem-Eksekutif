<?php
session_start();

// KONEKSI DATABASE
$conn = mysqli_connect("localhost", "root", "", "si_eksekutif");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

/* ===========================
   TAMBAH SPAB
=========================== */
if(isset($_POST['addnewspab'])){

    $nama_sekolah      = $_POST['nama_sekolah'];
    $kabupaten         = $_POST['kabupaten'];
    $tahun             = $_POST['tahun'];
    $sumber_pendanaan  = $_POST['sumber_pendanaan'];
    $tingkatan         = $_POST['tingkatan'];

    $query = "INSERT INTO spabb (nama_sekolah, kabupaten, tahun, sumber_pendanaan, tingkatan)
              VALUES ('$nama_sekolah', '$kabupaten', '$tahun', '$sumber_pendanaan', '$tingkatan')";
    $run = mysqli_query($conn, $query);

    header("Location: tabelspab.php?success=tambah");
    exit;

}

/* ===========================
   UPDATE SPAB
=========================== */
if(isset($_POST['updatespab'])){

    $id                = $_POST['id_spab'];
    $nama_sekolah      = $_POST['nama_sekolah'];
    $kabupaten         = $_POST['kabupaten'];
    $tahun             = $_POST['tahun'];
    $sumber_pendanaan  = $_POST['sumber_pendanaan'];
    $tingkatan         = $_POST['tingkatan'];

    $query = "UPDATE spabb SET 
                nama_sekolah='$nama_sekolah',
                kabupaten='$kabupaten',
                tahun='$tahun',
                sumber_pendanaan='$sumber_pendanaan',
                tingkatan='$tingkatan'
              WHERE id_spab='$id'";

    mysqli_query($conn, $query);

    header("Location: tabelspab.php?success=update");
    exit;
}

/* ===========================
   HAPUS SPAB
=========================== */
if(isset($_POST['hapusspab'])){

    $id = $_POST['id_spab'];

    mysqli_query($conn, "DELETE FROM spabb WHERE id_spab='$id'");

    header("location: masuk.php");
    exit;
}

/* ===========================
   DESTANA (BIARKAN)
=========================== */
if (isset($_POST['addnewdestana'])) {
    $desa = $_POST['desa'];
    $kecamatan = $_POST['kecamatan'];
    $kabupaten = $_POST['kabupaten'];
    $tahun = $_POST['tahun_pembentukan'];
    $sumber = $_POST['sumber_pendanaan'];
    $indeks = $_POST['indeks'];
    $tingkat = $_POST['tingkat'];

    mysqli_query($conn, "INSERT INTO destanaa 
        (desa,kecamatan,kabupaten,tahun_pembentukan,sumber_pendanaan,indeks,tingkat)
        VALUES ('$desa','$kecamatan','$kabupaten','$tahun','$sumber','$indeks','$tingkat')");

    header("location: keluar.php");
}

if (isset($_POST['updatedestana'])) {
    $id = $_POST['id_destana'];
    $desa = $_POST['desa'];
    $kecamatan = $_POST['kecamatan'];
    $kabupaten = $_POST['kabupaten'];
    $tahun = $_POST['tahun_pembentukan'];
    $sumber = $_POST['sumber_pendanaan'];
    $indeks = $_POST['indeks'];
    $tingkat = $_POST['tingkat'];

    mysqli_query($conn, "UPDATE destanaa SET 
        desa='$desa',
        kecamatan='$kecamatan',
        kabupaten='$kabupaten',
        tahun_pembentukan='$tahun',
        sumber_pendanaan='$sumber',
        indeks='$indeks',
        tingkat='$tingkat'
    WHERE id_destana='$id'");

    header("location: keluar.php");
}

if (isset($_POST['hapusdestana'])) {
    $id = $_POST['id_destana'];

    mysqli_query($conn, "DELETE FROM destanaa WHERE id_destana='$id'");

    header("location: keluar.php");
}
?>
