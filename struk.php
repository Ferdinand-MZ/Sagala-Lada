<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

$id = (int)($_GET['id'] ?? 0);
$pesanan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = $id"));

if (!$pesanan) {
    die("Pesanan tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?= $pesanan['status'] === 'Selesai' ? 'Pesanan Selesai' : 'Struk Pesanan' ?> #<?= $id ?> - Sagala Lada</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="assets/template/restoran/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/template/restoran/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php if ($pesanan['status'] === 'Selesai'): ?>
    <!-- TAMPILAN SELESAI -->
    <div class="container py-5">
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-check-circle text-success" style="font-size: 120px;"></i>
            </div>
            <h1 class="display-4 text-success fw-bold">Pesanan Selesai!</h1>
            <h3 class="my-4">Terima kasih atas pesanannya</h3>
            <p class="fs-5 text-muted">Nikmati hidangan Anda</p>
            <a href="index.php" class="btn btn-success btn-lg px-5 py-3 mt-3">
                <i class="fa fa-home me-2"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

<?php else: ?>
    <!-- TAMPILAN PENDING (seperti sebelumnya) -->
    <div class="container py-5">
        <div class="text-center mb-4">
            <h1 class="text-primary"><i class="fa fa-utensils"></i> Sagala Lada</h1>
            <h4>Pesanan Berhasil Dikirim!</h4>
        </div>
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">No. Pesanan: #<?= $id ?></h5>
            </div>
            <div class="card-body">
                <p><strong>Nama:</strong> <?= htmlspecialchars($pesanan['nama_pelanggan']) ?></p>
                <p><strong>Meja:</strong> <?= $pesanan['no_meja'] ?: '-' ?></p>
                <p><strong>Waktu:</strong> <?= date('d/m/Y H:i', strtotime($pesanan['tanggal_pesan'])) ?></p>
                <hr>
                <?php
                $items = mysqli_query($koneksi, "SELECT m.nama_menu, d.jumlah, d.subtotal 
                                                 FROM detail_pesanan d 
                                                 JOIN menu m ON d.id_menu = m.id_menu 
                                                 WHERE d.id_pesanan = $id");
                while ($i = mysqli_fetch_assoc($items)):
                ?>
                <div class="d-flex justify-content-between mb-2">
                    <div><?= htmlspecialchars($i['nama_menu']) ?> × <?= $i['jumlah'] ?></div>
                    <div>Rp <?= number_format($i['subtotal'],0,',','.') ?></div>
                </div>
                <?php endwhile; ?>
                <hr>
                <h5>Total: Rp <?= number_format($pesanan['total'],0,',','.') ?></h5>
                <div class="text-center mt-4">
                    <p>Tunggu sebentar, pesananmu sedang diproses kasir</p>
                    <a href="index.php" class="btn btn-primary">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

</body>
</html>