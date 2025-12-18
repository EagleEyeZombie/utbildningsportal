<?php
require_once "include/header.php";

// ---------------------------------------------------------
// SÄKERHETSVAKT (RBAC - Role Based Access Control)
// ---------------------------------------------------------
// Detta är portvakten för hela admin-delen (Flöde C).

// 1. AUTENTISERING (Är du inloggad?)
// Vi kollar om sessionsvariabeln 'user_id' existerar.
// Om inte, vet vi inte vem besökaren är -> Skicka till login.
if (!isset($_SESSION['user_id'])) {
    // Inte inloggad alls -> Gå till Login
    header("Location: login.php");
    exit;
}

// 2. AUKTORISERING (Har du rätt behörighet?)
// Även om man är inloggad, får inte vem som helst vara här.
// Elever har vanligtvis rollnivå 1. Lärare har 5, Admin har 10.
// Vi spärrar allt under nivå 5.
if ($_SESSION['role_level'] < 5) {
    // Inloggad men fel behörighet (t.ex. en Elev som försöker hacka sig in)
    // Skicka till en 403 Forbidden-sida.
    header("Location: 403.php");
    exit;
}
?>

<div class="container mt-5">
    
    <div class="mb-5">
        <h1>Adminpanel</h1>
        <p class="lead">Välkommen <?php echo htmlspecialchars($_SESSION['username']); ?> (Behörighet: <?php echo $_SESSION['role_level']; ?>)</p>
    </div>

    <div class="row">
        
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100 p-4">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-people display-4 text-dark me-3"></i>
                        <h3 class="card-title mb-0">Användare</h3>
                    </div>
                    <p class="text-muted">Lägg till nya elever, redigera konton eller återställ lösenord.</p>
                    <a href="user-management.php" class="btn btn-primary w-100">Hantera Användare</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100 p-4">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-pencil-square display-4 text-dark me-3"></i>
                        <h3 class="card-title mb-0">Uppgifter</h3>
                    </div>
                    <p class="text-muted">Skapa nya äventyr, redigera frågor och hantera innehåll.</p>
                    <a href="admin_tasks.php" class="btn btn-primary w-100">Hantera Uppgifter</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100 p-4">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-people-fill display-4 text-dark me-3"></i>
                        <h3 class="card-title mb-0">Klasser</h3>
                    </div>
                    <p class="text-muted">Skapa klasser och placera elever i grupper.</p>
                    <a href="admin_classes.php" class="btn btn-primary w-100">Hantera Klasser</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once "include/footer.php"; ?>
