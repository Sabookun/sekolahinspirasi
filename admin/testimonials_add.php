<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$page_title = "Tambah Testimoni - Admin";
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
        
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.1);
            padding: 2rem;
        }
        
        .rating-stars .form-check {
            margin-right: 1rem;
        }
        
        .rating-stars .form-check-input {
            display: none;
        }
        
        .rating-stars .form-check-label {
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .rating-stars .form-check-input:checked + .form-check-label {
            background: #FFFBEB;
            border: 2px solid #F59E0B;
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
                    <a class="nav-link" href="testimonials.php">
                        <i class="fas fa-comments"></i>Kelola Testimoni
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
                    <h5 class="mb-0">Tambah Testimoni Baru</h5>
                    <div>
                        <a href="testimonials.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid py-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-container">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['error']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <form action="testimonials_process.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="add">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap *</label>
                                    <input type="text" name="name" class="form-control" required 
                                           placeholder="Masukkan nama lengkap">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Peran *</label>
                                    <select name="role" class="form-select" required>
                                        <option value="">Pilih Peran</option>
                                        <option value="Orang Tua Siswa">Orang Tua Siswa</option>
                                        <option value="Siswa">Siswa</option>
                                        <option value="Alumni">Alumni</option>
                                        <option value="Guru">Guru</option>
                                        <option value="Staf Sekolah">Staf Sekolah</option>
                                        <option value="Masyarakat">Masyarakat</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Testimoni *</label>
                                <textarea name="content" class="form-control" rows="5" 
                                          placeholder="Tuliskan testimoni Anda..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Rating *</label>
                                <div class="rating-stars">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating1" value="1" required>
                                        <label class="form-check-label" for="rating1">
                                            ⭐
                                            <small class="d-block text-muted">1 Bintang</small>
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating2" value="2">
                                        <label class="form-check-label" for="rating2">
                                            ⭐⭐
                                            <small class="d-block text-muted">2 Bintang</small>
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating3" value="3">
                                        <label class="form-check-label" for="rating3">
                                            ⭐⭐⭐
                                            <small class="d-block text-muted">3 Bintang</small>
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating4" value="4">
                                        <label class="form-check-label" for="rating4">
                                            ⭐⭐⭐⭐
                                            <small class="d-block text-muted">4 Bintang</small>
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating5" value="5">
                                        <label class="form-check-label" for="rating5">
                                            ⭐⭐⭐⭐⭐
                                            <small class="d-block text-muted">5 Bintang</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Foto Profil</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB</small>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_approved" id="is_approved" checked>
                                    <label class="form-check-label" for="is_approved">
                                        Approve testimoni ini langsung
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="testimonials.php" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Simpan Testimoni
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>