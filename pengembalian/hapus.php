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

$cari = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT pengembalian.id_sewa, transaksi_sewa.id_iphone
     FROM pengembalian
     JOIN transaksi_sewa ON transaksi_sewa.id_sewa = pengembalian.id_sewa
     WHERE id_pengembalian='$id'"
));

if($cari){
    $id_sewa   = $cari['id_sewa'];
    $id_iphone = $cari['id_iphone'];

    mysqli_query($koneksi, "DELETE FROM pengembalian WHERE id_pengembalian='$id'");
    mysqli_query($koneksi, "UPDATE transaksi_sewa SET status_transaksi='berjalan' WHERE id_sewa='$id_sewa'");
    mysqli_query($koneksi, "UPDATE stok_iphone SET status_ketersediaan='disewa' WHERE id_iphone='$id_iphone'");
}

echo "<script>alert('Data pengembalian dihapus, status unit dikembalikan ke disewa.'); window.location='index.php';</script>";
exit();
?>
