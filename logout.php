<?php
session_start();

// Töm sessionen på data
session_unset();

// Förstör sessionen helt
session_destroy();

// Skicka användaren tillbaka till inloggningen
header("Location: login.php");
exit;
?>
