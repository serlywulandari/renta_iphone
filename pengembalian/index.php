<?php
session_start();
if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit();
}
include "../config/koneksi.php";

$query = mysqli_query($koneksi,
    "SELECT pengembalian.*, transaksi_sewa.kode_transaksi, pelanggan.nama_pelanggan, stok_iphone.tipe_unit
     FROM pengembalian
     JOIN transaksi_sewa ON transaksi_sewa.id_sewa = pengembalian.id_sewa
     JOIN pelanggan ON pelanggan.id_pelanggan = transaksi_sewa.id_pelanggan
     JOIN stok_iphone ON stok_iphone.id_iphone = transaksi_sewa.id_iphone
     ORDER BY pengembalian.id_pengembalian DESC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengembalian — Rental iPhone</title>
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
        <a href="../pengembalian/index.php" class="active">🔄 Pengembalian</a>
        <a href="../laporan/index.php">📊 Laporan</a>
        <a href="../auth/logout.php" class="sidebar-logout">🚪 Logout</a>
    </div>


    <div class="main-content">
        <div class="page-header">
            <div>
                <h2>Pengembalian iPhone</h2>
                <p class="subtitle">Kelola records unit yang kembali & kalkulasi denda</p>
            </div>
            <a href="tambah.php" class="btn btn-success px-4">+ Tambah Pengembalian</a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-wrapper" style="overflow-x:auto;">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kode Transaksi</th>
                                <th>Pelanggan</th>
                                <th>iPhone</th>
                                <th>Tgl Kembali Aktual</th>
                                <th>Kondisi Unit</th>
                                <th>Total Denda</th>
                                <th>Catatan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($query)){ ?>
                            <tr>
                                <td><strong style="color:var(--primary);"><?= !empty($row['kode_transaksi']) ? $row['kode_transaksi'] : 'TRX-00'.$row['id_sewa']; ?></strong></td>
                                <td class="fw-medium"><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                                <td><?= htmlspecialchars($row['tipe_unit']); ?></td>
                                <td><small><?= $row['tgl_kembali_aktual']; ?></small></td>
                                <td>
                                    <?php
                                    $k = $row['kondisi_unit'];
                                    $kb = $k=='mulus' ? 'bg-success' : ($k=='lecet ringan' ? 'bg-warning text-dark' : 'bg-danger');
                                    echo "<span class='badge $kb'>".ucfirst($k)."</span>";
                                    ?>
                                </td>
                                <td class="<?= $row['total_denda'] > 0 ? 'text-danger fw-bold' : 'text-muted'; ?>">
                                    Rp <?= number_format($row['total_denda']); ?>
                                </td>
                                <td><small class="text-muted"><?= htmlspecialchars($row['catatan']); ?></small></td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="edit.php?id=<?= $row['id_pengembalian']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="hapus.php?id=<?= $row['id_pengembalian']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
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
