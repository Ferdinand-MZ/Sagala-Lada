<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';

role_required(['admin']); // hanya admin yang boleh hapus

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /server/view/pengguna/pengguna.php?msg=error");
    exit;
}

$id = (int)$_GET['id'];

// Cek apakah user yang akan dihapus adalah owner
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';
$check = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT role FROM user WHERE id_user = $id"));

if (!$check) {
    header("Location: /server/view/pengguna/pengguna.php?msg=notfound");
    exit;
}

if ($check['role'] === 'owner') {
    header("Location: /server/view/pengguna/pengguna.php?msg=cantdeleteowner");
    exit;
}

// Hapus langsung
$stmt = $koneksi->prepare("DELETE FROM user WHERE id_user = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: /server/view/pengguna/pengguna.php?msg=deleted");
} else {
    header("Location: /server/view/pengguna/pengguna.php?msg=error");
}
exit;