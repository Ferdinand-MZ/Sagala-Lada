<?php
include $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

$sql = "SELECT p.*, py.metode_bayar, py.tanggal_bayar 
        FROM pesanan p
        JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
        WHERE p.status='Selesai'
        AND (p.nama_pelanggan LIKE '%$search%' 
         OR p.no_meja LIKE '%$search%'
         OR py.metode_bayar LIKE '%$search%')
        ORDER BY py.tanggal_bayar DESC
        LIMIT $limit OFFSET $offset";

$query = mysqli_query($koneksi, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

echo json_encode($data);
