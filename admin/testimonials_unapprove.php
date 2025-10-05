<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

error_log("Unapprove testimoni - ID: " . ($_GET['id'] ?? 'unknown'));

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $query = "UPDATE testimonials SET is_approved = 0 WHERE id = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = "Testimoni berhasil dibatalkan persetujuannya!";
            error_log("Testimoni unapproved successfully - ID: " . $id);
        } else {
            $_SESSION['error'] = "Gagal membatalkan persetujuan testimoni.";
            error_log("Failed to unapprove testimoni - ID: " . $id);
        }
    } catch (PDOException $e) {
        error_log("Unapprove testimoni error: " . $e->getMessage());
        $_SESSION['error'] = "Terjadi kesalahan sistem.";
    }
} else {
    $_SESSION['error'] = "ID testimoni tidak valid.";
}

header("Location: testimonials.php");
exit();
?>