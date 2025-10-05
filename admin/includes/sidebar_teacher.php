<?php
// teacher/includes/sidebar_teacher.php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar Guru -->
<div class="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-chalkboard-teacher me-2"></i>Guru Panel</h4>
        <p>Sekolah Inspirasi Bangsa</p>
    </div>
    
    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>Dashboard Guru
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo in_array($current_page, ['reports.php', 'reports_create.php', 'reports_edit.php']) ? 'active' : ''; ?>" href="reports.php">
                    <i class="fas fa-clipboard-list"></i>Laporan Siswa
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo in_array($current_page, ['students.php', 'student_view.php']) ? 'active' : ''; ?>" href="students.php">
                    <i class="fas fa-user-graduate"></i>Data Siswa
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo in_array($current_page, ['attendance.php', 'attendance_today.php']) ? 'active' : ''; ?>" href="attendance.php">
                    <i class="fas fa-calendar-check"></i>Presensi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo in_array($current_page, ['grades.php', 'grades_input.php']) ? 'active' : ''; ?>" href="grades.php">
                    <i class="fas fa-chart-bar"></i>Nilai Akademik
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'schedule.php' ? 'active' : ''; ?>" href="schedule.php">
                    <i class="fas fa-clock"></i>Jadwal Mengajar
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'messages.php' ? 'active' : ''; ?>" href="messages.php">
                    <i class="fas fa-envelope"></i>Pesan Orang Tua
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