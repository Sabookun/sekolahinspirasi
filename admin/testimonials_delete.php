<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Log untuk debugging
error_log("=== DELETE TESTIMONI PROCESS STARTED ===");
error_log("Timestamp: " . date('Y-m-d H:i:s'));
error_log("User ID: " . ($_SESSION['user_id'] ?? 'unknown'));
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));
error_log("GET data: " . print_r($_GET, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    error_log("Attempting to delete testimoni ID: " . $id);
    
    if ($id <= 0) {
        $_SESSION['error'] = "ID testimoni tidak valid.";
        error_log("Invalid testimoni ID: " . $id);
        header("Location: testimonials.php");
        exit();
    }
    
    try {
        // Cek dulu apakah testimoni ada
        $check_query = "SELECT * FROM testimonials WHERE id = ?";
        $check_stmt = $db->prepare($check_query);
        
        error_log("Executing check query: " . $check_query . " with ID: " . $id);
        
        if ($check_stmt->execute([$id])) {
            error_log("Check query executed successfully");
            error_log("Row count: " . $check_stmt->rowCount());
            
            if ($check_stmt->rowCount() > 0) {
                $testimonial = $check_stmt->fetch(PDO::FETCH_ASSOC);
                error_log("Testimoni found - Name: " . $testimonial['name'] . ", Image: " . $testimonial['image']);
                
                // Hapus file gambar jika ada dan bukan default
                if (!empty($testimonial['image'])) {
                    error_log("Checking image: " . $testimonial['image']);
                    
                    $is_default_image = str_contains($testimonial['image'], 'randomuser.me') || 
                                      str_contains($testimonial['image'], 'lego') ||
                                      str_contains($testimonial['image'], 'default');
                    
                    if (!$is_default_image) {
                        $file_path = '../' . $testimonial['image'];
                        error_log("Attempting to delete image file: " . $file_path);
                        
                        if (file_exists($file_path)) {
                            if (unlink($file_path)) {
                                error_log("Image file deleted successfully: " . $file_path);
                            } else {
                                error_log("Failed to delete image file: " . $file_path);
                            }
                        } else {
                            error_log("Image file not found: " . $file_path);
                        }
                    } else {
                        error_log("Skipping default image deletion");
                    }
                } else {
                    error_log("No image to delete");
                }
                
                // Hapus dari database
                $delete_query = "DELETE FROM testimonials WHERE id = ?";
                $delete_stmt = $db->prepare($delete_query);
                error_log("Executing delete query: " . $delete_query . " with ID: " . $id);
                
                if ($delete_stmt->execute([$id])) {
                    $affected_rows = $delete_stmt->rowCount();
                    error_log("Delete query executed successfully. Affected rows: " . $affected_rows);
                    
                    if ($affected_rows > 0) {
                        $_SESSION['success'] = "Testimoni dari '" . htmlspecialchars($testimonial['name']) . "' berhasil dihapus!";
                        error_log("Testimoni deleted successfully from database");
                    } else {
                        $_SESSION['error'] = "Tidak ada data yang terhapus. Testimoni mungkin sudah dihapus.";
                        error_log("No rows affected by delete query");
                    }
                } else {
                    $_SESSION['error'] = "Gagal menghapus testimoni dari database.";
                    error_log("Failed to execute delete query");
                }
            } else {
                $_SESSION['error'] = "Testimoni tidak ditemukan.";
                error_log("Testimoni not found in database");
            }
        } else {
            $_SESSION['error'] = "Gagal memeriksa testimoni.";
            error_log("Failed to execute check query");
        }
        
    } catch (PDOException $e) {
        error_log("Delete testimoni PDO error: " . $e->getMessage());
        error_log("Error code: " . $e->getCode());
        $_SESSION['error'] = "Terjadi kesalahan database: " . $e->getMessage();
    } catch (Exception $e) {
        error_log("Delete testimoni general error: " . $e->getMessage());
        $_SESSION['error'] = "Terjadi kesalahan sistem: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Request tidak valid. Pastikan mengirim form dengan method POST.";
    error_log("Invalid request - Method: " . $_SERVER['REQUEST_METHOD'] . ", ID set: " . (isset($_POST['id']) ? 'yes' : 'no'));
}

error_log("=== DELETE TESTIMONI PROCESS COMPLETED ===");
header("Location: testimonials.php");
exit();
?>