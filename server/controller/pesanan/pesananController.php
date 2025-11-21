<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: /login.php");
    exit;
}

/* ==============================================
   1. HAPUS PESANAN (termasuk detail & pembayaran otomatis terhapus karena ON DELETE CASCADE)
   ============================================== */
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $del = mysqli_query($koneksi, "DELETE FROM pesanan WHERE id_pesanan = $id");
    header("Location: /server/view/pesanan/pesanan.php?msg=" . ($del ? 'hapus_sukses' : 'hapus_gagal'));
    exit;
}

/* ==============================================
   2. TAMBAH PESANAN BARU
   ============================================== */
if (isset($_POST['tambah_pesanan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_pelanggan']);
    $meja = !empty($_POST['no_meja']) ? "'" . mysqli_real_escape_string($koneksi, $_POST['no_meja']) . "'" : "NULL";

    $sql = "INSERT INTO pesanan (nama_pelanggan, no_meja, status, total) 
            VALUES ('$nama', $meja, 'Pending', 0)";
    if (mysqli_query($koneksi, $sql)) {
        $id_pesanan = mysqli_insert_id($koneksi);
        header("Location: /server/view/pesanan/tambah_item.php?id=$id_pesanan");
    } else {
        header("Location: /server/view/pesanan/pesanan.php?msg=tambah_gagal");
    }
    exit;
}

/* ==============================================
   3. TAMBAH ITEM KE PESANAN (di halaman tambah_item.php)
   ============================================== */
if (isset($_POST['tambah_item'])) {
    $id_pesanan = (int)$_POST['id_pesanan'];
    $id_menu    = (int)$_POST['id_menu'];
    $jumlah     = (int)$_POST['jumlah'];

    $menu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT harga FROM menu WHERE id_menu = $id_menu"));
    $subtotal = $menu['harga'] * $jumlah;

    $sql = "INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, subtotal) 
            VALUES ($id_pesanan, $id_menu, $jumlah, $subtotal)";
    
    if (mysqli_query($koneksi, $sql)) {
        // Update total pesanan otomatis
        mysqli_query($koneksi, "UPDATE pesanan SET total = (
            SELECT COALESCE(SUM(subtotal), 0) FROM detail_pesanan WHERE id_pesanan = $id_pesanan
        ) WHERE id_pesanan = $id_pesanan");
    }
    header("Location: /server/view/pesanan/tambah_item.php?id=$id_pesanan&msg=item_ditambah");
    exit;
}

/* ==============================================
   4. HAPUS ITEM DARI PESANAN
   ============================================== */
if (isset($_GET['hapus_item'])) {
    $id_detail  = (int)$_GET['hapus_item'];
    $id_pesanan = (int)$_GET['id_pesanan'];

    $del = mysqli_query($koneksi, "DELETE FROM detail_pesanan WHERE id_detail = $id_detail");
    if ($del) {
        mysqli_query($koneksi, "UPDATE pesanan SET total = (
            SELECT COALESCE(SUM(subtotal), 0) FROM detail_pesanan WHERE id_pesanan = $id_pesanan
        ) WHERE id_pesanan = $id_pesanan");
    }
    header("Location: /server/view/pesanan/tambah_item.php?id=$id_pesanan");
    exit;
}

/* ==============================================
   5. PROSES PEMBAYARAN & SELESAIKAN PESANAN
   ============================================== */
if (isset($_POST['proses_bayar'])) {
    $id_pesanan   = (int)$_POST['id_pesanan'];
    $metode       = $_POST['metode_bayar'];
    $jumlah_bayar = (float)preg_replace('/\D/', '', $_POST['jumlah_bayar']);

    $pesanan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT total FROM pesanan WHERE id_pesanan = $id_pesanan"));

    if ($jumlah_bayar >= $pesanan['total']) {
        // Simpan pembayaran
        $sql_bayar = "INSERT INTO pembayaran (id_pesanan, metode_bayar, jumlah_bayar) 
                      VALUES ($id_pesanan, '$metode', $jumlah_bayar)";
        mysqli_query($koneksi, $sql_bayar);

        // Ubah status jadi Selesai
        mysqli_query($koneksi, "UPDATE pesanan SET status = 'Selesai' WHERE id_pesanan = $id_pesanan");

        header("Location: /server/view/pesanan/pesanan.php?msg=bayar_sukses");
    } else {
        header("Location: /server/view/pesanan/tambah_item.php?id=$id_pesanan&msg=bayar_kurang");
    }
    exit;
}

/* ==============================================
   EDIT PESANAN (nama & meja saja)
   ============================================== */
if (isset($_POST['edit_pesanan'])) {
    $id     = (int)$_POST['id_pesanan'];
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama_pelanggan']);
    $meja   = !empty($_POST['no_meja']) ? "'" . mysqli_real_escape_string($koneksi, $_POST['no_meja']) . "'" : "NULL";

    $sql = "UPDATE pesanan SET nama_pelanggan = '$nama', no_meja = $meja WHERE id_pesanan = $id";
    $ok = mysqli_query($koneksi, $sql);

    header("Location: /server/view/pesanan/pesanan.php?msg=" . ($ok ? 'edit_sukses' : 'edit_gagal'));
    exit;
}

/* ==============================================
   6. BATALKAN PESANAN
   ============================================== */
if (isset($_GET['batal'])) {
    $id = (int)$_GET['batal'];
    $ok = mysqli_query($koneksi, "UPDATE pesanan SET status = 'Dibatalkan' WHERE id_pesanan = $id");
    header("Location: /server/view/pesanan/pesanan.php?msg=" . ($ok ? 'batal_sukses' : 'batal_gagal'));
    exit;
}

/* ==============================================
   Jika tidak ada aksi → kembali ke daftar pesanan
   ============================================== */
header("Location: /server/view/pesanan/pesanan.php");
exit;
?>