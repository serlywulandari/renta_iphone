<?php
session_start();
if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit();
}
include "../config/koneksi.php";

$total_sewa  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_bayar) as t FROM transaksi_sewa"))['t'] ?? 0;
$total_denda = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_denda) as t FROM pengembalian"))['t'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan — Rental iPhone</title>
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
        <a href="../transaksi/index.php">📋 Transaksi</a>
        <a href="../pengembalian/index.php">🔄 Pengembalian</a>
        <a href="../laporan/index.php" class="active">📊 Laporan</a>
        <a href="../auth/logout.php" class="sidebar-logout">🚪 Logout</a>
    </div>


    <div class="main-content">
        <div class="page-header">
            <div>
                <h2>Laporan</h2>
                <p class="subtitle">Cetak laporan transaksi & pengembalian berdasarkan periode</p>
            </div>
        </div>

        <!-- Ringkasan Omset -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card" style="border-left:4px solid var(--primary);">
                    <div class="stat-icon">💰</div>
                    <div class="stat-number" style="font-size:1.4rem;">Rp <?= number_format($total_sewa); ?></div>
                    <div class="stat-label">Total Pendapatan Sewa</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-left:4px solid #ef4444;">
                    <div class="stat-icon">⚠️</div>
                    <div class="stat-number" style="font-size:1.4rem; color:#ef4444;">Rp <?= number_format($total_denda); ?></div>
                    <div class="stat-label">Total Denda</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-left:4px solid #22c55e;">
                    <div class="stat-icon">📈</div>
                    <div class="stat-number" style="font-size:1.4rem; color:#22c55e;">Rp <?= number_format($total_sewa + $total_denda); ?></div>
                    <div class="stat-label">Keuntungan Bersih</div>
                </div>
            </div>
        </div>

        <!-- Form Cetak -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">📋 Laporan Transaksi</div>
                    <div class="card-body">
                        <p class="text-muted small mb-4">Cetak data penyewaan iPhone berdasarkan tanggal mulai sewa.</p>
                        <form action="cetak_transaksi.php" method="GET" target="_blank">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" name="tgl_awal" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tgl_akhir" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100 py-2">🖨 Cetak Laporan Transaksi</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">🔄 Laporan Pengembalian</div>
                    <div class="card-body">
                        <p class="text-muted small mb-4">Cetak data pengembalian unit iPhone berdasarkan tanggal kembali aktual.</p>
                        <form action="cetak_pengembalian.php" method="GET" target="_blank">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" name="tgl_awal" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tgl_akhir" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">🖨 Cetak Laporan Pengembalian</button>
                        </form>
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
