<?php

ob_start(); // <-- NYTT: Starta Output Buffering (Fixar header-problem)
// --- SESSIONS-INSTÄLLNINGAR (SÄKERHET) ---

// 1. Sätt namn på sessionen (säkerhetspraxis)
session_name('UTB_PORTAL_SESSION');

// 2. Ställ in cookien så den dör när webbläsaren stängs (0)
// Vi sätter också HttpOnly för att skydda mot XSS-attacker som försöker stjäla cookies.
session_set_cookie_params([
    'lifetime' => 0,            // 0 = Till webbläsaren stängs
    'path' => '/',
    'domain' => '',             // Tomt = nuvarande domän
    'secure' => false,          // Ändra till true om du kör HTTPS (på riktig server)
    'httponly' => true,         // JavaScript kan inte läsa cookien (XSS-skydd)
    'samesite' => 'Strict'      // Skydd mot CSRF
]);

session_start();

// 3. Hantera Inaktivitet (60 minuter = 3600 sekunder)
$timeout_duration = 3600;

if (isset($_SESSION['last_activity'])) {
    $duration = time() - $_SESSION['last_activity'];
    
    if ($duration > $timeout_duration) {
        // Tiden har gått ut - Logga ut och rensa allt
        session_unset();
        session_destroy();
        
        // Skicka till login med meddelande (via GET för enkelhetens skull här)
        header("Location: login.php?timeout=1");
        exit;
    }
}

// Uppdatera tidsstämpeln för senaste aktivitet
$_SESSION['last_activity'] = time();


// --- DATABASKOPPLING ---
$host = '127.0.0.1';
$db   = 'utbildningsportal';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Databasfel: Kunde inte ansluta.");
}

// Ladda klassfiler
require_once "include/class_user.php";
require_once "include/class_task.php";
require_once "include/class_school.php";

$user_obj = new User($pdo);
$task_obj = new Task($pdo);
$school_obj = new School($pdo);

?>
