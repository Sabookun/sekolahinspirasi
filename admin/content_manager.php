<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Helper function
function getContentValue($content_array, $key, $default = '') {
    foreach($content_array as $content) {
        if ($content['key_name'] === $key) {
            return $content['content_value'];
        }
    }
    return $default;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update text content
    if (isset($_POST['update_content'])) {
        $key_name = $_POST['key_name'];
        $content_value = $_POST['content_value'];
        
        $query = "INSERT INTO dynamic_content (key_name, content_type, content_value) 
                 VALUES (?, 'text', ?) 
                 ON DUPLICATE KEY UPDATE content_value = ?";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$key_name, $content_value, $content_value])) {
            $_SESSION['success'] = "Konten berhasil diperbarui!";
        } else {
            $_SESSION['error'] = "Gagal memperbarui konten.";
        }
        header('Location: content_manager.php');
        exit;
    }
    
    // Handle hero image upload
    if (isset($_POST['upload_hero_image'])) {
        if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === 0) {
            $uploadDir = '../uploads/hero/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = 'hero_' . time() . '_' . basename($_FILES['hero_image']['name']);
            $targetPath = $uploadDir . $fileName;
            
            // Validate image
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($_FILES['hero_image']['tmp_name']);
            
            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['error'] = "Hanya format JPG, PNG, GIF, dan WebP yang diizinkan.";
            } elseif ($_FILES['hero_image']['size'] > 2097152) {
                $_SESSION['error'] = "Ukuran gambar terlalu besar. Maksimal 2MB.";
            } elseif (move_uploaded_file($_FILES['hero_image']['tmp_name'], $targetPath)) {
                $query = "INSERT INTO dynamic_content (key_name, content_type, content_value, description) 
                         VALUES ('hero_image', 'image', ?, 'Gambar Hero Section')
                         ON DUPLICATE KEY UPDATE content_value = ?";
                $stmt = $db->prepare($query);
                if ($stmt->execute(['uploads/hero/' . $fileName, 'uploads/hero/' . $fileName])) {
                    $_SESSION['success'] = "Gambar hero berhasil diupload!";
                }
            } else {
                $_SESSION['error'] = "Gagal mengupload gambar.";
            }
        } else {
            $_SESSION['error'] = "Silakan pilih file gambar.";
        }
        header('Location: content_manager.php');
        exit;
    }
    
    // Handle about image upload
    if (isset($_POST['upload_about_image'])) {
        $about_image_value = '';
        
        if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === 0) {
            $uploadDir = '../uploads/about/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = 'about_' . time() . '_' . basename($_FILES['about_image']['name']);
            $targetPath = $uploadDir . $fileName;
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($_FILES['about_image']['tmp_name']);
            
            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['error'] = "Hanya format JPG, PNG, GIF, dan WebP yang diizinkan.";
            } elseif ($_FILES['about_image']['size'] > 2097152) {
                $_SESSION['error'] = "Ukuran gambar terlalu besar. Maksimal 2MB.";
            } elseif (move_uploaded_file($_FILES['about_image']['tmp_name'], $targetPath)) {
                $about_image_value = 'uploads/about/' . $fileName;
            } else {
                $_SESSION['error'] = "Gagal mengupload gambar about.";
            }
        } elseif (!empty($_POST['about_image_url'])) {
            $about_image_value = $_POST['about_image_url'];
        } else {
            $_SESSION['error'] = "Silakan pilih file atau masukkan URL gambar.";
        }
        
        if (!isset($_SESSION['error']) && !empty($about_image_value)) {
            $query = "INSERT INTO dynamic_content (key_name, content_type, content_value, description) 
                     VALUES ('about_image', 'image', ?, 'Gambar About Section')
                     ON DUPLICATE KEY UPDATE content_value = ?";
            $stmt = $db->prepare($query);
            if ($stmt->execute([$about_image_value, $about_image_value])) {
                $_SESSION['success'] = "Gambar about berhasil diperbarui!";
            }
        }
        header('Location: content_manager.php');
        exit;
    }
}

// Get all dynamic content
$content_query = "SELECT * FROM dynamic_content ORDER BY key_name";
$content_stmt = $db->prepare($content_query);
$content_stmt->execute();
$dynamic_content = $content_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current images
$hero_image = getContentValue($dynamic_content, 'hero_image', 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&h=400&fit=crop');
$about_image = getContentValue($dynamic_content, 'about_image', 'https://images.unsplash.com/photo-1588072432836-4b4f32c93d8f?w=600&h=400&fit=crop');

// Convert relative paths to absolute URLs for display
if (strpos($hero_image, 'uploads/') === 0) {
    $hero_image = '../' . $hero_image;
}
if (strpos($about_image, 'uploads/') === 0) {
    $about_image = '../' . $about_image;
}

$page_title = "Content Manager - Admin";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Sekolah Inspirasi Bangsa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3B82F6;
            --primary-dark: #1D4ED8;
            --gradient: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
            --light: #F8FAFC;
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
            padding: 2rem;
        }
        .image-preview {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
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
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'includes/navbar.php'; ?>
        <div class="content-area">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">Content Manager</h1>
            </div>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="table-card">
                        <h2 class="h5 mb-4 border-bottom pb-2 text-primary"><i class="fas fa-image me-2"></i>Pengaturan Hero Section</h2>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <form method="POST">
                                    <input type="hidden" name="key_name" value="hero_title">
                                    <input type="hidden" name="update_content" value="1">
                                    <div class="mb-3">
                                        <label for="hero_title" class="form-label">Judul Utama (Hero Title)</label>
                                        <textarea name="content_value" id="hero_title" class="form-control" rows="2" required><?php echo htmlspecialchars(getContentValue($dynamic_content, 'hero_title', 'Sekolah Inspirasi Bangsa')); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Simpan Judul</button>
                                </form>
                                <hr class="my-4">
                                
                                <form method="POST">
                                    <input type="hidden" name="key_name" value="hero_subtitle">
                                    <input type="hidden" name="update_content" value="1">
                                    <div class="mb-3">
                                        <label for="hero_subtitle" class="form-label">Subjudul (Hero Subtitle)</label>
                                        <textarea name="content_value" id="hero_subtitle" class="form-control" rows="3" required><?php echo htmlspecialchars(getContentValue($dynamic_content, 'hero_subtitle', 'Mencetak generasi unggul yang berakhlak mulia dan berdaya saing global.')); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Simpan Subjudul</button>
                                </form>
                            </div>
                            
                            <div class="col-md-6">
                                <h4 class="h6">Gambar Hero Saat Ini</h4>
                                <img src="<?php echo htmlspecialchars($hero_image); ?>" alt="Hero Image Preview" class="img-fluid image-preview mb-3">
                                
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="upload_hero_image" value="1">
                                    <div class="mb-3">
                                        <label for="hero_image" class="form-label">Ganti Gambar Hero</label>
                                        <input type="file" name="hero_image" id="hero_image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" required>
                                        <small class="text-muted">Maksimal 2MB. Format: JPG, PNG, GIF, WebP.</small>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-upload me-1"></i>Upload Gambar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="table-card">
                        <h2 class="h5 mb-4 border-bottom pb-2 text-primary"><i class="fas fa-info-circle me-2"></i>Pengaturan About Section</h2>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <form method="POST">
                                    <input type="hidden" name="key_name" value="about_title">
                                    <input type="hidden" name="update_content" value="1">
                                    <div class="mb-3">
                                        <label for="about_title" class="form-label">Judul About Us</label>
                                        <input type="text" name="content_value" id="about_title" class="form-control" value="<?php echo htmlspecialchars(getContentValue($dynamic_content, 'about_title', 'Tentang Kami')); ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Simpan Judul</button>
                                </form>
                                <hr class="my-4">
                                
                                <form method="POST">
                                    <input type="hidden" name="key_name" value="about_content">
                                    <input type="hidden" name="update_content" value="1">
                                    <div class="mb-3">
                                        <label for="about_content" class="form-label">Isi Konten About Us</label>
                                        <textarea name="content_value" id="about_content" class="form-control" rows="5" required><?php echo htmlspecialchars(getContentValue($dynamic_content, 'about_content', 'Sekolah Inspirasi Bangsa didirikan dengan visi untuk menciptakan lingkungan belajar yang inspiratif dan berfokus pada pengembangan karakter serta keunggulan akademik setiap siswa.')); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Simpan Konten</button>
                                </form>
                            </div>
                            
                            <div class="col-md-6">
                                <h4 class="h6">Gambar About Saat Ini</h4>
                                <img src="<?php echo htmlspecialchars($about_image); ?>" alt="About Image Preview" class="img-fluid image-preview mb-3" id="aboutImageCurrentPreview">
                                
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="upload_about_image" value="1">
                                    <div class="mb-3">
                                        <label for="about_image" class="form-label">Ganti Gambar About (Pilih File)</label>
                                        <input type="file" name="about_image" id="aboutImageInput" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                                        <small class="text-muted">Maksimal 2MB. Format: JPG, PNG, GIF, WebP. (Akan menggantikan URL jika ada)</small>
                                        <img src="#" alt="New About Image Preview" class="img-fluid image-preview mt-2" id="aboutImageNewPreview" style="display: none;">
                                    </div>
                                    <div class="mb-3">
                                        <label for="about_image_url" class="form-label">Atau Masukkan URL Gambar Eksternal</label>
                                        <input type="url" name="about_image_url" id="aboutImageURL" class="form-control" value="">
                                        <small class="text-muted">Jika diisi, file yang diupload akan diabaikan.</small>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-upload me-1"></i>Perbarui Gambar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Preview for new about image file upload
    document.getElementById('aboutImageInput').addEventListener('change', function(e) {
        const preview = document.getElementById('aboutImageNewPreview');
        const urlInput = document.getElementById('aboutImageURL');
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(event) {
                preview.src = event.target.result;
                preview.style.display = 'block';
                urlInput.value = ''; // Clear URL if file is chosen
            }
            reader.readAsDataURL(this.files[0]);
        } else {
            preview.style.display = 'none';
        }
    });

    // Clear file input if URL is entered
    document.getElementById('aboutImageURL').addEventListener('input', function() {
        if (this.value.trim()) {
            document.getElementById('aboutImageInput').value = '';
            document.getElementById('aboutImageNewPreview').style.display = 'none';
        }
    });
    
    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert.classList.contains('show')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        });
    });
    
    // Form validation for file size
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const fileInputs = this.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                if (input.files.length > 0) {
                    const file = input.files[0];
                    const maxSize = 2 * 1024 * 1024; // 2MB
                    
                    if (file.size > maxSize) {
                        e.preventDefault();
                        alert('Ukuran gambar terlalu besar. Maksimal 2MB.');
                        input.focus();
                        return false;
                    }
                }
            });
        });
    });
    </script>
</body>
</html>