<?php
require_once "include/header.php";

// ---------------------------------------------------------
// SÄKERHETSVAKT (RBAC - HÖG NIVÅ)
// ---------------------------------------------------------
// Här är kraven strängare än vanligt.
// Vanliga lärare (Level 5) kanske får redigera, men bara Huvud-Admin (Level 10) får radera.
// Detta är en extra säkerhetsåtgärd för att förhindra olyckor.
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 10) {
    // Om man inte är Super-Admin, skicka tillbaka till dashboarden.
    header("Location: dashboard.php");
    exit;
}

// ---------------------------------------------------------
// MOTTAGNING AV BEGÄRAN (SÄKERHET)
// ---------------------------------------------------------
// Vi accepterar ENDAST anrop via metoden POST.
// Varför? Om vi tillät GET (t.ex. delete-user.php?uid=5) skulle en hackare kunna skicka
// en bildlänk till admin: <img src="delete-user.php?uid=5"> och radera en användare
// så fort admin öppnar mejlet. POST kräver ett aktivt formulärklick.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['uid'])) {
    
    // CSRF-skydd (Cross-Site Request Forgery)
    // Vi kollar att det medföljande "passet" (token) är giltigt.
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die("Ogiltig säkerhetstoken. Försök igen.");
    }

    $userId = $_POST['uid'];

    // ---------------------------------------------------------
    // LOGIK: SKYDD MOT SJÄLVMORD (SELF-DELETION)
    // ---------------------------------------------------------
    // Det är en dålig idé att låta en admin radera sitt eget konto medan de är inloggade.
    // Det kan leda till konstiga fel eller att systemet står utan admin.
    if ($userId == $_SESSION['user_id']) {
        // Skicka tillbaka med felmeddelande i URL:en
        header("Location: user-management.php?error=self_delete");
        exit;
    }

    // ---------------------------------------------------------
    // DATABAS: RADERING
    // ---------------------------------------------------------
    try {
        // Vi använder Prepared Statements för att förhindra SQL Injection.
        // DELETE är en definitiv åtgärd, så vi måste vara exakta.
        $stmt = $pdo->prepare("DELETE FROM users WHERE u_id = ?");
        $stmt->execute([$userId]);
        
        // Om vi kom hit utan krasch, har det lyckats.
        // Vi skickar en "flagga" (?deleted=success) till listan så den kan visa en grön ruta.
        header("Location: user-management.php?deleted=success");
        exit;

    } catch (PDOException $e) {
        // Logga felet eller visa det för admin (bra vid utveckling/felsökning)
        die("Kunde inte radera användare: " . $e->getMessage());
    }

} else {
    // Om någon försöker gå till denna sida direkt (GET), kasta bara tillbaka dem.
    header("Location: user-management.php");
    exit;
}
?>
