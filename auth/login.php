<?php
session_start();

if(isset($_SESSION['login'])){
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Rental iPhone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="login-wrapper">
    <div class="card login-box">

        <div class="card-header text-center">
            <div style="font-size:2.5rem; margin-bottom:8px;">📱</div>
            <h4 class="text-white fw-bold mb-1">Rental iPhone</h4>
            <p class="text-white mb-0" style="opacity:0.7; font-size:0.85rem;">Sistem Manajemen Penyewaan</p>
        </div>

        <div class="card-body">
            <p class="fw-bold mb-4" style="color:#374151; font-size:1.05rem;">Masuk ke Akun Admin</p>

            <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger rounded-3 py-2 px-3" style="font-size:0.875rem;">
                ⚠️ Username atau password salah.
            </div>
            <?php endif; ?>

            <form action="proses_login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn-custom w-100 py-3">
                    🔐 Login
                </button>
            </form>

            <hr class="my-4">
            <p class="text-center mb-0" style="font-size:0.8rem; color:#94a3b8;">
                UAS Basis Data · Sistem Informasi
            </p>
        </div>

    </div>
</div>

</body>
</html>
