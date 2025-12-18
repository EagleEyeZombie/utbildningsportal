<?php
require_once "include/header.php";

// ---------------------------------------------------------
// SÄKERHETSVAKT (RBAC)
// ---------------------------------------------------------
// 1. Inloggningskoll: Är användaren inloggad?
// 2. Behörighetskoll: Har användaren nivå 5 eller högre? (Lärare/Admin)
// Om inte: Kasta ut dem direkt till inloggningssidan.
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    header("Location: login.php");
    exit;
}

// ---------------------------------------------------------
// MOTTAGNING & SÄKERHET (CONTROLLER)
// ---------------------------------------------------------
// Vi tillåter ENDAST att radering sker via POST-metoden.
// Att radera via GET (t.ex. delete_task.php?id=1) är en säkerhetsrisk
// eftersom webbläsare kan förladda länkar eller hackare kan skicka dolda länkar.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {

    // --- SÄKERHET: CSRF-SKYDD ---
    // Vi verifierar att anropet kommer från vårt eget formulär (i admin_tasks.php)
    // och inte från en annan webbplats som försöker lura läraren.
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die("Ogiltig säkerhetstoken. Försök ladda om sidan.");
    }

    // Hämta ID på uppgiften som ska raderas
    $taskId = $_POST['id'];

    // ---------------------------------------------------------
    // ANROPA MODELLEN (LOGIK & DATABAS)
    // ---------------------------------------------------------
    // Vi skickar jobbet vidare till Task-klassen.
    // I 'class_task.php' finns funktionen deleteTask() som kör SQL-frågan:
    // "DELETE FROM tasks WHERE t_id = ?"
    $result = $task_obj->deleteTask($taskId);

    // ---------------------------------------------------------
    // FEEDBACK & OMDIRIGERING (VIEW)
    // ---------------------------------------------------------
    if ($result['success']) {
        // Om raderingen lyckades:
        // Skicka tillbaka läraren till listan och visa en flagga (?msg=deleted)
        // som admin_tasks.php kan använda för att visa en grön ruta ("Uppgift raderad").
        header("Location: admin_tasks.php?msg=deleted");
        exit;
    } else {
        // Om något gick fel (t.ex. databasfel), visa felet direkt.
        die("Fel vid radering: " . $result['error']);
    }

} else {
    // Om någon försöker gå in på sidan manuellt (via webbläsarens adressfält/GET),
    // omdirigera dem bara tillbaka till listan utan att göra något.
    header("Location: admin_tasks.php");
    exit;
}
?>
