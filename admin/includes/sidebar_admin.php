<?php
// admin/includes/sidebar_admin.php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar Admin -->
<div class="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-graduation-cap me-2"></i>Admin Panel</h4>
        <p>Sekolah Inspirasi Bangsa</p>
    </div>
    
    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo in_array($current_page, ['news.php', 'news_create.php', 'news_edit.php', 'news_add.php']) ? 'active' : ''; ?>" href="news.php">
                    <i class="fas fa-newspaper"></i>Kelola Berita
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo in_array($current_page, ['testimonials.php', 'testimonials_add.php', 'testimonials_edit.php']) ? 'active' : ''; ?>" href="testimonials.php">
                    <i class="fas fa-comments"></i>Kelola Testimoni
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'content_manager.php' ? 'active' : ''; ?>" href="content_manager.php">
                    <i class="fas fa-edit"></i>Kelola Konten
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo in_array($current_page, ['users.php', 'users_add.php', 'users_edit.php']) ? 'active' : ''; ?>" href="users.php">
                    <i class="fas fa-users"></i>Kelola Pengguna
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                    <i class="fas fa-cog"></i>Pengaturan
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