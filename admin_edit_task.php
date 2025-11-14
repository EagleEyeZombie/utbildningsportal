<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    header("Location: login.php");
    exit;
}

// 1. HÄMTA UPPGIFTENS ID FRÅN URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_tasks.php");
    exit;
}
$taskId = $_GET['id'];

// 2. HÄMTA UPPGIFTENS DATA FRÅN DATABASEN
$task = $task_obj->getTaskById($taskId);
if (!$task) {
    die("Hittade ingen uppgift med detta ID.");
}
$questions = json_decode($task['t_questions'], true);
$taskTypeName = strtolower($task['type_name']);

// 3. HÄMTA LISTOR FÖR DROPDOWNS
$types = $task_obj->getAllTypes();
$levels = $task_obj->getAllLevels();
$allClasses = $task_obj->getAllClasses(); // <-- NY
$errorMsg = "";
$successMsg = "";

// 4. HANTERA FORMULÄR-INLÄMNING (NÄR MAN SPARAR ÄNDRINGAR)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update-task'])) {
    
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        die("Ogiltig CSRF-token.");
    }

    $tName = cleanInput($_POST['t_name']);
    $tType = cleanInput($_POST['t_type']); 
    $tLevel = cleanInput($_POST['t_level']);
    $tText = cleanInput($_POST['t_text']); 
    $tXp = cleanInput($_POST['t_xp']);
    $tClass = cleanInput($_POST['t_class']); // <-- NY

    $tClass = empty($tClass) ? null : $tClass; // <-- NY

    $questionsData = [];
    
    $typeNameQuery = $pdo->prepare("SELECT tt_name FROM task_types WHERE tt_id = ?");
    $typeNameQuery->execute([$tType]);
    $postedTaskTypeName = strtolower($typeNameQuery->fetchColumn());

    if (strpos($postedTaskTypeName, 'flerval') !== false) {
        if (isset($_POST['questions_mc'])) {
            foreach ($_POST['questions_mc'] as $q) {
                if (!empty($q['question'])) {
                    $questionsData[] = ['q' => cleanInput($q['question']), 'a' => cleanInput($q['correct']), 'w1' => cleanInput($q['wrong1']), 'w2' => cleanInput($q['wrong2']), 'w3' => cleanInput($q['wrong3'])];
                }
            }
        }
    } 
    elseif (strpos($postedTaskTypeName, 'sant/falskt') !== false) {
        if (isset($_POST['questions_tf'])) {
            foreach ($_POST['questions_tf'] as $q) {
                if (!empty($q['question'])) {
                    $questionsData[] = ['q' => cleanInput($q['question']), 'a' => cleanInput($q['correct'])];
                }
            }
        }
    }

    $jsonQuestions = json_encode($questionsData, JSON_UNESCAPED_UNICODE);

    // Använd den uppdaterade updateTask-metoden - LADE TILL $tClass
    $result = $task_obj->updateTask($taskId, $tName, $tType, $tLevel, $tClass, $tText, $jsonQuestions, $tXp);

    if ($result['success']) {
        $successMsg = "Uppgiften har uppdaterats! <a href='admin_tasks.php'>Tillbaka till listan</a>";
        // Ladda om datan så att formuläret visar de nya ändringarna
        $task = $task_obj->getTaskById($taskId);
        $questions = json_decode($task['t_questions'], true);
        $taskTypeName = strtolower($task['type_name']);
    } else {
        $errorMsg = $result['error'];
    }
}
?>

<div class="container mt-5 mb-5">
    <h1>Redigera uppgift: <?php echo htmlspecialchars($task['t_name']); ?></h1>
    
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
                            <input type="text" name="t_name" class="form-control" required value="<?= htmlspecialchars($task['t_name']) ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Typ av uppgift</label>
                                <select name="t_type" id="taskTypeDropdown" class="form-select" required>
                                    <?php foreach ($types as $t): ?>
                                        <option value="<?= $t['tt_id'] ?>" <?php echo ($t['tt_id'] == $task['t_type_fk']) ? 'selected' : ''; ?>>
                                            <?= $t['tt_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Svårighetsgrad</label>
                                <select name="t_level" class="form-select" required>
                                    <?php foreach ($levels as $l): ?>
                                        <option value="<?= $l['tl_id'] ?>" <?php echo ($l['tl_id'] == $task['t_level_fk']) ? 'selected' : ''; ?>>
                                            <?= $l['tl_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Klass (Valfri)</label>
                                <select name="t_class" class="form-select">
                                    <option value="">Ingen specifik klass</option>
                                    <?php foreach ($allClasses as $class): ?>
                                        <option value="<?= $class['c_id'] ?>" <?php echo ($class['c_id'] == $task['t_class_fk']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($class['c_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Poäng (XP)</label>
                                <input type="number" name="t_xp" class="form-control" value="<?= htmlspecialchars($task['t_xp']) ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Instruktioner / Text</label>
                            <textarea name="t_text" class="form-control" rows="3"><?= htmlspecialchars($task['t_text']) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm task-form-section <?php echo (strpos($taskTypeName, 'flerval') === false) ? 'd-none' : ''; ?>" id="form-flerval">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Frågor (Flerval)</span>
                        <button type="button" class="btn btn-sm btn-light" onclick="addQuestionField()">+ Lägg till fråga</button>
                    </div>
                    <div class="card-body" id="questions-container">
                        </div>
                </div>

                <div class="card shadow-sm task-form-section <?php echo (strpos($taskTypeName, 'sant/falskt') === false) ? 'd-none' : ''; ?>" id="form-sant-falskt">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Frågor (Sant/Falskt)</span>
                        <button type="button" class="btn btn-sm btn-light" onclick="addTrueFalseField()">+ Lägg till påstående</button>
                    </div>
                    <div class="card-body" id="tf-questions-container">
                        </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" name="update-task" class="btn btn-success btn-lg">Spara ändringar</button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="alert alert-info">
                    <h5><i class="bi bi-info-circle"></i> Redigeringsläge</h5>
                    <p>Du redigerar nu en befintlig uppgift. Ändringar du sparar kommer att slå igenom omedelbart.</p>
                </div>
                <div class="d-grid gap-2">
                    <a href="admin_tasks.php" class="btn btn-outline-secondary">&laquo; Avbryt och gå tillbaka</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // 1. DATA FRÅN PHP
    const existingQuestions = <?php echo json_encode($questions); ?>;
    const taskType = "<?php echo $taskTypeName; ?>";

    // 2. LOGIK FÖR FLERVALSFRÅGOR
    let questionCount = 0;
    function addQuestionField(data = null) {
        questionCount++;
        const container = document.getElementById('questions-container');
        
        const q = data ? data.q : '';
        const a = data ? data.a : '';
        const w1 = data ? data.w1 : '';
        const w2 = data ? (data.w2 || '') : '';
        const w3 = data ? (data.w3 || '') : '';
        
        const html = `
        <div class="border p-3 mb-3 rounded bg-light position-relative" id="q-row-${questionCount}">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
            <div class="mb-2">
                <label class="form-label fw-bold">Fråga ${questionCount}</label>
                <input type="text" name="questions_mc[${questionCount}][question]" class="form-control" required value="${q}">
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="questions_mc[${questionCount}][correct]" class="form-control border-success" required value="${a}" placeholder="Rätt svar">
                </div>
                <div class="col-md-6">
                    <input type="text" name="questions_mc[${questionCount}][wrong1]" class="form-control border-danger" required value="${w1}" placeholder="Fel svar 1">
                </div>
                <div class="col-md-6">
                    <input type="text" name="questions_mc[${questionCount}][wrong2]" class="form-control border-danger" value="${w2}" placeholder="Fel svar 2 (Valfritt)">
                </div>
                <div class="col-md-6">
                    <input type="text" name="questions_mc[${questionCount}][wrong3]" class="form-control border-danger" value="${w3}" placeholder="Fel svar 3 (Valfritt)">
                </div>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

    // 3. LOGIK FÖR SANT/FALSKT
    let tfQuestionCount = 0;
    function addTrueFalseField(data = null) {
        tfQuestionCount++;
        const container = document.getElementById('tf-questions-container');
        
        const q = data ? data.q : '';
        const a = data ? data.a : 'Sant'; 
        
        const html = `
        <div class="border p-3 mb-3 rounded bg-light position-relative" id="tf-q-row-${tfQuestionCount}">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
            <div class="mb-2">
                <label class="form-label fw-bold">Påstående ${tfQuestionCount}</label>
                <input type="text" name="questions_tf[${tfQuestionCount}][question]" class="form-control" required value="${q}" placeholder="Påstående (t.ex. Himlen är blå)">
            </div>
            <div class="mb-2">
                <label class="form-label">Rätt svar</label>
                <select name="questions_tf[${tfQuestionCount}][correct]" class="form-select">
                    <option value="Sant" ${a === 'Sant' ? 'selected' : ''}>Sant</option>
                    <option value="Falskt" ${a === 'Falskt' ? 'selected' : ''}>Falskt</option>
                </select>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

    // 4. LOGIK FÖR ATT BYTA FORMULÄR
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

    // 5. INITIERING (LADDA BEFINTLIGA FRÅGOR)
    window.onload = function() {
        if (taskType.includes('flerval')) {
            if (existingQuestions && existingQuestions.length > 0) {
                existingQuestions.forEach(q => addQuestionField(q));
            } else {
                addQuestionField(); 
            }
        } else if (taskType.includes('sant/falskt')) {
            if (existingQuestions && existingQuestions.length > 0) {
                existingQuestions.forEach(q => addTrueFalseField(q));
            } else {
                addTrueFalseField();
            }
        }
    };
</script>

<?php require_once "include/footer.php"; ?>
