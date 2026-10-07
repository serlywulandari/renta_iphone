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
mysqli_query($koneksi, "DELETE FROM pelanggan WHERE id_pelanggan='$id'");

echo "<script>alert('Data pelanggan berhasil dihapus!'); window.location='index.php';</script>";
exit();
?>
