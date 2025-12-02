<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

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

/* ==============================================
   1. HAPUS PESANAN
   ============================================== */
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    $pesanan = mysqli_fetch_assoc(mysqli_query($koneksi, 
        "SELECT nama_pelanggan, no_meja, total FROM pesanan WHERE id_pesanan = $id"
    ));

    $del = mysqli_query($koneksi, "DELETE FROM pesanan WHERE id_pesanan = $id");
    
    if ($del && $pesanan) {
        catatLog($koneksi, $id_user, 'Hapus pesanan', 
            "Menghapus pesanan: {$pesanan['nama_pelanggan']} | Meja: {$pesanan['no_meja']} | Total: Rp " . number_format($pesanan['total'],0,',','.')
        );
        header("Location: /server/view/pesanan/pesanan.php?msg=hapus_sukses");
    } else {
        catatLog($koneksi, $id_user, 'Gagal hapus pesanan', "ID: $id");
        header("Location: /server/view/pesanan/pesanan.php?msg=hapus_gagal");
    }
    exit;
}

/* ==============================================
   2. TAMBAH PESANAN BARU
   ============================================== */
if (isset($_POST['tambah_pesanan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_pelanggan']);
    $meja = !empty($_POST['no_meja']) ? "'" . mysqli_real_escape_string($koneksi, $_POST['no_meja']) . "'" : "NULL";

    $sql = "INSERT INTO pesanan (nama_pelanggan, no_meja, status, total) 
            VALUES ('$nama', $meja, 'Pending', 0)";
    
    if (mysqli_query($koneksi, $sql)) {
        $id_pesanan = mysqli_insert_id($koneksi);
        catatLog($koneksi, $id_user, 'Buat pesanan baru', "Pelanggan: $nama | Meja: ".($_POST['no_meja'] ?: 'Take Away')." | ID: $id_pesanan");
        header("Location: /server/view/pesanan/tambah_item.php?id=$id_pesanan");
    } else {
        catatLog($koneksi, $id_user, 'Gagal buat pesanan', $nama);
        header("Location: /server/view/pesanan/pesanan.php?msg=tambah_gagal");
    }
    exit;
}

/* ==============================================
   3. TAMBAH ITEM KE PESANAN
   ============================================== */
if (isset($_POST['tambah_item'])) {
    $id_pesanan = (int)$_POST['id_pesanan'];
    $id_menu    = (int)$_POST['id_menu'];
    $jumlah     = (int)$_POST['jumlah'];

    $menu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_menu, harga FROM menu WHERE id_menu = $id_menu"));
    $subtotal = $menu['harga'] * $jumlah;

    $sql = "INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, subtotal) 
            VALUES ($id_pesanan, $id_menu, $jumlah, $subtotal)";
    
    if (mysqli_query($koneksi, $sql)) {
        mysqli_query($koneksi, "UPDATE pesanan SET total = (
            SELECT COALESCE(SUM(subtotal), 0) FROM detail_pesanan WHERE id_pesanan = $id_pesanan
        ) WHERE id_pesanan = $id_pesanan");

        catatLog($koneksi, $id_user, 'Tambah item pesanan', 
            "Menu: {$menu['nama_menu']} | Jumlah: $jumlah | Subtotal: Rp " . number_format($subtotal,0,',','.') . " | Pesanan ID: $id_pesanan"
        );
    }
    header("Location: /server/view/pesanan/tambah_item.php?id=$id_pesanan&msg=item_ditambah");
    exit;
}

/* ==============================================
   4. HAPUS ITEM DARI PESANAN
   ============================================== */
if (isset($_GET['hapus_item'])) {
    $id_detail  = (int)$_GET['hapus_item'];
    $id_pesanan = (int)$_GET['id_pesanan'];

    $item = mysqli_fetch_assoc(mysqli_query($koneksi, 
        "SELECT m.nama_menu, d.jumlah, d.subtotal 
         FROM detail_pesanan d 
         JOIN menu m ON d.id_menu = m.id_menu 
         WHERE d.id_detail = $id_detail"
    ));

    $del = mysqli_query($koneksi, "DELETE FROM detail_pesanan WHERE id_detail = $id_detail");
    if ($del) {
        mysqli_query($koneksi, "UPDATE pesanan SET total = (
            SELECT COALESCE(SUM(subtotal), 0) FROM detail_pesanan WHERE id_pesanan = $id_pesanan
        ) WHERE id_pesanan = $id_pesanan");

        if ($item) {
            catatLog($koneksi, $id_user, 'Hapus item pesanan', 
                "Menghapus: {$item['nama_menu']} | Jumlah: {$item['jumlah']} | Subtotal: Rp " . number_format($item['subtotal'],0,',','.') . " | Pesanan ID: $id_pesanan"
            );
        }
    }
    header("Location: /server/view/pesanan/tambah_item.php?id=$id_pesanan");
    exit;
}

/* ==============================================
   5. PROSES PEMBAYARAN & SELESAIKAN PESANAN
   ============================================== */
if (isset($_POST['proses_bayar'])) {
    $id_pesanan   = (int)$_POST['id_pesanan'];
    $metode       = $_POST['metode_bayar'];
    $jumlah_bayar = (float)preg_replace('/\D/', '', $_POST['jumlah_bayar']);

    $pesanan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_pelanggan, total FROM pesanan WHERE id_pesanan = $id_pesanan"));

    if ($jumlah_bayar >= $pesanan['total']) {
        $sql_bayar = "INSERT INTO pembayaran (id_pesanan, metode_bayar, jumlah_bayar) 
                      VALUES ($id_pesanan, '$metode', $jumlah_bayar)";
        mysqli_query($koneksi, $sql_bayar);
        mysqli_query($koneksi, "UPDATE pesanan SET status = 'Selesai' WHERE id_pesanan = $id_pesanan");

        catatLog($koneksi, $id_user, 'Selesaikan pembayaran', 
            "Pesanan ID: $id_pesanan | {$pesanan['nama_pelanggan']} | Total: Rp " . number_format($pesanan['total'],0,',','.') . 
            " | Bayar: Rp " . number_format($jumlah_bayar,0,',','.') . " | Metode: $metode"
        );

        header("Location: /server/view/pesanan/pesanan.php?msg=bayar_sukses");
    } else {
        catatLog($koneksi, $id_user, 'Gagal bayar (kurang)', "Pesanan ID: $id_pesanan | Kurang bayar");
        header("Location: /server/view/pesanan/tambah_item.php?id=$id_pesanan&msg=bayar_kurang");
    }
    exit;
}

/* ==============================================
   EDIT PESANAN (nama & meja)
   ============================================== */
if (isset($_POST['edit_pesanan'])) {
    $id     = (int)$_POST['id_pesanan'];
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama_pelanggan']);
    $meja   = !empty($_POST['no_meja']) ? "'" . mysqli_real_escape_string($koneksi, $_POST['no_meja']) . "'" : "NULL";

    $old = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_pelanggan, no_meja FROM pesanan WHERE id_pesanan = $id"));

    $sql = "UPDATE pesanan SET nama_pelanggan = '$nama', no_meja = $meja WHERE id_pesanan = $id";
    $ok = mysqli_query($koneksi, $sql);

    if ($ok) {
        $perubahan = [];
        if ($old['nama_pelanggan'] != $_POST['nama_pelanggan']) $perubahan[] = "nama: {$old['nama_pelanggan']} → $nama";
        if ($old['no_meja'] != ($_POST['no_meja'] ?: null)) $perubahan[] = "meja: {$old['no_meja']} → ".($_POST['no_meja'] ?: 'Take Away');
        $detail = !empty($perubahan) ? implode(' | ', $perubahan) : 'tidak ada perubahan';

        catatLog($koneksi, $id_user, 'Edit pesanan', "ID $id → $detail");
        header("Location: /server/view/pesanan/pesanan.php?msg=edit_sukses");
    } else {
        catatLog($koneksi, $id_user, 'Gagal edit pesanan', "ID: $id");
        header("Location: /server/view/pesanan/pesanan.php?msg=edit_gagal");
    }
    exit;
}

/* ==============================================
   6. BATALKAN PESANAN
   ============================================== */
if (isset($_GET['batal'])) {
    $id = (int)$_GET['batal'];
    $pesanan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_pelanggan, total FROM pesanan WHERE id_pesanan = $id"));

    $ok = mysqli_query($koneksi, "UPDATE pesanan SET status = 'Dibatalkan' WHERE id_pesanan = $id");
    
    if ($ok && $pesanan) {
        catatLog($koneksi, $id_user, 'Batalkan pesanan', 
            "Membatalkan pesanan: {$pesanan['nama_pelanggan']} | Total: Rp " . number_format($pesanan['total'],0,',','.')
        );
        header("Location: /server/view/pesanan/pesanan.php?msg=batal_sukses");
    } else {
        catatLog($koneksi, $id_user, 'Gagal batalkan pesanan', "ID: $id");
        header("Location: /server/view/pesanan/pesanan.php?msg=batal_gagal");
    }
    exit;
}

header("Location: /server/view/pesanan/pesanan.php");
exit;
?>