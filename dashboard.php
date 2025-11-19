<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
// 1. Är man inloggad?
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// 2. Är man Elev? (Admin ska inte vara här)
if ($_SESSION['role_level'] >= 5) {
    header("Location: admin_dashboard.php");
    exit;
}
// ---------------------

// --- FILTERLOGIK ---
$studentId = $_SESSION['user_id'];

// Hämta filter från URL, sätt till null om de inte finns
$filterTypeId = isset($_GET['type']) && $_GET['type'] !== '' ? (int)$_GET['type'] : null;
$filterGenreId = isset($_GET['genre']) && $_GET['genre'] !== '' ? (int)$_GET['genre'] : null;

// Hämta listor för att bygga knapparna
$allTypes = $task_obj->getAllTypes();
$allGenres = $task_obj->getAllGenres();

// Hämta uppgifter baserat på filtren
// Funktionen getTasksForStudent hanterar nu både typeId och genreId
$allTasks = $task_obj->getTasksForStudent($studentId, $filterTypeId, $filterGenreId);
// ---------------------
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

    <h3 class="mb-4 text-center text-white" style="text-shadow: 2px 2px 4px #000;">Välj Ditt Äventyr</h3>

    <!-- FILTERSEKTION (NY DESIGN MED STEN-KNAPPAR) -->
    <div class="filter-section text-center">
        
        <!-- Knapp för att nollställa allt -->
        <div class="mb-4">
            <a href="dashboard.php" class="btn btn-filter <?php echo ($filterTypeId === null && $filterGenreId === null) ? 'btn-filter-active' : ''; ?>">
                <i class="bi bi-globe"></i> Visa Allt
            </a>
        </div>

        <div class="row justify-content-center">
            <!-- Uppgiftstyper (Flerval, Sortering etc) -->
            <div class="col-md-5 mb-3">
                <span class="filter-label">Välj Spelsätt</span>
                <div class="d-flex flex-wrap justify-content-center">
                    <?php foreach ($allTypes as $type): ?>
                        <!-- Vi behåller genre-filtret i länken om det redan är valt -->
                        <a href="dashboard.php?type=<?= $type['tt_id'] ?><?= $filterGenreId ? '&genre='.$filterGenreId : '' ?>" 
                           class="btn btn-filter <?php echo ($filterTypeId === $type['tt_id']) ? 'btn-filter-active' : ''; ?>">
                            <?= htmlspecialchars($type['tt_name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Genrer (Fantasy, Sci-Fi etc) -->
            <div class="col-md-5 mb-3">
                <span class="filter-label">Välj Tema</span>
                <div class="d-flex flex-wrap justify-content-center">
                    <?php foreach ($allGenres as $genre): ?>
                        <!-- Vi behåller typ-filtret i länken om det redan är valt -->
                        <a href="dashboard.php?genre=<?= $genre['g_id'] ?><?= $filterTypeId ? '&type='.$filterTypeId : '' ?>" 
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
    <div class="row">
        <?php if (count($allTasks) > 0): ?>
            <?php foreach ($allTasks as $task): ?>
                <?php 
                    // --- PROGRESSIONSLOGIK ---
                    // Vi räknar ut olåst nivå för just DENNA uppgiftstyp
                    // Detta gör att "Nivå 2 Flerval" kan vara låst även om man klarat "Nivå 10 Sortering"
                    $unlockedLevelForThisType = $task_obj->getUnlockedLevel($_SESSION['user_id'], $task['t_type_fk']);
                    
                    // Om uppgiftens nivå är högre än vad eleven låst upp -> LÅST
                    $isLocked = ($task['tl_level'] > $unlockedLevelForThisType);
                ?>

                <div class="col-md-4 mb-4">
                    <!-- Lägg till stil för låsta kort (gråa och genomskinliga) -->
                    <div class="card shadow-sm h-100 <?php echo ($task['st_completed'] == 1) ? 'border-success' : 'border-0'; ?>"
                         style="<?php echo $isLocked ? 'opacity: 0.7; filter: grayscale(100%);' : ''; ?>">
                        
                        <div class="card-header bg-white border-bottom-0 pt-3">
                            <!-- Badges för Nivå och Typ -->
                            <div class="badge-container">
                                <span class="badge bg-primary badge-info-pill">Nivå <?= htmlspecialchars($task['level_name']) ?></span>
                                <span class="badge bg-secondary badge-info-pill"><?= htmlspecialchars($task['type_name']) ?></span>
                            </div>
                            
                            <!-- Badge för Genre (Om den finns) -->
                            <?php if (!empty($task['genre_name'])): ?>
                            <div class="text-center mb-2">
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($task['genre_name']) ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Resultat Badge (Om klarad eller försökt) -->
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
                                    <a href="task_view.php?id=<?= $task['t_id'] ?>" class="btn <?php echo ($task['st_completed'] == 1) ? 'btn-outline-success' : 'btn-outline-primary'; ?>">
                                        <?php echo ($task['st_completed'] == 1) ? 'Förbättra resultat' : 'Starta Äventyret'; ?> <i class="bi bi-arrow-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info" style="background-color: rgba(80, 88, 100, 0.9); border: 2px solid var(--border-thick); color: white;">
                    <h4>Inga äventyr hittades!</h4>
                    <p>Det finns inga uppgifter som matchar din filtrering just nu. Prova att välja "Visa Allt" eller en annan kategori.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once "include/footer.php";
?>
