<?php
session_start();
if(!isset($_SESSION['login'])) exit();
include "../config/koneksi.php";

$data = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY id_pelanggan DESC");

while($row = mysqli_fetch_assoc($data)){ ?>
<tr>
    <td><?= $row['id_pelanggan']; ?></td>
    <td class="fw-semibold text-dark"><?= $row['nama_pelanggan']; ?></td>
    <td><code class="text-muted"><?= $row['nik_ktp']; ?></code></td>
    <td><a href="https://wa.me/<?= $row['no_wa']; ?>" target="_blank" class="text-decoration-none text-success fw-medium"><?= $row['no_wa']; ?> ↗</a></td>
    <td><?= $row['alamat']; ?></td>
    <td>
        <div class="d-flex justify-content-center gap-2">
            <a href="edit.php?id=<?= $row['id_pelanggan']; ?>" class="btn btn-warning btn-sm px-3 rounded-3 fw-semibold text-dark">Edit</a>
            <a href="hapus.php?id=<?= $row['id_pelanggan']; ?>" class="btn btn-danger btn-sm px-3 rounded-3" onclick="return confirm('Yakin hapus data?')">Hapus</a>
        </div>
    </td>
</tr>
<?php } ?>