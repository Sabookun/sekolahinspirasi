<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Get statistics dengan error handling
try {
    // Total News
    $total_news = $db->query("SELECT COUNT(*) FROM news")->fetchColumn();
    
    // Total Testimonials
    $total_testimonials = $db->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
    
    // Pending Testimonials
    $pending_testimonials = $db->query("SELECT COUNT(*) FROM testimonials WHERE is_approved = 0")->fetchColumn();
    
    // Total Users
    $total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    
    // Published News
    $published_news = $db->query("SELECT COUNT(*) FROM news WHERE is_published = 1")->fetchColumn();
    
    // Get recent activities dengan error handling
    $recent_news = $db->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $recent_testimonials = $db->query("SELECT * FROM testimonials ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle database errors gracefully
    error_log("Database error in dashboard: " . $e->getMessage());
    $total_news = $total_testimonials = $pending_testimonials = $total_users = $published_news = 0;
    $recent_news = $recent_testimonials = [];
}

$page_title = "Admin Dashboard - Sekolah Inspirasi Bangsa";
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
        /* CSS styling sama seperti sebelumnya */
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
            --shadow: 0 4px 6px rgba(59, 130, 246, 0.1);
            --shadow-lg: 0 10px 25px rgba(59, 130, 246, 0.15);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light);
            color: var(--text);
        }
        
        /* Sidebar Styles */
        .sidebar {
            background: var(--gradient);
            color: white;
            height: 100vh;
            position: fixed;
            width: 280px;
            transition: all 0.3s;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-weight: 600;
        }
        
        .sidebar-header p {
            opacity: 0.8;
            margin: 0.25rem 0 0 0;
            font-size: 0.9rem;
        }
        
        .sidebar-menu {
            padding: 1.5rem 0;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            margin: 0.25rem 0.5rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            transform: translateX(5px);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 0;
            min-height: 100vh;
        }
        
        .navbar-top {
            background: white;
            box-shadow: var(--shadow);
            padding: 1rem 2rem;
            border-bottom: 1px solid #E5E7EB;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .content-area {
            padding: 2rem;
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .stat-news { background: var(--light-blue); color: var(--primary); }
        .stat-testimonials { background: #F0F9FF; color: var(--success); }
        .stat-pending { background: #FFFBEB; color: var(--warning); }
        .stat-users { background: #F0FDF4; color: var(--success); }
        .stat-published { background: #ECFDF5; color: var(--success); }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: var(--dark);
        }
        
        .stat-label {
            color: var(--text);
            opacity: 0.7;
            font-size: 0.9rem;
        }
        
        /* Tables */
        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .table-card-header {
            background: var(--light-blue);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .table-card-header h5 {
            margin: 0;
            color: var(--primary-dark);
            font-weight: 600;
        }
        
        .table {
            margin: 0;
        }
        
        .table th {
            background: var(--light-blue);
            border-bottom: 1px solid #E5E7EB;
            font-weight: 600;
            color: var(--primary-dark);
            padding: 1rem;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        
        .btn-sm {
            border-radius: 8px;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            margin: 0 2px;
        }
        
        /* Welcome Alert */
        .welcome-alert {
            border-left: 4px solid var(--primary);
            background: var(--light-blue);
        }
        
        /* Quick Actions */
        .quick-actions .btn {
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 500;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .content-area {
                padding: 1rem;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
        }

        /* Star Rating */
        .stars {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Include Sidebar Admin -->
    <?php include 'includes/sidebar_admin.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar-top">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h5 class="mb-0">Dashboard Admin</h5>
                    <div class="d-flex align-items-center">
                        <span class="me-3">Halo, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>Akun
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="../index.php"><i class="fas fa-home me-2"></i>Beranda</a></li>
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content Area (sama seperti sebelumnya) -->
        <div class="content-area">
            <!-- Welcome Message -->
            <div class="alert welcome-alert mb-4">
                <h5>Selamat datang, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h5>
                <p class="mb-0">Anda login sebagai <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong>.</p>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon stat-news">
                            <i class="fas fa-newspaper fa-lg"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_news; ?></div>
                        <div class="stat-label">Total Berita</div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon stat-testimonials">
                            <i class="fas fa-comments fa-lg"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_testimonials; ?></div>
                        <div class="stat-label">Total Testimoni</div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon stat-pending">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                        <div class="stat-number"><?php echo $pending_testimonials; ?></div>
                        <div class="stat-label">Menunggu Approve</div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon stat-users">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_users; ?></div>
                        <div class="stat-label">Total Pengguna</div>
                    </div>
                </div>
            </div>

            <!-- Recent News & Testimonials -->
            <div class="row">
                <!-- Recent News -->
                <div class="col-lg-6 mb-4">
                    <div class="table-card">
                        <div class="table-card-header">
                            <h5><i class="fas fa-newspaper me-2"></i>Berita Terbaru</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($recent_news) > 0): ?>
                                        <?php foreach ($recent_news as $news): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars(substr($news['title'], 0, 30) . (strlen($news['title']) > 30 ? '...' : '')); ?></td>
                                            <td>
                                                <span class="badge <?php echo $news['is_published'] ? 'bg-success' : 'bg-warning'; ?>">
                                                    <?php echo $news['is_published'] ? 'Published' : 'Draft'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d M Y', strtotime($news['created_at'])); ?></td>
                                            <td>
                                                <a href="news_edit.php?id=<?php echo $news['id']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">Tidak ada berita</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-card-header text-center">
                            <a href="news.php" class="btn btn-outline-primary btn-sm">Lihat Semua Berita</a>
                        </div>
                    </div>
                </div>

                <!-- Recent Testimonials -->
                <div class="col-lg-6 mb-4">
                    <div class="table-card">
                        <div class="table-card-header">
                            <h5><i class="fas fa-comments me-2"></i>Testimoni Terbaru</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Rating</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($recent_testimonials) > 0): ?>
                                        <?php foreach ($recent_testimonials as $testimonial): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($testimonial['name']); ?></td>
                                            <td>
                                                <span class="text-warning stars">
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
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $testimonial['is_approved'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?php echo $testimonial['is_approved'] ? 'Approved' : 'Pending'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d M Y', strtotime($testimonial['created_at'])); ?></td>
                                            <td>
                                                <a href="testimonials_edit.php?id=<?php echo $testimonial['id']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">Tidak ada testimoni</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-card-header text-center">
                            <a href="testimonials.php" class="btn btn-outline-primary btn-sm">Lihat Semua Testimoni</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="table-card">
                        <div class="table-card-header">
                            <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="p-3">
                            <div class="row quick-actions">
                                <div class="col-md-3 mb-3">
                                    <a href="news_add.php" class="btn btn-primary w-100">
                                        <i class="fas fa-plus me-2"></i>Tambah Berita
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="testimonials.php" class="btn btn-success w-100">
                                        <i class="fas fa-check me-2"></i>Approve Testimoni
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="content_manager.php" class="btn btn-info w-100">
                                        <i class="fas fa-edit me-2"></i>Kelola Konten
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="users.php" class="btn btn-warning w-100">
                                        <i class="fas fa-user-plus me-2"></i>Tambah User
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto refresh stats every 30 seconds
        setInterval(function() {
            fetch('api/get_stats.php')
                .then(response => response.json())
                .then(data => {
                    // Update stat numbers
                    const statNumbers = document.querySelectorAll('.stat-number');
                    if (statNumbers[0]) statNumbers[0].textContent = data.total_news || 0;
                    if (statNumbers[1]) statNumbers[1].textContent = data.total_testimonials || 0;
                    if (statNumbers[2]) statNumbers[2].textContent = data.pending_testimonials || 0;
                    if (statNumbers[3]) statNumbers[3].textContent = data.total_users || 0;
                })
                .catch(error => console.error('Error fetching stats:', error));
        }, 30000);

        // Sidebar toggle for mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            // You can add mobile toggle functionality here if needed
        });
    </script>
</body>
</html>