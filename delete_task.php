<?php
require_once "include/header.php";

// ---------------------------------------------------------
// SÄKERHETSVAKT
// ---------------------------------------------------------
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    header("Location: login.php");
    exit;
}

// ---------------------------------------------------------
// HANTERA RADERING (Endast POST tillåts)
// ---------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {

    // 1. Kontrollera CSRF-token (Säkerhet)
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die("Ogiltig säkerhetstoken. Försök ladda om sidan.");
    }

    // 2. Hämta ID
    $taskId = $_POST['id'];

    // 3. Anropa funktionen för att radera
    // (Denna funktion bör ligga i class_task.php)
    $result = $task_obj->deleteTask($taskId);

    // 4. Kontrollera resultat och omdirigera
    if ($result['success']) {
        // Skicka tillbaka till listan med meddelande
        header("Location: admin_tasks.php?msg=deleted");
        exit;
    } else {
        die("Fel vid radering: " . $result['error']);
    }

} else {
    // Om någon försöker gå hit direkt via webbläsaren (GET), skicka tillbaka dem
    header("Location: admin_tasks.php");
    exit;
}
?>
