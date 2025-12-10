<?php
session_start();
if(!isset($_SESSION['log']) || $_SESSION['role'] != 'admin'){
    header('location: ../login.php');
    exit;
}
?>
