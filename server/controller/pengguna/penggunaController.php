<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';

role_required(['admin']); // hanya admin

// Pastikan user login & ambil ID user yang sedang login
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}
$id_user = $_SESSION['user_id'];

// === FUNGSI CATAT LOG ===
function catatLog($koneksi, $id_user, $aktivitas, $keterangan = null) {
    $aktivitas  = mysqli_real_escape_string($koneksi, $aktivitas);
    $keterangan = $keterangan ? mysqli_real_escape_string($koneksi, $keterangan) : null;
    $sql = "INSERT INTO log (id_user, aktivitas, keterangan, created_at) 
            VALUES ($id_user, '$aktivitas', ".($keterangan ? "'$keterangan'" : "NULL").", NOW())";
    mysqli_query($koneksi, $sql);
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    /* ==============================================
       TAMBAH PENGGUNA BARU
       ============================================== */
    case 'tambah':
        $username = trim($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role     = $_POST['role'];

        $stmt = $koneksi->prepare("INSERT INTO user (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            catatLog($koneksi, $id_user, 'Tambah pengguna', "Menambahkan user: $username | Role: " . ucfirst($role));
            header("Location: /server/view/pengguna/pengguna.php?msg=added");
        } else {
            catatLog($koneksi, $id_user, 'Gagal tambah pengguna', "Username: $username (mungkin sudah ada)");
            header("Location: /server/view/pengguna/pengguna.php?msg=error");
        }
        $stmt->close();
        exit;

    /* ==============================================
       EDIT PENGGUNA
       ============================================== */
    case 'edit':
        $id       = (int)$_POST['id_user'];
        $username = trim($_POST['username']);
        $role     = $_POST['role'];

        // Cek data lama
        $old = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT username, role FROM user WHERE id_user = $id"));

        if (!$old) {
            catatLog($koneksi, $id_user, 'Gagal edit pengguna', "ID: $id (tidak ditemukan)");
            header("Location: /server/view/pengguna/pengguna.php?msg=error");
            exit;
        }

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
            $perubahan = [];
            if ($old['username'] != $username) $perubahan[] = "username: {$old['username']} → $username";
            if ($old['role'] != $role)         $perubahan[] = "role: {$old['role']} → " . ucfirst($role);
            if (!empty($_POST['password']))    $perubahan[] = "password diubah";

            $detail = !empty($perubahan) ? implode(' | ', $perubahan) : 'tidak ada perubahan';

            catatLog($koneksi, $id_user, 'Edit pengguna', "Mengubah user ID $id → $detail");
            header("Location: /server/view/pengguna/pengguna.php?msg=updated");
        } else {
            catatLog($koneksi, $id_user, 'Gagal edit pengguna', "ID: $id");
            header("Location: /server/view/pengguna/pengguna.php?msg=error");
        }
        $stmt->close();
        exit;

    /* ==============================================
       HAPUS PENGGUNA (kecuali owner)
       ============================================== */
    case 'hapus':
        $id = (int)($_GET['id'] ?? 0);

        $user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT username, role FROM user WHERE id_user = $id"));

        if (!$user) {
            catatLog($koneksi, $id_user, 'Gagal hapus pengguna', "ID: $id (tidak ditemukan)");
            header("Location: /server/view/pengguna/pengguna.php?msg=error");
            exit;
        }

        if ($user['role'] === 'owner') {
            catatLog($koneksi, $id_user, 'Gagal hapus pengguna', "Mencoba hapus owner: {$user['username']}");
            header("Location: /server/view/pengguna/pengguna.php?msg=error");
            exit;
        }

        $stmt = $koneksi->prepare("DELETE FROM user WHERE id_user = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            catatLog($koneksi, $id_user, 'Hapus pengguna', "Menghapus user: {$user['username']} | Role: " . ucfirst($user['role']));
            header("Location: /server/view/pengguna/pengguna.php?msg=deleted");
        } else {
            catatLog($koneksi, $id_user, 'Gagal hapus pengguna', "ID: $id");
            header("Location: /server/view/pengguna/pengguna.php?msg=error");
        }
        $stmt->close();
        exit;
}

// Jika tidak ada action valid
header("Location: /server/view/pengguna/pengguna.php");
exit;
?>