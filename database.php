<?php
try {
    $conn = new PDO("mysql:host=localhost;port=3307;dbname=aaa_system", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>