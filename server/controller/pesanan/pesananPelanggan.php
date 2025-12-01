<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

// Pastikan ada kirim pesanan dan keranjang tidak kosong
if (!isset($_POST['kirim_pesanan']) || empty($_SESSION['keranjang'])) {
    header("Location: /pesan.php");
    exit;
}

$nama    = trim($_POST['nama_pelanggan']);
$no_meja = trim($_POST['no_meja'] ?? '');

if ($nama === '') {
    header("Location: /pesan.php");
    exit;
}

// 1. Buat record pesanan
$meja_sql = $no_meja !== '' ? "'" . mysqli_real_escape_string($koneksi, $no_meja) . "'" : 'NULL';

$insert = mysqli_query($koneksi, "INSERT INTO pesanan 
    (nama_pelanggan, no_meja, tanggal_pesan, status, total) 
    VALUES ('" . mysqli_real_escape_string($koneksi, $nama) . "', $meja_sql, NOW(), 'Pending', 0)");

if (!$insert) {
    die("Gagal membuat pesanan: " . mysqli_error($koneksi));
}

$id_pesanan    = mysqli_insert_id($koneksi);
$total_pesanan = 0;

// 2. Masukkan semua item keranjang ke detail_pesanan
foreach ($_SESSION['keranjang'] as $id_menu => $item) {
    $id_menu  = (int)$id_menu;
    $jumlah   = (int)$item['jumlah'];
    $harga    = (float)$item['harga'];
    $subtotal = $harga * $jumlah;
    $total_pesanan += $subtotal;

    // Query yang benar (ada typo sebelumnya!)
    $sql_detail = "INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, subtotal) 
                   VALUES ($id_pesanan, $id_menu, $jumlah, $subtotal)";
    
    if (!mysqli_query($koneksi, $sql_detail)) {
        die("Gagal simpan detail: " . mysqli_error($koneksi));
    }
}

// 3. Update total pesanan
mysqli_query($koneksi, "UPDATE pesanan SET total = $total_pesanan WHERE id_pesanan = $id_pesanan");

// 4. Kosongkan keranjang
unset($_SESSION['keranjang']);

// 5. Redirect ke struk
header("Location: /struk.php?id=$id_pesanan");
exit;
?>