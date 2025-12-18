<?php
require_once "include/header.php";

// ---------------------------------------------------------
// SÄKERHETSVAKT (RBAC) - FLÖDE C
// ---------------------------------------------------------
// 1. Inloggningskoll.
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    header("Location: login.php");
    exit;
}

// ---------------------------------------------------------
// 1. VALIDERING & HÄMTNING (READ/PRE-FILL)
// ---------------------------------------------------------
// Vi måste veta vilken uppgift som ska redigeras.
// ID:t kommer via URL:en (GET), t.ex. admin_edit_task.php?id=5

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Om ID saknas eller är ogiltigt, skicka tillbaka till listan.
    header("Location: admin_tasks.php");
    exit;
}
$taskId = $_GET['id'];

// 2. HÄMTA DATA FRÅN DATABASEN (MODELL)
// Vi hämtar uppgiftsobjektet.
$task = $task_obj->getTaskById($taskId);
if (!$task) { die("Hittade ingen uppgift."); }

// VIKTIGT: Avkoda JSON-frågorna
// I databasen ligger frågorna som en JSON-sträng.
// Vi gör om det till en PHP-array så vi kan loopa ut dem i formuläret.
$questions = json_decode($task['t_questions'], true);
$taskTypeName = strtolower($task['type_name']);

// 3. HÄMTA LISTOR (För dropdowns)
// Vi behöver data för att fylla select-listorna (Typ, Nivå, Klass, Genre, Lärare).
$types = $task_obj->getAllTypes();
$levels = $task_obj->getAllLevels();
$allClasses = $task_obj->getAllClasses();
$allGenres = $task_obj->getAllGenres();

// Hämta alla lärare (RBAC: Endast användare med nivå 5+)
$stmt = $pdo->query("SELECT users.u_id, users.u_name 
                     FROM users 
                     JOIN roles ON users.u_role_fk = roles.r_id 
                     WHERE roles.r_level >= 5 
                     ORDER BY users.u_name ASC");
$allTeachers = $stmt->fetchAll();

$errorMsg = "";
$successMsg = "";

// ---------------------------------------------------------
// 4. HANTERA SPARA (UPDATE - CONTROLLER)
// ---------------------------------------------------------
// Detta körs när formuläret skickas (POST).

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update-task'])) {
    
    // --- SÄKERHET: CSRF ---
    // Skyddar mot att formuläret kapas från en annan sida.
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!verifyCsrfToken($token)) { die("Ogiltig CSRF-token."); }

    // --- SANITERING (XSS-SKYDD) ---
    // Vi tvättar all indata.
    $tName = cleanInput($_POST['t_name']);
    $tType = cleanInput($_POST['t_type']); 
    $tLevel = cleanInput($_POST['t_level']);
    $tText = cleanInput($_POST['t_text']);
    $tXp = cleanInput($_POST['t_xp']);
    
    // Hantera valfria fält (kan vara null i databasen)
    $tClass = !empty($_POST['t_class']) ? cleanInput($_POST['t_class']) : null;
    $tGenre = !empty($_POST['t_genre']) ? cleanInput($_POST['t_genre']) : null;
    
    // Om ingen lärare valts, behåll den gamla ägaren.
    $tTeacher = !empty($_POST['t_teacher']) ? cleanInput($_POST['t_teacher']) : $task['t_teacher_fk'];

    // --- STRUKTURERA DATA (JSON-BYGGE) ---
    // Beroende på vilken uppgiftstyp det är, måste vi bygga olika datastrukturer
    // för att spara frågorna korrekt i JSON-formatet.
    
    $questionsData = [];

    // Fall 1: Flerval (Multiple Choice)
    if (strpos($taskTypeName, 'flerval') !== false) {
        if (isset($_POST['questions_mc'])) {
            foreach ($_POST['questions_mc'] as $q) {
                // Vi sparar: Fråga (q), Rätt svar (a), Fel svar 1-3 (w1, w2, w3)
                if (!empty($q['question'])) {
                    $questionsData[] = ['q' => cleanInput($q['question']), 'a' => cleanInput($q['correct']), 'w1' => cleanInput($q['wrong1']), 'w2' => cleanInput($q['wrong2']), 'w3' => cleanInput($q['wrong3'])];
                }
            }
        }
    } 
    // Fall 2: Sant/Falskt
    elseif (strpos($taskTypeName, 'sant/falskt') !== false) {
        if (isset($_POST['questions_tf'])) {
            foreach ($_POST['questions_tf'] as $q) {
                if (!empty($q['question'])) {
                    $questionsData[] = ['q' => cleanInput($q['question']), 'a' => cleanInput($q['correct'])];
                }
            }
        }
    }
    // Fall 3: Sortering (Lista)
    elseif (strpos($taskTypeName, 'sortering') !== false) {
        if (isset($_POST['questions_sort'][0]['sentences'])) {
            // Vi delar upp textrutan rad för rad till en array
            $sentences = trim($_POST['questions_sort'][0]['sentences']);
            $sentencesArray = preg_split('/(\r\n|\r|\n)/', $sentences, -1, PREG_SPLIT_NO_EMPTY);
            $cleanedArray = array_map('cleanInput', $sentencesArray);
            $questionsData = ['s' => $cleanedArray];
        }
    }
    // Fall 4: Para ihop (Memory)
    elseif (strpos($taskTypeName, 'para ihop') !== false) {
        if (isset($_POST['questions_pair'])) {
            foreach ($_POST['questions_pair'] as $p) {
                if (!empty($p['term']) && !empty($p['def'])) {
                    $questionsData[] = ['term' => cleanInput($p['term']), 'def' => cleanInput($p['def'])];
                }
            }
        }
    }
    // Fall 5: Textluckor (Cloze)
    elseif (strpos($taskTypeName, 'textluckor') !== false) {
        $gaps = [];
        if (isset($_POST['questions_gaps'])) {
            foreach ($_POST['questions_gaps'] as $g) {
                if (!empty($g['sentence']) && !empty($g['word'])) {
                    $gaps[] = ['sentence' => cleanInput($g['sentence']), 'word' => cleanInput($g['word'])];
                }
            }
        }
        // Hantera "falska ord" (distraktorer)
        $distractors = [];
        if (!empty($_POST['gap_distractors'])) {
            $rawDistractors = explode(',', $_POST['gap_distractors']);
            foreach($rawDistractors as $d) { $d = trim($d); if(!empty($d)) $distractors[] = cleanInput($d); }
        }
        $questionsData = ['gaps' => $gaps, 'distractors' => $distractors];
    }

    // KODA TILL JSON
    // Nu gör vi om PHP-arrayen till en JSON-sträng för lagring i DB.
    // JSON_UNESCAPED_UNICODE ser till att ÅÄÖ fungerar korrekt.
    $jsonQuestions = json_encode($questionsData, JSON_UNESCAPED_UNICODE);

    // ANROPA MODELLEN (UPDATE)
    // updateTask() kör SQL UPDATE-kommandot.
    $result = $task_obj->updateTask($taskId, $tName, $tType, $tLevel, $tClass, $tGenre, $tText, $jsonQuestions, $tXp, $tTeacher);

    if ($result['success']) {
        $successMsg = "Uppgiften uppdaterad!";
        // Uppdatera variablerna så formuläret visar den nya datan direkt
        $task = $task_obj->getTaskById($taskId);
        $questions = json_decode($task['t_questions'], true);
    } else {
        $errorMsg = $result['error'];
    }
}
?>

<div class="container mt-5 mb-5">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3 text-center text-md-start">
        <h1 class="m-0">Redigera Uppgift: <?= htmlspecialchars($task['t_name']) ?></h1>
        
        <a href="admin_tasks.php" class="btn btn-outline-dark fw-bold">
            <i class="bi bi-arrow-left"></i> Tillbaka
        </a>
    </div>
    
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
                            <label for="t_name" class="form-label">Uppgiftens Namn</label>
                            <input type="text" name="t_name" id="t_name" class="form-control" value="<?= htmlspecialchars($task['t_name']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="t_teacher" class="form-label">Ägare / Lärare</label>
                            <select name="t_teacher" id="t_teacher" class="form-select">
                                <?php foreach ($allTeachers as $teacher): ?>
                                    <option value="<?= $teacher['u_id'] ?>" <?= ($teacher['u_id'] == $task['t_teacher_fk']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($teacher['u_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php if (!$task['t_teacher_fk']): ?>
                                    <option value="" selected style="color: red;">(Ingen ägare - Välj ny!)</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="taskTypeDropdown" class="form-label">Typ av uppgift</label>
                                <select name="t_type" id="taskTypeDropdown" class="form-select" required>
                                    <?php foreach ($types as $t): ?>
                                        <option value="<?= $t['tt_id'] ?>" <?= ($t['tt_id'] == $task['t_type_fk']) ? 'selected' : '' ?>><?= $t['tt_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="t_genre" class="form-label">Genre (Tema)</label>
                                <select name="t_genre" id="t_genre" class="form-select" required>
                                    <?php foreach ($allGenres as $g): ?>
                                        <option value="<?= $g['g_id'] ?>" <?= ($g['g_id'] == $task['t_genre_fk']) ? 'selected' : '' ?>><?= $g['g_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="t_level" class="form-label">Svårighetsgrad</label>
                                <select name="t_level" id="t_level" class="form-select" required>
                                    <?php foreach ($levels as $l): ?>
                                        <option value="<?= $l['tl_id'] ?>" <?= ($l['tl_id'] == $task['t_level_fk']) ? 'selected' : '' ?>><?= $l['tl_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="t_class" class="form-label">Klass (Valfri)</label>
                                <select name="t_class" id="t_class" class="form-select">
                                    <option value="">Ingen specifik klass</option>
                                    <?php foreach ($allClasses as $class): ?>
                                        <option value="<?= $class['c_id'] ?>" <?= ($class['c_id'] == $task['t_class_fk']) ? 'selected' : '' ?>><?= htmlspecialchars($class['c_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="t_xp" class="form-label">Poäng (XP)</label>
                                <input type="number" name="t_xp" id="t_xp" class="form-control" value="<?= $task['t_xp'] ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="t_text" class="form-label">Instruktioner / Text</label>
                            <textarea name="t_text" id="t_text" class="form-control" rows="3"><?= htmlspecialchars($task['t_text']) ?></textarea>
                        </div>
                    </div>
                </div>

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
                    <div class="card-body" id="sorting-questions-container"></div>
                </div>

                <div class="card shadow-sm task-form-section d-none" id="form-paraihop">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Para ihop</span>
                        <button type="button" class="btn btn-sm btn-light" onclick="addPairField()">+ Lägg till par</button>
                    </div>
                    <div class="card-body" id="pair-questions-container"></div>
                </div>

                <div class="card shadow-sm task-form-section d-none" id="form-textluckor">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Textluckor</span>
                        <button type="button" class="btn btn-sm btn-light" onclick="addGapField()">+ Lägg till mening</button>
                    </div>
                    <div class="card-body">
                        <div id="gaps-container"></div>
                        <div class="mt-4">
                            <label for="gap_distractors" class="form-label fw-bold">Falska ord</label>
                            <input type="text" name="gap_distractors" id="gap_distractors" class="form-control" 
                                   value="<?= isset($questions['distractors']) ? implode(', ', $questions['distractors']) : '' ?>">
                        </div>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" name="update-task" class="btn btn-primary btn-lg">Spara Ändringar</button>
                </div>
            </div>
            
             <div class="col-md-4">
                <div class="alert alert-info">
                    <h5><i class="bi bi-info-circle"></i> Redigeringsläge</h5>
                    <p>Du ändrar nu i en befintlig uppgift. Var noga med att inte byta typ om du inte vill skriva om alla frågor.</p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Vi hämtar PHP-variabler till JS för att kunna förifylla formulären
    const taskType = "<?= $taskTypeName ?>";
    const existingQuestions = <?= json_encode($questions) ?>;

    // Funktion för att lägga till en Flervalsfråga (HTML Injection)
    let questionCount = 0;
    function addQuestionField(data = null) {
        questionCount++;
        // Om vi har data (vid redigering), använd den. Annars tom sträng.
        const qVal = data ? data.q : '';
        const correctVal = data ? data.a : '';
        const w1Val = data ? data.w1 : '';
        const w2Val = data ? data.w2 : '';
        const w3Val = data ? data.w3 : '';
        
        const container = document.getElementById('questions-container');
        // Template Literal för HTML-blocket
        const html = `
        <div class="border p-3 mb-3 rounded bg-light position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
            <div class="mb-2">
                <label for="q_mc_${questionCount}" class="form-label fw-bold">Fråga ${questionCount}</label>
                <input type="text" id="q_mc_${questionCount}" name="questions_mc[${questionCount}][question]" class="form-control" value="${qVal}" required>
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="questions_mc[${questionCount}][correct]" class="form-control border-success" value="${correctVal}" required placeholder="Rätt svar" aria-label="Rätt svar">
                </div>
                <div class="col-md-6">
                    <input type="text" name="questions_mc[${questionCount}][wrong1]" class="form-control border-danger" value="${w1Val}" required placeholder="Fel svar 1" aria-label="Fel svar 1">
                </div>
                <div class="col-md-6">
                    <input type="text" name="questions_mc[${questionCount}][wrong2]" class="form-control border-danger" value="${w2Val}" placeholder="Fel svar 2" aria-label="Fel svar 2">
                </div>
                <div class="col-md-6">
                    <input type="text" name="questions_mc[${questionCount}][wrong3]" class="form-control border-danger" value="${w3Val}" placeholder="Fel svar 3" aria-label="Fel svar 3">
                </div>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

    // Funktion för Sant/Falskt
    let tfQuestionCount = 0;
    function addTrueFalseField(data = null) {
        tfQuestionCount++;
        const qVal = data ? data.q : '';
        const aVal = data ? data.a : 'Sant';
        
        const container = document.getElementById('tf-questions-container');
        const html = `
        <div class="border p-3 mb-3 rounded bg-light position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
            <div class="mb-2">
                <label for="q_tf_${tfQuestionCount}" class="form-label fw-bold">Påstående ${tfQuestionCount}</label>
                <input type="text" id="q_tf_${tfQuestionCount}" name="questions_tf[${tfQuestionCount}][question]" class="form-control" value="${qVal}" required>
            </div>
            <div class="mb-2">
                <label for="a_tf_${tfQuestionCount}" class="form-label">Rätt svar</label>
                <select id="a_tf_${tfQuestionCount}" name="questions_tf[${tfQuestionCount}][correct]" class="form-select">
                    <option value="Sant" ${aVal == 'Sant' ? 'selected' : ''}>Sant</option>
                    <option value="Falskt" ${aVal == 'Falskt' ? 'selected' : ''}>Falskt</option>
                </select>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

    // Funktion för Sortering
    function addSortingField(data = null) {
        const sentencesVal = data && data.s ? data.s.join('\n') : '';
        const container = document.getElementById('sorting-questions-container');
        container.innerHTML = `
            <div class="alert alert-info">Skriv meningarna i **rätt ordning**, en mening per rad.</div>
            <div class="mb-2">
                <label for="sort_sentences" class="form-label fw-bold">Sorterbara meningar</label>
                <textarea name="questions_sort[0][sentences]" id="sort_sentences" class="form-control" rows="8">${sentencesVal}</textarea>
            </div>`;
    }

    // Funktion för Para ihop
    let pairCount = 0;
    function addPairField(data = null) {
        pairCount++;
        const termVal = data ? data.term : '';
        const defVal = data ? data.def : '';
        
        const container = document.getElementById('pair-questions-container');
        const html = `
        <div class="border p-3 mb-3 rounded bg-light position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
            <div class="row">
                <div class="col-md-6">
                    <label for="pair_term_${pairCount}" class="form-label fw-bold">Term</label>
                    <input type="text" id="pair_term_${pairCount}" name="questions_pair[${pairCount}][term]" class="form-control" value="${termVal}" required>
                </div>
                <div class="col-md-6">
                    <label for="pair_def_${pairCount}" class="form-label fw-bold">Betydelse</label>
                    <input type="text" id="pair_def_${pairCount}" name="questions_pair[${pairCount}][def]" class="form-control" value="${defVal}" required>
                </div>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

    // Funktion för Textluckor
    let gapCount = 0;
    function addGapField(data = null) {
        gapCount++;
        const sentVal = data ? data.sentence : '';
        const wordVal = data ? data.word : '';
        
        const container = document.getElementById('gaps-container');
        const html = `
        <div class="border p-3 mb-3 rounded bg-light position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
            <div class="row">
                <div class="col-md-8">
                    <label for="gap_sent_${gapCount}" class="form-label fw-bold">Mening med lucka</label>
                    <input type="text" id="gap_sent_${gapCount}" name="questions_gaps[${gapCount}][sentence]" class="form-control" value="${sentVal}" required>
                </div>
                <div class="col-md-4">
                    <label for="gap_word_${gapCount}" class="form-label fw-bold">Rätt ord</label>
                    <input type="text" id="gap_word_${gapCount}" name="questions_gaps[${gapCount}][word]" class="form-control border-success" value="${wordVal}" required>
                </div>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

    const dropdown = document.getElementById('taskTypeDropdown');
    const forms = document.querySelectorAll('.task-form-section');
    
    // Visar rätt formulärsektion baserat på dropdown-valet
    function updateForms() {
        const selectedText = dropdown.options[dropdown.selectedIndex].text.toLowerCase();
        
        // Dölj alla först
        forms.forEach(form => {
            form.classList.add('d-none');
            // Stäng av "required" på dolda fält så formuläret kan skickas
            form.querySelectorAll('input, textarea, select').forEach(input => {
                input.disabled = true;
                input.required = false;
            });
        });

        let activeFormId = null;
        if (selectedText.includes('flerval')) { activeFormId = 'form-flerval'; } 
        else if (selectedText.includes('sant/falskt')) { activeFormId = 'form-sant-falskt'; } 
        else if (selectedText.includes('sortering')) { activeFormId = 'form-sortering'; }
        else if (selectedText.includes('para ihop')) { activeFormId = 'form-paraihop'; }
        else if (selectedText.includes('textluckor')) { activeFormId = 'form-textluckor'; }

        // Visa det valda och aktivera fälten
        if (activeFormId) {
            const activeForm = document.getElementById(activeFormId);
            activeForm.classList.remove('d-none');
            activeForm.querySelectorAll('input, textarea, select').forEach(input => {
                input.disabled = false;
                const name = input.name;
                // Sätt tillbaka required på viktiga fält
                 if (name.includes('[question]') || name.includes('[correct]') || name.includes('[term]') || name.includes('[def]') || name.includes('[sentences]') || name.includes('[sentence]') || name.includes('[word]')) {
                    input.required = true;
                }
            });
        }
    }
    
    dropdown.addEventListener('change', updateForms);

    // KÖRS VID SIDLADDNING (Pre-fill logic)
    // Här fyller vi i formuläret med den JSON-data vi fick från PHP
    window.onload = function() {
        updateForms(); 
        if (taskType.includes('flerval')) {
            if (existingQuestions && existingQuestions.length > 0) { existingQuestions.forEach(q => addQuestionField(q)); } else { addQuestionField(); }
        } else if (taskType.includes('sant/falskt')) {
            if (existingQuestions && existingQuestions.length > 0) { existingQuestions.forEach(q => addTrueFalseField(q)); } else { addTrueFalseField(); }
        } else if (taskType.includes('sortering')) {
            addSortingField(existingQuestions);
        } else if (taskType.includes('para ihop')) {
            if (existingQuestions && existingQuestions.length > 0) { existingQuestions.forEach(q => addPairField(q)); } else { addPairField(); }
        } else if (taskType.includes('textluckor')) {
            if (existingQuestions && existingQuestions.gaps && existingQuestions.gaps.length > 0) { 
                existingQuestions.gaps.forEach(q => addGapField(q)); 
            } else { addGapField(); }
        }
    };

    // Hjälpfunktion för validering
    document.addEventListener('invalid', function(e) {
        const target = e.target;
        if (target.validity.valueMissing) {
            target.setCustomValidity('Detta fält måste fyllas i.');
        }
    }, true);

    document.addEventListener('input', function(e) {
        e.target.setCustomValidity('');
    });
</script>

<?php require_once "include/footer.php"; ?>
