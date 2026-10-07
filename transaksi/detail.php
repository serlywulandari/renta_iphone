<?php
session_start();
if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit();
}
include "../config/koneksi.php";

if(!isset($_GET['id'])){
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT transaksi_sewa.*, pelanggan.nama_pelanggan, pelanggan.no_wa, pelanggan.alamat,
     stok_iphone.tipe_unit, stok_iphone.no_seri, stok_iphone.warna, stok_iphone.kapasitas_gb, stok_iphone.harga_sewa_perhari
     FROM transaksi_sewa
     JOIN pelanggan ON pelanggan.id_pelanggan = transaksi_sewa.id_pelanggan
     JOIN stok_iphone ON stok_iphone.id_iphone = transaksi_sewa.id_iphone
     WHERE transaksi_sewa.id_sewa='$id'"
));

if(!$data){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Transaksi — Rental iPhone</title>
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
        <a href="../pelanggan/index.php">👥 Pelanggan</a>
        <a href="../iphone/index.php">📱 Stok iPhone</a>
        <a href="../transaksi/index.php" class="active">📋 Transaksi</a>
        <a href="../pengembalian/index.php">🔄 Pengembalian</a>
        <a href="../laporan/index.php">📊 Laporan</a>
        <a href="../auth/logout.php" class="sidebar-logout">🚪 Logout</a>
    </div>


    <div class="main-content">
        <div class="page-header">
            <div>
                <h2>Detail Transaksi</h2>
                <p class="subtitle">Kode: <strong><?= $data['kode_transaksi']; ?></strong></p>
            </div>
            <a href="index.php" class="btn btn-secondary">← Kembali</a>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">👥 Data Pelanggan</div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr><td class="text-muted" width="140">Nama</td><td class="fw-bold"><?= htmlspecialchars($data['nama_pelanggan']); ?></td></tr>
                            <tr><td class="text-muted">WhatsApp</td><td><a href="https://wa.me/<?= $data['no_wa']; ?>" target="_blank" class="text-success"><?= $data['no_wa']; ?></a></td></tr>
                            <tr><td class="text-muted">Alamat</td><td><?= htmlspecialchars($data['alamat']); ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">📱 Data iPhone</div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr><td class="text-muted" width="140">Tipe</td><td class="fw-bold"><?= htmlspecialchars($data['tipe_unit']); ?></td></tr>
                            <tr><td class="text-muted">No. Seri</td><td><code><?= $data['no_seri']; ?></code></td></tr>
                            <tr><td class="text-muted">Warna</td><td><?= $data['warna']; ?></td></tr>
                            <tr><td class="text-muted">Kapasitas</td><td><span class="badge bg-secondary"><?= $data['kapasitas_gb']; ?> GB</span></td></tr>
                            <tr><td class="text-muted">Harga/Hari</td><td class="text-success fw-bold">Rp <?= number_format($data['harga_sewa_perhari']); ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">📋 Detail Sewa</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <small class="text-muted d-block">Tanggal Mulai</small>
                                <strong><?= $data['tgl_mulai']; ?></strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Tanggal Kembali</small>
                                <strong><?= $data['tgl_kembali']; ?></strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Jaminan</small>
                                <strong><?= $data['jaminan_identitas']; ?></strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Total Bayar</small>
                                <strong class="text-success" style="font-size:1.2rem;">Rp <?= number_format($data['total_bayar']); ?></strong>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">Status</small>
                                <?php
                                $s = $data['status_transaksi'];
                                $cls = $s=='selesai' ? 'bg-success' : ($s=='berjalan' ? 'bg-primary' : ($s=='terlambat' ? 'bg-danger' : 'bg-warning text-dark'));
                                echo "<span class='badge $cls px-3 py-2'>".ucfirst($s)."</span>";
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/app.js"></script>
</body>
</html>
