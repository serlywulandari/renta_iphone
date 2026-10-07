<?php
session_start();
if(!isset($_SESSION['login'])){
    exit("Unauthorized");
}

include "../config/koneksi.php";

// Hitung total pendapatan sewa
$total_sewa_query = mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM transaksi_sewa");
$total_pendapatan_sewa = $total_sewa_query ? mysqli_fetch_assoc($total_sewa_query)['total'] : 0;

// Hitung denda
$total_denda_query = mysqli_query($koneksi, "SELECT SUM(total_denda) as total FROM pengembalian");
$total_denda = $total_denda_query ? mysqli_fetch_assoc($total_denda_query)['total'] : 0;

$keuntungan_bersih = $total_pendapatan_sewa + $total_denda;

// Satukan data dalam format array JSON agar mudah dibaca oleh JavaScript
echo json_encode([
    'sewa' => "Rp " . number_format($total_pendapatan_sewa),
    'denda' => "Rp " . number_format($total_denda),
    'bersih' => "Rp " . number_format($keuntungan_bersih)
]);
exit();
?>