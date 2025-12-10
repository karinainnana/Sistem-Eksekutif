<?php
/**
 * Eksekutif Dashboard
 * Redirect ke halaman utama
 */

require_once dirname(__DIR__) . '/config/config.php';

// Check eksekutif role
if (!isset($_SESSION['log']) || !isset($_SESSION['role'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Redirect to main dashboard
header('Location: ../index.php');
exit;
?>
