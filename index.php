<?php
$page_title = "Sekolah Inspirasi Bangsa - Masa Depan Cerah";
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get dynamic content
$content_query = "SELECT * FROM dynamic_content";
$content_stmt = $db->prepare($content_query);
$content_stmt->execute();
$dynamic_content = $content_stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function
function getContent($content_array, $key, $default = '') {
    foreach($content_array as $content) {
        if ($content['key_name'] === $key) {
            return $content['content_value'];
        }
    }
    return $default;
}

// Get hero image
$hero_image = 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&h=400&fit=crop';
foreach($dynamic_content as $content) {
    if ($content['key_name'] === 'hero_image' && !empty($content['content_value'])) {
        $hero_image = $content['content_value'];
        if (strpos($hero_image, 'http') === false) {
            $hero_image = $hero_image; // Relative path
        }
    }
}

// Get news
$news_query = "SELECT * FROM news WHERE is_published = 1 ORDER BY published_at DESC LIMIT 3";
$news_stmt = $db->prepare($news_query);
$news_stmt->execute();
$news = $news_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get approved testimonials
$testimonials_query = "SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 8";
$testimonials_stmt = $db->prepare($testimonials_query);
$testimonials_stmt->execute();
$testimonials = $testimonials_stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title animate-fade-in"><?php echo getContent($dynamic_content, 'hero_title', 'Masa Depan Cerah Dimulai Dari Sini'); ?></h1>
                <p class="hero-subtitle animate-fade-in animate-delay-1">
                    <?php echo getContent($dynamic_content, 'hero_subtitle', 'Sekolah Inspirasi Bangsa - Membentuk generasi penerus yang berkarakter, cerdas, dan siap menghadapi masa depan dengan penuh percaya diri'); ?>
                </p>
                <div class="animate-fade-in animate-delay-2">
                    <a href="#about" class="btn btn-light btn-hero">Jelajahi Sekolah</a>
                    <a href="#contact" class="btn btn-outline-light btn-hero">Hubungi Kami</a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="<?php echo $hero_image; ?>" 
                     alt="Sekolah Inspirasi Bangsa" 
                     class="img-fluid rounded-3 shadow-lg animate-fade-in">
            </div>
        </div>
    </div>
</section>

<!-- About Section - POSISI ASLI SETELAH HERO -->
<section id="about" class="section-py about-section">
    <div class="container">
        <div class="section-title">
            <h2><?php echo getContent($dynamic_content, 'about_title', 'Mengapa Memilih Kami?'); ?></h2>
            <p><?php echo getContent($dynamic_content, 'about_description', 'Kami berkomitmen untuk memberikan pendidikan terbaik yang membentuk karakter dan mengembangkan potensi setiap siswa'); ?></p>
        </div>
        
        <div class="row content-spacing">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4>Pendidikan Karakter</h4>
                    <p>Membentuk siswa dengan nilai-nilai moral, etika, dan karakter yang kuat untuk menjadi pribadi yang bermartabat dan bertanggung jawab</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h4>Guru Berpengalaman</h4>
                    <p>Didukung oleh tenaga pendidik yang kompeten, berdedikasi tinggi, berpengalaman, dan memiliki passion dalam mengajar</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h4>Inovasi Pembelajaran</h4>
                    <p>Metode pembelajaran modern yang kreatif, inovatif, dan menyenangkan untuk mengembangkan potensi maksimal setiap siswa</p>
                </div>
            </div>
        </div>
        
        <!-- VISI MISI SECTION KEMBALI -->
        <div class="row inner-spacing">
            <div class="col-lg-6">
                <h3 class="mb-4" style="color: var(--primary-dark);">Visi Kami</h3>
                <p class="mb-4">Menjadi lembaga pendidikan unggulan yang mencetak generasi muda Indonesia yang berakhlak mulia, berprestasi, kreatif, inovatif, dan berkontribusi positif bagi masyarakat.</p>
                
                <h3 class="mb-4" style="color: var(--primary-dark);">Misi Kami</h3>
                <ul class="list-unstyled">
                    <li class="mb-3"><i class="fas fa-check text-success me-2"></i>Menyelenggarakan pendidikan berkualitas tinggi</li>
                    <li class="mb-3"><i class="fas fa-check text-success me-2"></i>Membangun karakter yang kuat dan berintegritas</li>
                    <li class="mb-3"><i class="fas fa-check text-success me-2"></i>Mengembangkan potensi unik setiap siswa</li>
                    <li class="mb-3"><i class="fas fa-check text-success me-2"></i>Menjalin kerjasama erat dengan orang tua</li>
                    <li class="mb-3"><i class="fas fa-check text-success me-2"></i>Menciptakan lingkungan belajar yang inspiratif</li>
                </ul>
            </div>
            
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1588072432836-4b4f32c93d8f?w=600&h=400&fit=crop" 
                     alt="Proses Belajar Mengajar" 
                     class="img-fluid rounded-3 shadow">
            </div>
        </div>
    </div>
</section>

<!-- News Section - POSISI ASLI SETELAH ABOUT -->
<section id="news" class="section-py news-section">
    <div class="container">
        <div class="section-title">
            <h2>Berita & Kegiatan Terbaru</h2>
            <p>Ikuti perkembangan terbaru dan kegiatan menarik di sekolah kami</p>
        </div>
        
        <div class="row content-spacing">
            <?php if (count($news) > 0): ?>
                <?php foreach ($news as $item): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="news-card">
                            <div class="position-relative">
                                <img src="<?php echo $item['image'] ?: 'https://images.unsplash.com/photo-1588072432836-4b4f32c93d8f?w=400&h=250&fit=crop'; ?>" 
                                     alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                     class="news-img">
                                <span class="news-badge badge-<?php echo $item['category']; ?>">
                                    <?php echo ucfirst($item['category']); ?>
                                </span>
                            </div>
                            <div class="news-content">
                                <h5 class="mb-3"><?php echo htmlspecialchars($item['title']); ?></h5>
                                <p class="text-muted mb-3">
                                    <?php echo htmlspecialchars($item['excerpt'] ?: substr($item['content'], 0, 100) . '...'); ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>
                                        <?php echo date('d M Y', strtotime($item['published_at'])); ?>
                                    </small>
                                    <a href="pages/news_detail.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">
                                        Baca Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Tidak ada berita untuk ditampilkan.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center element-spacing">
            <a href="pages/news.php" class="btn btn-primary btn-lg">
                <i class="fas fa-newspaper me-2"></i>Lihat Semua Berita
            </a>
        </div>
    </div>
</section>

<!-- Testimonials Carousel Section - SETELAH BERITA -->
<section id="testimonials" class="section-py testimonials-section">
    <div class="container">
        <div class="section-title">
            <h2 style="color: white;">Apa Kata Mereka?</h2>
            <p style="color: rgba(255,255,255,0.9);">Testimoni dari orang tua, siswa, dan alumni sekolah kami</p>
        </div>
        
        <?php if (count($testimonials) > 0): ?>
            <div class="testimonial-carousel-container">
                <div class="testimonial-carousel-wrapper">
                    <div class="testimonial-carousel" id="testimonialCarousel">
                        <?php foreach ($testimonials as $testimonial): ?>
                            <div class="testimonial-carousel-item">
                                <div class="testimonial-card-carousel">
                                    <div class="testimonial-header">
                                        <img src="<?php echo htmlspecialchars($testimonial['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($testimonial['name']); ?>" 
                                             class="testimonial-img-carousel"
                                             onerror="this.src='https://randomuser.me/api/portraits/lego/1.jpg'">
                                        <div class="testimonial-info">
                                            <h5><?php echo htmlspecialchars($testimonial['name']); ?></h5>
                                            <p class="role"><?php echo htmlspecialchars($testimonial['role']); ?></p>
                                        </div>
                                    </div>
                                    <div class="stars text-warning mb-3">
                                        <?php 
                                        $rating = $testimonial['rating'];
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $rating) {
                                                echo '<i class="fas fa-star"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <p class="testimonial-content">"<?php echo htmlspecialchars($testimonial['content']); ?>"</p>
                                    <div class="testimonial-date">
                                        <small><?php echo date('d M Y', strtotime($testimonial['created_at'])); ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if (count($testimonials) > 3): ?>
                    <button class="carousel-nav carousel-prev" id="carouselPrev">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="carousel-nav carousel-next" id="carouselNext">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                <?php endif; ?>
                
                <!-- Dots Indicator -->
                <?php if (count($testimonials) > 3): ?>
                    <div class="carousel-dots" id="carouselDots"></div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-comments fa-4x text-white-50 mb-3"></i>
                <h4 style="color: rgba(255,255,255,0.8);">Belum ada testimoni</h4>
                <p style="color: rgba(255,255,255,0.6);">Jadilah yang pertama memberikan testimoni!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Add Testimonial Form Section -->
<section id="testimonial-form" class="section-py bg-light">
    <div class="container">
        <div class="section-title">
            <h2>Berikan Testimoni Anda</h2>
            <p>Bagikan pengalaman dan kesan Anda tentang Sekolah Inspirasi Bangsa</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-container">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form action="api/add_testimonial.php" method="POST" enctype="multipart/form-data" id="testimonialForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Peran *</label>
                                <select name="role" class="form-select" required>
                                    <option value="">Pilih Peran</option>
                                    <option value="Orang Tua Siswa">Orang Tua Siswa</option>
                                    <option value="Siswa">Siswa</option>
                                    <option value="Alumni">Alumni</option>
                                    <option value="Guru">Guru</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Testimoni *</label>
                            <textarea name="content" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rating *</label>
                            <select name="rating" class="form-select" required>
                                <option value="">Pilih Rating</option>
                                <option value="5">⭐⭐⭐⭐⭐ (Sangat Puas)</option>
                                <option value="4">⭐⭐⭐⭐ (Puas)</option>
                                <option value="3">⭐⭐⭐ (Cukup)</option>
                                <option value="2">⭐⭐ (Kurang)</option>
                                <option value="1">⭐ (Sangat Kurang)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Foto Profil (Opsional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Testimoni
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Location Section -->
<section id="location" class="section-py location-section">
    <div class="container">
        <div class="section-title">
            <h2>Lokasi Sekolah</h2>
            <p>Kunjungi sekolah kami di lokasi yang strategis dan nyaman</p>
        </div>
        
        <div class="row content-spacing align-items-center">
            <div class="col-lg-6 mb-4">
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d524.5085454811997!2d98.04356649073296!3d4.302494037500535!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x303775a66fbfb48d%3A0x63352150d883bf39!2sSEKOLAH%20INSPIRASI!5e1!3m2!1sen!2sid!4v1756302216186!5m2!1sen!2sid" 
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="contact-info">
                    <h3>Informasi Kontak</h3>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1" style="color: var(--primary-dark);">Alamat</h6>
                            <p class="mb-0 text-muted"><?php echo getContent($dynamic_content, 'contact_address', 'Kebun Tanah Terban, Karang Baru, Aceh Tamiang Regency, Aceh'); ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h6 class="mb-1" style="color: var(--primary-dark);">Telepon</h6>
                            <p class="mb-0 text-muted"><?php echo getContent($dynamic_content, 'contact_phone', '0822 6701 3262'); ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h6 class="mb-1" style="color: var(--primary-dark);">Email</h6>
                            <p class="mb-0 text-muted"><?php echo getContent($dynamic_content, 'contact_email', 'humassekolahinspirasi@gmail.com'); ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h6 class="mb-1" style="color: var(--primary-dark);">Jam Operasional</h6>
                            <p class="mb-0 text-muted">Senin - Jumat: 07.00 - 16.00 WIB</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="section-py contact-section">
    <div class="container">
        <div class="section-title">
            <h2>Hubungi Kami</h2>
            <p>Kami siap mendengarkan pertanyaan, masukan, dan kerjasama dari Anda</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="contact-card">
                    <form action="api/send_contact.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Subjek *</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Pesan *</label>
                            <textarea name="message" class="form-control" rows="5" required></textarea>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- CSS dan JavaScript untuk carousel -->
<style>
.testimonial-carousel-container {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 60px;
}

.testimonial-carousel-wrapper {
    overflow: hidden;
    border-radius: 20px;
}

.testimonial-carousel {
    display: flex;
    transition: transform 0.5s ease-in-out;
    gap: 25px;
    padding: 10px 0;
}

.testimonial-carousel-item {
    flex: 0 0 calc(33.333% - 17px);
    min-width: calc(33.333% - 17px);
}

.testimonial-card-carousel {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.testimonial-card-carousel:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-5px);
}

.testimonial-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.testimonial-img-carousel {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255, 255, 255, 0.4);
    margin-right: 1rem;
}

.testimonial-info h5 {
    color: white;
    margin: 0;
    font-weight: 700;
}

.testimonial-info .role {
    color: rgba(255, 255, 255, 0.9);
    margin: 5px 0 0 0;
}

.testimonial-content {
    color: white;
    font-style: italic;
    line-height: 1.7;
    flex-grow: 1;
    margin-bottom: 1.5rem;
}

.carousel-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.25);
    border: 2px solid rgba(255, 255, 255, 0.3);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    z-index: 10;
}

.carousel-prev { left: 0; }
.carousel-next { right: 0; }

.carousel-nav:hover {
    background: rgba(255, 255, 255, 0.4);
}

/* Carousel Dots */
.carousel-dots {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
    gap: 8px;
}

.carousel-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.carousel-dot.active {
    background: white;
    transform: scale(1.2);
}

@media (max-width: 768px) {
    .testimonial-carousel-item {
        flex: 0 0 calc(100% - 20px);
        min-width: calc(100% - 20px);
    }
    
    .testimonial-carousel-container {
        padding: 0 20px;
    }
    
    .carousel-nav {
        width: 45px;
        height: 45px;
        font-size: 1.2rem;
    }
}

/* Form Container */
.form-container {
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: 1px solid #E5E7EB;
}
</style>

<script>
// Testimonial Carousel Functionality
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('testimonialCarousel');
    const prevBtn = document.getElementById('carouselPrev');
    const nextBtn = document.getElementById('carouselNext');
    const dotsContainer = document.getElementById('carouselDots');
    
    if (!carousel) return;
    
    const items = carousel.querySelectorAll('.testimonial-carousel-item');
    const itemCount = items.length;
    
    if (itemCount === 0) return;
    
    let currentIndex = 0;
    let itemsPerView = getItemsPerView();
    
    function getItemsPerView() {
        if (window.innerWidth <= 768) return 1;
        if (window.innerWidth <= 1024) return 2;
        return 3;
    }
    
    // Create dots indicator
    function createDots() {
        if (!dotsContainer) return;
        
        dotsContainer.innerHTML = '';
        const dotCount = Math.ceil(itemCount / itemsPerView);
        
        for (let i = 0; i < dotCount; i++) {
            const dot = document.createElement('button');
            dot.className = 'carousel-dot';
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(i));
            dotsContainer.appendChild(dot);
        }
    }
    
    // Update carousel position
    function updateCarousel() {
        const itemWidth = items[0].offsetWidth + 25;
        const translateX = -currentIndex * itemWidth;
        carousel.style.transform = `translateX(${translateX}px)`;
        updateDots();
    }
    
    // Update dots indicator
    function updateDots() {
        if (!dotsContainer) return;
        
        const dots = dotsContainer.querySelectorAll('.carousel-dot');
        const activeDotIndex = Math.floor(currentIndex / itemsPerView);
        
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === activeDotIndex);
        });
    }
    
    // Navigate to specific slide
    function goToSlide(index) {
        currentIndex = index * itemsPerView;
        if (currentIndex >= itemCount) {
            currentIndex = 0;
        }
        updateCarousel();
    }
    
    // Next slide
    function nextSlide() {
        if (currentIndex < itemCount - itemsPerView) {
            currentIndex += itemsPerView;
        } else {
            currentIndex = 0;
        }
        updateCarousel();
    }
    
    // Previous slide
    function prevSlide() {
        if (currentIndex > 0) {
            currentIndex -= itemsPerView;
        } else {
            currentIndex = Math.floor((itemCount - 1) / itemsPerView) * itemsPerView;
        }
        updateCarousel();
    }
    
    // Event listeners
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    
    // Initialize
    function initCarousel() {
        updateCarousel();
        createDots();
        
        // Auto slide
        setInterval(nextSlide, 5000);
    }
    
    // Initialize on load
    initCarousel();
    
    // Handle resize
    window.addEventListener('resize', function() {
        itemsPerView = getItemsPerView();
        currentIndex = 0;
        updateCarousel();
        createDots();
    });
});
</script>