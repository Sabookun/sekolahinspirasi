<?php
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email dan password harus diisi";
        header("Location: login.php");
        exit();
    }
    
    try {
        // Cari user di database
        $query = "SELECT id, username, email, password, role, full_name, is_active 
                  FROM users WHERE email = :email AND is_active = 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role']; // PASTIKAN INI 'role'
                $_SESSION['logged_in'] = true;
                
                // Clear error
                unset($_SESSION['error']);
                
                // Redirect berdasarkan role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: ../admin/dashboard.php");
                        break;
                    case 'teacher':
                        header("Location: ../teacher/dashboard.php");
                        break;
                    case 'parent':
                        header("Location: ../parent/dashboard.php");
                        break;
                    default:
                        header("Location: ../index.php");
                }
                exit();
                
            } else {
                $_SESSION['error'] = "Password salah";
                $_SESSION['login_email'] = $email;
            }
        } else {
            $_SESSION['error'] = "Email tidak ditemukan";
            $_SESSION['login_email'] = $email;
        }
        
    } catch (PDOException $e) {
        $_SESSION['error'] = "Terjadi kesalahan sistem";
        error_log("Login error: " . $e->getMessage());
    }
    
    header("Location: login.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>