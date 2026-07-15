<?php



function redirect($url) {
    header("Location: " . $url);
    exit();
}


function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}


function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        redirect('login.php'); 
    }
}


function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}


function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}


function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}


function validateInt($input) {
    $int = filter_var($input, FILTER_VALIDATE_INT);
    return ($int === false) ? null : $int;
}


function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>