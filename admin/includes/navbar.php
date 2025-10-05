<?php
// admin/includes/navbar.php
?>

<!-- Top Navbar -->
<nav class="navbar-top">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="mb-0">
                <?php 
                $page_titles = [
                    'dashboard.php' => 'Dashboard Admin',
                    'news.php' => 'Kelola Berita',
                    'news_create.php' => 'Tambah Berita Baru',
                    'testimonials.php' => 'Kelola Testimoni',
                    'testimonials_add.php' => 'Tambah Testimoni',
                    'content_manager.php' => 'Content Manager',
                    'users.php' => 'Kelola Pengguna'
                ];
                $current_page = basename($_SERVER['PHP_SELF']);
                echo $page_titles[$current_page] ?? 'Admin Panel';
                ?>
            </h5>
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