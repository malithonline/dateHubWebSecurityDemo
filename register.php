<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = $_POST['gender'];
    $birth_date = $_POST['birth_date'];
    $looking_for = $_POST['looking_for'];
    $location = $_POST['location'];
    $bio = $_POST['bio'];
    $interests = $_POST['interests'];

    // Basic validation
    if ($password !== $confirm_password) {
        header("Location: register.html?error=Passwords do not match");
        exit();
    }

    // Handle profile photo upload
    $profile_photo = null;
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
            $profile_photo = $upload_path;
        }
    }

    try {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            header("Location: register.html?error=Username already exists");
            exit();
        }

        // Insert new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            INSERT INTO users (username, email, password, profile_photo, gender, 
                             birth_date, looking_for, location, bio, interests) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $username, $email, $hashed_password, $profile_photo, $gender,
            $birth_date, $looking_for, $location, $bio, $interests
        ]);

        header("Location: index.php?success=Registration successful! Please login.");
        exit();
    } catch(PDOException $e) {
        header("Location: register.html?error=" . urlencode($e->getMessage()));
        exit();
    }
}
?>