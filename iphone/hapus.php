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

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// BUG FIX #2: Cek dulu apakah unit sedang disewa sebelum hapus
$cek = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT status_ketersediaan, tipe_unit FROM stok_iphone WHERE id_iphone='$id'"
));

if(!$cek){
    echo "<script>alert('Data tidak ditemukan!'); window.location='index.php';</script>";
    exit();
}

if($cek['status_ketersediaan'] === 'disewa'){
    echo "<script>alert('Gagal! Unit \"" . addslashes($cek['tipe_unit']) . "\" sedang dalam status DISEWA.\\nHapus tidak dapat dilakukan.'); window.location='index.php';</script>";
    exit();
}

// Cek juga apakah ada transaksi aktif yang referensi unit ini
$cek_transaksi = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(*) as total FROM transaksi_sewa WHERE id_iphone='$id' AND status_transaksi='berjalan'"
));

if($cek_transaksi['total'] > 0){
    echo "<script>alert('Gagal! Unit ini masih memiliki transaksi berjalan.'); window.location='index.php';</script>";
    exit();
}

mysqli_query($koneksi, "DELETE FROM stok_iphone WHERE id_iphone='$id'");
echo "<script>alert('Data iPhone berhasil dihapus!'); window.location='index.php';</script>";
exit();
?>
