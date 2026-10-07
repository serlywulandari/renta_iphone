<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit();
}
include "../config/koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id  = mysqli_real_escape_string($koneksi, $_GET['id']);
$row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM stok_iphone WHERE id_iphone='$id'"));

if (!$row) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['update'])) {
    $tipe   = mysqli_real_escape_string($koneksi, $_POST['tipe_unit']);
    $seri   = mysqli_real_escape_string($koneksi, $_POST['no_seri']);
    $warna  = mysqli_real_escape_string($koneksi, $_POST['warna']);
    $kapas  = (int)$_POST['kapasitas_gb'];
    $harga  = (int)$_POST['harga_sewa_perhari'];
    $status = mysqli_real_escape_string($koneksi, $_POST['status_ketersediaan']);

    $result = mysqli_query(
        $koneksi,
        "UPDATE stok_iphone SET
         tipe_unit='$tipe', no_seri='$seri', warna='$warna',
         kapasitas_gb='$kapas', harga_sewa_perhari='$harga', status_ketersediaan='$status'
         WHERE id_iphone='$id'"
    );

    if ($result) {
        echo "<script>alert('Data unit berhasil diperbarui!'); window.location='index.php';</script>";
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
    <title>Edit iPhone — Rental iPhone</title>
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
            <div style="max-width:540px; width:100%; margin-top:8px;">

                <div class="page-header">
                    <div>
                        <h2>Edit iPhone</h2>
                        <p class="subtitle">Ubah data: <strong><?= htmlspecialchars($row['tipe_unit']); ?></strong></p>
                    </div>
                    <a href="index.php" class="btn btn-secondary">← Kembali</a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger rounded-3"><?= $error ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Tipe Unit</label>
                                <input type="text" name="tipe_unit" class="form-control" value="<?= htmlspecialchars($row['tipe_unit']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nomor Seri</label>
                                <input type="text" name="no_seri" class="form-control" value="<?= htmlspecialchars($row['no_seri']); ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Warna</label>
                                    <input type="text" name="warna" class="form-control" value="<?= htmlspecialchars($row['warna']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kapasitas (GB)</label>
                                    <input type="number" name="kapasitas_gb" class="form-control" value="<?= $row['kapasitas_gb']; ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Harga Sewa / Hari (Rp)</label>
                                <input type="number" name="harga_sewa_perhari" class="form-control" value="<?= $row['harga_sewa_perhari']; ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Status Ketersediaan</label>
                                <select name="status_ketersediaan" class="form-control" required>
                                    <option value="tersedia" <?= $row['status_ketersediaan'] == 'tersedia' ? 'selected' : ''; ?>>✅ Tersedia</option>
                                    <option value="disewa" <?= $row['status_ketersediaan'] == 'disewa'   ? 'selected' : ''; ?>>🔒 Disewa</option>
                                    <option value="servis" <?= $row['status_ketersediaan'] == 'servis'   ? 'selected' : ''; ?>>🔧 Servis</option>
                                </select>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="update" class="btn btn-warning flex-grow-1 py-3 text-white">✏️ Simpan Perubahan</button>
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