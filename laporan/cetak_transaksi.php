<?php
session_start();
// BUG FIX: laporan cetak harus ada session check agar tidak bisa diakses langsung
if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/koneksi.php";

$tgl_awal  = mysqli_real_escape_string($koneksi, $_GET['tgl_awal']  ?? '');
$tgl_akhir = mysqli_real_escape_string($koneksi, $_GET['tgl_akhir'] ?? '');

if (!$tgl_awal || !$tgl_akhir) {
    die("Tanggal tidak valid.");
}

$data = mysqli_query(
    $koneksi,
    "SELECT transaksi_sewa.*, pelanggan.nama_pelanggan, stok_iphone.tipe_unit
     FROM transaksi_sewa
     JOIN pelanggan ON pelanggan.id_pelanggan = transaksi_sewa.id_pelanggan
     JOIN stok_iphone ON stok_iphone.id_iphone = transaksi_sewa.id_iphone
     WHERE tgl_mulai BETWEEN '$tgl_awal' AND '$tgl_akhir'
     ORDER BY tgl_mulai ASC"
);

$rows    = [];
$total   = 0;
while ($r = mysqli_fetch_assoc($data)) {
    $rows[]  = $r;
    $total  += $r['total_bayar'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fff;
        }

        .header-laporan {
            text-align: center;
            margin-bottom: 24px;
            border-bottom: 2px solid #009688;
            padding-bottom: 16px;
        }

        .header-laporan h3 {
            color: #009688;
            font-weight: 800;
            margin: 0;
        }

        .table th {
            background: #1a1a2e;
            color: white;
        }

        .total-row td {
            font-weight: 800;
            background: #f0faf9;
            color: #009688;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="p-4">

    <div class="no-print mb-3 d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary">🖨 Cetak</button>
        <a href="index.php" class="btn btn-secondary">← Kembali</a>
    </div>

    <div class="header-laporan">
        <h3>LAPORAN TRANSAKSI RENTAL IPHONE</h3>
        <p class="mb-0 text-muted">Periode: <?= date('d/m/Y', strtotime($tgl_awal)); ?> s/d <?= date('d/m/Y', strtotime($tgl_akhir)); ?></p>
        <small class="text-muted">Dicetak pada: <?= date('d/m/Y H:i'); ?></small>
    </div>

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th>iPhone</th>
                <th>Tgl Mulai</th>
                <th>Tgl Kembali</th>
                <th>Jaminan</th>
                <th>Total Bayar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $i => $row) { ?>
                <tr>
                    <td><?= $i + 1; ?></td>
                    <td><?= $row['kode_transaksi']; ?></td>
                    <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                    <td><?= htmlspecialchars($row['tipe_unit']); ?></td>
                    <td><?= $row['tgl_mulai']; ?></td>
                    <td><?= $row['tgl_kembali']; ?></td>
                    <td><?= $row['jaminan_identitas']; ?></td>
                    <td>Rp <?= number_format($row['total_bayar']); ?></td>
                    <td><?= ucfirst($row['status_transaksi']); ?></td>
                </tr>
            <?php } ?>
            <tr class="total-row">
                <td colspan="7" class="text-end">Total Pendapatan:</td>
                <td>Rp <?= number_format($total); ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <p class="text-muted small mt-3 text-center">Sistem Manajemen Rental iPhone — UAS Basis Data, Prodi Sistem Informasi</p>

</body>

</html>