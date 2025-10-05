<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Get all users
$users_query = "SELECT id, username, email, role, full_name, phone, is_active, created_at FROM users ORDER BY created_at DESC";
$users_stmt = $db->prepare($users_query);
$users_stmt->execute();
$users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Kelola Pengguna - Admin";
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
        <div class="sidebar-header">
            <h4><i class="fas fa-graduation-cap me-2"></i>Admin Panel</h4>
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
                    <a class="nav-link" href="news.php">
                        <i class="fas fa-newspaper"></i>Kelola Berita
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="testimonials.php">
                        <i class="fas fa-comments"></i>Kelola Testimoni
                    </a>
                </li>
                
                <!-- ✅ TAMBAH MENU KELOLA KONTEN -->
                <li class="nav-item">
                    <a class="nav-link" href="content_manager.php">
                        <i class="fas fa-edit"></i>Kelola Konten
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link active" href="users.php">
                        <i class="fas fa-users"></i>Kelola Pengguna
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
        <nav class="navbar-top">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h5 class="mb-0">Kelola Pengguna</h5>
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
                <h4>Daftar Pengguna</h4>
                <a href="users_add.php" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Tambah Pengguna
                </a>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($users) > 0): ?>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php echo $user['role'] == 'admin' ? 'bg-danger' : 
                                                   ($user['role'] == 'teacher' ? 'bg-warning' : 'bg-info'); ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $user['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $user['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <a href="users_edit.php?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="users_delete.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">Belum ada pengguna</p>
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
</body>
</html>