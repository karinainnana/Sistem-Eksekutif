<?php
session_start();
session_unset(); // Hapus semua session
session_destroy(); // Hancurkan session
header('location:login.php'); // Redirect ke login
exit();
?>