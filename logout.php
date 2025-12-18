<?php
// ---------------------------------------------------------
// SÄKER UTLOGGNING (SESSION MANAGEMENT)
// ---------------------------------------------------------

// Vi inkluderar config för att få rätt session_name och inställningar.
// OBS: config.php innehåller oftast session_start().
// Det är kritiskt att starta sessionen innan vi kan förstöra den,
// annars vet servern inte VEM det är som ska loggas ut.
require_once "include/config.php";

// ---------------------------------------------------------
// STEG 1: TÖM SESSIONSVARIABLERNA
// ---------------------------------------------------------
// $_SESSION är en global array som innehåller data som "user_id", "role_level" etc.
// Genom att sätta den till en tom array() raderar vi all data i minnet för stunden.
$_SESSION = array();

// ---------------------------------------------------------
// STEG 2: DÖDA COOKIEN (SESSION HIJACKING SKYDD)
// ---------------------------------------------------------
// Även om vi tömt datan på servern, finns "nyckeln" (Session ID) kvar i användarens webbläsare som en cookie.
// För att vara helt säkra, ber vi webbläsaren att radera den cookien också.

if (ini_get("session.use_cookies")) {
    // Hämta parametrarna för hur cookien skapades (domän, path, säkerhet etc.)
    $params = session_get_cookie_params();
    
    // setcookie() används normalt för att SKAPA en cookie.
    // Men genom att sätta tiden till det förflutna (time() - 42000),
    // tvingar vi webbläsaren att betrakta den som "utgången" och radera den direkt.
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ---------------------------------------------------------
// STEG 3: FÖRSTÖR SESSIONEN PÅ SERVERN
// ---------------------------------------------------------
// Detta raderar själva sessionsfilen på serverns hårddisk/minne.
// Nu är kopplingen helt bruten både hos klienten (cookien borta) och servern (datat borta).
session_destroy();

// ---------------------------------------------------------
// STEG 4: OMDIRIGERING
// ---------------------------------------------------------
// Skicka användaren tillbaka till inloggningssidan.
header("Location: login.php");
exit; // Alltid exit efter header location för att stoppa exekvering.
?>
