<?php
session_start();
if(!isset($_SESSION['log']) || $_SESSION['role'] != 'eksekutif'){
    header('location: ../login.php');
    exit;
}
?>

