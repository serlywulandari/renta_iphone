<?php
session_start();
if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit();
}
include "../config/koneksi.php";

$pelanggan_list = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC");
$iphone_list    = mysqli_query($koneksi, "SELECT * FROM stok_iphone WHERE status_ketersediaan='tersedia' ORDER BY tipe_unit ASC");

if(isset($_POST['simpan'])){
    $id_pelanggan = (int)$_POST['id_pelanggan'];
    $id_iphone    = (int)$_POST['id_iphone'];
    $tgl_mulai    = $_POST['tgl_mulai'];
    $tgl_kembali  = $_POST['tgl_kembali'];
    $jaminan      = mysqli_real_escape_string($koneksi, $_POST['jaminan_identitas']);
    $id_admin     = $_SESSION['id_admin'] ?? 1;

    $q_harga      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT harga_sewa_perhari FROM stok_iphone WHERE id_iphone='$id_iphone'"));
    $harga_perhari = $q_harga['harga_sewa_perhari'];

    $durasi = max(1, (int)((strtotime($tgl_kembali) - strtotime($tgl_mulai)) / 86400));
    $total_bayar = $durasi * $harga_perhari;

    $kode = "TRX-" . date("Ymd") . "-" . rand(10, 99);

    $simpan = mysqli_query($koneksi,
        "INSERT INTO transaksi_sewa (kode_transaksi, id_pelanggan, id_iphone, id_admin, tgl_mulai, tgl_kembali, total_bayar, jaminan_identitas, status_transaksi)
         VALUES ('$kode','$id_pelanggan','$id_iphone','$id_admin','$tgl_mulai','$tgl_kembali','$total_bayar','$jaminan','berjalan')"
    );

    if($simpan){
        mysqli_query($koneksi, "UPDATE stok_iphone SET status_ketersediaan='disewa' WHERE id_iphone='$id_iphone'");
        echo "<script>alert('Transaksi berhasil ditambahkan! Kode: $kode'); window.location='index.php';</script>";
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
    <title>Tambah Transaksi — Rental iPhone</title>
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
        <div style="max-width:580px; width:100%; margin-top:8px;">

            <div class="page-header">
                <div>
                    <h2>Tambah Transaksi</h2>
                    <p class="subtitle">Input data penyewaan unit iPhone baru</p>
                </div>
                <a href="index.php" class="btn btn-secondary">← Kembali</a>
            </div>

            <?php if(isset($error)): ?>
            <div class="alert alert-danger rounded-3"><?= $error ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" id="formTransaksi">
                        <div class="mb-3">
                            <label class="form-label">Pelanggan</label>
                            <select name="id_pelanggan" class="form-control" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                <?php while($p = mysqli_fetch_assoc($pelanggan_list)){ ?>
                                <option value="<?= $p['id_pelanggan']; ?>">
                                    <?= htmlspecialchars($p['nama_pelanggan']); ?> — <?= $p['no_wa']; ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">iPhone Tersedia</label>
                            <select name="id_iphone" class="form-control" id="selectIphone" required onchange="updateHarga()">
                                <option value="">-- Pilih iPhone --</option>
                                <?php while($i = mysqli_fetch_assoc($iphone_list)){ ?>
                                <option value="<?= $i['id_iphone']; ?>" data-harga="<?= $i['harga_sewa_perhari']; ?>">
                                    <?= htmlspecialchars($i['tipe_unit']); ?> — Rp <?= number_format($i['harga_sewa_perhari']); ?>/hari
                                </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tgl_mulai" id="tglMulai" class="form-control" onchange="hitungTotal()" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Kembali</label>
                                <input type="date" name="tgl_kembali" id="tglKembali" class="form-control" onchange="hitungTotal()" required>
                            </div>
                        </div>

                        <!-- Preview kalkulasi otomatis -->
                        <div id="previewHitung" class="alert mb-3" style="display:none; background:rgba(0,150,136,0.08); border:1px solid rgba(0,150,136,0.2); border-radius:12px; padding:16px;">
                            <div class="row text-center">
                                <div class="col-4">
                                    <small class="text-muted d-block">Harga/Hari</small>
                                    <strong id="previewHarga" style="color:var(--primary);">Rp 0</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Durasi</small>
                                    <strong id="previewDurasi" style="color:var(--primary);">0 hari</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Total Bayar</small>
                                    <strong id="previewTotal" style="color:var(--primary); font-size:1.1rem;">Rp 0</strong>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Jaminan Identitas</label>
                            <input type="text" name="jaminan_identitas" class="form-control" placeholder="Contoh: KTP Asli / KTM / SIM" required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="simpan" class="btn btn-custom flex-grow-1 py-3">💾 Simpan Transaksi</button>
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
<script>
let hargaPerHari = 0;

function updateHarga(){
    const sel = document.getElementById('selectIphone');
    const opt = sel.options[sel.selectedIndex];
    hargaPerHari = parseInt(opt.getAttribute('data-harga')) || 0;
    hitungTotal();
}

function hitungTotal(){
    const mulai   = document.getElementById('tglMulai').value;
    const kembali = document.getElementById('tglKembali').value;
    const preview = document.getElementById('previewHitung');

    if(mulai && kembali && hargaPerHari > 0){
        const durasi = Math.max(1, Math.ceil((new Date(kembali) - new Date(mulai)) / 86400000));
        const total  = durasi * hargaPerHari;

        document.getElementById('previewHarga').textContent  = 'Rp ' + hargaPerHari.toLocaleString('id-ID');
        document.getElementById('previewDurasi').textContent = durasi + ' hari';
        document.getElementById('previewTotal').textContent  = 'Rp ' + total.toLocaleString('id-ID');
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}
</script>
</body>
</html>
