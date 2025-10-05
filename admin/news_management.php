<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Handle form actions
if ($_POST) {
    if (isset($_POST['action']) && $_POST['action'] == 'add_news') {
        // Handle add news
        $query = "INSERT INTO news (title, content, author_id, category, status, is_featured) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            $_POST['title'],
            $_POST['content'],
            $_SESSION['user_id'],
            $_POST['category'],
            $_POST['status'],
            isset($_POST['is_featured']) ? 1 : 0
        ]);
        $_SESSION['success'] = "Berita berhasil ditambahkan!";
    }
    
    if (isset($_POST['action']) && $_POST['action'] == 'update_news') {
        // Handle update news
        $query = "UPDATE news SET title = ?, content = ?, category = ?, status = ?, is_featured = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([
            $_POST['title'],
            $_POST['content'],
            $_POST['category'],
            $_POST['status'],
            isset($_POST['is_featured']) ? 1 : 0,
            $_POST['news_id']
        ]);
        $_SESSION['success'] = "Berita berhasil diperbarui!";
    }
}

// Get all news for management
$news_query = "SELECT n.*, u.full_name as author_name FROM news n LEFT JOIN users u ON n.author_id = u.id ORDER BY n.created_at DESC";
$news_stmt = $db->prepare($news_query);
$news_stmt->execute();
$all_news = $news_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Kelola Berita - Admin Panel";
?>

<!-- Buat file admin panel untuk kelola berita (bisa dibuat mirip dengan teacher dashboard) -->