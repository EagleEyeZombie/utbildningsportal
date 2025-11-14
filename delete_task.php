<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    header("Location: login.php");
    exit;
}

// Hämta uppgiftens ID från URL:en
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_tasks.php");
    exit;
}
$taskId = $_GET['id'];

// Hämta uppgiftens info (för att visa namnet)
$task = $task_obj->getTaskById($taskId);
if (!$task) {
    header("Location: admin_tasks.php");
    exit;
}

// Hantera formuläret (Bekräftelse)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Om man trycker "AVBRYT"
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'back') {
        header("Location: admin_tasks.php");
        exit;
    }
    
    // 2. Om man trycker "TA BORT"
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'delete') {
        // CSRF Check
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            die("Ogiltig säkerhetstoken.");
        }
        
        // Kör delete-metoden
        $result = $task_obj->deleteTask($taskId);
        
        // Skicka tillbaka till listan
        header("Location: admin_tasks.php");
        exit;
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">Bekräfta borttagning</h3>
                </div>
                <div class="card-body p-4 text-center">
                    <p class="lead">Är du säker på att du vill ta bort uppgiften:</p>
                    <h2 class="mb-4">"<?php echo htmlspecialchars($task['t_name']); ?>"</h2>
                    <p class="text-muted">Denna åtgärd går inte att ångra. Alla studentresultat kopplade till denna uppgift kommer också att raderas permanent.</p>

                    <form action="" method="POST" class="mt-4">
                        <?= csrfInput() ?>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <button type="submit" name="confirm" value="back" class="btn btn-secondary btn-lg">Avbryt</button>
                            <button type="submit" name="confirm" value="delete" class="btn btn-danger btn-lg">Ja, ta bort</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "include/footer.php"; ?>
