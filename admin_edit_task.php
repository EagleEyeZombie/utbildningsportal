<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    header("Location: login.php");
    exit;
}

// 1. HÄMTA ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_tasks.php");
    exit;
}
$taskId = $_GET['id'];

// 2. HÄMTA DATA
$task = $task_obj->getTaskById($taskId);
if (!$task) { die("Hittade ingen uppgift."); }
$questions = json_decode($task['t_questions'], true);
$taskTypeName = strtolower($task['type_name']);

// 3. HÄMTA LISTOR
$types = $task_obj->getAllTypes();
$levels = $task_obj->getAllLevels();
$allClasses = $task_obj->getAllClasses();
$allGenres = $task_obj->getAllGenres(); // <-- NY
$errorMsg = "";
$successMsg = "";

// 4. HANTERA SPARA
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update-task'])) {
    
    if (!verifyCsrfToken($_POST['csrf_token'])) { die("Ogiltig CSRF-token."); }

    $tName = cleanInput($_POST['t_name']);
    $tType = cleanInput($_POST['t_type']); 
    $tLevel = cleanInput($_POST['t_level']);
    $tText = cleanInput($_POST['t_text']); 
    $tXp = cleanInput($_POST['t_xp']);
    $tClass = cleanInput($_POST['t_class']); 
    $tClass = empty($tClass) ? null : $tClass;

    $tGenre = cleanInput($_POST['t_genre']); // <-- NY
    $tGenre = empty($tGenre) ? null : $tGenre; // <-- NY

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
    elseif (strpos($postedTaskTypeName, 'sortering') !== false) {
        if (isset($_POST['questions_sort'][0]['sentences'])) {
            $sentences = trim($_POST['questions_sort'][0]['sentences']);
            $sentencesArray = preg_split('/(\r\n|\r|\n)/', $sentences, -1, PREG_SPLIT_NO_EMPTY);
            $cleanedArray = array_map('cleanInput', $sentencesArray);
            $questionsData = ['s' => $cleanedArray];
        }
    }

    $jsonQuestions = json_encode($questionsData, JSON_UNESCAPED_UNICODE);

    // LADE TILL $tGenre
    $result = $task_obj->updateTask($taskId, $tName, $tType, $tLevel, $tClass, $tGenre, $tText, $jsonQuestions, $tXp);

    if ($result['success']) {
        $successMsg = "Uppgiften har uppdaterats! <a href='admin_tasks.php'>Tillbaka till listan</a>";
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
    
    <?php if ($errorMsg): ?> <div class="alert alert-danger"><?= $errorMsg ?></div> <?php endif; ?>
    <?php if ($successMsg): ?> <div class="alert alert-success"><?= $successMsg ?></div> <?php endif; ?>

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
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Typ av uppgift</label>
                                <select name="t_type" id="taskTypeDropdown" class="form-select" required>
                                    <?php foreach ($types as $t): ?>
                                        <option value="<?= $t['tt_id'] ?>" <?php echo ($t['tt_id'] == $task['t_type_fk']) ? 'selected' : ''; ?>>
                                            <?= $t['tt_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Genre</label>
                                <select name="t_genre" class="form-select" required>
                                    <?php foreach ($allGenres as $g): ?>
                                        <option value="<?= $g['g_id'] ?>" <?php echo ($g['g_id'] == $task['t_genre_fk']) ? 'selected' : ''; ?>>
                                            <?= $g['g_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                         <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Svårighetsgrad</label>
                                <select name="t_level" class="form-select" required>
                                    <?php foreach ($levels as $l): ?>
                                        <option value="<?= $l['tl_id'] ?>" <?php echo ($l['tl_id'] == $task['t_level_fk']) ? 'selected' : ''; ?>>
                                            <?= $l['tl_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
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
                            <div class="col-md-4 mb-3">
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

                <!-- FORMULÄR-DELAR (SAMMA SOM FÖRUT) -->
                <div class="card shadow-sm task-form-section" id="form-flerval">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Frågor (Flerval)</span>
                        <button type="button" class="btn btn-sm btn-light" onclick="addQuestionField()">+ Lägg till fråga</button>
                    </div>
                    <div class="card-body" id="questions-container"></div>
                </div>

                <div class="card shadow-sm task-form-section d-none" id="form-sant-falskt">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Frågor (Sant/Falskt)</span>
                        <button type="button" class="btn btn-sm btn-light" onclick="addTrueFalseField()">+ Lägg till påstående</button>
                    </div>
                    <div class="card-body" id="tf-questions-container"></div>
                </div>

                <div class="card shadow-sm task-form-section d-none" id="form-sortering">
                    <div class="card-header bg-secondary text-white"><span>Frågor (Sortering)</span></div>
                    <div class="card-body" id="sorting-questions-container">
                        <div class="alert alert-info">Skriv meningarna i **rätt ordning**, en mening per rad.</div>
                        <div class="mb-2">
                            <label class="form-label fw-bold">Sorterbara meningar</label>
                            <textarea name="questions_sort[0][sentences]" class="form-control" rows="8"></textarea>
                        </div>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" name="update-task" class="btn btn-success btn-lg">Spara ändringar</button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="alert alert-info">
                    <h5><i class="bi bi-info-circle"></i> Redigeringsläge</h5>
                    <p>Du redigerar nu en befintlig uppgift.</p>
                </div>
                <div class="d-grid gap-2">
                    <a href="admin_tasks.php" class="btn btn-outline-secondary">&laquo; Avbryt</a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- JAVASCRIPT (SAMMA SOM CREATE, PLUS FYLLA I DATA) -->
<script>
    const existingQuestions = <?php echo json_encode($questions); ?>;
    const taskType = "<?php echo $taskTypeName; ?>";

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
                <div class="col-md-6"><input type="text" name="questions_mc[${questionCount}][correct]" class="form-control border-success" required value="${a}" placeholder="Rätt svar"></div>
                <div class="col-md-6"><input type="text" name="questions_mc[${questionCount}][wrong1]" class="form-control border-danger" required value="${w1}" placeholder="Fel svar 1"></div>
                <div class="col-md-6"><input type="text" name="questions_mc[${questionCount}][wrong2]" class="form-control border-danger" value="${w2}" placeholder="Fel svar 2"></div>
                <div class="col-md-6"><input type="text" name="questions_mc[${questionCount}][wrong3]" class="form-control border-danger" value="${w3}" placeholder="Fel svar 3"></div>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

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
                <input type="text" name="questions_tf[${tfQuestionCount}][question]" class="form-control" required value="${q}">
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

    function addSortingField(data = null) {
        const container = document.getElementById('sorting-questions-container');
        const sentences = (data && data.s) ? data.s.join('\n') : '';
        const textarea = container.querySelector('textarea[name="questions_sort[0][sentences]"]');
        textarea.value = sentences;
    }

    const dropdown = document.getElementById('taskTypeDropdown');
    const forms = document.querySelectorAll('.task-form-section');
    
    function updateForms() {
        const selectedText = dropdown.options[dropdown.selectedIndex].text.toLowerCase();
        forms.forEach(form => {
            form.classList.add('d-none');
            form.querySelectorAll('input, textarea, select').forEach(input => {
                input.disabled = true;
                input.required = false; 
            });
        });
        let activeFormId = null;
        if (selectedText.includes('flerval')) { activeFormId = 'form-flerval'; } 
        else if (selectedText.includes('sant/falskt')) { activeFormId = 'form-sant-falskt'; } 
        else if (selectedText.includes('sortering')) { activeFormId = 'form-sortering'; }

        if (activeFormId) {
            const activeForm = document.getElementById(activeFormId);
            activeForm.classList.remove('d-none');
            activeForm.querySelectorAll('input, textarea, select').forEach(input => {
                input.disabled = false;
                const name = input.name;
                if (name.includes('[question]') || name.includes('[correct]') || name.includes('[wrong1]') || name.includes('[sentences]')) {
                    input.required = true;
                }
            });
        }
    }
    
    dropdown.addEventListener('change', updateForms);

    window.onload = function() {
        updateForms(); 
        if (taskType.includes('flerval')) {
            if (existingQuestions && existingQuestions.length > 0) { existingQuestions.forEach(q => addQuestionField(q)); } else { addQuestionField(); }
        } else if (taskType.includes('sant/falskt')) {
            if (existingQuestions && existingQuestions.length > 0) { existingQuestions.forEach(q => addTrueFalseField(q)); } else { addTrueFalseField(); }
        } else if (taskType.includes('sortering')) {
            addSortingField(existingQuestions);
        }
    };
</script>

<?php require_once "include/footer.php"; ?>
