<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$id_user = $_SESSION['user_id'];

// Fungsi log
function catatLog($koneksi, $id_user, $aktivitas, $keterangan = null) {
    $aktivitas  = mysqli_real_escape_string($koneksi, $aktivitas);
    $keterangan = $keterangan ? mysqli_real_escape_string($koneksi, $keterangan) : null;
    $sql = "INSERT INTO log (id_user, aktivitas, keterangan, created_at)
            VALUES ($id_user, '$aktivitas', ".($keterangan ? "'$keterangan'" : "NULL").", NOW())";
    mysqli_query($koneksi, $sql);
}

// === HAPUS MENU (DENGAN LOG DETAIL LENGKAP) ===
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];

    // Ambil data menu sebelum dihapus (untuk log detail)
    $menu = mysqli_fetch_assoc(mysqli_query($koneksi, 
        "SELECT nama_menu, jenis, harga, gambar FROM menu WHERE id_menu = $id"
    ));

    if ($menu) {
        // Hapus file gambar jika ada
        if (!empty($menu['gambar']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/img/menu/' . $menu['gambar'])) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/assets/img/menu/' . $menu['gambar']);
        }

        $del = mysqli_query($koneksi, "DELETE FROM menu WHERE id_menu = $id");

        if ($del) {
            catatLog($koneksi, $id_user, 'Hapus menu', 
                "Menghapus menu: {$menu['nama_menu']} | Jenis: {$menu['jenis']} | Harga: Rp " . 
                number_format($menu['harga'], 0, ',', '.') . " | Gambar: " . ($menu['gambar'] ?: 'tidak ada')
            );
            header("Location: /server/view/menu/menu.php?msg=hapus_sukses");
        } else {
            catatLog($koneksi, $id_user, 'Gagal hapus menu', "ID: $id (menu tidak ditemukan atau error)");
            header("Location: /server/view/menu/menu.php?msg=hapus_gagal");
        }
    } else {
        catatLog($koneksi, $id_user, 'Gagal hapus menu', "ID: $id (menu tidak ditemukan)");
        header("Location: /server/view/menu/menu.php?msg=hapus_gagal");
    }
    exit;
}

// === TAMBAH MENU ===
if (isset($_POST['tambah'])) {
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama_menu']);
    $jenis = $_POST['jenis'];
    $harga = (int)preg_replace('/\D/', '', $_POST['harga']);
    $gambar = '';

    if (!empty($_FILES['gambar']['name']) && move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        $gambar = $nama_file;
    }

    $sql = "INSERT INTO menu (nama_menu, jenis, harga, gambar) VALUES ('$nama', '$jenis', $harga, '$gambar')";
    $ok = mysqli_query($koneksi, $sql);

    if ($ok) {
        $id_baru = mysqli_insert_id($koneksi);
        catatLog($koneksi, $id_user, 'Tambah menu baru', 
            "Menambahkan: $nama | Jenis: $jenis | Harga: Rp " . number_format($harga,0,',','.'));
        header("Location: /server/view/menu/menu.php?msg=tambah_sukses");
    } else {
        catatLog($koneksi, $id_user, 'Gagal tambah menu', $nama);
        header("Location: /server/view/menu/menu.php?msg=tambah_gagal");
    }
    exit;
}

// === EDIT MENU ===
if (isset($_POST['edit'])) {
    $id    = (int)$_POST['id_menu'];
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama_menu']);
    $jenis = $_POST['jenis'];
    $harga = (int)preg_replace('/\D/', '', $_POST['harga']);
    $gambar_lama = $_POST['gambar_lama'];
    $gambar = $gambar_lama;

    $old = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_menu, jenis, harga, gambar FROM menu WHERE id_menu = $id"));

    if (!empty($_FILES['gambar']['name']) && move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        if ($gambar_lama && file_exists($_SERVER['DOCUMENT_ROOT'].'/assets/img/menu/'.$gambar_lama)) {
            unlink($_SERVER['DOCUMENT_ROOT'].'/assets/img/menu/'.$gambar_lama);
        }
        $gambar = $nama_file;
    }

    $sql = "UPDATE menu SET nama_menu='$nama', jenis='$jenis', harga=$harga, gambar='$gambar' WHERE id_menu=$id";
    $ok = mysqli_query($koneksi, $sql);

    if ($ok) {
        $perubahan = [];
        if ($old['nama_menu'] != $nama) $perubahan[] = "nama: {$old['nama_menu']} → $nama";
        if ($old['jenis'] != $jenis)     $perubahan[] = "jenis: {$old['jenis']} → $jenis";
        if ($old['harga'] != $harga)     $perubahan[] = "harga: Rp ".number_format($old['harga'],0,',','.')." → Rp ".number_format($harga,0,',','.');
        if ($old['gambar'] != $gambar)   $perubahan[] = "gambar diubah";

        $detail = !empty($perubahan) ? implode(' | ', $perubahan) : 'tidak ada perubahan';
        catatLog($koneksi, $id_user, 'Edit menu', "Mengubah menu ID $id → $detail");
        header("Location: /server/view/menu/menu.php?msg=edit_sukses");
    } else {
        catatLog($koneksi, $id_user, 'Gagal edit menu', "ID: $id");
        header("Location: /server/view/menu/menu.php?msg=edit_gagal");
    }
    exit;
}

header("Location: menu.php");
exit;
?>