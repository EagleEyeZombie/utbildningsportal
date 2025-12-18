<?php

// ---------------------------------------------------------
// 1. OUTPUT BUFFERING
// ---------------------------------------------------------
// Vi startar en "buffert". Det betyder att ingen HTML skickas till webbläsaren
// förrän skriptet är klart (eller vi säger till).
// VARFÖR? Detta löser problemet med "Headers already sent"-fel om vi vill använda
// header("Location: ...") långt ner i koden.
ob_start(); // <-- NYTT: Starta Output Buffering (Fixar header-problem)

// ---------------------------------------------------------
// 2. SESSIONS-INSTÄLLNINGAR (SÄKERHET)
// ---------------------------------------------------------
// Här härdar vi sessionen mot attacker. Detta är "Best Practice" för säkerhet.

// 1. Sätt namn på sessionen (säkerhetspraxis)
// Genom att byta namn från standard "PHPSESSID" gör vi det liiite svårare för hackers
// att gissa att vi kör PHP, men framförallt separerar vi denna session från andra på samma server.
session_name('UTB_PORTAL_SESSION');

// 2. Ställ in cookien så den dör när webbläsaren stängs (0)
// Vi sätter också HttpOnly för att skydda mot XSS-attacker som försöker stjäla cookies.
session_set_cookie_params([
    'lifetime' => 0,            // 0 = Sessionen dör när webbläsaren stängs (bra för offentliga datorer).
    'path' => '/',              // Cookien gäller för hela webbplatsen.
    'domain' => '',             // Tomt = nuvarande domän.
    'secure' => false,          // VIKTIGT: Ändra till true om du kör HTTPS (på riktig server). False för localhost.
    'httponly' => true,         // SÄKERHET (XSS): JavaScript kan INTE läsa cookien. Skyddar mot sessionsstöld via JS.
    'samesite' => 'Strict'      // SÄKERHET (CSRF): Cookien skickas bara om förfrågan kommer från samma sajt.
]);

// Nu när inställningarna är klara, startar vi sessionen.
session_start();

// 3. Hantera Inaktivitet (60 minuter = 3600 sekunder)
// Detta är en extra säkerhetsåtgärd. Om man går ifrån datorn ska man loggas ut automatiskt.
$timeout_duration = 3600;

if (isset($_SESSION['last_activity'])) {
    $duration = time() - $_SESSION['last_activity'];
    
    if ($duration > $timeout_duration) {
        // Tiden har gått ut - Logga ut och rensa allt
        session_unset();     // Töm variablerna
        session_destroy();   // Döda sessionen på servern
        
        // Skicka till login med meddelande (via GET för enkelhetens skull här)
        header("Location: login.php?timeout=1");
        exit;
    }
}

// Uppdatera tidsstämpeln för senaste aktivitet så klockan nollställs
$_SESSION['last_activity'] = time();


// ---------------------------------------------------------
// 3. DATABASKOPPLING (PDO)
// ---------------------------------------------------------
// Vi använder PDO (PHP Data Objects) för att koppla upp oss.
// PDO är bättre än MySQLi eftersom det stödjer flera databaser och har bättre felhantering.

$host = '127.0.0.1';
$db   = 'utbildningsportal';
$user = 'root';
$pass = '';
$charset = 'utf8mb4'; // VIKTIGT: utf8mb4 stödjer alla tecken (inklusive emojis) och skyddar mot vissa SQL-attacker.

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Kasta fel (Exceptions) om SQL misslyckas. Bra för debugging.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Hämta data som associativa arrayer (['name' => 'Kalle']) istället för nummer.
    PDO::ATTR_EMULATE_PREPARES => false, // SÄKERHET: Tvingar databasen att använda ÄKTA Prepared Statements. Maximerar skyddet mot SQL Injection.
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Om vi inte kan koppla upp oss, döda skriptet.
    // I produktion bör man logga felet till en fil istället för att visa det för användaren.
    die("Databasfel: Kunde inte ansluta.");
}

// ---------------------------------------------------------
// 4. DEPENDENCY INJECTION (LADDA KLASSER)
// ---------------------------------------------------------
// Här laddar vi in våra klassfiler och skapar objekt.
// "Dependency Injection" betyder att vi skickar med det beroende ($pdo) som klassen behöver
// in i konstruktorn, istället för att klassen ska skapa det själv.

require_once "include/class_user.php";
require_once "include/class_task.php";
require_once "include/class_school.php";

// Vi skapar EN instans av varje klass och återanvänder dem på alla sidor som inkluderar config.php.
$user_obj = new User($pdo);
$task_obj = new Task($pdo);
$school_obj = new School($pdo);

?>
