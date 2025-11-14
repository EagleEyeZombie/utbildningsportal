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
$allClasses = $task_obj->getAllClasses(); // <-- NY
$errorMsg = "";
$successMsg = "";

// Hantera formulär-inlämning
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create-task'])) {
    
    // CSRF Check
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        die("Ogiltig CSRF-token.");
    }

    $tName = cleanInput($_POST['t_name']);
    $tType = cleanInput($_POST['t_type']); // Detta är IDt (t.ex. 1, 2, 3)
    $tLevel = cleanInput($_POST['t_level']);
    $tText = cleanInput($_POST['t_text']); 
    $teacherId = $_SESSION['user_id'];
    $tXp = cleanInput($_POST['t_xp']);
    $tClass = cleanInput($_POST['t_class']); // <-- NY: Hämta klass-ID

    // Konvertera tom sträng till NULL för databasen (om man valt "Ingen specifik klass")
    $tClass = empty($tClass) ? null : $tClass; // <-- NY

    $questionsData = [];
    
    // Hämta namnet på typen vi valde (för att veta vilken array vi ska läsa)
    $typeNameQuery = $pdo->prepare("SELECT tt_name FROM task_types WHERE tt_id = ?");
    $typeNameQuery->execute([$tType]);
    $taskTypeName = $typeNameQuery->fetchColumn();
    
    // Packa JSON baserat på uppgiftstyp
    if (strpos(strtolower($taskTypeName), 'flerval') !== false) {
        if (isset($_POST['questions_mc'])) {
            foreach ($_POST['questions_mc'] as $q) {
                if (!empty($q['question'])) {
                    $questionsData[] = ['q' => cleanInput($q['question']), 'a' => cleanInput($q['correct']), 'w1' => cleanInput($q['wrong1']), 'w2' => cleanInput($q['wrong2']), 'w3' => cleanInput($q['wrong3'])];
                }
            }
        }
    } 
    elseif (strpos(strtolower($taskTypeName), 'sant/falskt') !== false) {
        if (isset($_POST['questions_tf'])) {
            foreach ($_POST['questions_tf'] as $q) {
                if (!empty($q['question'])) {
                    $questionsData[] = ['q' => cleanInput($q['question']), 'a' => cleanInput($q['correct'])];
                }
            }
        }
    }

    $jsonQuestions = json_encode($questionsData, JSON_UNESCAPED_UNICODE);

    // Spara via klassen - LADE TILL $tClass
    $result = $task_obj->createTask($tName, $tType, $tLevel, $teacherId, $tClass, $tText, $jsonQuestions, $tXp);

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
                            <input type="text" name="t_name" class="form-control" required placeholder="T.ex. Verb och Substantiv">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Typ av uppgift</label>
                                <select name="t_type" id="taskTypeDropdown" class="form-select" required>
                                    <?php foreach ($types as $t): ?>
                                        <option value="<?= $t['tt_id'] ?>"><?= $t['tt_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Svårighetsgrad</label>
                                <select name="t_level" class="form-select" required>
                                    <?php foreach ($levels as $l): ?>
                                        <option value="<?= $l['tl_id'] ?>"><?= $l['tl_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Klass (Valfri)</label>
                                <select name="t_class" class="form-select">
                                    <option value="">Ingen specifik klass</option>
                                    <?php foreach ($allClasses as $class): ?>
                                        <option value="<?= $class['c_id'] ?>"><?= htmlspecialchars($class['c_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Poäng (XP)</label>
                                <input type="number" name="t_xp" class="form-control" value="10" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Instruktioner / Text</label>
                            <textarea name="t_text" class="form-control" rows="3" placeholder="Förklaring till eleven..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm task-form-section" id="form-flerval">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Frågor (Flerval)</span>
                        <button type="button" class="btn btn-sm btn-light" onclick="addQuestionField()">+ Lägg till fråga</button>
                    </div>
                    <div class="card-body" id="questions-container">
                        </div>
                </div>

                <div class="card shadow-sm task-form-section d-none" id="form-sant-falskt">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Frågor (Sant/Falskt)</span>
                        <button type="button" class="btn btn-sm btn-light" onclick="addTrueFalseField()">+ Lägg till påstående</button>
                    </div>
                    <div class="card-body" id="tf-questions-container">
                        </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" name="create-task" class="btn btn-success btn-lg">Spara Uppgift</button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="alert alert-info">
                    <h5><i class="bi bi-info-circle"></i> Tips</h5>
                    <p>Välj "Ingen specifik klass" om uppgiften ska vara tillgänglig för alla elever, oavsett klass.</p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // --- Logik för Flervalsfrågor ---
    let questionCount = 0;
    function addQuestionField() {
        questionCount++;
        const container = document.getElementById('questions-container');
        const html = `
        <div class="border p-3 mb-3 rounded bg-light position-relative" id="q-row-${questionCount}">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
            <div class="mb-2">
                <label class="form-label fw-bold">Fråga ${questionCount}</label>
                <input type="text" name="questions_mc[${questionCount}][question]" class="form-control" required placeholder="Vad heter...?">
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="questions_mc[${questionCount}][correct]" class="form-control border-success" required placeholder="Rätt svar">
                </div>
                <div class="col-md-6">
                    <input type="text" name="questions_mc[${questionCount}][wrong1]" class="form-control border-danger" required placeholder="Fel svar 1">
                </div>
                <div class="col-md-6">
                    <input type="text" name="questions_mc[${questionCount}][wrong2]" class="form-control border-danger" placeholder="Fel svar 2 (Valfritt)">
                </div>
                <div class="col-md-6">
                    <input type="text" name="questions_mc[${questionCount}][wrong3]" class="form-control border-danger" placeholder="Fel svar 3 (Valfritt)">
                </div>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

    // --- Logik för Sant/Falskt ---
    let tfQuestionCount = 0;
    function addTrueFalseField() {
        tfQuestionCount++;
        const container = document.getElementById('tf-questions-container');
        const html = `
        <div class="border p-3 mb-3 rounded bg-light position-relative" id="tf-q-row-${tfQuestionCount}">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
            <div class="mb-2">
                <label class="form-label fw-bold">Påstående ${tfQuestionCount}</label>
                <input type="text" name="questions_tf[${tfQuestionCount}][question]" class="form-control" required placeholder="Påstående (t.ex. Himlen är blå)">
            </div>
            <div class="mb-2">
                <label class="form-label">Rätt svar</label>
                <select name="questions_tf[${tfQuestionCount}][correct]" class="form-select">
                    <option value="Sant">Sant</option>
                    <option value="Falskt">Falskt</option>
                </select>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

    // --- Logik för att byta formulär ---
    const dropdown = document.getElementById('taskTypeDropdown');
    const forms = document.querySelectorAll('.task-form-section');
    
    dropdown.addEventListener('change', function() {
        const selectedText = this.options[this.selectedIndex].text.toLowerCase();
        forms.forEach(form => form.classList.add('d-none'));

        if (selectedText.includes('flerval')) {
            document.getElementById('form-flerval').classList.remove('d-none');
        } else if (selectedText.includes('sant/falskt')) {
            document.getElementById('form-sant-falskt').classList.remove('d-none');
        }
    });

    // Initiera formulär på sidladdning
    function initForms() {
        const selectedText = dropdown.options[dropdown.selectedIndex].text.toLowerCase();
        if (selectedText.includes('flerval')) {
            document.getElementById('form-flerval').classList.remove('d-none');
            addQuestionField(); 
        } else if (selectedText.includes('sant/falskt')) {
            document.getElementById('form-sant-falskt').classList.remove('d-none');
            addTrueFalseField(); 
        }
    }
    
    window.onload = initForms;
</script>

<?php require_once "include/footer.php"; ?>
