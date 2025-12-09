<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';
role_required(['owner', 'admin']);

use Dompdf\Dompdf;
use Dompdf\Options;
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);

// PAKSA BULAN DESEMBER 2025 (karena data kamu di sini)
$bulan_ini = '2025-12';

// TOTAL BULAN INI
$total = mysqli_fetch_assoc(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(total),0) as t FROM pesanan 
     WHERE DATE_FORMAT(tanggal_pesan,'%Y-%m')='$bulan_ini' AND status='Selesai'"))['t'];

// REVENUE 7 HARI
$revenue = [];
$hari = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $rev = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COALESCE(SUM(total),0) as r FROM pesanan 
         WHERE DATE(tanggal_pesan)='$tgl' AND status='Selesai'"))['r'] ?? 0;
    $revenue[] = $rev;
}

// SEMUA TRANSAKSI DESEMBER 2025
$result = mysqli_query($koneksi, "
    SELECT p.id_pesanan, p.nama_pelanggan, p.total, 
           COALESCE(b.metode_bayar,'Tunai') as metode,
           DATE_FORMAT(p.tanggal_pesan,'%d/%m/%Y %H:%i') as tgl
    FROM pesanan p
    LEFT JOIN pembayaran b ON p.id_pesanan = b.id_pesanan
    WHERE DATE_FORMAT(p.tanggal_pesan,'%Y-%m')='$bulan_ini' 
      AND p.status='Selesai'
    ORDER BY p.tanggal_pesan DESC
");

$html = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>
    body{font-family:"DejaVu Sans",sans-serif;margin:50px;color:#333}
    h1{color:#EAB308;text-align:center;font-size:32px}
    h2{text-align:center;color:#555;margin-bottom:40px}
    .total{background:#FEF3C7;padding:20px;text-align:center;font-size:22px;font-weight:bold;border-radius:10px;margin:30px 0}
    table{width:100%;border-collapse:collapse;margin:25px 0}
    th{background:#EAB308;color:white;padding:15px;text-align:center}
    td{padding:12px;border-bottom:1px solid #ddd;text-align:center}
    .left{text-align:left!important}
    .right{text-align:right!important}
</style></head><body>
    <h1>Sagala Lada</h1>
    <h2>Laporan Bulanan • Desember 2025</h2>
    <div class="total">TOTAL PENJUALAN: Rp '.number_format($total,0,',','.').'</div>

    <h3>Revenue 7 Hari Terakhir</h3>
    <table><tr><th>Hari</th><th>Penjualan</th></tr>';
for ($i = 6; $i >= 0; $i--) {
    $h = $hari[$i];
    $r = $revenue[6-$i];
    $html .= "<tr><td><strong>$h</strong></td><td class='right'>Rp ".number_format($r,0,',','.')."</td></tr>";
}
$html .= '</table>

    <h3>Daftar Transaksi Desember 2025</h3>
    <table>
        <tr><th>No</th><th>ID</th><th class="left">Pelanggan</th><th class="right">Total</th><th>Metode</th><th>Tanggal</th></tr>';

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $html .= "<tr>
        <td>$no</td>
        <td>#{$row['id_pesanan']}</td>
        <td class='left'>".htmlspecialchars($row['nama_pelanggan'])."</td>
        <td class='right'>Rp ".number_format($row['total'],0,',','.')."</td>
        <td>{$row['metode']}</td>
        <td>{$row['tgl']}</td>
    </tr>";
    $no++;
}
if ($no == 1) $html .= "<tr><td colspan='6'>Tidak ada data</td></tr>";
$html .= '</table>
    <p style="text-align:center;color:#888;margin-top:100px">
        Dicetak pada '.date('d/m/Y H:i').' • Sagala Lada © 2025
    </p>
</body></html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Laporan_Bulanan_Desember_2025.pdf", ['Attachment' => true]);
exit;