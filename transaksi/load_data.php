<?php
session_start();
if(!isset($_SESSION['login'])) exit();
include "../config/koneksi.php";

$query = mysqli_query($koneksi, 
    "SELECT transaksi_sewa.*, pelanggan.nama_pelanggan, stok_iphone.tipe_unit 
     FROM transaksi_sewa 
     JOIN pelanggan ON pelanggan.id_pelanggan = transaksi_sewa.id_pelanggan 
     JOIN stok_iphone ON stok_iphone.id_iphone = transaksi_sewa.id_iphone 
     ORDER BY id_sewa DESC"
);

while($row = mysqli_fetch_assoc($query)){ ?>
<tr>
    <td><?= $row['id_sewa']; ?></td>
    <td><strong><?= $row['kode_transaksi']; ?></strong></td> 
    <td><?= $row['nama_pelanggan']; ?></td>
    <td><?= $row['tipe_unit']; ?></td>
    <td><?= $row['tgl_mulai']; ?></td>
    <td><?= $row['tgl_kembali']; ?></td>
    <td><?= $row['jaminan_identitas']; ?></td> 
    <td><strong class="text-success">Rp <?= number_format($row['total_bayar']); ?></strong></td>
    <td>
        <span class="badge <?= $row['status_transaksi'] == 'selesai' ? 'bg-success' : 'bg-primary'; ?>">
            <?= $row['status_transaksi']; ?>
        </span>
    </td>
    <td>
        <a href="hapus.php?id=<?= $row['id_sewa']; ?>" class="btn btn-danger btn-sm px-3 rounded-3" onclick="return confirm('Hapus transaksi?')">Hapus</a>
    </td>
</tr>
<?php } ?>