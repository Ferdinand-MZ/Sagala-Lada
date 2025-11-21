<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';
if (!isset($_GET['id'])) { header("Location: pesanan.php"); exit; }
$id = (int)$_GET['id'];

// Cek apakah boleh dihapus (hanya Pending atau Dibatalkan)
$cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT status FROM pesanan WHERE id_pesanan = $id"));
if (!$cek || !in_array($cek['status'], ['Pending','Dibatalkan'])) {
    header("Location: pesanan.php?msg=hapus_gagal"); exit;
}

// Hapus (ON DELETE CASCADE akan hapus detail_pesanan & pembayaran otomatis)
mysqli_query($koneksi, "DELETE FROM pesanan WHERE id_pesanan = $id");

header("Location: pesanan.php?msg=hapus_sukses");
exit;
?>