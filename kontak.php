<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sagala Lada</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="assets/template/restoran/img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="assets/template/restoran/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="assets/template/restoran/css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar & Hero Start -->
        <?php include 'navbar.php'; ?>

            <div class="container-xxl py-5 bg-dark hero-header mb-5">
            <div class="container text-center my-5 py-5">
                <h1 class="display-3 text-white mb-3 animated slideInDown">Hubungi Kami</h1>
                <p class="text-white fs-5 mb-0">Ada pertanyaan? Kritik? Saran? Kami siap mendengar!</p>
            </div>
        </div>

        <!-- Contact Info & Form -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="section-title ff-secondary text-center text-primary fw-normal">Kontak</h5>
                    <h1 class="mb-5">Hubungi Sagala Lada</h1>
                </div>

                <div class="row g-5">
                    <!-- Informasi Kontak -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:60px;height:60px;">
                                <i class="fa fa-map-marker-alt fa-2x text-white"></i>
                            </div>
                            <div class="ps-4">
                                <h5 class="mb-2">Alamat</h5>
                                <p class="mb-0">Jl. R.A Kartini No.39<br>Soklat, Subang, Jawa Barat</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:60px;height:60px;">
                                <i class="fa fa-phone-alt fa-2x text-white"></i>
                            </div>
                            <div class="ps-4">
                                <h5 class="mb-2">Telepon / WhatsApp</h5>
                                <p class="mb-0">+62 812-8668-3093</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:60px;height:60px;">
                                <i class="fa fa-clock fa-2x text-white"></i>
                            </div>
                            <div class="ps-4">
                                <h5 class="mb-2">Jam Buka</h5>
                                <p class="mb-0">Selasa - Minggu<br>11:00 - 20:00 WIB<br><small>(Senin Tutup)</small></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-5 mt-3">
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="ratio ratio-16x9 rounded overflow-hidden">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.387391234567!2d107.755555614763!3d-6.548888995246789!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e691b8f8f8f8f8f%3A0x9c8f8f8f8f8f8f8f!2sSagala%20Lada!5e0!3m2!1sid!2sid!4v1735600000000" 
                                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>

                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                        <h4 class="mb-4">Kirim Pesan</h4>
                        <form action="https://formspree.io/f/mayrbjkl" method="POST"> <!-- Ganti dengan Formspree / PHP handler -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Nama Anda" required>
                                        <label for="name">Nama Anda</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" name="email" id="email" placeholder="Email Anda" required>
                                        <label for="email">Email Anda</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="subject" id="subject" placeholder="Subjek">
                                        <label for="subject">Subjek</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" name="message" placeholder="Pesan Anda" id="message" style="height: 150px" required></textarea>
                                        <label for="message">Pesan Anda</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="submit">
                                        Kirim Pesan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->


        <!-- Footer Start -->
        <?php include 'footer.php'; ?>
        <!-- Footer End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/template/restoran/lib/wow/wow.min.js"></script>
    <script src="assets/template/restoran/lib/easing/easing.min.js"></script>
    <script src="assets/template/restoran/lib/waypoints/waypoints.min.js"></script>
    <script src="assets/template/restoran/lib/counterup/counterup.min.js"></script>
    <script src="assets/template/restoran/lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="assets/template/restoran/lib/tempusdominus/js/moment.min.js"></script>
    <script src="assets/template/restoran/lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="assets/template/restoran/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="assets/template/restoran/js/main.js"></script>
</body>

</html>