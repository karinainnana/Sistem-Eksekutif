<?php
/**
 * 401 Unauthorized Page
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>401 - Tidak Diizinkan</title>
    <link href="assets/css/styles.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .error-container { text-align: center; color: white; }
        .error-code { font-size: 8rem; font-weight: 700; margin: 0; text-shadow: 4px 4px 0 rgba(0,0,0,0.2); }
        .error-title { font-size: 2rem; margin-bottom: 1rem; }
        .error-message { margin-bottom: 2rem; opacity: 0.9; }
        .btn-home { background: white; color: #667eea; padding: 12px 30px; border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s; }
        .btn-home:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); color: #764ba2; }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">401</h1>
        <h2 class="error-title">Tidak Diizinkan</h2>
        <p class="error-message">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="auth/login.php" class="btn-home"><i class="fas fa-sign-in-alt me-2"></i>Login</a>
    </div>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</body>
</html>
