<?php
/**
 * Login Page
 * Halaman login dengan tampilan yang lebih baik
 */

require_once dirname(__DIR__) . '/includes/auth_functions.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    if (hasRole('admin')) {
        header('Location: ../admin/index.php');
    } else {
        header('Location: ../index.php');
    }
    exit;
}

$error = '';
$success = '';

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $result = loginUser($email, $password);
    
    if ($result['success']) {
        header('Location: ' . $result['redirect']);
        exit;
    } else {
        $error = $result['message'];
    }
}

// Check for URL error messages
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'email':
            $error = 'Email tidak ditemukan dalam sistem';
            break;
        case 'password':
            $error = 'Password yang Anda masukkan salah';
            break;
        case 'role_invalid':
            $error = 'Role pengguna tidak valid';
            break;
        case 'session_expired':
            $error = 'Sesi Anda telah berakhir, silakan login kembali';
            break;
    }
}

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'logout':
            $success = 'Anda berhasil logout';
            break;
        case 'register':
            $success = 'Registrasi berhasil! Silakan login';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Login - Sistem Informasi Eksekutif BPBD DIY" />
    <title>Login - <?php echo APP_NAME; ?></title>
    
    <link href="../assets/css/styles.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .login-card {
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
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
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
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
        .form-floating label {
            color: #718096;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #718096;
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
                            <div class="card login-card shadow-lg border-0 mt-5">
                                <div class="card-header text-center">
                                    <div class="logo-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <h3 class="font-weight-bold my-2"><?php echo APP_NAME; ?></h3>
                                    <p class="text-muted mb-0">Masuk ke akun Anda</p>
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
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputEmail" name="email" type="email" 
                                                   placeholder="name@example.com" required autofocus
                                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
                                            <label for="inputEmail">
                                                <i class="fas fa-envelope me-2"></i>Email Address
                                            </label>
                                        </div>
                                        
                                        <div class="form-floating mb-3 position-relative">
                                            <input class="form-control" id="inputPassword" name="password" 
                                                   type="password" placeholder="Password" required />
                                            <label for="inputPassword">
                                                <i class="fas fa-lock me-2"></i>Password
                                            </label>
                                            <span class="password-toggle" onclick="togglePassword()">
                                                <i class="fas fa-eye" id="toggleIcon"></i>
                                            </span>
                                        </div>
                                        
                                        <div class="form-check mb-4">
                                            <input class="form-check-input" id="inputRememberPassword" type="checkbox" />
                                            <label class="form-check-label" for="inputRememberPassword">
                                                Ingat Saya
                                            </label>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary btn-login" type="submit" name="login">
                                                <i class="fas fa-sign-in-alt me-2"></i>Masuk
                                            </button>
                                        </div>
                                        
                                        <div class="text-center mt-4">
                                            <a class="small text-decoration-none" href="forgot-password.php">
                                                Lupa Password?
                                            </a>
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
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('inputPassword');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
