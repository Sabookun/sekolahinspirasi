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

// Get all students
$students = $db->query("SELECT * FROM students ORDER BY class, full_name");
$students = $students->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Data Siswa - Guru";
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
        .sidebar {
            background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
            color: white;
            height: 100vh;
            position: fixed;
            width: 280px;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 0;
        }
        
        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.1);
            overflow: hidden;
        }
        
        .student-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .student-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-chalkboard-teacher me-2"></i>Guru Panel</h4>
            <p>Sekolah Inspirasi Bangsa</p>
        </div>
        
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reports.php">
                        <i class="fas fa-clipboard-list"></i>Laporan Perkembangan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="students.php">
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

    <div class="main-content">
        <nav class="navbar navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h5 class="mb-0">Data Siswa</h5>
                    <div>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid py-4">
            <!-- Class Filter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Daftar Siswa</h4>
                </div>
                <div class="col-md-6 text-end">
                    <select class="form-select w-auto d-inline-block" id="classFilter">
                        <option value="">Semua Kelas</option>
                        <option value="7A">Kelas 7A</option>
                        <option value="7B">Kelas 7B</option>
                        <option value="8A">Kelas 8A</option>
                        <option value="8B">Kelas 8B</option>
                    </select>
                </div>
            </div>

            <div class="row" id="studentsContainer">
                <?php if (count($students) > 0): ?>
                    <?php foreach ($students as $student): ?>
                    <div class="col-lg-4 col-md-6 mb-3" data-class="<?php echo $student['class']; ?>">
                        <div class="student-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0"><?php echo htmlspecialchars($student['full_name']); ?></h6>
                                <span class="badge bg-primary"><?php echo $student['class']; ?></span>
                            </div>
                            <p class="text-muted mb-2">
                                <small>NIS: <?php echo $student['nis']; ?></small>
                            </p>
                            <?php if ($student['birth_date']): ?>
                                <p class="text-muted mb-2">
                                    <small>TTL: <?php echo date('d M Y', strtotime($student['birth_date'])); ?></small>
                                </p>
                            <?php endif; ?>
                            <div class="d-flex justify-content-end align-items-center">
                                <a href="dashboard.php" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-plus me-1"></i>Laporan
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada data siswa</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    