<?php
// ---------------------------------------------------------
// INKLUDERING AV GEMENSAM KOD
// ---------------------------------------------------------
// require_once laddar in filen header.php.
// Detta är viktigt för DRY (Don't Repeat Yourself).
// I header.php startas sessionen (session_start) och databaskopplingen skapas ($pdo).
require_once "include/header.php";

// ---------------------------------------------------------
// LOGIK: OMDIRIGERING (CONTROLLER)
// ---------------------------------------------------------
// Syfte: Om användaren redan är inloggad ska de inte se denna "Välkommen"-sida.
// De ska skickas direkt till sin dashboard.

// 1. Kollar om 'user_id' finns i sessionen (tecken på att man är inloggad).
if (isset($_SESSION['user_id'])) {
    
    // 2. Kollar roll: Admin eller Elev?
    // isset() kollar så variabeln finns, och >= 5 kollar om det är en lärare/admin.
    if (isset($_SESSION['role_level']) && $_SESSION['role_level'] >= 5) {
        // Skicka administratörer till Admin-panelen
        header("Location: admin_dashboard.php");
    } else {
        // Skicka elever till Spelplanen
        header("Location: dashboard.php");
    }
    
    // 3. Stoppa scriptet
    // exit; är kritiskt efter en header("Location: ...").
    // Det hindrar servern från att fortsätta ladda resten av HTML-koden nedanför i onödan.
    exit;
}
?>

<div class="container mt-4 mb-5">
    
    <div class="p-4 p-md-5 mb-4 rounded-3 text-center position-relative" 
         style="background: rgba(0, 0, 0, 0.6); border: 4px solid var(--accent-gold); box-shadow: 0 0 20px rgba(0,0,0,0.8);">
        
        <h1 class="display-5 display-md-3 fw-bold text-white mb-3" 
            style="font-family: 'Cinzel Decorative', serif; text-shadow: 3px 3px 0px #000;">
            Välkommen Äventyrare!
        </h1>
        
        <p class="col-md-8 fs-5 mx-auto text-light mb-4" style="text-shadow: 1px 1px 2px #000;">
            Är du redo att ge dig ut på en resa genom kunskapens rike? 
            Lös uppgifter, samla XP och bli en mästare.
        </p>
        
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="login.php" class="btn btn-primary btn-lg px-4 fw-bold">Logga in</a>
            </div>
    </div>

    <div class="row align-items-stretch g-4"> 
        
        <div class="col-md-4">
            <div class="h-100 p-4 rounded-3 text-center" style="background: rgba(60, 68, 80, 0.9); border: 2px solid var(--border-thick);">
                <div class="mb-3">
                    <i class="bi bi-compass display-4 text-white"></i>
                </div>
                <h2 class="h3 text-white" style="font-family: 'Cinzel Decorative'; color: var(--accent-gold) !important;">Utforska</h2>
                <p class="text-light">Upptäck spännande berättelser och uppgifter i olika genrer som Fantasy och Sci-Fi.</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="h-100 p-4 rounded-3 text-center" style="background: rgba(60, 68, 80, 0.9); border: 2px solid var(--border-thick);">
                <div class="mb-3">
                    <i class="bi bi-lightning-charge display-4 text-white"></i>
                </div>
                <h2 class="h3 text-white" style="font-family: 'Cinzel Decorative'; color: var(--accent-gold) !important;">Samla XP</h2>
                <p class="text-light">Klara utmaningar för att tjäna poäng och nå högre nivåer. Kan du nå nivå 10?</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="h-100 p-4 rounded-3 text-center" style="background: rgba(60, 68, 80, 0.9); border: 2px solid var(--border-thick);">
                <div class="mb-3">
                    <i class="bi bi-trophy display-4 text-white"></i>
                </div>
                <h2 class="h3 text-white" style="font-family: 'Cinzel Decorative'; color: var(--accent-gold) !important;">Utmärkelser</h2>
                <p class="text-light">Lås upp unika badges och visa upp dina framsteg för läraren.</p>
            </div>
        </div>
        
    </div>
</div>

<?php require_once "include/footer.php"; ?>
