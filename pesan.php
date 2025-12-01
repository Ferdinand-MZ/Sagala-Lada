<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

$keranjang = $_SESSION['keranjang'] ?? [];

// Tambah ke keranjang
if (isset($_POST['tambah_ke_keranjang'])) {
    $id_menu = (int)$_POST['id_menu'];
    $jumlah  = max(1, (int)$_POST['jumlah']);

    $menu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_menu, harga FROM menu WHERE id_menu = $id_menu"));
    if ($menu) {
        if (isset($keranjang[$id_menu])) {
            $keranjang[$id_menu]['jumlah'] += $jumlah;
        } else {
            $keranjang[$id_menu] = [
                'nama'   => $menu['nama_menu'],
                'harga'  => $menu['harga'],
                'jumlah' => $jumlah
            ];
        }
        $_SESSION['keranjang'] = $keranjang;
    }
    header("Location: pesan.php");
    exit;
}

// Hapus item
if (isset($_GET['hapus'])) {
    unset($keranjang[(int)$_GET['hapus']]);
    $_SESSION['keranjang'] = $keranjang;
    header("Location: pesan.php");
    exit;
}

// Kosongkan keranjang
if (isset($_GET['kosongkan'])) {
    unset($_SESSION['keranjang']);
    header("Location: pesan.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pesan Makanan & Minuman - Sagala Lada</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/template/restoran/img/favicon.ico" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/template/restoran/lib/animate/animate.min.css" rel="stylesheet">
    <link href="assets/template/restoran/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/template/restoran/css/style.css" rel="stylesheet">
    <style>
        .qty-btn { width: 38px; height: 38px; }
        .cart-item { background:#f8f9fa; border-radius:12px; padding:12px; margin-bottom:8px; }
        .sticky-cart { position:sticky; top:100px; }
    </style>
</head>
<body>
<div class="container-xxl bg-white p-0">

    <!-- Navbar -->
    <?php include 'navbar.php';?>

    <!-- Hero -->
    <div class="container-xxl py-5 bg-dark hero-header mb-5">
        <div class="container text-center my-5 py-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Pesan Makanan & Minuman</h1>
            <p class="text-white fs-5">Pilih menu favoritmu lalu kirim pesanan ke kasir</p>
        </div>
    </div>

    <div class="container py-5">

        <!-- Tab Switcher -->
        <ul class="nav nav-pills justify-content-center mb-5" id="menuTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-5 py-3" id="makanan-tab" data-bs-toggle="pill" data-bs-target="#makanan" type="button">Makanan</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-5 py-3" id="minuman-tab" data-bs-toggle="pill" data-bs-target="#minuman" type="button">Minuman</button>
            </li>
        </ul>

        <div class="row g-5">
            <!-- Daftar Menu -->
            <div class="col-lg-8">
                <div class="tab-content" id="menuTabContent">

                    <!-- TAB MAKANAN -->
                    <div class="tab-pane fade show active" id="makanan">
                        <div class="row g-4">
                            <?php
                            $makanan = mysqli_query($koneksi, "SELECT * FROM menu WHERE jenis = 'Makanan' ORDER BY nama_menu");
                            while ($m = mysqli_fetch_assoc($makanan)):
                            ?>
                            <div class="col-md-6 wow fadeInUp">
                                <div class="border rounded p-4 h-100">
                                    <div class="d-flex align-items-start">
                                        <img src="/assets/img/menu/<?= $m['gambar'] ?: 'default.jpg' ?>" 
                                             class="flex-shrink-0 rounded me-3" style="width:90px;height:90px;object-fit:cover;">
                                        <div class="w-100">
                                            <h5 class="mb-1"><?= htmlspecialchars($m['nama_menu']) ?></h5>
                                            <p class="text-primary fw-bold mb-2">Rp <?= number_format($m['harga'],0,',','.') ?></p>
                                            <span class="badge bg-warning text-dark">Makanan</span>
                                        </div>
                                    </div>
                                    <form method="POST" class="mt-3">
                                        <input type="hidden" name="id_menu" value="<?= $m['id_menu'] ?>">
                                        <div class="input-group w-75 mx-auto">
                                            <button type="button" class="btn btn-outline-secondary qty-btn" onclick="this.parentNode.querySelector('input[type=number]').stepDown()">−</button>
                                            <input type="number" name="jumlah" value="1" min="1" class="form-control text-center">
                                            <button type="button" class="btn btn-outline-secondary qty-btn" onclick="this.parentNode.querySelector('input[type=number]').stepUp()">+</button>
                                            <button type="submit" name="tambah_ke_keranjang" class="btn btn-primary ms-2">Add</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <!-- TAB MINUMAN -->
                    <div class="tab-pane fade" id="minuman">
                        <div class="row g-4">
                            <?php
                            $minuman = mysqli_query($koneksi, "SELECT * FROM menu WHERE jenis = 'Minuman' ORDER BY nama_menu");
                            while ($m = mysqli_fetch_assoc($minuman)):
                            ?>
                            <div class="col-md-6 wow fadeInUp">
                                <div class="border rounded p-4 h-100">
                                    <div class="d-flex align-items-start">
                                        <img src="/assets/img/menu/<?= $m['gambar'] ?: 'default.jpg' ?>" 
                                             class="flex-shrink-0 rounded me-3" style="width:90px;height:90px;object-fit:cover;">
                                        <div class="w-100">
                                            <h5 class="mb-1"><?= htmlspecialchars($m['nama_menu']) ?></h5>
                                            <p class="text-primary fw-bold mb-2">Rp <?= number_format($m['harga'],0,',','.') ?></p>
                                            <span class="badge bg-info text-dark">Minuman</span>
                                        </div>
                                    </div>
                                    <form method="POST" class="mt-3">
                                        <input type="hidden" name="id_menu" value="<?= $m['id_menu'] ?>">
                                        <div class="input-group w-75 mx-auto">
                                            <button type="button" class="btn btn-outline-secondary qty-btn" onclick="this.parentNode.querySelector('input[type=number]').stepDown()">−</button>
                                            <input type="number" name="jumlah" value="1" min="1" class="form-control text-center">
                                            <button type="button" class="btn btn-outline-secondary qty-btn" onclick="this.parentNode.querySelector('input[type=number]').stepUp()">+</button>
                                            <button type="submit" name="tambah_ke_keranjang" class="btn btn-primary ms-2">Add</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                </div>
            </div>

            <!-- KERANJANG (Sticky) -->
            <div class="col-lg-4">
                <div class="bg-light rounded p-4 sticky-cart">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Keranjang Pesanan</h4>
                        <?php if(!empty($keranjang)): ?>
                            <a href="?kosongkan=1" class="text-danger small">Kosongkan</a>
                        <?php endif; ?>
                    </div>
                    <hr>

                    <?php if(empty($keranjang)): ?>
                        <p class="text-center text-muted mb-0">Keranjang kosong</p>
                    <?php else:
                        $total = 0;
                        foreach($keranjang as $id => $item):
                            $subtotal = $item['harga'] * $item['jumlah'];
                            $total += $subtotal;
                        ?>
                        <div class="cart-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= htmlspecialchars($item['nama']) ?></strong><br>
                                <small class="text-muted">Rp <?= number_format($item['harga']) ?> × <?= $item['jumlah'] ?></small>
                            </div>
                            <div class="text-end">
                                <strong>Rp <?= number_format($subtotal) ?></strong>
                                <a href="?hapus=<?= $id ?>" class="text-danger ms-3">Delete</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <hr>
                        <h5>Total: <span class="text-primary">Rp <?= number_format($total) ?></span></h5>

                        <form action="/server/controller/pesanan/pesananPelanggan.php" method="POST" class="mt-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">No. Meja (opsional)</label>
                                <input type="text" name="no_meja" class="form-control" placeholder="Contoh: A3">
                            </div>
                            <button type="submit" name="kirim_pesanan" class="btn btn-success w-100 py-3">
                                Kirim Pesanan
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top">Up</a>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/template/restoran/lib/wow/wow.min.js"></script>
<script src="assets/template/restoran/js/main.js"></script>
</body>
</html>