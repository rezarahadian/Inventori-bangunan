<?php
session_start();
session_destroy(); // Menghapus semua data sesi
header(header: "location:../login.php"); // Kembali ke login.php
exit();
?>
