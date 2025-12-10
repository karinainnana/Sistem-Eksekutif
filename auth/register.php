<?php
/**
 * Register Page
 * Halaman registrasi pengguna baru
 */

require_once dirname(__DIR__) . '/includes/auth_functions.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

$error = '';
$success = '';

// Process registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'eksekutif';
    
    // Validasi input
    if (empty($nama) || empty($email) || empty($password)) {
        $error = 'Semua field harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok';
    } else {
        // Cek email sudah terdaftar
        global $conn;
        $check_email = mysqli_query($conn, "SELECT * FROM pengguna WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'");
        
        if (mysqli_num_rows($check_email) > 0) {
            $error = 'Email sudah terdaftar';
        } else {
            // Insert user baru
            $nama = mysqli_real_escape_string($conn, $nama);
            $email = mysqli_real_escape_string($conn, $email);
            $password_db = mysqli_real_escape_string($conn, $password); // Plain text untuk kompatibilitas
            $role = mysqli_real_escape_string($conn, $role);
            
            $query = "INSERT INTO pengguna (nama, email, password, role) VALUES ('$nama', '$email', '$password_db', '$role')";
            
            if (mysqli_query($conn, $query)) {
                header('Location: login.php?success=register');
                exit;
            } else {
                $error = 'Terjadi kesalahan saat mendaftar';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Register - Sistem Informasi Eksekutif BPBD DIY" />
    <title>Daftar - <?php echo APP_NAME; ?></title>
    
    <link href="../assets/css/styles.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .register-card {
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }
        .card-header {
            background: transparent !important;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding: 2rem;
        }
        .card-header h3 {
            color: #1a1c2c;
            font-weight: 700;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .logo-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>
<body>
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6 col-md-8">
                            <div class="card register-card shadow-lg border-0 mt-5">
                                <div class="card-header text-center">
                                    <div class="logo-icon">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <h3 class="font-weight-bold my-2">Buat Akun Baru</h3>
                                    <p class="text-muted mb-0">Daftar untuk mengakses sistem</p>
                                </div>
                                <div class="card-body p-4">
                                    
                                    <?php if ($error): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <?php echo htmlspecialchars($error); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <form method="post" action="">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputNama" name="nama" type="text" 
                                                   placeholder="Nama Lengkap" required
                                                   value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>" />
                                            <label for="inputNama">
                                                <i class="fas fa-user me-2"></i>Nama Lengkap
                                            </label>
                                        </div>
                                        
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputEmail" name="email" type="email" 
                                                   placeholder="Email" required
                                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
                                            <label for="inputEmail">
                                                <i class="fas fa-envelope me-2"></i>Email Address
                                            </label>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputPassword" name="password" 
                                                           type="password" placeholder="Password" required />
                                                    <label for="inputPassword">
                                                        <i class="fas fa-lock me-2"></i>Password
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputConfirmPassword" name="confirm_password" 
                                                           type="password" placeholder="Konfirmasi Password" required />
                                                    <label for="inputConfirmPassword">
                                                        <i class="fas fa-check me-2"></i>Konfirmasi
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label class="form-label text-muted">Role</label>
                                            <select class="form-select" name="role">
                                                <option value="eksekutif" selected>Eksekutif</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary btn-register" type="submit" name="register">
                                                <i class="fas fa-user-plus me-2"></i>Daftar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3 bg-light" style="border-radius: 0 0 20px 20px;">
                                    <div class="small">
                                        Sudah punya akun? 
                                        <a href="login.php" class="text-decoration-none fw-bold">Masuk di sini</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        
        <div id="layoutAuthentication_footer">
            <footer class="py-4 mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-center small text-white">
                        <div>Copyright &copy; <?php echo APP_NAME; ?> <?php echo date('Y'); ?></div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
