<?php
session_start();
require_once '../config/database.php';

// Cek auth admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$page_title = "Tambah Berita Baru";
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
        
        .url-preview {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 8px 12px;
            font-size: 0.875rem;
            border-left: 3px solid var(--primary);
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
                    <h5 class="mb-0">Tambah Berita Baru</h5>
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
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Tambah Berita Baru</h1>
                <a href="news.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form action="news_process.php" method="POST" enctype="multipart/form-data" id="newsForm">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Judul -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Berita *</label>
                                    <input type="text" class="form-control" id="title" name="title" required 
                                           placeholder="Masukkan judul berita">
                                </div>

                                <!-- URL Opsional -->
                                <div class="mb-3">
                                    <label for="external_url" class="form-label">URL Eksternal (Opsional)</label>
                                    <input type="url" class="form-control" id="external_url" name="external_url" 
                                           placeholder="https://example.com/berita-lengkap">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle text-info"></i> 
                                        Jika diisi, berita akan mengarah ke URL ini (untuk berita dari sumber eksternal)
                                    </div>
                                    <div id="urlPreview" class="url-preview mt-2" style="display: none;">
                                        <strong>Preview:</strong> <span id="previewUrl"></span>
                                    </div>
                                </div>

                                <!-- Konten -->
                                <div class="mb-3">
                                    <label for="content" class="form-label">Konten Berita *</label>
                                    <textarea class="form-control" id="content" name="content" rows="10" 
                                              placeholder="Tulis konten berita disini..." required></textarea>
                                    <div class="form-text">
                                        <i class="fas fa-lightbulb text-warning"></i> 
                                        Jika URL eksternal diisi, konten ini akan ditampilkan sebagai ringkasan
                                    </div>
                                </div>

                                <!-- Excerpt -->
                                <div class="mb-3">
                                    <label for="excerpt" class="form-label">Ringkasan (Excerpt)</label>
                                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3" 
                                              placeholder="Ringkasan singkat berita (akan ditampilkan di halaman list)"></textarea>
                                    <div class="form-text">Opsional. Jika kosong, akan diambil otomatis dari konten.</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Sidebar Settings -->
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <i class="fas fa-cog"></i> Pengaturan
                                    </div>
                                    <div class="card-body">
                                        <!-- Kategori -->
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Kategori *</label>
                                            <select class="form-select" id="category" name="category" required>
                                                <option value="">Pilih Kategori</option>
                                                <option value="kegiatan">Kegiatan</option>
                                                <option value="prestasi">Prestasi</option>
                                                <option value="pengumuman">Pengumuman</option>
                                                <option value="info">Info</option>
                                            </select>
                                        </div>

                                        <!-- Status -->
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status *</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="draft">Draft</option>
                                                <option value="published">Published</option>
                                            </select>
                                        </div>

                                        <!-- Featured -->
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1">
                                            <label class="form-check-label" for="is_featured">Jadikan Featured News</label>
                                        </div>

                                        <!-- Publish Date -->
                                        <div class="mb-3">
                                            <label for="published_at" class="form-label">Tanggal Publikasi</label>
                                            <input type="datetime-local" class="form-control" id="published_at" name="published_at">
                                            <div class="form-text">Kosongkan untuk publish sekarang</div>
                                        </div>

                                        <!-- Open in New Tab -->
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="open_new_tab" name="open_new_tab" value="1">
                                            <label class="form-check-label" for="open_new_tab">Buka di tab baru</label>
                                            <div class="form-text">Hanya berlaku jika URL diisi</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Featured Image -->
                                <div class="card mt-3">
                                    <div class="card-header bg-info text-white">
                                        <i class="fas fa-image"></i> Featured Image
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Gambar Berita</label>
                                            <input type="file" class="form-control" id="image" name="image" 
                                                   accept="image/*" onchange="previewImage(this)">
                                            <div class="form-text">Format: JPG, PNG, GIF. Max: 2MB</div>
                                        </div>
                                        
                                        <!-- Image Preview -->
                                        <div id="imagePreview" class="mt-2 text-center" style="display: none;">
                                            <img id="preview" class="img-thumbnail" style="max-height: 200px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid gap-2 mt-3">
                                    <button type="submit" name="create_news" class="btn btn-success btn-lg">
                                        <i class="fas fa-plus-circle"></i> Tambah Berita
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Image preview function
    function previewImage(input) {
        const preview = document.getElementById('preview');
        const previewContainer = document.getElementById('imagePreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            previewContainer.style.display = 'none';
        }
    }

    // URL preview function
    document.getElementById('external_url').addEventListener('input', function() {
        const url = this.value.trim();
        const previewContainer = document.getElementById('urlPreview');
        const previewUrl = document.getElementById('previewUrl');
        
        if (url) {
            previewUrl.textContent = url;
            previewContainer.style.display = 'block';
            
            // Auto-check "open in new tab" if URL is provided
            document.getElementById('open_new_tab').checked = true;
        } else {
            previewContainer.style.display = 'none';
            document.getElementById('open_new_tab').checked = false;
        }
    });

    // Auto generate excerpt from content
    document.getElementById('content').addEventListener('input', function() {
        const excerptField = document.getElementById('excerpt');
        const content = this.value;
        
        // Only auto-generate if excerpt is empty
        if (!excerptField.value && content.length > 0) {
            // Take first 150 characters as excerpt
            const excerpt = content.substring(0, 150);
            excerptField.value = excerpt + (content.length > 150 ? '...' : '');
        }
    });

    // Form validation
    document.getElementById('newsForm').addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const content = document.getElementById('content').value.trim();
        const category = document.getElementById('category').value;
        const externalUrl = document.getElementById('external_url').value.trim();
        
        if (!title) {
            e.preventDefault();
            alert('Judul berita harus diisi!');
            document.getElementById('title').focus();
            return false;
        }
        
        if (!content) {
            e.preventDefault();
            alert('Konten berita harus diisi!');
            document.getElementById('content').focus();
            return false;
        }
        
        if (!category) {
            e.preventDefault();
            alert('Kategori harus dipilih!');
            document.getElementById('category').focus();
            return false;
        }
        
        // Validate URL format if provided
        if (externalUrl) {
            const urlPattern = /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([/\w .-]*)*\/?$/;
            if (!urlPattern.test(externalUrl)) {
                e.preventDefault();
                alert('Format URL tidak valid! Pastikan URL dimulai dengan http:// atau https://');
                document.getElementById('external_url').focus();
                return false;
            }
        }
    });

    // Auto-add http:// if missing
    document.getElementById('external_url').addEventListener('blur', function() {
        let url = this.value.trim();
        if (url && !url.startsWith('http://') && !url.startsWith('https://')) {
            this.value = 'https://' + url;
        }
    });
    </script>
</body>
</html>