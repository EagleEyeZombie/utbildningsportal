<?php
require_once "include/header.php"; // Laddar config, funktioner, klasser och $pdo

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

// Hämta data från formuläret
$taskId = $_POST['task_id'];
$userAnswers = isset($_POST['answers']) ? $_POST['answers'] : [];
$userId = $_SESSION['user_id'];

// Hämta facit (uppgiftens data) från databasen
$task = $task_obj->getTaskById($taskId);
if (!$task) {
    die("Uppgiften hittades inte.");
}

// Avkoda JSON-frågorna
$questions = json_decode($task['t_questions'], true);
$totalQuestions = count($questions);
$correctCount = 0;

// --- RÄTTNING ---
// Loopa igenom facit (frågorna från databasen)
foreach ($questions as $index => $q) {
    
    // Frågorna i formuläret är numrerade 1, 2, 3... (från $qCount)
    $questionKey = $index + 1; 
    
    // Kolla om eleven har skickat ett svar för denna fråga
    if (isset($userAnswers[$questionKey])) {
        
        $userAnswer = trim($userAnswers[$questionKey]);
        $correctAnswer = trim($q['a']); // 'a' är alltid rätt svar i vår JSON-struktur
        
        // Jämför elevens svar med facit
        if ($userAnswer === $correctAnswer) {
            $correctCount++;
        }
    }
}

// --- RESULTATBERÄKNING ---
$scorePercent = ($totalQuestions > 0) ? round(($correctCount / $totalQuestions) * 100) : 0;
$passed = ($scorePercent >= 70) ? 1 : 0; // 1 = Godkänd (TRUE), 0 = Ej godkänd


// --- DATABASUPPDATERING (XP & RESULTAT) ---

// === HÄR ÄR DEN NYA KODEN ===
// Kolla om eleven klarade provet
if ($passed) {
    // Om vi klarade uppgiften, ge XP!
    $taskXp = $task['t_xp']; // Hämta XP-värdet från uppgiften (tack vare getTaskById)
    
    // Uppdatera användarens totala XP i 'users'-tabellen
    $updateXpSql = "UPDATE users SET u_xp = u_xp + ? WHERE u_id = ?";
    $stmt = $pdo->prepare($updateXpSql);
    $stmt->execute([$taskXp, $userId]);
    
    // Uppdatera sessionen direkt så poängen syns på dashboarden
    $_SESSION['user_xp'] = (isset($_SESSION['user_xp']) ? $_SESSION['user_xp'] : 0) + $taskXp;
}
// === SLUT PÅ NY KOD ===


// Spara resultatet (poäng och godkänd-status) i 'student_tasks'-tabellen
// Detta görs oavsett om man klarade provet eller ej
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
                            <p class="mb-0">Du har klarat uppgiften och fått <strong><?= $taskXp ?> XP</strong>! Resultatet är sparat.</p>
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
