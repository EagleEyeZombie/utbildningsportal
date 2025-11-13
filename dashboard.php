<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
// 1. Är man inloggad?
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// 2. Är man Elev? (Om man är admin (nivå 5+), skicka till admin-sidan)
if ($_SESSION['role_level'] >= 5) {
    header("Location: admin_dashboard.php");
    exit;
}
// ---------------------
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="p-5 mb-4 bg-light rounded-3 shadow-sm">
                <div class="container-fluid py-5">
                    <h1 class="display-5 fw-bold">Välkommen, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                    <p class="col-md-8 fs-4">Detta är din student-panel. Här kommer du snart att se dina poäng, din level och dina tillgängliga uppgifter.</p>
                    <button class="btn btn-primary btn-lg" type="button">Se mina uppgifter (Kommer snart)</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Dina Poäng</h5>
                    <p class="card-text display-6">0 XP</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Din Level</h5>
                    <p class="card-text display-6">Nivå 1</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once "include/footer.php";
?>
