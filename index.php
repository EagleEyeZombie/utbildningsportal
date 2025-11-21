<?php
require_once "include/header.php";

// Om användaren redan är inloggad, skicka direkt till dashboard
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role_level']) && $_SESSION['role_level'] >= 5) {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit;
}
?>

<div class="container mt-5 text-center">
    
    <!-- HERO SEKTION -->
    <div class="p-5 mb-4 rounded-3" style="background: rgba(0, 0, 0, 0.6); border: 4px solid var(--accent-gold); box-shadow: 0 0 20px rgba(0,0,0,0.8);">
        <div class="container-fluid py-5">
            <h1 class="display-3 fw-bold text-white" style="font-family: 'Cinzel Decorative', serif; text-shadow: 3px 3px 0px #000;">
                Välkommen Äventyrare!
            </h1>
            <p class="col-md-8 fs-4 mx-auto text-light mt-4" style="text-shadow: 1px 1px 2px #000;">
                Är du redo att ge dig ut på en resa genom kunskapens rike? 
                Lös uppgifter, samla XP och bli en mästare.
            </p>
            
            <div class="d-flex justify-content-center gap-3 mt-5">
                <a href="login.php" class="btn btn-primary btn-lg px-5 py-3" style="font-size: 1.5rem; border-width: 3px;">
                    Logga In för att börja
                </a>
                <!-- "Börja Nu"-knappen är borttagen -->
            </div>
        </div>
    </div>

    <!-- FUNKTIONER (Tre kolumner) -->
    <div class="row align-items-md-stretch mt-5">
        <div class="col-md-4 mb-4">
            <div class="h-100 p-4 rounded-3" style="background: rgba(60, 68, 80, 0.9); border: 2px solid var(--border-thick);">
                <h2 class="text-white" style="font-family: 'Cinzel Decorative'; color: var(--accent-gold);">Utforska</h2>
                <p class="text-light">Upptäck spännande berättelser och uppgifter i olika genrer som Fantasy och Sci-Fi.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="h-100 p-4 rounded-3" style="background: rgba(60, 68, 80, 0.9); border: 2px solid var(--border-thick);">
                <h2 class="text-white" style="font-family: 'Cinzel Decorative'; color: var(--accent-gold);">Samla XP</h2>
                <p class="text-light">Klara utmaningar för att tjäna poäng och nå högre nivåer. Kan du nå nivå 10?</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="h-100 p-4 rounded-3" style="background: rgba(60, 68, 80, 0.9); border: 2px solid var(--border-thick);">
                <h2 class="text-white" style="font-family: 'Cinzel Decorative'; color: var(--accent-gold);">Lär Dig</h2>
                <p class="text-light">Träna din läsförståelse och logiska förmåga genom interaktiva sorteringsövningar.</p>
            </div>
        </div>
    </div>

</div>

<?php
require_once "include/footer.php";
?>
