<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['id'])) {
    $news_id = $_GET['id'];
    
    // Get current status
    $query = "SELECT status, is_published FROM news WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$news_id]);
    $news = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($news) {
        $new_status = $news['status'] === 'published' ? 'draft' : 'published';
        $new_published = $news['is_published'] ? 0 : 1;
        
        // Update status
        $update_query = "UPDATE news SET status = ?, is_published = ? WHERE id = ?";
        $update_stmt = $db->prepare($update_query);
        
        if ($update_stmt->execute([$new_status, $new_published, $news_id])) {
            $_SESSION['success'] = "Status berita berhasil diubah!";
        } else {
            $_SESSION['error'] = "Gagal mengubah status berita.";
        }
    }
}

header('Location: news.php');
exit;
?>