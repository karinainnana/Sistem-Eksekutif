<?php
/**
 * Logout Page
 * Halaman untuk logout user
 */

require_once dirname(__DIR__) . '/includes/auth_functions.php';

// Destroy session
session_unset();
session_destroy();

// Redirect to login with success message
header('Location: login.php?success=logout');
exit;
?>
