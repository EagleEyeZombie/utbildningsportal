<?php

/**
 * Rensar data från skadlig kod (XSS-skydd).
 * Används på all indata från formulär.
 */
function cleanInput($data) {
    $data = trim($data);            // Ta bort onödig whitespace
    $data = stripslashes($data);    // Ta bort backslashes
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Omvandla specialtecken till HTML-entiteter
    return $data;
}

/**
 * Genererar ett CSRF-token fält för formulär.
 * Skyddar mot Cross-Site Request Forgery.
 */
function csrfInput() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

/**
 * Verifierar att CSRF-token är giltig.
 */
function verifyCsrfToken($token) {
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    }
    return false;
}
?>
