<?php

/**
 * Rensar data från skadlig kod (XSS-skydd).
 * Används på all indata från formulär.
 */
/**
 * Rensar data från skadlig kod (XSS-skydd).
 */
// ---------------------------------------------------------
// SÄKERHET: XSS (Cross-Site Scripting)
// ---------------------------------------------------------
// Denna funktion används i FLÖDE A (Registrering) och FLÖDE C (Skapa uppgifter).
// Den "tvättar" all data som användaren skickar in.
function cleanInput($data) {
    // Om datan är null (tom), returnera tom sträng för att undvika fel.
    if ($data === null) {
        return ''; // Returnera tom sträng om data saknas
    }
    
    // 1. Ta bort onödiga mellanslag i början/slutet (t.ex. "  admin  " -> "admin")
    $data = trim($data);
    
    // 2. Ta bort backslashes (\) som kan användas för att "rymma" ur strängar.
    $data = stripslashes($data);
    
    // 3. DET VIKTIGASTE STEGET: htmlspecialchars
    // Detta omvandlar tecken som <, >, &, " till HTML-entiteter (&lt;, &gt;, etc).
    // Om en hacker skriver "<script>alert('hack')</script>", blir det "&lt;script&gt;..."
    // Webbläsaren visar då texten istället för att KÖRA koden.
    // ENT_QUOTES: Hanterar både enkla (') och dubbla (") citattecken.
    // UTF-8: Säkerställer att vi hanterar svenska tecken korrekt.
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    return $data;
}

/**
 * Genererar ett CSRF-token fält för formulär.
 * Skyddar mot Cross-Site Request Forgery.
 */
// ---------------------------------------------------------
// SÄKERHET: CSRF (Cross-Site Request Forgery) - GENERERING
// ---------------------------------------------------------
// Denna funktion anropas inuti <form>-taggar (t.ex. i register.php, admin_create_task.php).
// Den skapar ett dolt fält med en hemlig kod.
function csrfInput() {
    // Om vi inte redan har en token i sessionen, skapa en ny.
    if (empty($_SESSION['csrf_token'])) {
        // random_bytes(32) genererar en kryptografiskt säker slumpsträng.
        // bin2hex gör den läsbar (a-f, 0-9).
        // Detta är mycket säkrare än rand() eller time().
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    // Returnera HTML-koden för input-fältet.
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

/**
 * Verifierar att CSRF-token är giltig.
 */
// ---------------------------------------------------------
// SÄKERHET: CSRF - VERIFIERING
// ---------------------------------------------------------
// Denna funktion anropas högst upp i controllers när ett formulär tagits emot (POST).
// Den kollar: "Är koden som skickades med formuläret samma som den vi har i sessionen?"
function verifyCsrfToken($token) {
    // Lägg till en koll: Om token saknas (är null), returnera false direkt.
    if (empty($token) || !is_string($token)) {
        return false;
    }

    // Jämför sessionens token med den inskickade token ($token).
    // VIKTIGT: hash_equals() skyddar mot "Timing Attacks".
    // Om vi använde '==' skulle en hacker kunna gissa koden genom att mäta hur lång tid jämförelsen tar.
    // hash_equals tar alltid lika lång tid oavsett om strängen är rätt eller fel.
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    }
    return false;
}
?>
