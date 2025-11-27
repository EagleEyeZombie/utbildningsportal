<?php
// Vi inkluderar config för att få rätt session_name och inställningar
// (Detta startar sessionen automatiskt eftersom session_start() ligger i config.php)
require_once "include/config.php";

// 1. Töm alla sessionsvariabler
$_SESSION = array();

// 2. Ta bort sessions-cookien (Viktigt för säkerhet!)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Förstör sessionen helt
session_destroy();

// 4. Skicka användaren till inloggningssidan
header("Location: login.php");
exit;
?>
