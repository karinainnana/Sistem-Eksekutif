<?php
/**
 * Authentication Functions
 * Fungsi-fungsi untuk autentikasi
 */

require_once dirname(__DIR__) . '/config/config.php';

/**
 * Login user
 */
function loginUser($email, $password) {
    global $conn;
    
    // Validasi input
    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Email dan password harus diisi'];
    }
    
    // Sanitize input
    $email = mysqli_real_escape_string($conn, $email);
    
    // Cek email di database
    $query = "SELECT * FROM pengguna WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        return ['success' => false, 'message' => 'Email tidak ditemukan'];
    }
    
    $user = mysqli_fetch_assoc($result);
    
    // Verifikasi password (plain text untuk kompatibilitas dengan sistem yang ada)
    // TODO: Gunakan password_verify() untuk keamanan lebih baik
    if ($password !== $user['password']) {
        return ['success' => false, 'message' => 'Password salah'];
    }
    
    // Cek role valid
    if (!in_array($user['role'], ['admin', 'eksekutif'])) {
        return ['success' => false, 'message' => 'Role pengguna tidak valid'];
    }
    
    // Set session
    $_SESSION['log'] = true;
    $_SESSION['user_id'] = $user['id_pengguna'] ?? $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['nama'] = $user['nama'] ?? $user['email'];
    
    // Determine redirect URL
    $redirect = ($user['role'] === 'admin') ? '../admin/index.php' : '../index.php';
    
    return ['success' => true, 'message' => 'Login berhasil', 'redirect' => $redirect];
}

/**
 * Logout user
 */
function logoutUser() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['log']) && $_SESSION['log'] === true;
}

/**
 * Check if user has specific role
 */
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Require login - redirect to login page if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/auth/login.php');
        exit;
    }
}

/**
 * Require specific role
 */
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header('Location: ' . APP_URL . '/auth/unauthorized.php');
        exit;
    }
}
?>
