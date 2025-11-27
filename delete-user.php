<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
// Endast Admin (Level 10) får radera användare
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 10) {
    header("Location: dashboard.php");
    exit;
}

// Vi kräver POST och en giltig CSRF-token
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['uid'])) {
    
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die("Ogiltig säkerhetstoken. Försök igen.");
    }

    $userId = $_POST['uid'];

    // Skydd: Man får inte radera sig själv
    if ($userId == $_SESSION['user_id']) {
        // ÄNDRAT: user-management.php
        header("Location: user-management.php?error=self_delete");
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE u_id = ?");
        $stmt->execute([$userId]);
        
        // ÄNDRAT: user-management.php
        header("Location: user-management.php?deleted=success");
        exit;

    } catch (PDOException $e) {
        die("Kunde inte radera användare: " . $e->getMessage());
    }

} else {
    // ÄNDRAT: user-management.php
    header("Location: user-management.php");
    exit;
}
?>
