<?php

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function validateInt($input) {
    $int = filter_var($input, FILTER_VALIDATE_INT);
    return ($int === false) ? null : $int;
}

/**
 * Validates and sanitizes email input.
 * @param string $email
 * @return string|false Validated email or false
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
