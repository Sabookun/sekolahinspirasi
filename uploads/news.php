<?php
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 6;
$offset = ($page - 1) * $records_per_page;

// Get total news count
$total_news_query = "SELECT COUNT(*) as total FROM news WHERE status = 'published'";
$total_news_stmt = $db->prepare($total_news_query);
$total_news_stmt->execute();
$total_news = $total_news_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_news / $records_per_page);

// Get news with pagination
$news_query = "
    SELECT n.*, u.full_name as author_name 
    FROM news n 
    LEFT JOIN users u ON n.author_id = u.id 
    WHERE n.status = 'published' 
    ORDER BY n.created_at DESC 
    LIMIT :offset, :records_per_page
";
$news_stmt = $db->prepare($news_query);
$news_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$news_stmt->bindValue(':records_per_page', $records_per_page, PDO::PARAM_INT);
$news_stmt->execute();
$news = $news_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get featured news (latest 3)
$featured_query = "
    SELECT n.*, u.full_name as author_name 
    FROM news n 
    LEFT JOIN users u ON n.author_id = u.id 
    WHERE n.status = 'published' AND n.is_featured = 1 
    ORDER BY n.created_at DESC 
    LIMIT 3
";
$featured_stmt = $db->prepare($featured_query);
$featured_stmt->execute();
$featured_news = $featured_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Berita - Sekolah Inspirasi Bangsa";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #3B82F6;
            --secondary-color: #1E40AF;
            --accent-color: #10B981;
            --dark-color: #1E293B;
            --light-color: #F8FAFC;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
        }
        
        .news-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            border: none;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .news-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        
        .news-category {
            background: var(--primary-color);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .news-title {
            font-weight: 700;
            color: var(--dark-color);
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .news-excerpt {
            color: #64748B;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .news-meta {
            color: #94A3B8;
            font-size: 0.85rem;
        }
        
        .featured-news {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            height: 400px;
            margin-bottom: 2rem;
        }
        
        .featured-news img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .featured-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 2rem;
        }
        
        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 2rem;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }
        
        .pagination .page-link {
            color: var(--primary-color);
            border: 1px solid #E5E7EB;
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .sidebar-widget {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        
        .widget-title {
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            padding-bottom: 10px;
            border-bottom: 2px solid #E5E7EB;
        }
        
        .popular-news-item {
            display: flex;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .popular-news-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .popular-news-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }
        
        .popular-news-title {
            font-weight: 600;
            font-size: 0.9rem;
            line-height: 1.3;
            margin-bottom: 5px;
        }
        
        .categories-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .categories-list li {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .categories-list li:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .categories-list a {
            color: #64748B;
            text-decoration: none;
            display: flex;
            justify-content: space-between;
            transition: color 0.3s ease;
        }
        
        .categories-list a:hover {
            color: var(--primary-color);
        }
        
        .categories-list .badge {
            background: #E5E7EB;
            color: #64748B;
        }
        
        .footer {
            background: var(--dark-color);
            color: white;
            padding: 3rem 0;
            margin-top: 4rem;
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 2rem 0;
            }
            
            .featured-news {
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-school me-2"></i>Sekolah Inspirasi Bangsa
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="news.php">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="testimonials.php">Testimoni</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">Berita & Informasi Terkini</h1>
                    <p class="lead mb-0">Ikuti perkembangan terbaru seputar kegiatan, prestasi, dan informasi dari Sekolah Inspirasi Bangsa</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <i class="fas fa-newspaper fa-5x opacity-75"></i>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- Featured News -->
        <?php if (count($featured_news) > 0): ?>
        <section class="mb-5">
            <h2 class="section-title">Berita Utama</h2>
            <div class="row">
                <?php foreach ($featured_news as $featured): ?>
                <div class="col-lg-4 mb-4">
                    <div class="featured-news">
                        <img src="<?php echo !empty($featured['image']) ? 'uploads/news/' . $featured['image'] : 'img/logo1.png'; ?>" alt="<?php echo htmlspecialchars($featured['title']); ?>">
                        <div class="featured-overlay">
                            <span class="news-category">Utama</span>
                            <h3 class="h4"><?php echo htmlspecialchars($featured['title']); ?></h3>
                            <p class="mb-0"><?php echo date('d M Y', strtotime($featured['created_at'])); ?></p>
                            <a href="news_detail.php?id=<?php echo $featured['id']; ?>" class="btn btn-primary btn-sm mt-2">Baca Selengkapnya</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <h2 class="section-title">Semua Berita</h2>
                
                <?php if (count($news) > 0): ?>
                    <div class="row">
                        <?php foreach ($news as $item): ?>
                        <div class="col-md-6 mb-4">
                            <div class="news-card">
                                <img src="<?php echo !empty($item['image']) ? 'uploads/news/' . $item['image'] : 'img/logo1.png'; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="news-image">
                                <div class="p-3">
                                    <span class="news-category">Berita</span>
                                    <h3 class="h5 news-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <p class="news-excerpt"><?php 
                                        $excerpt = strip_tags($item['content']);
                                        echo strlen($excerpt) > 120 ? substr($excerpt, 0, 120) . '...' : $excerpt;
                                    ?></p>
                                    <div class="d-flex justify-content-between align-items-center news-meta">
                                        <span><i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($item['author_name']); ?></span>
                                        <span><i class="fas fa-calendar me-1"></i> <?php echo date('d M Y', strtotime($item['created_at'])); ?></span>
                                    </div>
                                    <a href="news_detail.php?id=<?php echo $item['id']; ?>" class="btn btn-outline-primary btn-sm mt-3 w-100">Baca Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="news.php?page=<?php echo $page - 1; ?>">Sebelumnya</a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="news.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="news.php?page=<?php echo $page + 1; ?>">Selanjutnya</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Belum ada berita</h4>
                        <p class="text-muted">Silakan kembali lagi nanti untuk melihat berita terbaru.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Popular News Widget -->
                <div class="sidebar-widget">
                    <h4 class="widget-title">Berita Populer</h4>
                    <?php
                    $popular_query = "
                        SELECT n.*, u.full_name as author_name 
                        FROM news n 
                        LEFT JOIN users u ON n.author_id = u.id 
                        WHERE n.status = 'published' 
                        ORDER BY n.views DESC 
                        LIMIT 5
                    ";
                    $popular_stmt = $db->prepare($popular_query);
                    $popular_stmt->execute();
                    $popular_news = $popular_stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <?php if (count($popular_news) > 0): ?>
                        <?php foreach ($popular_news as $popular): ?>
                        <div class="popular-news-item">
                            <img src="<?php echo !empty($popular['image']) ? 'uploads/news/' . $popular['image'] : 'img/logo1.png'; ?>" alt="<?php echo htmlspecialchars($popular['title']); ?>" class="popular-news-img">
                            <div>
                                <h5 class="popular-news-title"><?php echo htmlspecialchars($popular['title']); ?></h5>
                                <div class="news-meta">
                                    <small><i class="fas fa-eye me-1"></i> <?php echo $popular['views']; ?> views</small>
                                </div>
                                <a href="news_detail.php?id=<?php echo $popular['id']; ?>" class="btn btn-link btn-sm p-0 text-primary">Baca</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Belum ada berita populer.</p>
                    <?php endif; ?>
                </div>

                <!-- Categories Widget -->
                <div class="sidebar-widget">
                    <h4 class="widget-title">Kategori</h4>
                    <?php
                    $categories_query = "
                        SELECT category, COUNT(*) as count 
                        FROM news 
                        WHERE status = 'published' 
                        GROUP BY category 
                        ORDER BY count DESC
                    ";
                    $categories_stmt = $db->prepare($categories_query);
                    $categories_stmt->execute();
                    $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <ul class="categories-list">
                        <?php if (count($categories) > 0): ?>
                            <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="news.php?category=<?php echo urlencode($category['category']); ?>">
                                    <?php echo htmlspecialchars($category['category']); ?>
                                    <span class="badge"><?php echo $category['count']; ?></span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a href="#">Umum <span class="badge">0</span></a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Newsletter Widget -->
                <div class="sidebar-widget bg-primary text-white">
                    <h4 class="widget-title text-white">Newsletter</h4>
                    <p>Dapatkan berita terbaru langsung ke email Anda.</p>
                    <form>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Alamat email Anda" required>
                        </div>
                        <button type="submit" class="btn btn-light w-100">Berlangganan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="mb-3">Sekolah Inspirasi Bangsa</h5>
                    <p>Membentuk generasi penerus yang berkarakter, berprestasi, dan berakhlak mulia.</p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5 class="mb-3">Tautan Cepat</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light text-decoration-none">Beranda</a></li>
                        <li><a href="news.php" class="text-light text-decoration-none">Berita</a></li>
                        <li><a href="testimonials.php" class="text-light text-decoration-none">Testimoni</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5 class="mb-3">Kontak</h5>
                    <p><i class="fas fa-map-marker-alt me-2"></i> Jl. Pendidikan No. 123, Jakarta</p>
                    <p><i class="fas fa-phone me-2"></i> (021) 1234-5678</p>
                    <p><i class="fas fa-envelope me-2"></i> info@inspirasibangsa.sch.id</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2025 Sekolah Inspirasi Bangsa. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>