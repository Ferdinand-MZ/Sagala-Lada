<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';

// Hanya admin yang boleh hapus
role_required(['admin']);

if (!isset($_GET['id'])) {
    header("Location: pesanan.php");
    exit;
}

$id = (int)$_GET['id'];
$id_user = $_SESSION['user_id'];

// === FUNGSI LOG ===
function catatLog($koneksi, $id_user, $aktivitas, $keterangan = null) {
    $aktivitas  = mysqli_real_escape_string($koneksi, $aktivitas);
    $keterangan = $keterangan ? mysqli_real_escape_string($koneksi, $keterangan) : null;
    $sql = "INSERT INTO log (id_user, aktivitas, keterangan, created_at) 
            VALUES ($id_user, '$aktivitas', ".($keterangan ? "'$keterangan'" : "NULL").", NOW())";
    mysqli_query($koneksi, $sql);
}

// Ambil data pesanan sebelum dihapus (untuk log detail)
$pesanan = mysqli_fetch_assoc(mysqli_query($koneksi, 
    "SELECT nama_pelanggan, no_meja, total, status 
     FROM pesanan 
     WHERE id_pesanan = $id"
));

if (!$pesanan) {
    catatLog($koneksi, $id_user, 'Gagal hapus pesanan', "ID: $id (pesanan tidak ditemukan)");
    header("Location: pesanan.php?msg=hapus_gagal");
    exit;
}

// Cek status: hanya boleh hapus jika Pending atau Dibatalkan
if (!in_array($pesanan['status'], ['Pending', 'Dibatalkan'])) {
    catatLog($koneksi, $id_user, 'Gagal hapus pesanan', 
        "ID: $id | Status: {$pesanan['status']} (tidak diizinkan hapus)"
    );
    header("Location: pesanan.php?msg=hapus_gagal");
    exit;
}

// Proses hapus (ON DELETE CASCADE otomatis bersihin detail & pembayaran)
$hapus = mysqli_query($koneksi, "DELETE FROM pesanan WHERE id_pesanan = $id");

if ($hapus) {
    catatLog($koneksi, $id_user, 'Hapus pesanan', 
        "Menghapus pesanan: {$pesanan['nama_pelanggan']} | Meja: " . ($pesanan['no_meja'] ?: 'Take Away') . 
        " | Total: Rp " . number_format($pesanan['total'], 0, ',', '.') . 
        " | Status sebelumnya: {$pesanan['status']} | ID: $id"
    );
    header("Location: pesanan.php?msg=hapus_sukses");
} else {
    catatLog($koneksi, $id_user, 'Gagal hapus pesanan', "ID: $id (error database)");
    header("Location: pesanan.php?msg=hapus_gagal");
}

exit;
?>