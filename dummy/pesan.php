<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

// Jika sudah ada pesanan sementara di session, ambil
$keranjang = $_SESSION['keranjang'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Pesan Makanan - Sagala Lada</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="assets/template/restoran/img/favicon.ico" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/template/restoran/lib/animate/animate.min.css" rel="stylesheet">
    <link href="assets/template/restoran/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/template/restoran/css/style.css" rel="stylesheet">
    <style>
        .qty-btn { width: 35px; height: 35px; font-size: 18px; }
        .cart-item { background: #f8f9fa; border-radius: 10px; padding: 12px; margin-bottom: 10px; }
        .badge-total { font-size: 1.5rem; }
    </style>
</head>
<body>
    <div class="container-xxl bg-white p-0">
        <!-- Spinner -->
       

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
            <a href="index.php" class="navbar-brand p-0">
                <h1 class="text-primary m-0"><i class="fa fa-utensils me-3"></i>Sagala Lada</h1>
            </a>
            <div class="navbar-nav ms-auto">
                <a href="index.php" class="nav-item nav-link">Beranda</a>
                <a href="pesan.php" class="nav-item nav-link active">Pesan Makanan</a>
            </div>
        </nav>

        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center wow fadeInUp">
                    <h1 class="mb-4">Pesan Makanan & MinUMAN</h1>
                    <p>Pilih menu favoritmu, lalu klik <strong>Pesan Sekarang</strong></p>
                </div>

                <div class="row g-5">
                    <!-- Daftar Menu -->
                    <div class="col-lg-8">
                        <div class="row g-4">
                            <?php
                            $menus = mysqli_query($koneksi, "SELECT * FROM menu ORDER BY jenis, nama_menu");
                            while ($m = mysqli_fetch_assoc($menus)):
                            ?>
                            <div class="col-md-6 wow fadeInUp">
                                <div class="border rounded p-4 h-100 position-relative">
                                    <div class="d-flex align-items-start">
                                        <img src="/assets/img/menu/<?= $m['gambar'] ?: 'default.jpg' ?>" 
                                             class="flex-shrink-0 img-fluid rounded me-3" 
                                             style="width: 90px; height: 90px; object-fit: cover;">
                                        <div class="w-100">
                                            <h5 class="mb-1"><?= htmlspecialchars($m['nama_menu']) ?></h5>
                                            <p class="text-primary fw-bold mb-2">Rp <?= number_format($m['harga'], 0, ',', '.') ?></p>
                                            <span class="badge bg-<?= $m['jenis']=='Makanan' ? 'warning' : 'info' ?> text-dark">
                                                <?= $m['jenis'] ?>
                                            </span>
                                        </div>
                                    </div>
                                    <form action="" method="POST" class="mt-3">
                                        <input type="hidden" name="id_menu" value="<?= $m['id_menu'] ?>">
                                        <div class="input-group w-75 mx-auto">
                                            <button type="button" class="btn btn-outline-secondary qty-btn" onclick="this.parentNode.querySelector('input[type=number]').stepDown()">-</button>
                                            <input type="number" name="jumlah" value="1" min="1" class="form-control text-center" style="width:60px;">
                                            <button type="button" class="btn btn-outline-secondary qty-btn" onclick="this.parentNode.querySelector('input[type=number]').stepUp()">+</button>
                                            <button type="submit" name="tambah_ke_keranjang" class="btn btn-primary ms-2">
                                                <i class="fa fa-cart-plus"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <!-- Keranjang -->
                    <div class="col-lg-4">
                        <div class="bg-light rounded p-4 position-sticky" style="top: 100px;">
                            <h4>Keranjang Pesanan</h4>
                            <hr>
                            <?php if (empty($keranjang)): ?>
                                <p class="text-center text-muted">Keranjang kosong</p>
                            <?php else: 
                                $total = 0;
                            ?>
                                <div id="keranjang-items">
                                    <?php foreach ($keranjang as $id_menu => $item): 
                                        $subtotal = $item['harga'] * $item['jumlah'];
                                        $total += $subtotal;
                                    ?>
                                    <div class="cart-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars($item['nama']) ?></strong><br>
                                            <small>Rp <?= number_format($item['harga']) ?> × <?= $item['jumlah'] ?></small>
                                        </div>
                                        <div>
                                            <strong>Rp <?= number_format($subtotal) ?></strong>
                                            <a href="?hapus_dari_keranjang=<?= $id_menu ?>" class="text-danger ms-3">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <hr>
                                <h5>Total: <span class="badge bg-primary badge-total">Rp <?= number_format($total) ?></span></h5>
                            <?php endif; ?>

                            <?php if (!empty($keranjang)): ?>
                            <form action="/server/controller/pesanan_pelanggan.php" method="POST" class="mt-4">
                                <div class="mb-3">
                                    <label class="form-label">Nama Pelanggan</label>
                                    <input type="text" name="nama_pelanggan" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No. Meja (opsional)</label>
                                    <input type="text" name="no_meja" class="form-control" placeholder="Contoh: Meja 5">
                                </div>
                                <button type="submit" name="kirim_pesanan" class="btn btn-success w-100 py-3">
                                    <i class="fa fa-paper-plane"></i> Kirim Pesanan ke Kasir
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'footer.php'; ?>
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- Proses Keranjang (di file ini juga) -->
    <?php
    // Tambah ke keranjang
    if (isset($_POST['tambah_ke_keranjang'])) {
        $id_menu = (int)$_POST['id_menu'];
        $jumlah = (int)$_POST['jumlah'];
        $menu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_menu, harga FROM menu WHERE id_menu = $id_menu"));

        if ($menu) {
            if (isset($keranjang[$id_menu])) {
                $keranjang[$id_menu]['jumlah'] += $jumlah;
            } else {
                $keranjang[$id_menu] = [
                    'nama' => $menu['nama_menu'],
                    'harga' => $menu['harga'],
                    'jumlah' => $jumlah
                ];
            }
            $_SESSION['keranjang'] = $keranjang;
        }
        echo "<script>location.reload();</script>";
    }

    // Hapus dari keranjang
    if (isset($_GET['hapus_dari_keranjang'])) {
        unset($keranjang[$_GET['hapus_dari_keranjang']]);
        $_SESSION['keranjang'] = $keranjang;
        echo "<script>location.href='pesan.php';</script>";
    }
    ?>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/template/restoran/lib/wow/wow.min.js"></script>
    <script src="assets/template/restoran/js/main.js"></script>
</body>
</html>