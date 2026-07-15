<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'phpmyadmin'); 
define('DB_USER', 'phpmyadmin'); 
define('DB_PASS', 'kali'); 

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage()); 
    die("Database connection failed. Please try again later."); 
}
?>