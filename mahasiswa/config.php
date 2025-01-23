<?php
$host ="localhost";
$username ="root";
$password ="";
$database ="db_kampus";

$config = mysqli_connect($host, $username, $password, $database);

// Periksa koneksi
if (!$config) {
    die("Gagal koneksi ke database: " . mysqli_connect_error());
}
?>