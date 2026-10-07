<?php
session_start();
if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit();
}
include "../config/koneksi.php";

// BUG FIX #7: validasi id_admin dari session
if(!isset($_SESSION['id_admin'])){
    echo "<script>alert('Session admin tidak valid, silakan login ulang.'); window.location='../auth/login.php';</script>";
    exit();
}

$id_sewa_param = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$transaksi_list = mysqli_query($koneksi,
    "SELECT transaksi_sewa.*, pelanggan.nama_pelanggan, stok_iphone.tipe_unit, stok_iphone.harga_sewa_perhari
     FROM transaksi_sewa
     JOIN pelanggan ON pelanggan.id_pelanggan = transaksi_sewa.id_pelanggan
     JOIN stok_iphone ON stok_iphone.id_iphone = transaksi_sewa.id_iphone
     WHERE transaksi_sewa.status_transaksi='berjalan'
     ORDER BY transaksi_sewa.id_sewa DESC"
);

// Simpan ke array agar bisa di-loop ulang nanti
$transaksi_arr = [];
while($t = mysqli_fetch_assoc($transaksi_list)){
    $transaksi_arr[] = $t;
}

if(isset($_POST['simpan'])){
    $id_sewa            = (int)$_POST['id_sewa'];
    $tgl_kembali_aktual = $_POST['tgl_kembali_aktual'];
    $kondisi            = mysqli_real_escape_string($koneksi, $_POST['kondisi_unit']);
    $denda_rusak        = (int)$_POST['denda_kerusakan'];
    $catatan            = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    $id_admin           = (int)$_SESSION['id_admin'];

    // Ambil data transaksi untuk hitung denda otomatis
    $trx = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT transaksi_sewa.*, stok_iphone.harga_sewa_perhari, stok_iphone.id_iphone
         FROM transaksi_sewa
         JOIN stok_iphone ON stok_iphone.id_iphone = transaksi_sewa.id_iphone
         WHERE id_sewa='$id_sewa'"
    ));

    if(!$trx){
        $error = "Transaksi tidak ditemukan!";
    } else {
        $id_iphone    = $trx['id_iphone'];
        $tgl_kembali_rencana = $trx['tgl_kembali'];
        $harga_perhari       = $trx['harga_sewa_perhari'];

        // BUG FIX #4: Hitung denda keterlambatan OTOMATIS
        $selisih_hari = (int)((strtotime($tgl_kembali_aktual) - strtotime($tgl_kembali_rencana)) / 86400);
        $denda_telat  = $selisih_hari > 0 ? $selisih_hari * $harga_perhari : 0;
        $total_denda  = $denda_telat + $denda_rusak;

        $simpan = mysqli_query($koneksi,
            "INSERT INTO pengembalian (id_sewa, id_admin, tgl_kembali_aktual, kondisi_unit, denda_keterlambatan, denda_kerusakan, total_denda, catatan)
             VALUES ('$id_sewa','$id_admin','$tgl_kembali_aktual','$kondisi','$denda_telat','$denda_rusak','$total_denda','$catatan')"
        );

        if($simpan){
            mysqli_query($koneksi, "UPDATE transaksi_sewa SET status_transaksi='selesai' WHERE id_sewa='$id_sewa'");
            mysqli_query($koneksi, "UPDATE stok_iphone SET status_ketersediaan='tersedia' WHERE id_iphone='$id_iphone'");
            echo "<script>alert('Pengembalian berhasil dicatat!'); window.location='index.php';</script>";
            exit();
        } else {
            $error = "Gagal menyimpan: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Pengembalian — Rental iPhone</title>
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
        <a href="index.php" class="active">🔄 Pengembalian</a>
        <a href="../laporan/index.php">📊 Laporan</a>
        <a href="../auth/logout.php" class="sidebar-logout">🚪 Logout</a>
    </div>

    <div class="main-content">
        <div style="max-width:640px;">
            <div class="page-header">
                <div>
                    <h2>Form Pengembalian</h2>
                    <p class="subtitle">Denda keterlambatan dihitung <strong>otomatis</strong> dari tanggal</p>
                </div>
                <a href="index.php" class="btn btn-secondary">← Kembali</a>
            </div>

            <?php if(isset($error)): ?>
            <div class="alert alert-danger rounded-3"><?= $error ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" id="formKembali">

                        <div class="mb-3">
                            <label class="form-label">Pilih Transaksi Berjalan</label>
                            <select name="id_sewa" class="form-control" id="selectTrx" required onchange="updateInfoTrx()">
                                <option value="">-- Pilih Transaksi --</option>
                                <?php foreach($transaksi_arr as $t):
                                    $sel = ($id_sewa_param == $t['id_sewa']) ? 'selected' : '';
                                ?>
                                <option value="<?= $t['id_sewa']; ?>"
                                    data-tgl="<?= $t['tgl_kembali']; ?>"
                                    data-harga="<?= $t['harga_sewa_perhari']; ?>"
                                    data-pelanggan="<?= htmlspecialchars($t['nama_pelanggan']); ?>"
                                    data-iphone="<?= htmlspecialchars($t['tipe_unit']); ?>"
                                    <?= $sel ?>>
                                    <?= !empty($t['kode_transaksi']) ? $t['kode_transaksi'] : 'TRX-00'.$t['id_sewa']; ?>
                                    — <?= htmlspecialchars($t['nama_pelanggan']); ?> (<?= $t['tipe_unit']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Info transaksi yang dipilih -->
                        <div id="infoTrx" class="alert mb-3" style="display:none; background:rgba(0,150,136,0.07); border:1px solid rgba(0,150,136,0.2); border-radius:12px; padding:14px;">
                            <div class="row g-2">
                                <div class="col-6"><small class="text-muted">Pelanggan</small><div class="fw-bold" id="infoPelanggan">-</div></div>
                                <div class="col-6"><small class="text-muted">Unit iPhone</small><div class="fw-bold" id="infoIphone">-</div></div>
                                <div class="col-6"><small class="text-muted">Jatuh Tempo Kembali</small><div class="fw-bold text-warning" id="infoJatuhTempo">-</div></div>
                                <div class="col-6"><small class="text-muted">Harga/Hari</small><div class="fw-bold" id="infoHarga">-</div></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Kembali Aktual</label>
                                <input type="date" name="tgl_kembali_aktual" id="tglAktual" class="form-control"
                                    value="<?= date('Y-m-d'); ?>" required onchange="hitungDendaOtomatis()">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kondisi Unit</label>
                                <select name="kondisi_unit" class="form-control" required>
                                    <option value="mulus">✅ Mulus</option>
                                    <option value="lecet ringan">⚠️ Lecet Ringan</option>
                                    <option value="rusak">❌ Rusak</option>
                                    <option value="hilang">🚫 Hilang</option>
                                </select>
                            </div>
                        </div>

                        <!-- Preview denda otomatis -->
                        <div id="previewDenda" class="alert mb-3" style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.25); border-radius:12px; padding:14px;">
                            <div class="row text-center g-2">
                                <div class="col-4">
                                    <small class="text-muted d-block">Keterlambatan</small>
                                    <strong id="prevTelat" class="text-warning">0 hari</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Denda Telat (otomatis)</small>
                                    <strong id="prevDendaTelat" class="text-danger">Rp 0</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Total Denda</small>
                                    <strong id="prevTotal" style="color:var(--primary);">Rp 0</strong>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Denda Kerusakan (Rp) <small class="text-muted">— isi jika ada</small></label>
                            <input type="number" name="denda_kerusakan" id="dendaRusak" class="form-control" value="0" min="0" oninput="hitungDendaOtomatis()" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Catatan Tambahan</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Keterangan kondisi atau catatan lainnya..."></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="simpan" class="btn btn-custom flex-grow-1 py-3">✅ Simpan Pengembalian</button>
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
let tglJatuhTempo = '';

function updateInfoTrx(){
    const sel = document.getElementById('selectTrx');
    const opt = sel.options[sel.selectedIndex];
    if(!opt.value){ document.getElementById('infoTrx').style.display='none'; return; }

    hargaPerHari  = parseInt(opt.getAttribute('data-harga')) || 0;
    tglJatuhTempo = opt.getAttribute('data-tgl') || '';

    document.getElementById('infoPelanggan').textContent  = opt.getAttribute('data-pelanggan');
    document.getElementById('infoIphone').textContent     = opt.getAttribute('data-iphone');
    document.getElementById('infoJatuhTempo').textContent = formatTgl(tglJatuhTempo);
    document.getElementById('infoHarga').textContent      = 'Rp ' + hargaPerHari.toLocaleString('id-ID');
    document.getElementById('infoTrx').style.display      = 'block';
    hitungDendaOtomatis();
}

function hitungDendaOtomatis(){
    const tglAktual = document.getElementById('tglAktual').value;
    const dendaRusak = parseInt(document.getElementById('dendaRusak').value) || 0;

    if(tglJatuhTempo && tglAktual){
        const selisih = Math.max(0, Math.ceil((new Date(tglAktual) - new Date(tglJatuhTempo)) / 86400000));
        const dendaTelat = selisih * hargaPerHari;
        const totalDenda = dendaTelat + dendaRusak;

        document.getElementById('prevTelat').textContent      = selisih + ' hari' + (selisih > 0 ? ' ⚠️' : ' ✅');
        document.getElementById('prevDendaTelat').textContent = 'Rp ' + dendaTelat.toLocaleString('id-ID');
        document.getElementById('prevTotal').textContent      = 'Rp ' + totalDenda.toLocaleString('id-ID');
    }
}

function formatTgl(d){
    if(!d) return '-';
    const [y,m,t] = d.split('-');
    return t+'/'+m+'/'+y;
}

// Jalankan otomatis jika halaman dibuka dari link "Kembalikan"
window.addEventListener('load', function(){
    const sel = document.getElementById('selectTrx');
    if(sel.value) updateInfoTrx();
});
</script>
</body>
</html>
