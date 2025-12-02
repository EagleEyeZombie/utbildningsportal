<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$studentId = $_SESSION['user_id'];

// Hämta XP-progress data
$xpProgress = $user_obj->getLevelProgress($_SESSION['user_xp']);

// --- UPPDATERA DATA & KOLLA BADGES ---
$stmt = $pdo->prepare("SELECT u_xp, u_level FROM users WHERE u_id = ?");
$stmt->execute([$studentId]);
$freshUser = $stmt->fetch();

if ($freshUser) {
    $_SESSION['user_xp'] = $freshUser['u_xp'];
    $_SESSION['user_level'] = $freshUser['u_level'];
    $task_obj->checkAchievements($studentId, $_SESSION['user_xp']);
}

// --- FILTERLOGIK ---
$filterTypeId = isset($_GET['type']) && $_GET['type'] !== '' ? (int)$_GET['type'] : null;
$filterGenreId = isset($_GET['genre']) && $_GET['genre'] !== '' ? (int)$_GET['genre'] : null;
$viewAllLevels = isset($_GET['view']) && $_GET['view'] === 'all';

$hasActiveFilter = ($filterTypeId !== null || $filterGenreId !== null || $viewAllLevels);

$allTypes = $task_obj->getAllTypes();
$allGenres = $task_obj->getAllGenres();

$allTasks = [];
$sectionTitle = ""; 

if ($hasActiveFilter) {
    $allTasks = $task_obj->getTasksForStudent($studentId, $filterTypeId, $filterGenreId);
    $sectionTitle = "Sökresultat";
} else {
    $allTasks = $task_obj->getRecentAndNextTasks($studentId, 8);
    
    if (count($allTasks) > 0) {
        $sectionTitle = "Fortsätt spela & Rekommenderat";
    } else {
        $allTasks = $task_obj->getTasksForStudent($studentId); 
        $sectionTitle = "Börja ditt äventyr här!";
    }
}

$myBadges = $task_obj->getStudentBadges($studentId);

function buildUrl($params) {
    return 'dashboard.php?' . http_build_query(array_merge($_GET, $params));
}
?>

<div class="container mt-4">
    
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="p-3 dashboard-hero rounded-3 d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3">
                
                <div class="hero-left d-flex flex-column gap-2 ps-lg-3 text-lg-start text-center">
                    <div class="hero-stat">
                        <i class="bi bi-star-fill text-warning"></i> 
                        <span>Poäng: <strong><?php echo isset($_SESSION['user_xp']) ? $_SESSION['user_xp'] : 0; ?> XP</strong></span>
                    </div>
                    <div class="hero-stat">
                        <i class="bi bi-trophy-fill text-warning"></i> 
                        <span>Nivå: Level <strong><?php echo isset($_SESSION['user_level']) ? $_SESSION['user_level'] : 1; ?></strong></span>
                    </div>
                </div>

                <div class="hero-center text-center flex-grow-1 d-flex flex-column justify-content-center">
                    <h2 class="fw-bold mb-2" style="font-family: 'Cinzel Decorative', serif; color: var(--accent-gold); text-shadow: 2px 2px 0 #000; font-size: 1.8rem;">
                        Välkommen, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                    </h2>
                    
                    <div class="w-75 mx-auto">
                        <div class="progress" style="height: 20px; background-color: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: <?= $xpProgress['percent'] ?>%;" 
                                 aria-valuenow="<?= $xpProgress['percent'] ?>" aria-valuemin="0" aria-valuemax="100">
                                 <span style="font-size: 0.8rem; text-shadow: 1px 1px 2px #000; font-weight: bold;"><?= $xpProgress['percent'] ?>%</span>
                            </div>
                        </div>
                        <div class="mt-1 text-white small" style="text-shadow: 1px 1px 2px #000;">
                            <?php if ($xpProgress['is_max']): ?>
                                <span class="text-warning">Max Level! Du är en legend!</span>
                            <?php else: ?>
                                <span><?= $xpProgress['current'] ?> / <?= $xpProgress['target'] ?> XP</span>
                                <span class="opacity-75 mx-1">|</span> 
                                <span class="text-warning fw-bold"><?= $xpProgress['needed'] ?> XP till nästa nivå</span>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <div class="hero-right pe-lg-3 text-lg-end text-center" style="min-width: 150px;">
                    <a href="badges.php" class="text-decoration-none">
                        <div class="small mb-1 text-uppercase fw-bold badge-link-hover" style="color: var(--accent-gold); letter-spacing: 1px; font-size: 0.8rem;">
                            Mina badges <i class="bi bi-chevron-right small"></i>
                        </div>
                    </a>
                    <?php if (!empty($myBadges)): ?>
                        <div class="d-flex flex-wrap justify-content-center justify-content-lg-end gap-1">
                            <?php 
                            $displayBadges = array_slice($myBadges, 0, 4); 
                            foreach ($displayBadges as $badge): 
                            ?>
                                <div class="mini-badge" role="button" tabindex="0" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-placement="bottom" title="<?= htmlspecialchars($badge['a_name']) ?>" data-bs-content="<?= htmlspecialchars($badge['a_description']) ?>">
                                    <i class="bi <?= htmlspecialchars($badge['a_icon']) ?> fs-5 text-warning"></i>
                                </div>
                            <?php endforeach; ?>
                            <?php if(count($myBadges) > 4): ?>
                                <a href="badges.php" class="mini-badge text-decoration-none bg-dark border-secondary" title="Visa alla..." data-bs-toggle="tooltip">
                                    <span class="text-white small">+<?= count($myBadges) - 4 ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-white-50 small fst-italic">Inga badges än...</div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <div class="filter-section pt-3 pb-3 mb-4">
        <div class="row align-items-center mb-2">
            <div class="col-6 col-md-3 order-2 order-md-1 text-md-start text-center mt-2 mt-md-0">
                <?php if ($viewAllLevels): ?>
                    <a href="<?= buildUrl(['view' => 'focus']) ?>" class="btn btn-filter btn-sm" style="border-color: #fff; min-width: 100px;">
                        <i class="bi bi-eye-slash"></i> Dölj låsta
                    </a>
                <?php else: ?>
                    <a href="<?= buildUrl(['view' => 'all']) ?>" class="btn btn-filter btn-sm" style="background-color: var(--accent-gold); color: #2c2c2c; border-color: #fff; min-width: 100px;">
                        <i class="bi bi-eye"></i> Visa allt!
                    </a>
                <?php endif; ?>
            </div>
            <div class="col-12 col-md-6 order-1 order-md-2 text-center">
                <h3 class="m-0" style="color: var(--accent-gold); font-family: 'Cinzel Decorative', serif; text-shadow: 2px 2px 2px #000; font-size: 1.8rem;">
                    Välj din väg!
                </h3>
                <div class="text-white mt-1" style="font-size: 1.1rem; text-shadow: 1px 1px 1px #000;">
                    Klicka på knappar nedan för att filtrera.
                </div>
            </div>
            <div class="col-6 col-md-3 order-3 order-md-3 text-md-end text-center mt-2 mt-md-0">
                <a href="dashboard.php" class="btn btn-filter btn-sm <?php echo (!$hasActiveFilter && !$viewAllLevels) ? 'btn-filter-active' : ''; ?>" style="min-width: 100px;">
                    <i class="bi bi-x-circle"></i> Rensa
                </a>
            </div>
        </div>
        <hr class="border-secondary mb-3 mt-1">
        <div class="row">
            <div class="col-md-6 mb-2 border-end-md border-secondary">
                <div class="d-flex align-items-center mb-2 justify-content-center justify-content-md-start">
                    <span class="text-white text-uppercase fw-bold me-2" style="font-size: 1.2rem;">Spelsätt:</span>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                    <?php foreach ($allTypes as $type): ?>
                        <a href="<?= buildUrl(['type' => $type['tt_id']]) ?>" 
                           class="btn btn-filter btn-sm <?php echo ($filterTypeId === $type['tt_id']) ? 'btn-filter-active' : ''; ?>"> 
                            <?= htmlspecialchars($type['tt_name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-6 mb-2 ps-md-4">
                <div class="d-flex align-items-center mb-2 justify-content-center justify-content-md-start">
                    <span class="text-white text-uppercase fw-bold me-2" style="font-size: 1.2rem;">Genre:</span>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                    <?php foreach ($allGenres as $genre): ?>
                        <a href="<?= buildUrl(['genre' => $genre['g_id']]) ?>" 
                           class="btn btn-filter btn-sm <?php echo ($filterGenreId === $genre['g_id']) ? 'btn-filter-active' : ''; ?>">
                            <?= htmlspecialchars($genre['g_name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php if (!empty($allTasks)): ?>
            <div class="col-12 mb-3">
                <h4 class="text-white border-bottom border-secondary pb-2" style="font-family: 'Cinzel Decorative', serif; color: var(--accent-gold) !important;">
                    <i class="bi bi-compass"></i> <?= $sectionTitle ?>
                </h4>
            </div>
        <?php endif; ?>

        <?php if (count($allTasks) > 0): ?>
            <?php 
            foreach ($allTasks as $task): 
                // Filterlogik (behövs för "sökresultat"-vyn där vi inte har färdiga data i $allTasks på samma sätt)
                if ($hasActiveFilter) {
                    $currentGenreId = $filterGenreId ?? $task['t_genre_fk'];
                    $unlockedLevelForThisType = $task_obj->getUnlockedLevel($_SESSION['user_id'], $task['t_type_fk'], $currentGenreId);
                    $isLocked = ($task['tl_level'] > $unlockedLevelForThisType);
                    $isCompleted = ($task['st_completed'] == 1);
                } else {
                    // På startsidan är datan redan filtrerad och klar från getRecentAndNextTasks
                    $isLocked = false; // De vi hämtar är "nästa" eller "klara", så de är upplåsta
                    $isCompleted = ($task['st_completed'] == 1);
                }
            ?>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card shadow-sm h-100 <?php echo ($isCompleted) ? 'border-success' : 'border-0'; ?>"
                     style="<?php echo $isLocked ? 'opacity: 0.7; filter: grayscale(100%);' : ''; ?>">
                    
                    <div class="card-header bg-white border-bottom-0 pt-2 pb-2">
                        <div class="badge-container mb-1">
                            <span class="badge bg-primary badge-info-pill small-pill"><?= htmlspecialchars($task['level_name']) ?></span>
                            <span class="badge bg-secondary badge-info-pill small-pill"><?= htmlspecialchars($task['type_name']) ?></span>
                        </div>
                        <?php if (!empty($task['genre_name'])): ?>
                        <div class="text-center mb-1">
                            <span class="badge bg-light text-dark border small"><?= htmlspecialchars($task['genre_name']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($isCompleted): ?>
                            <span class="badge bg-success badge-result py-1"><i class="bi bi-check-lg"></i> KLARAD <span class="percent ms-1"><?= $task['st_score'] ?>%</span></span>
                        <?php elseif (isset($task['st_score']) && $task['st_score'] !== null): ?>
                            <span class="badge bg-warning text-dark badge-result py-1">FÖRSÖK IGEN <span class="percent ms-1"><?= $task['st_score'] ?>%</span></span>
                        <?php endif; ?>
                    </div>

                    <div class="card-body pt-0 pb-2 px-3">
                        <h5 class="card-title fw-bold mb-1" style="font-size: 1.1rem;">
                            <?php if ($isLocked): ?><i class="bi bi-lock-fill"></i> Låst<?php else: ?><?= htmlspecialchars($task['t_name']) ?><?php endif; ?>
                        </h5>
                        <p class="card-text text-muted small mb-2" style="font-size: 0.85rem; line-height: 1.4;">
                            <?php if ($isLocked): ?>Du måste klara föregående kapitel.<?php else: ?><?= htmlspecialchars(mb_strimwidth($task['t_text'], 0, 60, "...")) ?><?php endif; ?>
                        </p>
                    </div>

                    <div class="card-footer bg-white border-top-0 pb-2 px-2 pt-0">
                        <div class="d-grid">
                            <?php if ($isLocked): ?>
                                <button class="btn btn-secondary btn-sm disabled">Låst <i class="bi bi-lock"></i></button>
                            <?php else: ?>
                                
                                <?php 
                                    $btnText = "Starta";
                                    $btnClass = "btn-outline-primary";
                                    $btnIcon = "bi-arrow-right";

                                    if ($isCompleted) {
                                        $btnText = "Förbättra";
                                        $btnClass = "btn-outline-success";
                                    } elseif (isset($task['sort_priority']) && $task['sort_priority'] == 2) {
                                        // Priority 2 = Nästa logiska steg i äventyret
                                        $btnText = "Fortsätt äventyret";
                                        $btnClass = "btn-primary fw-bold"; // Solid färg för att sticka ut
                                        $btnIcon = "bi-play-circle-fill";
                                    }
                                ?>

                                <a href="task_view.php?id=<?= $task['t_id'] ?>" class="btn btn-sm <?= $btnClass ?>">
                                    <?= $btnText ?> <i class="bi <?= $btnIcon ?>"></i>
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
                    <p>Det finns inga uppgifter som matchar din filtrering just nu.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once "include/footer.php"; ?>
