<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';
role_required(['admin']); // hanya admin

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'tambah':
        $username = trim($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role     = $_POST['role'];

        $stmt = $koneksi->prepare("INSERT INTO user (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);
        if ($stmt->execute()) {
            header("Location: /server/view/pengguna/pengguna.php?msg=updatedpengguna.php?msg=added");
        } else {
            header("Location: /server/view/pengguna/pengguna.php?msg=updatedpengguna.php?msg=error");
        }
        exit;

    case 'edit':
        $id       = (int)$_POST['id_user'];
        $username = trim($_POST['username']);
        $role     = $_POST['role'];

        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE user SET username=?, password=?, role=? WHERE id_user=?";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("sssi", $username, $password, $role, $id);
        } else {
            $sql = "UPDATE user SET username=?, role=? WHERE id_user=?";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("ssi", $username, $role, $id);
        }

        if ($stmt->execute()) {
            header("Location: /server/view/pengguna/pengguna.php?msg=updatedpengguna.php?msg=updated");
        } else {
            header("Location: /server/view/pengguna/pengguna.php?msg=updatedpengguna.php?msg=error");
        }
        exit;

    case 'hapus':
        $id = (int)$_GET['id'];
        $stmt = $koneksi->prepare("DELETE FROM user WHERE id_user = ? AND role != 'owner'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: /server/view/pengguna/pengguna.php?msg=updatedpengguna.php?msg=deleted");
        exit;
}