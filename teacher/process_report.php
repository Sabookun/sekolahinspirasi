<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacher_id = $_SESSION['user_id'];
    $student_id = $_POST['student_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    try {
        $query = "INSERT INTO student_reports (student_id, teacher_id, title, content, development_area, period, created_at) 
                 VALUES (?, ?, ?, ?, 'akademik', 'semester1', NOW())";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$student_id, $teacher_id, $title, $content])) {
            $_SESSION['success'] = "Laporan berhasil dibuat!";
        } else {
            $_SESSION['error'] = "Gagal membuat laporan.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    header("Location: reports.php");
    exit();
}
?>