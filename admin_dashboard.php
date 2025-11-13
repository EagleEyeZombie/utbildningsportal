<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
// 1. Är man inloggad?
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// 2. Är man Admin/Lärare? (Kräver nivå 5 eller högre)
if ($_SESSION['role_level'] < 5) {
    // Om man är elev som försöker hacka sig in -> skicka tillbaka till dashboard
    header("Location: dashboard.php");
    exit;
}
// ---------------------
?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col">
            <h1>Adminpanel</h1>
            <p class="lead">Välkommen <?php echo htmlspecialchars($_SESSION['username']); ?> (Behörighet: <?php echo $_SESSION['role_level']; ?>)</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <h3 class="card-title"><i class="bi bi-people"></i> Användare</h3>
                    <p class="card-text">Lägg till, redigera eller ta bort elever och lärare.</p>
                    <a href="user-management.php" class="btn btn-primary">Hantera Användare</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <h3 class="card-title"><i class="bi bi-pencil-square"></i> Uppgifter</h3>
                    <p class="card-text">Skapa nya quiz, redigera frågor och se material.</p>
                    <a href="#" class="btn btn-secondary disabled">Kommer snart (Sprint 4)</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <h3 class="card-title"><i class="bi bi-graph-up"></i> Klasser</h3>
                    <p class="card-text">Se översikt över klasser och resultat.</p>
                    <a href="#" class="btn btn-secondary disabled">Kommer snart</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once "include/footer.php";
?>
