<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

$teacher_id = $_SESSION['user_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// FIXED QUERY: Get students that this teacher has created reports for
$students_query = "
    SELECT DISTINCT s.* 
    FROM students s 
    JOIN student_reports sr ON s.id = sr.student_id 
    WHERE sr.teacher_id = ?
    ORDER BY s.class, s.full_name
";
$students_stmt = $db->prepare($students_query);
$students_stmt->execute([$teacher_id]);
$students = $students_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total reports by this teacher
$total_reports_query = "
    SELECT COUNT(*) as total_reports 
    FROM student_reports 
    WHERE teacher_id = ?
";
$total_reports_stmt = $db->prepare($total_reports_query);
$total_reports_stmt->execute([$teacher_id]);
$total_reports = $total_reports_stmt->fetch(PDO::FETCH_ASSOC)['total_reports'];

// Get recent reports
$recent_reports_query = "
    SELECT sr.*, s.full_name as student_name, s.class 
    FROM student_reports sr 
    JOIN students s ON sr.student_id = s.id 
    WHERE sr.teacher_id = ? 
    ORDER BY sr.created_at DESC 
    LIMIT 5
";
$recent_reports_stmt = $db->prepare($recent_reports_query);
$recent_reports_stmt->execute([$teacher_id]);
$recent_reports = $recent_reports_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Dasbor Guru - Sekolah Inspirasi Bangsa";
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
            margin: 0;
            padding: 0;
        }
        
        .sidebar {
            background: var(--gradient);
            color: white;
            height: 100vh;
            position: fixed;
            width: 280px;
            left: 0;
            top: 0;
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
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }
        
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
        }
        
        .content-area {
            padding: 2rem;
        }
        
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
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
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
        
        .stat-students { background: var(--light-blue); color: var(--primary); }
        .stat-reports { background: #F0F9FF; color: #10B981; }
        .stat-pending { background: #FFFBEB; color: #F59E0B; }
        .stat-average { background: #ECFDF5; color: #10B981; }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: #1E293B;
        }
        
        .stat-label {
            color: #64748B;
            font-size: 0.9rem;
        }
        
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
            color: #1D4ED8;
            font-weight: 600;
        }
        
        .welcome-alert {
            border-left: 4px solid var(--primary);
            background: var(--light-blue);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .quick-actions .btn {
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 500;
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
    <!-- SIDEBAR DIRECT CODE -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-chalkboard-teacher me-2"></i>Panel Guru</h4>
            <p>Sekolah Inspirasi Bangsa</p>
        </div>
        
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reports.php">
                        <i class="fas fa-clipboard-list"></i>Laporan Perkembangan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="students.php">
                        <i class="fas fa-users"></i>Data Siswa
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link" href="../auth/logout.php">
                        <i class="fas fa-sign-out-alt"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- NAVBAR DIRECT CODE -->
        <nav class="navbar-top">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h5 class="mb-0">Dashboard Guru</h5>
                    <div class="d-flex align-items-center">
                        <span class="me-3">Halo, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>Akun
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="../index.php"><i class="fas fa-home me-2"></i>Beranda</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- CONTENT AREA -->
        <div class="content-area">
            <!-- Welcome Message -->
            <div class="alert welcome-alert">
                <h5>Selamat datang, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h5>
                <p class="mb-0">Anda login sebagai <strong>Guru</strong>. Kelola data siswa dan laporan perkembangan dengan mudah.</p>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon stat-students">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <div class="stat-number"><?php echo count($students); ?></div>
                        <div class="stat-label">Total Siswa</div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon stat-reports">
                            <i class="fas fa-clipboard-list fa-lg"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_reports; ?></div>
                        <div class="stat-label">Total Laporan</div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon stat-pending">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                        <div class="stat-number"><?php echo count($students); ?></div>
                        <div class="stat-label">Siswa dengan Laporan</div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon stat-average">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>
                        <div class="stat-number">
                            <?php echo count($students) > 0 ? round(($total_reports / count($students)), 1) : '0'; ?>
                        </div>
                        <div class="stat-label">Rata-rata Laporan/Siswa</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recent Reports -->
                <div class="col-lg-6 mb-4">
                    <div class="table-card">
                        <div class="table-card-header">
                            <h5><i class="fas fa-history me-2"></i>Laporan Terbaru</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Siswa</th>
                                        <th>Bidang</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($recent_reports) > 0): ?>
                                        <?php foreach ($recent_reports as $report): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($report['student_name']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo $report['class']; ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($report['development_area']); ?></span>
                                            </td>
                                            <td><?php echo date('d M Y', strtotime($report['created_at'])); ?></td>
                                            <td>
                                                <a href="report_detail.php?id=<?php echo $report['id']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                                                <br>Tidak ada laporan
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-card-header text-center">
                            <a href="reports.php" class="btn btn-outline-primary btn-sm">Lihat Semua Laporan</a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-lg-6 mb-4">
                    <div class="table-card">
                        <div class="table-card-header">
                            <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="p-3">
                            <div class="row quick-actions">
                                <div class="col-md-6 mb-3">
                                    <a href="reports.php" class="btn btn-primary w-100">
                                        <i class="fas fa-plus me-2"></i>Buat Laporan
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="students.php" class="btn btn-success w-100">
                                        <i class="fas fa-users me-2"></i>Data Siswa
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="reports.php" class="btn btn-info w-100">
                                        <i class="fas fa-list me-2"></i>Semua Laporan
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="../index.php" class="btn btn-warning w-100">
                                        <i class="fas fa-home me-2"></i>Beranda
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
</body>
</html>