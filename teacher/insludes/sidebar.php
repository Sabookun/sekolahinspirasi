<?php
// teacher/includes/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar Teacher -->
<div class="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-chalkboard-teacher me-2"></i>Panel Guru</h4>
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
                <a class="nav-link <?php echo in_array($current_page, ['reports.php', 'report_detail.php', 'process_report.php']) ? 'active' : ''; ?>" href="reports.php">
                    <i class="fas fa-clipboard-list"></i>Laporan Perkembangan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'students.php' ? 'active' : ''; ?>" href="students.php">
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