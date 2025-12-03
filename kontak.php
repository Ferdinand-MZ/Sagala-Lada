<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Sagala Lada - Hubungi Kami</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon & Fonts (sama) -->
    <link href="assets/template/restoran/img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">

    <!-- Icons & Libraries -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Bootstrap & Custom CSS -->
    <link href="assets/template/restoran/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/template/restoran/css/style.css" rel="stylesheet">
    
    <style>
        .contact-icon {
            transition: all 0.4s ease;
        }
        .contact-icon:hover {
            transform: translateY(-10px) scale(1.1);
            box-shadow: 0 20px 30px rgba(0,0,0,0.2);
        }
        .form-floating label { transition: all 0.3s; }
    </style>
</head>
<body>
    <div class="container-xxl bg-white p-0">
        <!-- Spinner -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
        </div>

        <!-- Navbar -->
        <?php include 'navbar.php'; ?>

        <!-- Hero Contact -->
        <div class="container-xxl py-5 bg-dark hero-header mb-5">
            <div class="container text-center my-5 pt-5">
                <h1 class="display-3 text-white mb-3 animated slideInDown">Hubungi Kami</h1>
                <p class="text-white fs-5 animated zoomIn">Ada pertanyaan? Kritik? Saran? Kami siap mendengar!</p>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="section-title ff-secondary text-center text-primary fw-normal">Kontak Kami</h5>
                    <h1 class="mb-5">Sagala Lada Selalu Ada Untuk Anda</h1>
                </div>

                <!-- Info Kontak dengan Animasi Hover -->
                <div class="row g-5 justify-content-center">
                    <div class="col-lg-4 col-md-6 wow zoomIn contact-icon" data-wow-delay="0.2s">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-4 mx-auto" style="width:90px;height:90px;">
                            <i class="fa fa-map-marker-alt fa-2x"></i>
                        </div>
                        <h4 class="text-center">Alamat</h4>
                        <p class="text-center">Jl. R.A Kartini No.39<br>Soklat, Subang, Jawa Barat</p>
                    </div>

                    <div class="col-lg-4 col-md-6 wow zoomIn contact-icon" data-wow-delay="0.4s">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-4 mx-auto" style="width:90px;height:90px;">
                            <i class="fa fa-phone-alt fa-2x"></i>
                        </div>
                        <h4 class="text-center">Telepon / WhatsApp</h4>
                        <p class="text-center">+62 812-8668-3093</p>
                    </div>

                    <div class="col-lg-4 col-md-6 wow zoomIn contact-icon" data-wow-delay="0.6s">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-4 mx-auto" style="width:90px;height:90px;">
                            <i class="fa fa-clock fa-2x"></i>
                        </div>
                        <h4 class="text-center">Jam Buka</h4>
                        <p class="text-center">Selasa - Minggu<br>11:00 - 20:00 WIB<br><small class="text-danger">(Senin Tutup)</small></p>
                    </div>
                </div>

                <!-- Map + Form -->
                <div class="row g-5 mt-5 align-items-center">
                    <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.3s">
                        <div class="ratio ratio-16x9 rounded shadow-lg overflow-hidden">
                            <iframe src="https://www.google.com/maps/embed?pb=..." allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>

                    <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.5s">
                        <h3 class="mb-4">Kirim Pesan Sekarang</h3>
                        <form action="https://formspree.io/f/mayrbjkl" method="POST">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="name" id="name" required>
                                        <label for="name">Nama Lengkap</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" name="email" id="email" required>
                                        <label for="email">Email</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="subject" id="subject">
                                        <label for="subject">Subjek</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" name="message" id="message" style="height:150px" required></textarea>
                                        <label for="message">Pesan Anda</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3 wow pulse" data-wow-delay="0.7s" type="submit">
                                        <i class="fa fa-paper-plane me-2"></i>Kirim Pesan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include 'footer.php'; ?>

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JS Libraries (sama) -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <script>
        new WOW().init();
    </script>
    <script src="assets/template/restoran/js/main.js"></script>
</body>
</html>