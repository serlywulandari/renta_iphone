<?php
session_start();
if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit();
}
include "../config/koneksi.php";

if(isset($_POST['simpan'])){
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nik    = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $wa     = mysqli_real_escape_string($koneksi, $_POST['wa']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $link   = mysqli_real_escape_string($koneksi, $_POST['link']);

    $result = mysqli_query($koneksi,
        "INSERT INTO pelanggan (nama_pelanggan, nik_ktp, no_wa, alamat, link_sosmed)
         VALUES ('$nama','$nik','$wa','$alamat','$link')"
    );

    if($result){
        echo "<script>alert('Data pelanggan berhasil ditambahkan!'); window.location='index.php';</script>";
        exit();
    } else {
        $error = "Gagal menyimpan: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Pelanggan — Rental iPhone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar-custom">
    <button class="hamburger-btn" id="sidebarToggle" aria-label="Buka menu" type="button">☰</button>
    <a class="navbar-brand" href="#">📱 Rental iPhone</a>
    <div class="ms-auto">
        <span class="text-white">👤 <strong><?= htmlspecialchars($_SESSION['nama_admin']); ?></strong></span>
        <a href="../auth/logout.php" class="btn btn-danger btn-sm px-3 ms-2">Logout</a>
    </div>
</nav>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="layout-wrapper">
    <div class="sidebar" id="sidebarMenu">
        <a href="../index.php">🏠 Dashboard</a>
        <a href="../pelanggan/index.php" class="active">👥 Pelanggan</a>
        <a href="../iphone/index.php">📱 Stok iPhone</a>
        <a href="../transaksi/index.php">📋 Transaksi</a>
        <a href="../pengembalian/index.php">🔄 Pengembalian</a>
        <a href="../laporan/index.php">📊 Laporan</a>
        <a href="../auth/logout.php" class="sidebar-logout">🚪 Logout</a>
    </div>


    <div class="main-content">
        <div style="max-width:560px; width:100%; margin-top:8px;">

            <div class="page-header">
                <div>
                    <h2>Tambah Pelanggan</h2>
                    <p class="subtitle">Registrasi data profil pelanggan baru</p>
                </div>
                <a href="index.php" class="btn btn-secondary">← Kembali</a>
            </div>

            <?php if(isset($error)): ?>
            <div class="alert alert-danger rounded-3"><?= $error ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Pelanggan</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama lengkap sesuai identitas" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIK (Nomor Induk Kependudukan)</label>
                            <input type="text" name="nik" class="form-control" placeholder="16 digit nomor NIK KTP" maxlength="16" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. WhatsApp</label>
                            <input type="text" name="wa" class="form-control" placeholder="Contoh: 628123456789" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat domisili saat ini..." required></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Link Sosial Media <small class="text-muted">(opsional)</small></label>
                            <input type="text" name="link" class="form-control" placeholder="instagram.com/username">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="simpan" class="btn btn-custom flex-grow-1 py-3">💾 Simpan Pelanggan</button>
                            <a href="index.php" class="btn btn-secondary px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/app.js"></script>
</body>
</html>
