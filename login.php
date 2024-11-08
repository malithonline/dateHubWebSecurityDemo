<?php
session_start();
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Log the login activity
            $log_stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, 'login')");
            $log_stmt->execute([$user['id']]);
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password";
            header("Location: index.html?error=" . urlencode($error));
            exit();
        }
    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        header("Location: index.html?error=" . urlencode($error));
        exit();
    }
}
?>