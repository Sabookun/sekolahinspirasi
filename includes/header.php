<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Sekolah Inspirasi Bangsa - Masa Depan Cerah'; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #3B82F6;
            --primary-dark: #1D4ED8;
            --primary-light: #60A5FA;
            --secondary: #1E40AF;
            --accent: #2563EB;
            --success: #10B981;
            --warning: #F59E0B;
            --light: #F8FAFC;
            --light-blue: #EFF6FF;
            --dark: #1E293B;
            --text: #374151;
            --gradient: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
            --gradient-light: linear-gradient(135deg, #60A5FA 0%, #3B82F6 100%);
            --shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
            --shadow-lg: 0 20px 40px rgba(59, 130, 246, 0.2);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light);
            color: var(--text);
            line-height: 1.7;
            overflow-x: hidden;
        }

        /* ===== SPACING SYSTEM ===== */
        .section-py {
            padding: 6rem 0;
        }

        .section-py-lg {
            padding: 8rem 0;
        }

        .section-py-xl {
            padding: 10rem 0;
        }

        .mb-section {
            margin-bottom: 6rem;
        }

        .mt-section {
            margin-top: 6rem;
        }

        .content-spacing {
            margin: 3rem 0;
        }

        .element-spacing {
            margin: 2rem 0;
        }

        .inner-spacing {
            padding: 2rem 0;
        }

        .card-spacing {
            margin-bottom: 2rem;
        }

        /* ===== NAVIGATION ===== */
        .navbar {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow);
            padding: 1.2rem 0;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(59, 130, 246, 0.1);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.6rem;
            color: var(--primary) !important;
        }

        .navbar-brand i {
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            font-weight: 500;
            margin: 0 0.3rem;
            padding: 0.7rem 1.2rem !important;
            border-radius: 25px;
            transition: all 0.3s ease;
            color: var(--text) !important;
            position: relative;
        }

        .nav-link:hover,
        .nav-link.active {
            background: var(--gradient);
            color: white !important;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 80%;
        }

        /* ===== HERO SECTION ===== */
        .hero-section {
            background: var(--gradient);
            padding: 12rem 0 8rem 0;
            position: relative;
            overflow: hidden;
            color: white;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><path fill="rgba(255,255,255,0.1)" d="M0,0 L800,150 L1000,500 L1000,1000 L0,1000 Z"/></svg>');
            background-size: cover;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            opacity: 0.95;
            font-weight: 400;
        }

        .btn-hero {
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0.5rem;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-primary {
            background: var(--gradient-light);
            border: none;
            box-shadow: var(--shadow);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .btn-outline-light {
            border: 2px solid white;
            background: transparent;
        }

        .btn-outline-light:hover {
            background: white;
            color: var(--primary) !important;
            transform: translateY(-3px);
        }

        /* ===== SECTION STYLES ===== */
        .section-title {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-title h2 {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 1.2rem;
            position: relative;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient);
            border-radius: 2px;
        }

        .section-title p {
            font-size: 1.2rem;
            color: var(--text);
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.8;
        }

        /* ===== ABOUT SECTION ===== */
        .about-section {
            background: white;
        }

        .feature-card {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(59, 130, 246, 0.1);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: var(--shadow);
        }

        .feature-icon i {
            font-size: 2rem;
            color: white;
        }

        .feature-card h4 {
            color: var(--primary-dark);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .feature-card p {
            color: var(--text);
            opacity: 0.8;
        }

        /* ===== NEWS SECTION ===== */
        .news-section {
            background: var(--light-blue);
        }

        .news-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(59, 130, 246, 0.1);
        }

        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .news-img {
            height: 220px;
            width: 100%;
            object-fit: cover;
        }

        .news-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: var(--shadow);
        }

        .badge-kegiatan {
            background: var(--success);
            color: white;
        }

        .badge-prestasi {
            background: var(--warning);
            color: white;
        }

        .badge-pengumuman {
            background: var(--primary);
            color: white;
        }

        .badge-info {
            background: var(--accent);
            color: white;
        }

        .news-content {
            padding: 2rem;
        }

        .news-content h5 {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        /* ===== TESTIMONIALS SECTION ===== */
        .testimonials-section {
            background: var(--gradient);
            color: white;
            position: relative;
        }

        .testimonial-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            margin: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            position: relative;
        }

        .testimonial-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
        }

        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 1rem;
            left: 1.5rem;
            font-size: 4rem;
            color: rgba(255, 255, 255, 0.3);
            font-family: Georgia, serif;
            line-height: 1;
        }

        .testimonial-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
        }

        .testimonial-card h5 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .testimonial-card .role {
            opacity: 0.9;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        /* ===== LOCATION SECTION ===== */
        .location-section {
            background: white;
        }

        .map-container {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            height: 450px;
            border: 1px solid rgba(59, 130, 246, 0.1);
        }

        .contact-info {
            background: var(--light-blue);
            padding: 2.5rem;
            border-radius: 20px;
            height: 100%;
            border: 1px solid rgba(59, 130, 246, 0.1);
        }

        .contact-info h3 {
            color: var(--primary-dark);
            margin-bottom: 2rem;
            font-weight: 600;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.1);
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .contact-icon i {
            color: white;
            font-size: 1.2rem;
        }

        /* ===== CONTACT SECTION ===== */
        .contact-section {
            background: var(--light-blue);
        }

        .contact-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: var(--shadow);
            border: 1px solid rgba(59, 130, 246, 0.1);
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 1rem 1.2rem;
            border: 2px solid #E5E7EB;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* ===== FOOTER ===== */
        footer {
            background: var(--dark);
            color: white;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            margin-right: 1rem;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-2px);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .section-py {
                padding: 4rem 0;
            }

            .section-py-lg {
                padding: 6rem 0;
            }

            .mb-section {
                margin-bottom: 4rem;
            }

            .section-title h2 {
                font-size: 2.2rem;
            }
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        .animate-delay-1 {
            animation-delay: 0.2s;
        }

        .animate-delay-2 {
            animation-delay: 0.4s;
        }
    </style>
</head>

<body>
          <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-graduation-cap me-2"></i>Sekolah Inspirasi Bangsa
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-home me-1"></i>Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#about">
                            <i class="fas fa-info-circle me-1"></i>Tentang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/news.php">
                            <i class="fas fa-newspaper me-1"></i>Berita
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#testimonials">
                            <i class="fas fa-comments me-1"></i>Testimoni
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#contact">
                            <i class="fas fa-envelope me-1"></i>Kontak
                        </a>
                    </li>
                    
                 <!-- Di bagian User Menu -->
<?php if (isset($_SESSION['user_id'])): ?>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-user me-1"></i> <?php echo $_SESSION['user_name']; ?>
        </a>
        <ul class="dropdown-menu">
            <li>
                <!-- GUNAKAN $_SESSION['role'] -->
                <a class="dropdown-item" href="<?php echo $_SESSION['role']; ?>/dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="auth/logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </li>
        </ul>
    </li>
<?php else: ?>
    <li class="nav-item">
        <a class="nav-link" href="auth/login.php">
            <i class="fas fa-sign-in-alt me-1"></i> Login
        </a>
    </li>
<?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- 🔥 HAPUS MODAL LOGIN - KARENA PAKAI HALAMAN TERPISAH -->
    <!-- Login Modal -->
   