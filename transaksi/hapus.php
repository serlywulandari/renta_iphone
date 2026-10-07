<?php
session_start();
if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit();
}
include "../config/koneksi.php";

if(!isset($_GET['id'])){
    header("Location: index.php");
    exit();
}

$id   = mysqli_real_escape_string($koneksi, $_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM transaksi_sewa WHERE id_sewa='$id'"));

if(!$data){
    echo "<script>alert('Data tidak ditemukan!'); window.location='index.php';</script>";
    exit();
}

// BUG FIX #5: hapus data pengembalian terkait dulu sebelum hapus transaksi
// agar tidak error FOREIGN KEY constraint
$cek_kembali = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(*) as total FROM pengembalian WHERE id_sewa='$id'"
));

if($cek_kembali['total'] > 0){
    // Hapus pengembalian terkait dulu
    mysqli_query($koneksi, "DELETE FROM pengembalian WHERE id_sewa='$id'");
}

$id_iphone = $data['id_iphone'];

mysqli_query($koneksi, "DELETE FROM transaksi_sewa WHERE id_sewa='$id'");

// Kembalikan status iphone ke tersedia hanya jika transaksi masih berjalan
if($data['status_transaksi'] === 'berjalan'){
    mysqli_query($koneksi, "UPDATE stok_iphone SET status_ketersediaan='tersedia' WHERE id_iphone='$id_iphone'");
}

echo "<script>alert('Transaksi berhasil dihapus!'); window.location='index.php';</script>";
exit();
?>
