<?php
session_start();
if(!isset($_SESSION['login'])) exit();
include "../config/koneksi.php";

$data = mysqli_query($koneksi, "SELECT * FROM stok_iphone ORDER BY id_iphone DESC");

while($row = mysqli_fetch_assoc($data)){ 
    $status_real = isset($row['status_ketersediaan']) ? $row['status_ketersediaan'] : (isset($row['status']) ? $row['status'] : 'tersedia');
?>
<tr>
    <td><?= $row['id_iphone']; ?></td>
    <td class="fw-semibold text-dark"><?= $row['tipe_unit']; ?></td>
    <td><code class="text-muted"><?= $row['no_seri']; ?></code></td>
    <td><?= $row['warna']; ?></td>
    <td><span class="badge bg-secondary text-white"><?= $row['kapasitas_gb']; ?> GB</span></td>
    <td><strong class="text-success">Rp <?= number_format($row['harga_sewa_perhari']); ?></strong></td>
    <td>
        <span class="badge <?= $status_real == 'tersedia' ? 'bg-success' : 'bg-primary'; ?>">
            <?= $status_real; ?>
        </span>
    </td>
    <td>
        <div class="d-flex justify-content-center gap-2">
            <a href="edit.php?id=<?= $row['id_iphone']; ?>" class="btn btn-warning btn-sm px-3 rounded-3 fw-semibold text-dark">Edit</a>
            <a href="hapus.php?id=<?= $row['id_iphone']; ?>" class="btn btn-danger btn-sm px-3 rounded-3" onclick="return confirm('Yakin hapus data?')">Hapus</a>
        </div>
    </td>
</tr>
<?php } ?>