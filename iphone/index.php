<?php
session_start();
if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit();
}
include "../config/koneksi.php";
$query = mysqli_query($koneksi, "SELECT * FROM stok_iphone ORDER BY id_iphone ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stok iPhone — Rental iPhone</title>
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
        <a href="../iphone/index.php" class="active">📱 Stok iPhone</a>
        <a href="../transaksi/index.php">📋 Transaksi</a>
        <a href="../pengembalian/index.php">🔄 Pengembalian</a>
        <a href="../laporan/index.php">📊 Laporan</a>
        <a href="../auth/logout.php" class="sidebar-logout">🚪 Logout</a>
    </div>


    <div class="main-content">
        <div class="page-header">
            <div>
                <h2>Stok iPhone</h2>
                <p class="subtitle">Kelola unit, spesifikasi, harga sewa & ketersediaan</p>
            </div>
            <a href="tambah.php" class="btn btn-success px-4">+ Tambah iPhone</a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-wrapper">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipe Unit</th>
                                <th>No. Seri</th>
                                <th>Warna</th>
                                <th>Kapasitas</th>
                                <th>Harga / Hari</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($query)){ ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $row['id_iphone']; ?></span></td>
                                <td class="fw-bold"><?= htmlspecialchars($row['tipe_unit']); ?></td>
                                <td><code style="font-size:0.8rem;color:#64748b;"><?= $row['no_seri']; ?></code></td>
                                <td><?= htmlspecialchars($row['warna']); ?></td>
                                <td><span class="badge bg-secondary"><?= $row['kapasitas_gb']; ?> GB</span></td>
                                <td class="fw-bold text-success">Rp <?= number_format($row['harga_sewa_perhari']); ?></td>
                                <td>
                                    <?php
                                    $s = $row['status_ketersediaan'];
                                    $badge = $s === 'tersedia' ? 'bg-success' : ($s === 'disewa' ? 'bg-warning text-dark' : 'bg-secondary');
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= ucfirst($s); ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="edit.php?id=<?= $row['id_iphone']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="hapus.php?id=<?= $row['id_iphone']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus unit ini?')">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/app.js"></script>
</body>
</html>
