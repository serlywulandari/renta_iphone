<?php
include "../config/koneksi.php";

// Query join untuk menarik data dari transaksi, pelanggan, dan stok iphone secara berurutan (No 1 di atas)
$query = mysqli_query(
    $koneksi,
    "SELECT transaksi_sewa.*, pelanggan.nama_pelanggan, stok_iphone.tipe_unit 
     FROM transaksi_sewa 
     JOIN pelanggan ON pelanggan.id_pelanggan = transaksi_sewa.id_pelanggan
     JOIN stok_iphone ON stok_iphone.id_iphone = transaksi_sewa.id_iphone
     ORDER BY transaksi_sewa.id_sewa ASC"
);

if (mysqli_num_rows($query) == 0) {
    echo "<tr><td colspan='8' class='text-center text-muted py-4'>Tidak ada data transaksi masuk.</td></tr>";
} else {
    while ($row = mysqli_fetch_assoc($query)) {
        // Logika untuk menampilkan nominal denda dari tabel pengembalian jika statusnya sudah 'selesai'
        $id_sewa = $row['id_sewa'];
        $cek_denda = mysqli_query($koneksi, "SELECT total_denda FROM pengembalian WHERE id_sewa = '$id_sewa'");
        $denda_data = mysqli_fetch_assoc($cek_denda);
        $denda = isset($denda_data['total_denda']) ? $denda_data['total_denda'] : 0;
        ?>
        <tr>
            <td><strong><?= !empty($row['kode_transaksi']) ? $row['kode_transaksi'] : 'TRX-00' . $row['id_sewa']; ?></strong></td>
            <td><?= $row['nama_pelanggan']; ?></td>
            <td><?= $row['tipe_unit']; ?></td>
            <td><?= $row['tgl_mulai']; ?></td>
            <td><?= $row['tgl_kembali']; ?></td>
            <td class="text-success fw-medium">Rp <?= number_format($row['total_bayar']); ?></td>
            <td class="<?= ($denda > 0) ? 'text-danger fw-bold' : 'text-muted'; ?>">Rp <?= number_format($denda); ?></td>
            <td>
                <span class="badge <?= ($row['status_transaksi'] == 'selesai') ? 'bg-success' : 'bg-primary'; ?>">
                    <?= $row['status_transaksi']; ?>
                </span>
            </td>
        </tr>
        <?php
    }
}
?>