<?php
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 6;
$offset = ($page - 1) * $records_per_page;

// Get category filter
$category_filter = "";
$category_param = "";
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category_filter = "WHERE n.category = :category";
    $category_param = $_GET['category'];
}

// Get total news count - FIXED: tanpa kolom status
$total_news_query = "SELECT COUNT(*) as total FROM news n $category_filter";
$total_news_stmt = $db->prepare($total_news_query);
if (!empty($category_param)) {
    $total_news_stmt->bindValue(':category', $category_param);
}
$total_news_stmt->execute();
$total_news = $total_news_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_news / $records_per_page);

// Get news with pagination - FIXED: tanpa kolom status
$news_query = "
    SELECT n.*, u.full_name as author_name 
    FROM news n 
    LEFT JOIN users u ON n.author_id = u.id 
    $category_filter
    ORDER BY n.created_at DESC 
    LIMIT :offset, :records_per_page
";
$news_stmt = $db->prepare($news_query);
if (!empty($category_param)) {
    $news_stmt->bindValue(':category', $category_param);
}
$news_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$news_stmt->bindValue(':records_per_page', $records_per_page, PDO::PARAM_INT);
$news_stmt->execute();
$news = $news_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get featured news - FIXED: tanpa kolom is_featured, ambil 3 berita terbaru sebagai featured
$featured_query = "
    SELECT n.*, u.full_name as author_name 
    FROM news n 
    LEFT JOIN users u ON n.author_id = u.id 
    ORDER BY n.created_at DESC 
    LIMIT 3
";
$featured_stmt = $db->prepare($featured_query);
$featured_stmt->execute();
$featured_news = $featured_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter - FIXED: tanpa kolom status
$categories_query = "
    SELECT DISTINCT category, COUNT(*) as count 
    FROM news 
    GROUP BY category 
    ORDER BY count DESC
";
$categories_stmt = $db->prepare($categories_query);
$categories_stmt->execute();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

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
            --gold-color: #F59E0B;
            --light-color: #F8FAFC;
            --dark-color: #1E293B;
        }
        
        .news-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .news-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,1000 1000,0 1000,1000"/></svg>');
            background-size: cover;
        }
        
        .news-hero-content {
            position: relative;
            z-index: 2;
        }
        
        .future-badge {
            background: linear-gradient(135deg, var(--gold-color), #D97706);
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 1rem;
            display: inline-block;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(245, 158, 11, 0.3);
        }
        
        .news-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: none;
            margin-bottom: 30px;
        }
        
        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .news-image {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }
        
        .news-category {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .news-title {
            font-weight: 700;
            color: var(--dark-color);
            line-height: 1.4;
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        
        .news-excerpt {
            color: #64748B;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .news-meta {
            color: #94A3B8;
            font-size: 0.85rem;
            border-top: 1px solid #E5E7EB;
            padding-top: 15px;
        }
        
        .category-filter {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .category-btn {
            background: #F8FAFC;
            border: 2px solid #E5E7EB;
            color: #64748B;
            padding: 8px 20px;
            border-radius: 25px;
            margin: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .category-btn:hover,
        .category-btn.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-color: var(--primary-color);
        }
        
        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin-bottom: 25px;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #64748B;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #CBD5E1;
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="news-hero">
        <div class="container">
            <div class="news-hero-content">
                <span class="future-badge">
                    <i class="fas fa-rocket me-2"></i>Membawa Masa Depan yang Lebih Cerah
                </span>
                <h1 class="display-4 fw-bold mb-3">Berita & Informasi</h1>
                <p class="lead mb-4">Ikuti perkembangan terbaru kegiatan, prestasi, dan informasi inspiratif dari Sekolah Inspirasi Bangsa</p>
                
                <!-- Quick Stats -->
                <div class="row justify-content-center">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo $total_news; ?></div>
                            <div class="stats-label">Total Berita</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo count($featured_news); ?></div>
                            <div class="stats-label">Berita Utama</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo count($categories); ?></div>
                            <div class="stats-label">Kategori</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <!-- Category Filter -->
        <?php if (count($categories) > 0): ?>
        <div class="category-filter">
            <h4 class="mb-3">Filter Berdasarkan Kategori:</h4>
            <div class="d-flex flex-wrap">
                <a href="news.php" class="category-btn <?php echo empty($category_param) ? 'active' : ''; ?>">
                    Semua Berita
                </a>
                <?php foreach ($categories as $cat): ?>
                <a href="news.php?category=<?php echo urlencode($cat['category']); ?>" 
                   class="category-btn <?php echo $category_param == $cat['category'] ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat['category']); ?> 
                    <span class="badge bg-light text-dark ms-1"><?php echo $cat['count']; ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Featured News -->
        <?php if (count($featured_news) > 0): ?>
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="mb-4">
                    <i class="fas fa-star me-2 text-warning"></i>
                    Berita Utama
                </h2>
            </div>
            <?php foreach ($featured_news as $featured): ?>
            <div class="col-lg-4 col-md-6">
                <div class="news-card">
                    <img src="<?php echo !empty($featured['image']) ? '../uploads/news/' . $featured['image'] : '../img/logo1.png'; ?>" 
                         alt="<?php echo htmlspecialchars($featured['title']); ?>" 
                         class="news-image">
                    <div class="p-4">
                        <span class="news-category"><?php echo !empty($featured['category']) ? htmlspecialchars($featured['category']) : 'Umum'; ?></span>
                        <h3 class="news-title"><?php echo htmlspecialchars($featured['title']); ?></h3>
                        <p class="news-excerpt">
                            <?php 
                            $excerpt = strip_tags($featured['content']);
                            echo strlen($excerpt) > 120 ? substr($excerpt, 0, 120) . '...' : $excerpt;
                            ?>
                        </p>
                        <div class="news-meta">
                            <div class="d-flex justify-content-between">
                                <?php if (!empty($featured['author_name'])): ?>
                                <span><i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($featured['author_name']); ?></span>
                                <?php endif; ?>
                                <span><i class="fas fa-calendar me-1"></i> <?php echo date('d M Y', strtotime($featured['created_at'])); ?></span>
                            </div>
                        </div>
                        <a href="news_detail.php?id=<?php echo $featured['id']; ?>" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-book-open me-2"></i>Baca Selengkapnya
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- All News -->
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">
                    <i class="fas fa-newspaper me-2 text-primary"></i>
                    Semua Berita
                    <?php if (!empty($category_param)): ?>
                    <small class="text-muted">- Kategori: <?php echo htmlspecialchars($category_param); ?></small>
                    <?php endif; ?>
                </h2>
            </div>
            
            <?php if (count($news) > 0): ?>
                <?php foreach ($news as $item): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="news-card">
                        <img src="<?php echo !empty($item['image']) ? '../uploads/news/' . $item['image'] : '../img/logo1.png'; ?>" 
                             alt="<?php echo htmlspecialchars($item['title']); ?>" 
                             class="news-image">
                        <div class="p-4">
                            <span class="news-category"><?php echo !empty($item['category']) ? htmlspecialchars($item['category']) : 'Umum'; ?></span>
                            <h3 class="news-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p class="news-excerpt">
                                <?php 
                                $excerpt = strip_tags($item['content']);
                                echo strlen($excerpt) > 120 ? substr($excerpt, 0, 120) . '...' : $excerpt;
                                ?>
                            </p>
                            <div class="news-meta">
                                <div class="d-flex justify-content-between">
                                    <?php if (!empty($item['author_name'])): ?>
                                    <span><i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($item['author_name']); ?></span>
                                    <?php endif; ?>
                                    <span><i class="fas fa-calendar me-1"></i> <?php echo date('d M Y', strtotime($item['created_at'])); ?></span>
                                </div>
                            </div>
                            <a href="news_detail.php?id=<?php echo $item['id']; ?>" class="btn btn-outline-primary w-100 mt-3">
                                <i class="fas fa-book-open me-2"></i>Baca Selengkapnya
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-newspaper"></i>
                        <h4 class="text-muted">Tidak ada berita</h4>
                        <p class="text-muted">
                            <?php if (!empty($category_param)): ?>
                            Tidak ada berita dalam kategori "<?php echo htmlspecialchars($category_param); ?>"
                            <?php else: ?>
                            Belum ada berita yang diterbitkan
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-5">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="news.php?page=<?php echo $page - 1; ?><?php echo !empty($category_param) ? '&category=' . urlencode($category_param) : ''; ?>">
                        <i class="fas fa-chevron-left me-2"></i>Sebelumnya
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="news.php?page=<?php echo $i; ?><?php echo !empty($category_param) ? '&category=' . urlencode($category_param) : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="news.php?page=<?php echo $page + 1; ?><?php echo !empty($category_param) ? '&category=' . urlencode($category_param) : ''; ?>">
                        Selanjutnya<i class="fas fa-chevron-right ms-2"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>