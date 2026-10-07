<?php

session_start();

include "../config/koneksi.php";

if(!isset($_POST['username']) || !isset($_POST['password'])){
    header("Location: login.php");
    exit();
}

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = $_POST['password'];

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM admin WHERE username='$username'"
);

$data = mysqli_fetch_assoc($query);

// BUG FIX: Cek password juga, tidak cukup hanya cek username
if($data && $data['password'] === $password){

    $_SESSION['login']      = true;
    $_SESSION['id_admin']   = $data['id_admin'];
    $_SESSION['nama_admin'] = $data['nama_admin'];

    header("Location: ../index.php");
    exit();

} else {

    echo "
    <script>
    alert('Username atau Password salah!');
    location='login.php';
    </script>
    ";

}
?>
