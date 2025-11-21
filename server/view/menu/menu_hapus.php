<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';
$id = (int)$_GET['id'];

mysqli_query($koneksi, "DELETE FROM menu WHERE id_menu = $id");
header("Location: menu.php");
exit;
?>