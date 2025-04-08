<?php
// Memanggil koneksi database
session_start();
include 'koneksi.php';

$username = mysqli_real_escape_string($config, $_POST["username"]);
$password = mysqli_real_escape_string($config, $_POST["password"]);

// Pertama, periksa apakah username ada
$check_username = mysqli_query($config, "SELECT * FROM tb_user WHERE username='$username'");
$username_exists = mysqli_num_rows($check_username);

if ($username_exists > 0) {
    // Username ditemukan, sekarang periksa password
    $user = mysqli_query($config, "SELECT * FROM tb_user WHERE username='$username' AND password='$password'");
    $cek = mysqli_num_rows($user);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($user);

        if ($data['role'] == "owner") {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = "owner";
            header("location:owner/owner.php");
            exit();
        } else if ($data['role'] == "admin") {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = "admin";
            header("location:admin/admin.php");
            exit();
        } else if ($data['role'] == "petugas") {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = "petugas";
            header("location:petugas/petugas.php");
            exit();
        }
    } else {
        echo "<script>alert('Maaf, login gagal. Password tidak sesuai.'); document.location='index.php'</script>";
    }
} else {
    echo "<script>alert('Maaf, login gagal. Username tidak terdaftar.'); document.location='index.php'</script>";
}
