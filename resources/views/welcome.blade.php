<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disquseria - Forum Diskusi Mini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            --secondary-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --accent-color: #FFD700;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #0F172A;
            color: #fff;
            overflow-x: hidden;
        }

        .header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 1.5rem;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
        }

        .logo {
            font-weight: 700;
            font-size: 1.5rem;
            color: #fff;
            text-decoration: none;
        }

        .logo span {
            color: var(--accent-color);
        }

        .main-container {
            position: relative;
            min-height: 100vh;
        }

        .shape {
            position: absolute;
            z-index: -1;
        }

        .shape-1 {
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            top: -150px;
            right: -150px;
            filter: blur(80px);
            opacity: 0.5;
        }

        .shape-2 {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            bottom: -100px;
            left: -100px;
            filter: blur(80px);
            opacity: 0.4;
        }

        .hero-section {
            display: flex;
            min-height: 100vh;
            padding: 6rem 2rem;
            align-items: center;
        }

        .hero-content {
            max-width: 700px;
            margin: 0 auto;
            text-align: center;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, #fff, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .hero-title span {
            display: block;
            background: linear-gradient(to right, #ffd700, #ffa500);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            font-weight: 300;
            color: #a5b4fc;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .hero-buttons {
            margin-top: 2rem;
        }

        .btn-primary-custom {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            box-shadow: 0 10px 20px rgba(106, 17, 203, 0.3);
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(106, 17, 203, 0.4);
        }

        .btn-secondary-custom {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.2);
            padding: 0.8rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-secondary-custom:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
        }

        .features-section {
            padding: 4rem 2rem;
            background: rgba(15, 23, 42, 0.6);
        }

        .section-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 3rem;
            color: #fff;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 2rem;
            height: 100%;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .feature-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin-bottom: 1.5rem;
            background: var(--primary-gradient);
            font-size: 1.8rem;
        }

        .feature-card:nth-child(2) .feature-icon {
            background: linear-gradient(135deg, #FF6B6B, #FFE66D);
        }

        .feature-card:nth-child(3) .feature-icon {
            background: linear-gradient(135deg, #11998e, #38ef7d);
        }

        .feature-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
            color: #fff;
        }

        .feature-text {
            color: #a5b4fc;
            font-size: 1rem;
            line-height: 1.6;
        }

        .footer {
            text-align: center;
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.02);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .social-links {
            margin-bottom: 1.5rem;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--primary-gradient);
            transform: translateY(-3px);
        }

        .copyright {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .feature-card {
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Background shapes -->
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>

        <!-- Header -->
        <header class="header d-flex justify-content-between align-items-center">
            <a href="#" class="logo">Disqus<span>eria</span></a>
            <div>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm me-2">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-warning btn-sm">Daftar</a>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-content">
                    <h1 class="hero-title">Tempat Diskusi yang Menyenangkan <span>untuk Semua Orang</span></h1>
                    <p class="hero-subtitle">Diskusikan topik yang kamu sukai, temukan teman baru dengan minat yang sama, dan bagikan ide-ide kreatifmu dalam komunitas Disquseria</p>
                    <div class="hero-buttons">
                        <a href="{{ route('login') }}" class="btn btn-primary-custom me-3 mb-2">Mulai Diskusi</a>
                        <a href="{{ route('register') }}" class="btn btn-secondary-custom mb-2">Buat Akun Baru</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="container">
                <h2 class="section-title">Fitur Disquseria</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-comments text-white"></i>
                            </div>
                            <h3 class="feature-title">Forum Diskusi</h3>
                            <p class="feature-text">Buat thread diskusi dengan berbagai topik menarik, berikan pendapatmu, dan diskusikan dengan pengguna lain.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-tags text-white"></i>
                            </div>
                            <h3 class="feature-title">Kategori & Tag</h3>
                            <p class="feature-text">Organisasi topik diskusi dengan kategori dan tag untuk menemukan konten yang kamu minati dengan mudah.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-thumbs-up text-white"></i>
                            </div>
                            <h3 class="feature-title">Voting & Komentar</h3>
                            <p class="feature-text">Berikan suaramu pada diskusi yang bermanfaat dan berkomentar untuk memperkaya diskusi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-github"></i></a>
            </div>
            <p class="copyright">
                &copy; {{ date('Y') }} Disquseria. Dibuat dengan <span class="text-danger">❤️</span> oleh Tim Developer.
            </p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
