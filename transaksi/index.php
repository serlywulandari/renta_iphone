<?php
session_start();
if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit();
}
include "../config/koneksi.php";

$query = mysqli_query($koneksi,
    "SELECT transaksi_sewa.*, pelanggan.nama_pelanggan, stok_iphone.tipe_unit
     FROM transaksi_sewa
     JOIN pelanggan ON pelanggan.id_pelanggan = transaksi_sewa.id_pelanggan
     JOIN stok_iphone ON stok_iphone.id_iphone = transaksi_sewa.id_iphone
     ORDER BY transaksi_sewa.id_sewa DESC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaksi — Rental iPhone</title>
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
                <h2>Transaksi Sewa</h2>
                <p class="subtitle">Kelola riwayat penyewaan iPhone</p>
            </div>
            <a href="tambah.php" class="btn btn-success px-4">+ Tambah Transaksi</a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-wrapper" style="overflow-x:auto;">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Pelanggan</th>
                                <th>iPhone</th>
                                <th>Tgl Mulai</th>
                                <th>Tgl Kembali</th>
                                <th>Jaminan</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($query)){ ?>
                            <tr>
                                <td><strong style="color:var(--primary);"><?= !empty($row['kode_transaksi']) ? $row['kode_transaksi'] : 'TRX-00'.$row['id_sewa']; ?></strong></td>
                                <td class="fw-medium"><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                                <td><?= htmlspecialchars($row['tipe_unit']); ?></td>
                                <td><small><?= $row['tgl_mulai']; ?></small></td>
                                <td><small><?= $row['tgl_kembali']; ?></small></td>
                                <td><small class="text-muted"><?= $row['jaminan_identitas']; ?></small></td>
                                <td class="fw-bold text-success">Rp <?= number_format($row['total_bayar']); ?></td>
                                <td>
                                    <?php
                                    $s = $row['status_transaksi'];
                                    $cls = $s=='selesai' ? 'bg-success' : ($s=='berjalan' ? 'bg-primary' : ($s=='terlambat' ? 'bg-danger' : 'bg-warning text-dark'));
                                    echo "<span class='badge $cls'>".ucfirst($s)."</span>";
                                    ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                        <a href="detail.php?id=<?= $row['id_sewa']; ?>" class="btn btn-info btn-sm">Detail</a>
                                        <?php if($row['status_transaksi']=='berjalan'): ?>
                                        <a href="../pengembalian/tambah.php?id=<?= $row['id_sewa']; ?>" class="btn btn-success btn-sm">Kembalikan</a>
                                        <?php endif; ?>
                                        <a href="hapus.php?id=<?= $row['id_sewa']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus transaksi ini?')">Hapus</a>
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
