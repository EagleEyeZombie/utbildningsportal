<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id'])) {
    // Inte inloggad alls -> Gå till Login
    header("Location: login.php");
    exit;
}

if ($_SESSION['role_level'] < 5) {
    // Inloggad men fel behörighet (t.ex. Elev försöker nå Admin) -> Gå till 403
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
                    <a href="admin_classes.php" class="btn btn-primary w-100">Hantera Klasser</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once "include/footer.php"; ?>
