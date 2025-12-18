<?php
require_once "include/header.php";

// ---------------------------------------------------------
// SIDA FÖR ÅTKOMST NEKAD (HTTP 403 Forbidden)
// ---------------------------------------------------------
// Denna sida visas när en användare försöker nå en resurs de inte har behörighet till.
// Exempel: En elev (Level 1) försöker gå till admin_dashboard.php (Kräver Level 5).
// Säkerhetsmekanismen (RBAC) i de andra filerna omdirigerar hit via header("Location: 403.php").
?>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center p-5 rounded shadow-lg" style="background: rgba(0, 0, 0, 0.6); border: 2px solid var(--danger-red); max-width: 600px;">
        
        <div class="mb-4">
            <i class="bi bi-shield-lock-fill text-danger" style="font-size: 5rem;"></i>
        </div>
        
        <h1 class="display-4 fw-bold text-white mb-3" style="font-family: 'Cinzel Decorative', serif;">Åtkomst Nekad</h1>
        
        <p class="lead text-white mb-4">
            Stopp där! Du har inte behörighet att se den här sidan. 
            Detta område är skyddat av magiska besvärjelser (och administratörer).
        </p>
        
        <hr class="border-secondary mb-4">
        
        <div class="d-grid gap-3 d-sm-flex justify-content-center">
            
            <a href="dashboard.php" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-house-door-fill"></i> Tillbaka till säkerhet
            </a>

            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="btn btn-outline-light btn-lg px-4">
                    Logga in
                </a>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once "include/footer.php"; ?>
