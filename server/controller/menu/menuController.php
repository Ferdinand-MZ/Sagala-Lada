<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

// Cek login (opsional, tambahkan kalau pakai session)
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// === HAPUS MENU ===
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $del = mysqli_query($koneksi, "DELETE FROM menu WHERE id_menu = $id");
    if ($del) {
        header("Location: /server/view/menu/menu.php?msg=hapus_sukses");
    } else {
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

    // Proses upload gambar
    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $nama_file = time() . '_' . rand(1000,9999) . '.' . $ext;
        $target = $_SERVER['DOCUMENT_ROOT'] . '/assets/img/menu/' . $nama_file;
        
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
            $gambar = $nama_file;
        }
    }

    $sql = "INSERT INTO menu (nama_menu, jenis, harga, gambar) VALUES ('$nama', '$jenis', $harga, '$gambar')";
    $ok = mysqli_query($koneksi, $sql);
    header("Location: /server/view/menu/menu.php?msg=" . ($ok ? 'tambah_sukses' : 'tambah_gagal'));
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

    // Jika upload gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $nama_file = time() . '_' . rand(1000,9999) . '.' . $ext;
        $target = $_SERVER['DOCUMENT_ROOT'] . '/assets/img/menu/' . $nama_file;
        
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
            // Hapus gambar lama jika ada
            if ($gambar_lama && file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/img/menu/' . $gambar_lama)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . '/assets/img/menu/' . $gambar_lama);
            }
            $gambar = $nama_file;
        }
    }

    $sql = "UPDATE menu SET 
            nama_menu = '$nama',
            jenis = '$jenis',
            harga = $harga,
            gambar = '$gambar'
            WHERE id_menu = $id";

    $ok = mysqli_query($koneksi, $sql);
    header("Location: /server/view/menu/menu.php?msg=" . ($ok ? 'edit_sukses' : 'edit_gagal'));
    exit;
}

// Kalau tidak ada aksi → redirect ke menu
header("Location: menu.php");
exit;
?>