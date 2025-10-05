<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Get all news with author info
$news_query = "
    SELECT n.*, u.full_name as author_name 
    FROM news n 
    LEFT JOIN users u ON n.author_id = u.id 
    ORDER BY n.created_at DESC
";
$news_stmt = $db->prepare($news_query);
$news_stmt->execute();
$all_news = $news_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Kelola Berita - Admin";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #3B82F6;
            --primary-dark: #1D4ED8;
            --gradient: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
            --light: #F8FAFC;
            --light-blue: #EFF6FF;
            --shadow: 0 4px 6px rgba(59, 130, 246, 0.1);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light);
        }
        
        .sidebar {
            background: var(--gradient);
            color: white;
            height: 100vh;
            position: fixed;
            width: 280px;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 0;
        }
        
        .navbar-top {
            background: white;
            box-shadow: var(--shadow);
            padding: 1rem 2rem;
        }
        
        .content-area {
            padding: 2rem;
        }
        
        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        
        .table-card-header {
            background: var(--light-blue);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .btn-group-sm > .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .badge {
            font-size: 0.75em;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header p-3">
            <h4><i class="fas fa-graduation-cap me-2"></i>Admin Panel</h4>
            <p class="mb-0 opacity-75">Sekolah Inspirasi Bangsa</p>
        </div>
        
        <div class="sidebar-menu p-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active text-white" href="news.php">
                        <i class="fas fa-newspaper me-2"></i>Kelola Berita
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="testimonials.php">
                        <i class="fas fa-comments me-2"></i>Kelola Testimoni
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="content_manager.php">
                        <i class="fas fa-edit me-2"></i>Kelola Konten
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="users.php">
                        <i class="fas fa-users me-2"></i>Kelola Pengguna
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link text-white" href="../auth/logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <nav class="navbar-top">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h5 class="mb-0">Kelola Berita</h5>
                    <div class="d-flex align-items-center">
                        <span class="me-3">Halo, <?php echo $_SESSION['user_name']; ?></span>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>Akun
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="../index.php"><i class="fas fa-home me-2"></i>Beranda</a></li>
                                <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="content-area">
            <!-- Success/Error Messages -->
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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Daftar Berita</h4>
                <div>
                    <a href="news_create.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Berita Baru
                    </a>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Views</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($all_news) > 0): ?>
                                <?php foreach ($all_news as $news): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($news['image'])): ?>
                                                <img src="../uploads/news/<?php echo htmlspecialchars($news['image']); ?>" 
                                                     alt="Thumbnail" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?php echo htmlspecialchars($news['title']); ?></strong>
                                                <?php if ($news['is_featured']): ?>
                                                    <br><small class="text-warning"><i class="fas fa-star"></i> Featured</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo ucfirst($news['category']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $news['status'] == 'published' ? 'bg-success' : 'bg-warning'; ?>">
                                            <?php echo ucfirst($news['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($news['is_featured']): ?>
                                            <span class="badge bg-warning"><i class="fas fa-star"></i> Featured</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Normal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-eye me-1"></i><?php echo $news['views']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($news['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="news_edit.php?id=<?php echo $news['id']; ?>" class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="news_toggle_featured.php?id=<?php echo $news['id']; ?>" class="btn btn-outline-warning" title="<?php echo $news['is_featured'] ? 'Hapus Featured' : 'Jadikan Featured'; ?>">
                                                <i class="fas fa-star"></i>
                                            </a>
                                            <a href="news_toggle_status.php?id=<?php echo $news['id']; ?>" class="btn btn-outline-<?php echo $news['status'] == 'published' ? 'warning' : 'success'; ?>" title="<?php echo $news['status'] == 'published' ? 'Set Draft' : 'Publish'; ?>">
                                                <i class="fas fa-<?php echo $news['status'] == 'published' ? 'eye-slash' : 'eye'; ?>"></i>
                                            </a>
                                            <a href="news_delete.php?id=<?php echo $news['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Yakin hapus berita ini?')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-newspaper fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">Belum ada berita</p>
                                        <a href="news_create.php" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus me-2"></i>Tambah Berita Pertama
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Quick actions dengan AJAX
    document.addEventListener('DOMContentLoaded', function() {
        // Quick toggle featured
        const featuredButtons = document.querySelectorAll('a[href*="news_toggle_featured"]');
        featuredButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.href;
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
        
        // Quick toggle status
        const statusButtons = document.querySelectorAll('a[href*="news_toggle_status"]');
        statusButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Yakin ubah status berita?')) {
                    const url = this.href;
                    
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        });
    });
    </script>
</body>
</html>