<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    die("Ogiltig säkerhetstoken (CSRF). Gå tillbaka och försök igen.");
}

// Hämta data
// --- RÄTTNING ---
// Flöde B. Steg 1.1: Hämta svaren från POST
$taskId = $_POST['task_id'];
$userAnswers = isset($_POST['answers']) ? $_POST['answers'] : [];
$userId = $_SESSION['user_id'];

$task = $task_obj->getTaskById($taskId);
if (!$task) { die("Uppgiften hittades inte."); }

$questions = json_decode($task['t_questions'], true);
$taskTypeName = strtolower($task['type_name']);
$correctCount = 0;
$totalQuestions = 0;



// 1. SORTERING
if (strpos($taskTypeName, 'sortering') !== false) {
    $correctOrder = $questions['s'];
    $totalQuestions = count($correctOrder);
    for ($i = 0; $i < $totalQuestions; $i++) {
        $correctSentence = trim($correctOrder[$i]);
        $studentSentence = isset($userAnswers[$i]) ? trim($userAnswers[$i]) : '';
        if ($correctSentence === $studentSentence) { $correctCount++; }
    }
} 

// 2. PARA IHOP
elseif (strpos($taskTypeName, 'para ihop') !== false) {
    $correctPairs = $questions; 
    $totalQuestions = count($correctPairs);
    
    for ($i = 0; $i < $totalQuestions; $i++) {
        $correctTerm = trim($correctPairs[$i]['term']);
        $studentTerm = isset($userAnswers[$i]) ? trim($userAnswers[$i]) : '';
        if ($correctTerm === $studentTerm) {
            $correctCount++;
        }
    }
}

// 3. TEXTLUCKOR
elseif (strpos($taskTypeName, 'textluckor') !== false) {
    $gaps = $questions['gaps'];
    $totalQuestions = count($gaps);
    
    for ($i = 0; $i < $totalQuestions; $i++) {
        $correctWord = trim(strtolower($gaps[$i]['word']));
        $studentWord = isset($userAnswers[$i]) ? trim(strtolower($userAnswers[$i])) : '';
        if ($correctWord === $studentWord) {
            $correctCount++;
        }
    }
}

// 4. FLERVAL & SANT/FALSKT
else {
    $totalQuestions = count($questions);
    foreach ($questions as $index => $q) {
        $questionKey = $index + 1; 
        if (isset($userAnswers[$questionKey])) {
            $userAnswer = trim($userAnswers[$questionKey]);
            if (strpos($taskTypeName, 'flerval') !== false) {
                $correctAnswer = trim($q['a']);
                if ($userAnswer === $correctAnswer) { $correctCount++; }
            } 
            elseif (strpos($taskTypeName, 'sant/falskt') !== false) {
                $correctAnswer = "";
                if (isset($q['a'])) { $correctAnswer = trim($q['a']); } 
                elseif (isset($q['correct'])) { $correctAnswer = $q['correct'] ? "Sant" : "Falskt"; }
                if ($userAnswer === $correctAnswer) { $correctCount++; }
            }
        }
    }
}

// --- RESULTATBERÄKNING ---
// Flöde B. Steg 1.2: Beräkna resultat och godkännande
$scorePercent = ($totalQuestions > 0) ? round(($correctCount / $totalQuestions) * 100) : 0;
$passed = ($scorePercent >= 70) ? 1 : 0;

$newBadges = [];
$nextTaskId = null;
$leveledUp = false;
$newLevel = $_SESSION['user_level'];
$gainedXp = 0; 

// Spara resultatet
// Flöde B. Steg 1.3: Spara resultatet i databasen (student_tasks)
$saved = $task_obj->saveTaskResult($userId, $taskId, $scorePercent, $passed);

if ($passed) {
    $xpResult = $user_obj->addXpAndCheckLevelup($userId, $task['t_xp']);
    
    if ($xpResult) {
        $gainedXp = $xpResult['gained_xp'];
        $leveledUp = $xpResult['leveled_up'];
        $newLevel = $xpResult['new_level'];
        $newBadges = $task_obj->checkAchievements($userId, $xpResult['new_xp']);
    }

    // Hitta nästa uppgift
    $currentTaskLevel = $task['tl_level'];
    $targetTaskLevel = $currentTaskLevel + 1;
    
    $sqlNext = "SELECT t_id FROM tasks 
                JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id 
                WHERE t_type_fk = ? AND task_levels.tl_level = ? ";
    $paramsNext = [$task['t_type_fk'], $targetTaskLevel];

    if (!empty($task['t_genre_fk'])) {
        $sqlNext .= " AND t_genre_fk = ?";
        $paramsNext[] = $task['t_genre_fk'];
    }
    $sqlNext .= " LIMIT 1";

    $stmtNext = $pdo->prepare($sqlNext);
    $stmtNext->execute($paramsNext);
    $nextTask = $stmtNext->fetch(PDO::FETCH_ASSOC);
    
    if ($nextTask) {
        $nextTaskId = $nextTask['t_id'];
    }
}
?>

<div class="container mt-5 task-result-page">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow text-center">
                <div class="card-header bg-white border-0 pt-4">
                    <?php if ($passed): ?>
                        <i class="bi bi-trophy-fill text-warning display-1"></i>
                        <h2 class="mt-3 text-success">Bra jobbat!</h2>
                        
                    <!-- Flöde B. Steg 4 -->
                        <?php if ($leveledUp): ?>
                            <div class="alert alert-info mt-3 shadow-sm border-info">
                                <h4><i class="bi bi-arrow-up-circle-fill"></i> LEVEL UP!</h4>
                                <p class="mb-0">Grattis! Du har nått <strong>Nivå <?= $newLevel ?></strong>!</p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($newBadges)): ?>
                            <div class="mt-3 p-3 bg-light border rounded shadow-sm">
                                <h5 class="text-dark mb-2">Du har låst upp nya utmärkelser!</h5>
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <?php foreach ($newBadges as $badge): ?>
                                        <div class="badge bg-warning text-dark p-2 fs-6 border border-dark">
                                            <i class="bi <?= htmlspecialchars($badge['a_icon']) ?>"></i> <?= htmlspecialchars($badge['a_name']) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <i class="bi bi-emoji-frown text-secondary display-1"></i>
                        <h2 class="mt-3 text-danger">Försök igen!</h2>
                    <?php endif; ?>
                </div>
                
                <div class="card-body p-4">
                    <h4 class="mb-3">Du fick <?= $scorePercent ?>% rätt</h4>
                    <p class="lead">
                        Du svarade rätt på <strong><?= $correctCount ?></strong> av <strong><?= $totalQuestions ?></strong>.
                    </p>

                    <?php if ($passed): ?>
                        <div class="alert alert-success">
                            <p class="mb-0">Du har klarat uppgiften och fått <strong><?= $gainedXp ?> XP</strong>!</p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <p class="mb-0">Du behöver minst 70% för att bli godkänd.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2 mt-4">
                        <?php if ($passed && $nextTaskId): ?>
                            <a href="task_view.php?id=<?= $nextTaskId ?>" class="btn btn-success btn-lg shadow">
                                Fortsätt äventyret <i class="bi bi-arrow-right-circle"></i>
                            </a>
                        <?php endif; ?>
                        
                        <a href="dashboard.php" class="btn btn-primary">Tillbaka till Dashboard</a>
                        <a href="task_view.php?id=<?= $taskId ?>" class="btn btn-outline-secondary">Gör om uppgiften</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "include/footer.php"; ?>
