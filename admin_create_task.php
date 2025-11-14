<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    header("Location: login.php");
    exit;
}

// Hämta data till dropdowns
$types = $task_obj->getAllTypes();
$levels = $task_obj->getAllLevels();
$errorMsg = "";
$successMsg = "";

// Hantera formulär-inlämning
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create-task'])) {
    
    // CSRF Check
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        die("Ogiltig CSRF-token.");
    }

    $tName = cleanInput($_POST['t_name']);
    $tType = cleanInput($_POST['t_type']);
    $tLevel = cleanInput($_POST['t_level']);
    $tText = cleanInput($_POST['t_text']); // Instruktioner
    $teacherId = $_SESSION['user_id'];

    // LÄGG TILL DENNA RAD HÄR:
    $tXp = cleanInput($_POST['t_xp']); // Hämta XP-värdet från formuläret

    // Hantera frågorna (som skickas som en array från formuläret)
    // Vi gör om arrayen till JSON för att spara den smidigt i databasen
    $questionsData = [];
    if (isset($_POST['questions'])) {
        foreach ($_POST['questions'] as $q) {
            // En enkel struktur: Fråga, Rätt Svar, Fel Svar 1, Fel Svar 2...
            if (!empty($q['question'])) {
                $questionsData[] = [
                    'q' => cleanInput($q['question']),
                    'a' => cleanInput($q['correct']), // Rätt svar
                    'w1' => cleanInput($q['wrong1']), // Fel svar
                    'w2' => cleanInput($q['wrong2']),
                    'w3' => cleanInput($q['wrong3'])
                ];
            }
        }
    }
    $jsonQuestions = json_encode($questionsData, JSON_UNESCAPED_UNICODE);

    // Spara via klassen
    // ÄNDRA DENNA RAD SÅ ATT DEN INKLUDERAR $tXp PÅ SLUTET:
    $result = $task_obj->createTask($tName, $tType, $tLevel, $teacherId, $tText, $jsonQuestions, $tXp);

    if ($result['success']) {
        $successMsg = "Uppgiften skapad! <a href='admin_tasks.php'>Tillbaka till listan</a>";
    } else {
        $errorMsg = $result['error'];
    }
}
?>

<div class="container mt-5 mb-5">
    <h1>Skapa ny uppgift</h1>
    
    <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?= $errorMsg ?></div>
    <?php endif; ?>
    <?php if ($successMsg): ?>
        <div class="alert alert-success"><?= $successMsg ?></div>
    <?php endif; ?>

    <form action="" method="POST" id="taskForm">
        <?= csrfInput() ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">Grundinformation</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Uppgiftens Namn</label>
                            <input type="text" name="t_name" class="form-control" required placeholder="T.ex. Nalle går till butiken">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Typ av uppgift</label>
                                <select name="t_type" class="form-select" required>
                                    <?php foreach ($types as $t): ?>
                                        <option value="<?= $t['tt_id'] ?>"><?= $t['tt_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Svårighetsgrad</label>
                                <select name="t_level" class="form-select" required>
                                    <?php foreach ($levels as $l): ?>
                                        <option value="<?= $l['tl_id'] ?>"><?= $l['tl_name'] ?> (Nivå <?= $l['tl_level'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Poäng (XP)</label>
                                <input type="number" name="t_xp" class="form-control" value="10" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Instruktioner / Text</label>
                            <textarea name="t_text" class="form-control" rows="3" placeholder="Övningens text..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Frågor (Flerval)</span>
                        <button type="button" class="btn btn-sm btn-light" onclick="addQuestionField()">+ Lägg till fråga</button>
                    </div>
                    <div class="card-body" id="questions-container">
                        </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" name="create-task" class="btn btn-success btn-lg">Spara Uppgift</button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="alert alert-info">
                    <h5><i class="bi bi-info-circle"></i> Tips</h5>
                    <p>Skriv frågan överst. Ange sedan <strong>Rätt svar</strong> i det gröna fältet och tre felaktiga svar i de röda fälten.</p>
                    <p>Systemet kommer automatiskt att blanda svarsalternativen för eleven.</p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let questionCount = 0;

    function addQuestionField() {
        questionCount++;
        const container = document.getElementById('questions-container');
        
        const html = `
        <div class="border p-3 mb-3 rounded bg-light position-relative" id="q-row-${questionCount}">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
            
            <div class="mb-2">
                <label class="form-label fw-bold">Fråga ${questionCount}</label>
                <input type="text" name="questions[${questionCount}][question]" class="form-control" required placeholder="Vad heter...?">
            </div>
            
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="questions[${questionCount}][correct]" class="form-control border-success" required placeholder="Rätt svar">
                </div>
                <div class="col-md-6">
                    <input type="text" name="questions[${questionCount}][wrong1]" class="form-control border-danger" required placeholder="Fel svar 1">
                </div>
                <div class="col-md-6">
                    <input type="text" name="questions[${questionCount}][wrong2]" class="form-control border-danger" placeholder="Fel svar 2 (Valfritt)">
                </div>
                <div class="col-md-6">
                    <input type="text" name="questions[${questionCount}][wrong3]" class="form-control border-danger" placeholder="Fel svar 3 (Valfritt)">
                </div>
            </div>
        </div>
        `;
        
        container.insertAdjacentHTML('beforeend', html);
    }

    // Lägg till en första fråga direkt när sidan laddas
    window.onload = addQuestionField;
</script>

<?php require_once "include/footer.php"; ?>
