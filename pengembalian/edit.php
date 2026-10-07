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
    "SELECT pengembalian.*, pelanggan.nama_pelanggan, stok_iphone.tipe_unit
     FROM pengembalian
     JOIN transaksi_sewa ON transaksi_sewa.id_sewa = pengembalian.id_sewa
     JOIN pelanggan ON pelanggan.id_pelanggan = transaksi_sewa.id_pelanggan
     JOIN stok_iphone ON stok_iphone.id_iphone = transaksi_sewa.id_iphone
     WHERE id_pengembalian='$id'"
));

if(!$data){
    header("Location: index.php");
    exit();
}

if(isset($_POST['update'])){
    $tgl     = $_POST['tgl_kembali_aktual'];
    $kondisi = mysqli_real_escape_string($koneksi, $_POST['kondisi_unit']);
    $d_telat = (int)$_POST['denda_keterlambatan'];
    $d_rusak = (int)$_POST['denda_kerusakan'];
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    $total   = $d_telat + $d_rusak;

    $result = mysqli_query($koneksi,
        "UPDATE pengembalian SET
         tgl_kembali_aktual='$tgl', kondisi_unit='$kondisi',
         denda_keterlambatan='$d_telat', denda_kerusakan='$d_rusak',
         total_denda='$total', catatan='$catatan'
         WHERE id_pengembalian='$id'"
    );

    if($result){
        echo "<script>alert('Data pengembalian berhasil diperbarui!'); window.location='index.php';</script>";
        exit();
    } else {
        $error = "Gagal update: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Pengembalian — Rental iPhone</title>
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
        <div style="max-width:600px; width:100%; margin-top:8px;">

            <div class="page-header">
                <div>
                    <h2>Edit Pengembalian</h2>
                    <p class="subtitle">Pelanggan: <strong><?= htmlspecialchars($data['nama_pelanggan']); ?></strong></p>
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
                            <label class="form-label">Unit iPhone</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($data['tipe_unit']); ?>" readonly style="background:#f8fafc;">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Kembali Aktual</label>
                                <input type="date" name="tgl_kembali_aktual" class="form-control" value="<?= $data['tgl_kembali_aktual']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kondisi Unit</label>
                                <select name="kondisi_unit" class="form-control" required>
                                    <option value="mulus"       <?= $data['kondisi_unit']=='mulus'       ? 'selected' : ''; ?>>✅ Mulus</option>
                                    <option value="lecet ringan"<?= $data['kondisi_unit']=='lecet ringan'? 'selected' : ''; ?>>⚠️ Lecet Ringan</option>
                                    <option value="rusak"       <?= $data['kondisi_unit']=='rusak'       ? 'selected' : ''; ?>>❌ Rusak</option>
                                    <option value="hilang"      <?= $data['kondisi_unit']=='hilang'      ? 'selected' : ''; ?>>🚫 Hilang</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Denda Keterlambatan (Rp)</label>
                                <input type="number" name="denda_keterlambatan" class="form-control" value="<?= $data['denda_keterlambatan']; ?>" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Denda Kerusakan (Rp)</label>
                                <input type="number" name="denda_kerusakan" class="form-control" value="<?= $data['denda_kerusakan']; ?>" min="0" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3"><?= htmlspecialchars($data['catatan']); ?></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="update" class="btn btn-warning flex-grow-1 py-3 text-white">✏️ Perbarui Data</button>
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
