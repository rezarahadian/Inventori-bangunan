<?php
$host ="localhost";
$username ="root";
$password ="";
$database ="db_inventori";

$config = mysqli_connect($host, $username, $password, $database);

// Periksa koneksi
if (!$config) {
    die("Gagal koneksi ke database: " . mysqli_connect_error());
}
?>