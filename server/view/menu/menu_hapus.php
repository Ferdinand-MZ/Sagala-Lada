<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

// Cek login & role owner
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: /login.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$id_menu = (int)($_GET['id'] ?? 0);

if ($id_menu <= 0) {
    header("Location: menu.php?msg=invalid");
    exit;
}

// Fungsi log
function catatLog($koneksi, $id_user, $aktivitas, $keterangan = null) {
    $aktivitas  = mysqli_real_escape_string($koneksi, $aktivitas);
    $keterangan = $keterangan ? mysqli_real_escape_string($koneksi, $keterangan) : 'NULL';
    $sql = "INSERT INTO log (id_user, aktivitas, keterangan, created_at) 
            VALUES ($id_user, '$aktivitas', '$keterangan', NOW())";
    mysqli_query($koneksi, $sql);
}

// Ambil data menu sebelum dihapus (untuk log detail)
$menu = mysqli_fetch_assoc(mysqli_query($koneksi, 
    "SELECT nama_menu, jenis, harga, gambar FROM menu WHERE id_menu = $id_menu"
));

if (!$menu) {
    catatLog($koneksi, $id_user, 'Gagal hapus menu', "Menu ID $id_menu tidak ditemukan");
    header("Location: menu.php?msg=gagal");
    exit;
}

// Hapus gambar fisik jika ada
if (!empty($menu['gambar']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/img/menu/' . $menu['gambar'])) {
    unlink($_SERVER['DOCUMENT_ROOT'] . '/assets/img/menu/' . $menu['gambar']);
}

// Hapus dari database
$hapus = mysqli_query($koneksi, "DELETE FROM menu WHERE id_menu = $id_menu");

if ($hapus) {
    catatLog($koneksi, $id_user, 'Hapus menu', 
        "Menghapus: {$menu['nama_menu']} | Jenis: {$menu['jenis']} | Harga: Rp " . 
        number_format($menu['harga'], 0, ',', '.') . " | Gambar: " . ($menu['gambar'] ?: 'tidak ada')
    );
    header("Location: menu.php?msg=hapus_sukses");
} else {
    catatLog($koneksi, $id_user, 'Gagal hapus menu', "ID: $id_menu (error database)");
    header("Location: menu.php?msg=hapus_gagal");
}
exit;
?>