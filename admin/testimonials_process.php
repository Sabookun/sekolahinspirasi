<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'add') {
            // Handle testimoni addition
            $name = trim($_POST['name']);
            $role = trim($_POST['role']);
            $content = trim($_POST['content']);
            $rating = intval($_POST['rating']);
            $is_approved = isset($_POST['is_approved']) ? 1 : 0;
            
            // Handle image upload
            $image = 'https://randomuser.me/api/portraits/lego/1.jpg'; // default image
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $uploadDir = '../uploads/testimonials/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $image = 'uploads/testimonials/' . $fileName;
                }
            }
            
            // Insert into database
            $query = "INSERT INTO testimonials (name, role, content, rating, image, is_approved) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([$name, $role, $content, $rating, $image, $is_approved])) {
                $_SESSION['success'] = "Testimoni berhasil ditambahkan!";
            } else {
                $_SESSION['error'] = "Gagal menambahkan testimoni.";
            }
        }
        
        header("Location: testimonials.php");
        exit();
        
    } catch (PDOException $e) {
        error_log("Testimoni process error: " . $e->getMessage());
        $_SESSION['error'] = "Terjadi kesalahan sistem. Silakan coba lagi.";
        header("Location: testimonials.php");
        exit();
    }
}
?>