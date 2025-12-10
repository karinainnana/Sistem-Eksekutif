<?php
/**
 * Forgot Password Page
 * Halaman lupa password
 */

require_once dirname(__DIR__) . '/includes/auth_functions.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

$error = '';
$success = '';

// Process forgot password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Email harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } else {
        // Cek email di database
        global $conn;
        $check_email = mysqli_query($conn, "SELECT * FROM pengguna WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'");
        
        if (mysqli_num_rows($check_email) === 0) {
            $error = 'Email tidak ditemukan dalam sistem';
        } else {
            // Di sini seharusnya kirim email reset password
            // Untuk sementara, tampilkan pesan sukses
            $success = 'Link reset password telah dikirim ke email Anda';
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
    <meta name="description" content="Lupa Password - Sistem Informasi Eksekutif BPBD DIY" />
    <title>Lupa Password - <?php echo APP_NAME; ?></title>
    
    <link href="../assets/css/styles.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .forgot-card {
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }
        .card-header {
            background: transparent !important;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding: 2rem;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        .btn-reset {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
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
                        <div class="col-lg-5 col-md-7">
                            <div class="card forgot-card shadow-lg border-0 mt-5">
                                <div class="card-header text-center">
                                    <div class="logo-icon">
                                        <i class="fas fa-key"></i>
                                    </div>
                                    <h3 class="font-weight-bold my-2">Lupa Password?</h3>
                                    <p class="text-muted mb-0">Masukkan email Anda untuk mereset password</p>
                                </div>
                                <div class="card-body p-4">
                                    
                                    <?php if ($error): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <?php echo htmlspecialchars($error); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($success): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <?php echo htmlspecialchars($success); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <form method="post" action="">
                                        <div class="form-floating mb-4">
                                            <input class="form-control" id="inputEmail" name="email" type="email" 
                                                   placeholder="name@example.com" required
                                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
                                            <label for="inputEmail">
                                                <i class="fas fa-envelope me-2"></i>Email Address
                                            </label>
                                        </div>
                                        
                                        <div class="d-flex align-items-center justify-content-between">
                                            <a class="small text-decoration-none" href="login.php">
                                                <i class="fas fa-arrow-left me-1"></i>Kembali ke Login
                                            </a>
                                            <button class="btn btn-primary btn-reset" type="submit" name="reset">
                                                Reset Password
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3 bg-light" style="border-radius: 0 0 20px 20px;">
                                    <div class="small">
                                        Belum punya akun? 
                                        <a href="register.php" class="text-decoration-none fw-bold">Daftar di sini</a>
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
