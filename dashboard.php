<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['role_level'] >= 5) {
    header("Location: admin_dashboard.php");
    exit;
}
// ---------------------

// Hämta alla tillgängliga uppgifter
$allTasks = $task_obj->getAllTasks();
?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="p-5 bg-light rounded-3 shadow-sm">
                <h1 class="display-5 fw-bold">Välkommen, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p class="fs-4">Här hittar du dina tillgängliga uppgifter. Välj en och börja samla poäng!</p>
            </div>
        </div>
    </div>

    <h3 class="mb-3">Tillgängliga Uppgifter</h3>

    <div class="row">
        <?php if (count($allTasks) > 0): ?>
            <?php foreach ($allTasks as $task): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-header bg-white border-bottom-0 pt-3">
                            <span class="badge bg-primary">Nivå <?= htmlspecialchars($task['level_name']) ?></span>
                            <span class="badge bg-secondary"><?= htmlspecialchars($task['type_name']) ?></span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($task['t_name']) ?></h5>
                            <p class="card-text text-muted small">
                                <?= htmlspecialchars(mb_strimwidth($task['t_text'], 0, 80, "...")) ?>
                            </p>
                        </div>
                        <div class="card-footer bg-white border-top-0 pb-3">
                            <div class="d-grid">
                                <a href="task_view.php?id=<?= $task['t_id'] ?>" class="btn btn-outline-primary">
                                    Starta Uppgift <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    Det finns inga uppgifter upplagda just nu. Be din lärare lägga till några!
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once "include/footer.php";
?>
