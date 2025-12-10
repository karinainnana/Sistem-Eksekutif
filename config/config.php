<?php
/**
 * Application Configuration
 * Konfigurasi aplikasi utama
 */

// App Settings
define('APP_NAME', 'BPBD PKRR DIY');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/project-eksekutif');

// Session Configuration
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include Database
require_once __DIR__ . '/database.php';
?>
