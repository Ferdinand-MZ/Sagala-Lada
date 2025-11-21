<?php

$host     = "localhost";
$user     = "root";        // ubah jika pakai user lain
$pass     = "";            // isi password MySQL jika ada
$database = "sagala_lada";

$koneksi = mysqli_connect($host, $user, $pass, $database);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// optional: set charset utf8mb4
mysqli_set_charset($koneksi, "utf8mb4");
?>