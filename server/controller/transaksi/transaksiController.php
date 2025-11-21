<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: /login.php");
    exit;
}

if (isset($_POST['bayar'])) {
    $id_pesanan   = (int)$_POST['id_pesanan'];
    $metode       = $_POST['metode_bayar'];
    $jumlah_bayar = (int)preg_replace('/\D/', '', $_POST['jumlah_bayar']);

    // Cek total pesanan
    $pesanan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT total FROM pesanan WHERE id_pesanan = $id_pesanan"));

    if ($jumlah_bayar >= $pesanan['total']) {
        // Simpan pembayaran
        $sql = "INSERT INTO pembayaran (id_pesanan, metode_bayar, jumlah_bayar) 
                VALUES ($id_pesanan, '$metode', $jumlah_bayar)";
        mysqli_query($koneksi, $sql);

        // Update status jadi Selesai
        mysqli_query($koneksi, "UPDATE pesanan SET status = 'Selesai' WHERE id_pesanan = $id_pesanan");

        $msg = "bayar_sukses";
    } else {
        $msg = "bayar_kurang";
    }
    header("Location: /server/view/pesanan/pesanan.php?msg=$msg");
    exit;
}

header("Location: /server/view/pesanan/pesanan.php");
exit;
?>