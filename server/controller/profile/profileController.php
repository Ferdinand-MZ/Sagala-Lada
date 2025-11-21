<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /auth/login.php");
    exit;
}

$id = $_SESSION['user_id'];
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '') {
    header("Location: /server/view/profile/profile.php?msg=error");
    exit;
}

// Cek apakah username sudah dipakai orang lain
$check = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_user FROM user WHERE username = '$username' AND id_user != $id"));
if ($check) {
    header("Location: /server/view/profile/profile.php?msg=error");
    exit;
}

if (!empty($password)) {
    if ($password !== ($_POST['password_confirm'] ?? '')) {
        header("Location: /server/view/profile/profile.php?msg=error");
        exit;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE user SET username = ?, password = ? WHERE id_user = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ssi", $username, $hash, $id);
} else {
    $sql = "UPDATE user SET username = ? WHERE id_user = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("si", $username, $id);
}

if ($stmt->execute()) {
    $_SESSION['username'] = $username; // update session
    header("Location: /server/view/profile/profile.php?msg=success");
} else {
    header("Location: /server/view/profile/profile.php?msg=error");
}
exit;