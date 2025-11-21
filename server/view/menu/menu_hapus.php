<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';

// Halaman khusus owner
role_required('owner');
?>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';
$id = (int)$_GET['id'];

mysqli_query($koneksi, "DELETE FROM menu WHERE id_menu = $id");
header("Location: menu.php");
exit;
?>