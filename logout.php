<?php
session_start();
require_once 'database.php';

if (isset($_SESSION['user_id'])) {
    // Log the logout activity
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, 'logout')");
    $stmt->execute([$_SESSION['user_id']]);
}

// Destroy session
session_destroy();

// Redirect to login page
header("Location: index.php");
exit();
?>