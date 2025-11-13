<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Endast POST tillåts
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

// CSRF Check
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    die("Ogiltig säkerhetstoken (CSRF). Gå tillbaka och försök igen.");
}

// Hämta data
$taskId = $_POST['task_id'];
$userAnswers = isset($_POST['answers']) ? $_POST['answers'] : [];
$userId = $_SESSION['user_id'];

// Hämta facit (uppgiftens data)
$task = $task_obj->getTaskById($taskId);
if (!$task) {
    die("Uppgiften hittades inte.");
}

$questions = json_decode($task['t_questions'], true);
$totalQuestions = count($questions);
$correctCount = 0;

// --- RÄTTNING ---
foreach ($questions as $index => $q) {
    // Index i userAnswers börjar på 1 (från vår loop i view), men arrayen frågor börjar på 0
    // Vi måste matcha dem. I task_view använde vi $qCount som nyckel (1, 2, 3...)
    $questionKey = $index + 1;
    
    if (isset($userAnswers[$questionKey])) {
        $userAnswer = trim($userAnswers[$questionKey]);
        $correctAnswer = trim($q['a']); // 'a' är alltid rätt svar i vår JSON-struktur
        
        if ($userAnswer === $correctAnswer) {
            $correctCount++;
        }
    }
}

// --- RESULTAT ---
$scorePercent = ($totalQuestions > 0) ? round(($correctCount / $totalQuestions) * 100) : 0;
$passed = ($scorePercent >= 70) ? 1 : 0; // 1 = Godkänd (TRUE), 0 = Ej godkänd

// Spara i databasen
$saved = $task_obj->saveTaskResult($userId, $taskId, $scorePercent, $passed);

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow text-center">
                <div class="card-header bg-white border-0 pt-4">
                    <?php if ($passed): ?>
                        <i class="bi bi-trophy-fill text-warning display-1"></i>
                        <h2 class="mt-3 text-success">Bra jobbat!</h2>
                    <?php else: ?>
                        <i class="bi bi-emoji-frown text-secondary display-1"></i>
                        <h2 class="mt-3 text-danger">Försök igen!</h2>
                    <?php endif; ?>
                </div>
                
                <div class="card-body p-4">
                    <h4 class="mb-3">Du fick <?= $scorePercent ?>% rätt</h4>
                    <p class="lead">
                        Du svarade rätt på <strong><?= $correctCount ?></strong> av <strong><?= $totalQuestions ?></strong> frågor.
                    </p>

                    <?php if ($passed): ?>
                        <div class="alert alert-success">
                            <p class="mb-0">Du har klarat uppgiften! Resultatet är sparat.</p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <p class="mb-0">Du behöver minst 70% för att bli godkänd.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2 mt-4">
                        <a href="dashboard.php" class="btn btn-primary btn-lg">Tillbaka till Dashboard</a>
                        <a href="task_view.php?id=<?= $taskId ?>" class="btn btn-outline-secondary">Gör om uppgiften</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "include/footer.php"; ?>
