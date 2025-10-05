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
    
    // Get current featured status
    $query = "SELECT is_featured FROM news WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$news_id]);
    $news = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($news) {
        $new_status = $news['is_featured'] ? 0 : 1;
        
        // Update featured status
        $update_query = "UPDATE news SET is_featured = ? WHERE id = ?";
        $update_stmt = $db->prepare($update_query);
        
        if ($update_stmt->execute([$new_status, $news_id])) {
            $_SESSION['success'] = "Status featured berita berhasil diubah!";
        } else {
            $_SESSION['error'] = "Gagal mengubah status featured.";
        }
    }
}

header('Location: news.php');
exit;
?>