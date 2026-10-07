<?php
session_start();
if(!isset($_SESSION['login'])){
    header("Location: auth/login.php");
    exit();
}
include "config/koneksi.php";

$pelanggan    = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pelanggan"));
$iphone       = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM stok_iphone"));
$transaksi    = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM transaksi_sewa"));
$pengembalian = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pengembalian"));
$tersedia     = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM stok_iphone WHERE status_ketersediaan='tersedia'"));
$disewa       = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM stok_iphone WHERE status_ketersediaan='disewa'"));
$omset_q      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM transaksi_sewa"));
$total_omset  = $omset_q['total'] ?? 0;
$base_url     = '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — Rental iPhone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include "template/navbar.php"; ?>

<div class="layout-wrapper">
    <div class="sidebar" id="sidebarMenu">
        <a href="index.php" class="active">🏠 Dashboard</a>
        <a href="pelanggan/index.php">👥 Pelanggan</a>
        <a href="iphone/index.php">📱 Stok iPhone</a>
        <a href="transaksi/index.php">📋 Transaksi</a>
        <a href="pengembalian/index.php">🔄 Pengembalian</a>
        <a href="laporan/index.php">📊 Laporan</a>
        <a href="auth/logout.php" class="sidebar-logout">🚪 Logout</a>
    </div>

    <div class="main-content">

        <div class="page-header">
            <div>
                <h2>Dashboard</h2>
                <p class="subtitle">Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama_admin'] ?? 'Admin'); ?></strong></p>
            </div>
            <span class="badge bg-success px-3 py-2" style="font-size:0.8rem;">● Sistem Aktif</span>
        </div>

        <!-- Row 1: Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-label">Total Pelanggan</div>
                    <div class="stat-number"><?= $pelanggan ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon">📱</div>
                    <div class="stat-label">Total iPhone</div>
                    <div class="stat-number"><?= $iphone ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon">📋</div>
                    <div class="stat-label">Total Transaksi</div>
                    <div class="stat-number"><?= $transaksi ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon">🔄</div>
                    <div class="stat-label">Total Pengembalian</div>
                    <div class="stat-number"><?= $pengembalian ?></div>
                </div>
            </div>
        </div>

        <!-- Row 2 -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card" style="border-left:4px solid #22c55e;">
                    <div class="stat-icon">✅</div>
                    <div class="stat-label">iPhone Tersedia</div>
                    <div class="stat-number" style="color:#22c55e;"><?= $tersedia ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-left:4px solid #f59e0b;">
                    <div class="stat-icon">🔒</div>
                    <div class="stat-label">Sedang Disewa</div>
                    <div class="stat-number" style="color:#f59e0b;"><?= $disewa ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-left:4px solid var(--primary);">
                    <div class="stat-icon">💰</div>
                    <div class="stat-label">Total Omset</div>
                    <div class="stat-number" style="font-size:1.3rem;">Rp <?= number_format($total_omset) ?></div>
                </div>
            </div>
        </div>

        <!-- Menu Cepat -->
        <div class="card">
            <div class="card-header">⚡ Menu Cepat</div>
            <div class="card-body">
                <div class="row g-3">
                    <?php
                    $menus = [
                        ['pelanggan/index.php',  '👥', 'Data Pelanggan',   'Kelola data pelanggan',    'var(--primary)'],
                        ['iphone/index.php',     '📱', 'Stok iPhone',      'Kelola unit & harga',      'var(--primary)'],
                        ['transaksi/index.php',  '📋', 'Transaksi Sewa',   'Catat penyewaan baru',     'var(--primary)'],
                        ['pengembalian/index.php','🔄','Pengembalian',     'Proses pengembalian unit', 'var(--primary)'],
                        ['laporan/index.php',    '📊', 'Laporan',          'Cetak & ekspor laporan',   'var(--primary)'],
                        ['transaksi/tambah.php', '➕', 'Tambah Transaksi', 'Sewa baru sekarang',       '#22c55e'],
                    ];
                    foreach($menus as $m): ?>
                    <div class="col-md-4 col-6">
                        <a href="<?= $m[0] ?>" class="btn-menu-quick" style="background:linear-gradient(135deg,<?= $m[4] ?>,<?= $m[4] === 'var(--primary)' ? '#00796B' : '#16a34a' ?>);">
                            <span style="font-size:1.8rem;"><?= $m[1] ?></span>
                            <strong style="display:block;margin-top:6px;font-size:0.95rem;"><?= $m[2] ?></strong>
                            <small style="opacity:0.82;"><?= $m[3] ?></small>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="section-divider"></div>
            <strong>Sistem Manajemen Rental iPhone</strong><br>
            UAS Mata Kuliah Basis Data · Prodi Sistem Informasi
        </div>
    </div>
</div>

<style>
.btn-menu-quick {
    display: block;
    width: 100%;
    padding: 18px 16px;
    border-radius: 14px;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
}
.btn-menu-quick:hover { transform: translateY(-4px); box-shadow: 0 8px 22px rgba(0,0,0,0.18); color: white; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
