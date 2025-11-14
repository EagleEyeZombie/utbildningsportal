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

// Hämta alla uppgifter INKLUSIVE elevens resultat
$allTasks = $task_obj->getTasksForStudent($_SESSION['user_id']);
?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="p-5 dashboard-hero rounded-3">
                <h1 class="display-5 fw-bold">Välkommen, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p class="fs-4">Här hittar du dina tillgängliga uppgifter. Välj en och börja samla poäng!</p>
            </div>
        </div>
    </div>

    <!-- HÄR BÖRJAR DEN NYA KODEN FÖR POÄNG/LEVEL -->
    <div class="row mt-4 justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-header">
                    Dina Poäng
                </div>
                <div class="card-body p-4">
                    <p class="card-text display-4 text-white fw-bold">
                        <?php echo isset($_SESSION['user_xp']) ? $_SESSION['user_xp'] : 0; ?> XP
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-header">
                    Din Nivå
                </div>
                <div class="card-body p-4">
                    <p class="card-text display-4 text-white fw-bold">
                        Nivå <?php echo isset($_SESSION['user_level']) ? $_SESSION['user_level'] : 1; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- SLUT PÅ NY KOD -->


    <h3 class="mb-3">Tillgängliga Uppgifter</h3>

    <div class="row">
        <?php if (count($allTasks) > 0): ?>
            <?php foreach ($allTasks as $task): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 <?php echo ($task['st_completed'] == 1) ? 'border-success' : 'border-0'; ?>">
                        
                        <div class="card-header bg-white border-bottom-0 pt-3">
                            <div class="badge-container">
                                <span class="badge bg-primary badge-info-pill">Nivå <?= htmlspecialchars($task['level_name']) ?></span>
                                <span class="badge bg-secondary badge-info-pill"><?= htmlspecialchars($task['type_name']) ?></span>
                            </div>
                            
                            <?php if (isset($task['st_completed'])): ?>
                                <?php if ($task['st_completed'] == 1): ?>
                                    <span class="badge bg-success badge-result">
                                        <i class="bi bi-check-lg"></i> KLARAD
                                        <span class="percent"><?= $task['st_score'] ?>% RÄTT</span>
                                    </span>
                                <?php elseif ($task['st_score'] !== null): ?>
                                    <span class="badge bg-warning text-dark badge-result">
                                        FÖRSÖK IGEN
                                        <span class="percent"><?= $task['st_score'] ?>% RÄTT</span>
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($task['t_name']) ?></h5>
                            <p class="card-text text-muted small">
                                <?= htmlspecialchars(mb_strimwidth($task['t_text'], 0, 80, "...")) ?>
                            </p>
                        </div>
                        
                        <div class="card-footer bg-white border-top-0 pb-3">
                            <div class="d-grid">
                                <a href="task_view.php?id=<?= $task['t_id'] ?>" class="btn <?php echo ($task['st_completed'] == 1) ? 'btn-outline-success' : 'btn-outline-primary'; ?>">
                                    <?php echo ($task['st_completed'] == 1) ? 'Förbättra resultat' : 'Starta Uppgift'; ?> <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    Det finns inga uppgifter upplagda just nu.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once "include/footer.php";
?>
