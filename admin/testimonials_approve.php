<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

error_log("Approve testimoni - ID: " . ($_GET['id'] ?? 'unknown'));

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $query = "UPDATE testimonials SET is_approved = 1 WHERE id = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = "Testimoni berhasil disetujui!";
            error_log("Testimoni approved successfully - ID: " . $id);
        } else {
            $_SESSION['error'] = "Gagal menyetujui testimoni.";
            error_log("Failed to approve testimoni - ID: " . $id);
        }
    } catch (PDOException $e) {
        error_log("Approve testimoni error: " . $e->getMessage());
        $_SESSION['error'] = "Terjadi kesalahan sistem.";
    }
} else {
    $_SESSION['error'] = "ID testimoni tidak valid.";
}

header("Location: testimonials.php");
exit();
?>