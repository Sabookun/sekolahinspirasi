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

// Get all students for dropdown
$students_query = "SELECT * FROM students ORDER BY class, full_name";
$students_stmt = $db->prepare($students_query);
$students_stmt->execute();
$students = $students_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Laporan Perkembangan - Guru";
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
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; }
        .sidebar { background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%); color: white; height: 100vh; position: fixed; width: 280px; }
        .main-content { margin-left: 280px; padding: 0; }
        .navbar-top { background: white; padding: 1rem 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .content-area { padding: 2rem; }
        @media (max-width: 768px) { .sidebar { width: 100%; position: relative; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'includes/navbar.php'; ?>
        <div class="content-area">
            <h1>Laporan Perkembangan</h1>
            <p>Halaman untuk mengelola laporan perkembangan siswa.</p>
            
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Buat Laporan Baru</h5>
                    <form action="process_report.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Pilih Siswa</label>
                            <select name="student_id" class="form-select" required>
                                <option value="">Pilih Siswa</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student['id']; ?>">
                                        <?php echo htmlspecialchars($student['full_name']); ?> (<?php echo $student['class']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Judul Laporan</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="content" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Laporan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>