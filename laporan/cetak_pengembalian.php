<?php
session_start();
// BUG FIX: harus ada session check
if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit();
}

include "../config/koneksi.php";

$tgl_awal  = mysqli_real_escape_string($koneksi, $_GET['tgl_awal']  ?? '');
$tgl_akhir = mysqli_real_escape_string($koneksi, $_GET['tgl_akhir'] ?? '');

if(!$tgl_awal || !$tgl_akhir){
    die("Tanggal tidak valid.");
}

$query = mysqli_query($koneksi,
    "SELECT pengembalian.*, pelanggan.nama_pelanggan, stok_iphone.tipe_unit, transaksi_sewa.kode_transaksi
     FROM pengembalian
     JOIN transaksi_sewa ON transaksi_sewa.id_sewa = pengembalian.id_sewa
     JOIN pelanggan ON pelanggan.id_pelanggan = transaksi_sewa.id_pelanggan
     JOIN stok_iphone ON stok_iphone.id_iphone = transaksi_sewa.id_iphone
     WHERE tgl_kembali_aktual BETWEEN '$tgl_awal' AND '$tgl_akhir'
     ORDER BY tgl_kembali_aktual ASC"
);

$rows         = [];
$total_denda  = 0;
while($r = mysqli_fetch_assoc($query)){
    $rows[]      = $r;
    $total_denda += $r['total_denda'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Pengembalian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #fff; }
        .header-laporan { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #009688; padding-bottom: 16px; }
        .header-laporan h3 { color: #009688; font-weight: 800; margin: 0; }
        .table th { background: #1a1a2e; color: white; }
        .total-row td { font-weight: 800; background: #fff5f5; color: #ef4444; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body class="p-4">

<div class="no-print mb-3 d-flex gap-2">
    <button onclick="window.print()" class="btn btn-primary">🖨 Cetak</button>
    <a href="index.php" class="btn btn-secondary">← Kembali</a>
</div>

<div class="header-laporan">
    <h3>LAPORAN PENGEMBALIAN IPHONE</h3>
    <p class="mb-0 text-muted">Periode: <?= date('d/m/Y', strtotime($tgl_awal)); ?> s/d <?= date('d/m/Y', strtotime($tgl_akhir)); ?></p>
    <small class="text-muted">Dicetak pada: <?= date('d/m/Y H:i'); ?></small>
</div>

<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Transaksi</th>
            <th>Pelanggan</th>
            <th>iPhone</th>
            <th>Tgl Kembali</th>
            <th>Kondisi</th>
            <th>Denda Telat</th>
            <th>Denda Kerusakan</th>
            <th>Total Denda</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($rows as $i => $row){ ?>
        <tr>
            <td><?= $i+1; ?></td>
            <td><?= $row['kode_transaksi']; ?></td>
            <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
            <td><?= htmlspecialchars($row['tipe_unit']); ?></td>
            <td><?= $row['tgl_kembali_aktual']; ?></td>
            <td><?= ucfirst($row['kondisi_unit']); ?></td>
            <td>Rp <?= number_format($row['denda_keterlambatan']); ?></td>
            <td>Rp <?= number_format($row['denda_kerusakan']); ?></td>
            <td>Rp <?= number_format($row['total_denda']); ?></td>
        </tr>
        <?php } ?>
        <tr class="total-row">
            <td colspan="8" class="text-end">Total Denda Terkumpul:</td>
            <td>Rp <?= number_format($total_denda); ?></td>
        </tr>
    </tbody>
</table>

<p class="text-muted small mt-3 text-center">Sistem Manajemen Rental iPhone — UAS Basis Data, Prodi Sistem Informasi</p>

</body>
</html>
