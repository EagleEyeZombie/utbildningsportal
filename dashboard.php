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

// --- FILTERLOGIK ---
$studentId = $_SESSION['user_id'];

// Hämta filter från URL
$filterTypeId = isset($_GET['type']) && $_GET['type'] !== '' ? (int)$_GET['type'] : null;
$filterGenreId = isset($_GET['genre']) && $_GET['genre'] !== '' ? (int)$_GET['genre'] : null;
$viewAllLevels = isset($_GET['view']) && $_GET['view'] === 'all';

// Kolla om vi har något filter aktivt
$hasActiveFilter = ($filterTypeId !== null || $filterGenreId !== null);

// VIKTIGT: Vi visar listan om man valt ett filter ELLER om man klickat "Visa allt!"
$shouldShowTasks = ($hasActiveFilter || $viewAllLevels);

// Hämta listor för att bygga knapparna
$allTypes = $task_obj->getAllTypes();
$allGenres = $task_obj->getAllGenres();

// Hämta uppgifter
$allTasks = [];
if ($shouldShowTasks) {
    $allTasks = $task_obj->getTasksForStudent($studentId, $filterTypeId, $filterGenreId);
}

// Hämta badges för att visa dem (HÄR VAR FELET - NU TILLBAKA)
$myBadges = $task_obj->getStudentBadges($studentId);

// Hjälpfunktion för att bygga länkar
function buildUrl($params) {
    return 'dashboard.php?' . http_build_query(array_merge($_GET, $params));
}
?>

<div class="container mt-5">
    
    <!-- VÄLKOMSTPANEL (HERO) -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="p-5 dashboard-hero rounded-3">
                <h1 class="display-5 fw-bold">Välkommen, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p class="fs-4 lead">Välj ett äventyr nedan och börja samla poäng!</p>
            </div>
        </div>
    </div>

    <!-- STATISTIK (XP & LEVEL) -->
    <div class="row mt-4 justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-header">Dina Poäng</div>
                <div class="card-body p-4">
                    <p class="card-text display-4 text-white fw-bold">
                        <?php echo isset($_SESSION['user_xp']) ? $_SESSION['user_xp'] : 0; ?> XP
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-header">Din Nivå</div>
                <div class="card-body p-4">
                    <p class="card-text display-4 text-white fw-bold">
                        Nivå <?php echo isset($_SESSION['user_level']) ? $_SESSION['user_level'] : 1; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- BADGES (NU TILLBAKA!) -->
    <?php if (!empty($myBadges)): ?>
    <div class="row justify-content-center mb-5">
        <div class="col-md-12">
            <div class="card text-center shadow-sm">
                <div class="card-header">Dina Utmärkelser</div>
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <?php foreach ($myBadges as $badge): ?>
                            <div class="badge-item p-2 text-center">
                                <i class="bi <?= htmlspecialchars($badge['a_icon']) ?> display-4 text-warning" style="text-shadow: 1px 1px 2px #000;"></i>
                                <div class="mt-1 fw-bold text-white"><?= htmlspecialchars($badge['a_name']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <h3 class="mb-4 text-center text-white" style="text-shadow: 2px 2px 4px #000;">Välj Ditt Äventyr</h3>

    <!-- FILTERSEKTION -->
    <div class="filter-section text-center">
        
        <!-- TOPPKNAPPAR (Rensa & Visa Allt) -->
        <div class="mb-4 d-flex justify-content-center flex-wrap gap-3">
            
            <!-- 1. Rensa val! -->
            <a href="dashboard.php" class="btn btn-filter <?php echo (!$hasActiveFilter && !$viewAllLevels) ? 'btn-filter-active' : ''; ?>">
                <i class="bi bi-x-circle"></i> Rensa val!
            </a>

            <!-- 2. Visa allt! (Guldig knapp) -->
            <?php if ($viewAllLevels): ?>
                <a href="<?= buildUrl(['view' => 'focus']) ?>" class="btn btn-filter" style="border-color: #fff;">
                    <i class="bi bi-eye-slash"></i> Dölj låsta
                </a>
            <?php else: ?>
                <!-- Guld-styling direkt här för att den ska sticka ut -->
                <a href="<?= buildUrl(['view' => 'all']) ?>" class="btn btn-filter" style="background-color: var(--accent-gold); color: #2c2c2c; border-color: #fff; box-shadow: 0 0 15px rgba(255, 215, 0, 0.4);">
                    <i class="bi bi-eye"></i> Visa allt!
                </a>
            <?php endif; ?>

        </div>

        <div class="row justify-content-center">
            <!-- Uppgiftstyper -->
            <div class="col-md-5 mb-3">
                <span class="filter-label">Välj Spelsätt</span>
                <div class="d-flex flex-wrap justify-content-center">
                    <?php foreach ($allTypes as $type): ?>
                        <a href="<?= buildUrl(['type' => $type['tt_id']]) ?>" 
                           class="btn btn-filter <?php echo ($filterTypeId === $type['tt_id']) ? 'btn-filter-active' : ''; ?>">
                            <?= htmlspecialchars($type['tt_name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Genrer -->
            <div class="col-md-5 mb-3">
                <span class="filter-label">Välj Tema</span>
                <div class="d-flex flex-wrap justify-content-center">
                    <?php foreach ($allGenres as $genre): ?>
                        <a href="<?= buildUrl(['genre' => $genre['g_id']]) ?>" 
                           class="btn btn-filter <?php echo ($filterGenreId === $genre['g_id']) ? 'btn-filter-active' : ''; ?>">
                            <?= htmlspecialchars($genre['g_name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- SLUT FILTERSEKTION -->

    <!-- UPPGIFTSLISTA -->
    <?php if ($shouldShowTasks): ?>
        
        <div class="row">
            <?php if (count($allTasks) > 0): ?>
                <?php 
                $visibleCount = 0;
                foreach ($allTasks as $task): 
                    // --- PROGRESSIONSLOGIK ---
                    $currentGenreId = $filterGenreId ?? $task['t_genre_fk'];
                    $unlockedLevelForThisType = $task_obj->getUnlockedLevel($_SESSION['user_id'], $task['t_type_fk'], $currentGenreId);
                    
                    $isLocked = ($task['tl_level'] > $unlockedLevelForThisType);
                    $isCompleted = ($task['st_completed'] == 1);
                    
                    // VIKTIGT: Om inte "Visa Alla", dölj låsta OCH klara uppgifter
                    if (!$viewAllLevels) {
                        if ($isLocked) continue;
                        if ($isCompleted) continue;
                    }
                    $visibleCount++;
                ?>

                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 <?php echo ($isCompleted) ? 'border-success' : 'border-0'; ?>"
                         style="<?php echo $isLocked ? 'opacity: 0.7; filter: grayscale(100%);' : ''; ?>">
                        
                        <div class="card-header bg-white border-bottom-0 pt-3">
                            <div class="badge-container">
                                <span class="badge bg-primary badge-info-pill">Nivå <?= htmlspecialchars($task['level_name']) ?></span>
                                <span class="badge bg-secondary badge-info-pill"><?= htmlspecialchars($task['type_name']) ?></span>
                            </div>
                            
                            <?php if (!empty($task['genre_name'])): ?>
                            <div class="text-center mb-2">
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($task['genre_name']) ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($isCompleted): ?>
                                <span class="badge bg-success badge-result">
                                    <i class="bi bi-check-lg"></i> KLARAD
                                    <span class="percent"><?= $task['st_score'] ?>% RÄTT</span>
                                </span>
                            <?php elseif (isset($task['st_score']) && $task['st_score'] !== null): ?>
                                <span class="badge bg-warning text-dark badge-result">
                                    FÖRSÖK IGEN
                                    <span class="percent"><?= $task['st_score'] ?>% RÄTT</span>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title fw-bold">
                                <?php if ($isLocked): ?>
                                    <i class="bi bi-lock-fill"></i> Låst Kapitel
                                <?php else: ?>
                                    <?= htmlspecialchars($task['t_name']) ?>
                                <?php endif; ?>
                            </h5>
                            
                            <p class="card-text text-muted small">
                                <?php if ($isLocked): ?>
                                    Du måste klara föregående kapitel i denna serie för att låsa upp detta äventyr.
                                <?php else: ?>
                                    <?= htmlspecialchars(mb_strimwidth($task['t_text'], 0, 80, "...")) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <div class="card-footer bg-white border-top-0 pb-3">
                            <div class="d-grid">
                                <?php if ($isLocked): ?>
                                    <button class="btn btn-secondary disabled">Låst <i class="bi bi-lock"></i></button>
                                <?php else: ?>
                                    <a href="task_view.php?id=<?= $task['t_id'] ?>" class="btn <?php echo ($isCompleted) ? 'btn-outline-success' : 'btn-outline-primary'; ?>">
                                        <?php echo ($isCompleted) ? 'Förbättra resultat' : 'Starta Äventyret'; ?> <i class="bi bi-arrow-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if ($visibleCount == 0): ?>
                     <div class="col-12">
                        <div class="alert alert-success text-center" style="background-color: rgba(40, 167, 69, 0.8); color: white; border: 2px solid white;">
                            <h4>Wow! Du har klarat allt här!</h4>
                            <p>Du har inga nya uppgifter i den här kategorin just nu. Klicka på "Visa allt!" för att se dina prestationer.</p>
                        </div>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info" style="background-color: rgba(80, 88, 100, 0.9); border: 2px solid var(--border-thick); color: white;">
                        <h4>Inga äventyr hittades!</h4>
                        <p>Det finns inga uppgifter som matchar din filtrering just nu.</p>
                    </div>
                </div>
            <?php endif; ?>
    </div>

    <?php else: ?>
        <!-- OM INGET FILTER ÄR VALT (Startsidan för dashboard) -->
        <div class="row">
            <div class="col-12">
                <div class="p-5 rounded-3 text-center" style="background: rgba(0,0,0,0.3); border: 2px dashed var(--accent-gold);">
                    <h2 style="color: var(--accent-gold); font-family: 'Cinzel Decorative';">Välj din väg!</h2>
                    <p class="lead text-white">Klicka på en knapp ovan (Spelsätt eller Tema) för att se dina uppdrag.</p>
                    <i class="bi bi-arrow-up-circle display-3 text-white"></i>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once "include/footer.php"; ?>
