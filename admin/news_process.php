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

if ($_POST) {
    try {
        // Get form data
        $title = $_POST['title'];
        $content = $_POST['content'];
        $excerpt = $_POST['excerpt'];
        $category = $_POST['category'];
        $status = $_POST['status'];
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $published_at = $_POST['published_at'] ? $_POST['published_at'] : date('Y-m-d H:i:s');
        $author_id = $_SESSION['user_id'];
        
        // Auto generate excerpt if empty
        if (empty($excerpt)) {
            $excerpt = strip_tags($content);
            $excerpt = substr($excerpt, 0, 150);
            if (strlen($content) > 150) {
                $excerpt .= '...';
            }
        }
        
        // Set is_published based on status
        $is_published = ($status === 'published') ? 1 : 0;
        
        // Handle image upload
        $image_name = null;
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "../uploads/news/";
            
            // Create directory if not exists
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $image_file = $_FILES['image']['name'];
            $image_ext = strtolower(pathinfo($image_file, PATHINFO_EXTENSION));
            $image_name = time() . '_' . uniqid() . '.' . $image_ext;
            $target_file = $target_dir . $image_name;
            
            // Check image file
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check === false) {
                throw new Exception("File yang diupload bukan gambar.");
            }
            
            // Check file size (2MB max)
            if ($_FILES['image']['size'] > 2097152) {
                throw new Exception("Ukuran gambar terlalu besar. Maksimal 2MB.");
            }
            
            // Allow certain file formats
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($image_ext, $allowed_ext)) {
                throw new Exception("Hanya format JPG, JPEG, PNG & GIF yang diizinkan.");
            }
            
            // Upload file
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                throw new Exception("Maaf, terjadi error saat upload gambar.");
            }
        }
        
        // Insert into database
        $query = "INSERT INTO news 
                 (title, content, excerpt, image, category, author_id, status, is_featured, is_published, published_at, created_at) 
                 VALUES 
                 (:title, :content, :excerpt, :image, :category, :author_id, :status, :is_featured, :is_published, :published_at, NOW())";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':excerpt', $excerpt);
        $stmt->bindParam(':image', $image_name);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':author_id', $author_id);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':is_featured', $is_featured);
        $stmt->bindParam(':is_published', $is_published);
        $stmt->bindParam(':published_at', $published_at);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Berita berhasil ditambahkan!";
            header('Location: news.php');
            exit;
        } else {
            throw new Exception("Gagal menambahkan berita.");
        }
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: news_create.php');
        exit;
    }
} else {
    header('Location: news_create.php');
    exit;
}
?>