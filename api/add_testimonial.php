<?php
// api/add_testimonial.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $name = $_POST['name'];
    $role = $_POST['role'];
    $content = $_POST['content'];
    $rating = $_POST['rating'];
    $image = $_POST['image'] ?: 'https://randomuser.me/api/portraits/lego/1.jpg';
    
    $query = "INSERT INTO testimonials (name, role, content, rating, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute([$name, $role, $content, $rating, $image])) {
        $_SESSION['success'] = "Testimoni berhasil dikirim! Menunggu persetujuan admin.";
    } else {
        $_SESSION['error'] = "Gagal mengirim testimoni. Silakan coba lagi.";
    }
    
    header("Location: ../index.php#testimonials");
    exit();
}
?>