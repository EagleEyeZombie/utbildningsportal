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
$allGenres = $task_obj->getAllGenres();

// Hämta alla lärare
$stmt = $pdo->query("SELECT users.u_id, users.u_name 
                     FROM users 
                     JOIN roles ON users.u_role_fk = roles.r_id 
                     WHERE roles.r_level >= 5 
                     ORDER BY users.u_name ASC");
$allTeachers = $stmt->fetchAll();

$errorMsg = "";
$successMsg = "";

// 4. HANTERA SPARA
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update-task'])) {
    
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!verifyCsrfToken($token)) { die("Ogiltig CSRF-token."); }

    $tName = cleanInput($_POST['t_name']);
    $tType = cleanInput($_POST['t_type']); 
    $tLevel = cleanInput($_POST['t_level']);
    $tText = cleanInput($_POST['t_text']);
    $tXp = cleanInput($_POST['t_xp']);
    $tClass = !empty($_POST['t_class']) ? cleanInput($_POST['t_class']) : null;
    $tGenre = !empty($_POST['t_genre']) ? cleanInput($_POST['t_genre']) : null;
    
    $tTeacher = !empty($_POST['t_teacher']) ? cleanInput($_POST['t_teacher']) : $task['t_teacher_fk'];

    $questionsData = [];

    if (strpos($taskTypeName, 'flerval') !== false) {
        if (isset($_POST['questions_mc'])) {
            foreach ($_POST['questions_mc'] as $q) {
                if (!empty($q['question'])) {
                    $questionsData[] = ['q' => cleanInput($q['question']), 'a' => cleanInput($q['correct']), 'w1' => cleanInput($q['wrong1']), 'w2' => cleanInput($q['wrong2']), 'w3' => cleanInput($q['wrong3'])];
                }
            }
        }
    } 
    elseif (strpos($taskTypeName, 'sant/falskt') !== false) {
        if (isset($_POST['questions_tf'])) {
            foreach ($_POST['questions_tf'] as $q) {
                if (!empty($q['question'])) {
                    $questionsData[] = ['q' => cleanInput($q['question']), 'a' => cleanInput($q['correct'])];
                }
            }
        }
    }
    elseif (strpos($taskTypeName, 'sortering') !== false) {
        if (isset($_POST['questions_sort'][0]['sentences'])) {
            $sentences = trim($_POST['questions_sort'][0]['sentences']);
            $sentencesArray = preg_split('/(\r\n|\r|\n)/', $sentences, -1, PREG_SPLIT_NO_EMPTY);
            $cleanedArray = array_map('cleanInput', $sentencesArray);
            $questionsData = ['s' => $cleanedArray];
        }
    }
    elseif (strpos($taskTypeName, 'para ihop') !== false) {
        if (isset($_POST['questions_pair'])) {
            foreach ($_POST['questions_pair'] as $p) {
                if (!empty($p['term']) && !empty($p['def'])) {
                    $questionsData[] = ['term' => cleanInput($p['term']), 'def' => cleanInput($p['def'])];
                }
            }
        }
    }
    elseif (strpos($taskTypeName, 'textluckor') !== false) {
        $gaps = [];
        if (isset($_POST['questions_gaps'])) {
            foreach ($_POST['questions_gaps'] as $g) {
                if (!empty($g['sentence']) && !empty($g['word'])) {
                    $gaps[] = ['sentence' => cleanInput($g['sentence']), 'word' => cleanInput($g['word'])];
                }
            }
        }
        $distractors = [];
        if (!empty($_POST['gap_distractors'])) {
            $rawDistractors = explode(',', $_POST['gap_distractors']);
            foreach($rawDistractors as $d) { $d = trim($d); if(!empty($d)) $distractors[] = cleanInput($d); }
        }
        $questionsData = ['gaps' => $gaps, 'distractors' => $distractors];
    }

    $jsonQuestions = json_encode($questionsData, JSON_UNESCAPED_UNICODE);

    $result = $task_obj->updateTask($taskId, $tName, $tType, $tLevel, $tClass, $tGenre, $tText, $jsonQuestions, $tXp, $tTeacher);

    if ($result['success']) {
        $successMsg = "Uppgiften uppdaterad!";
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
    const taskType = "<?= $taskTypeName ?>";
    const existingQuestions = <?= json_encode($questions) ?>;

    let questionCount = 0;
    function addQuestionField(data = null) {
        questionCount++;
        const qVal = data ? data.q : '';
        const correctVal = data ? data.a : '';
        const w1Val = data ? data.w1 : '';
        const w2Val = data ? data.w2 : '';
        const w3Val = data ? data.w3 : '';
        
        const container = document.getElementById('questions-container');
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
        else if (selectedText.includes('para ihop')) { activeFormId = 'form-paraihop'; }
        else if (selectedText.includes('textluckor')) { activeFormId = 'form-textluckor'; }

        if (activeFormId) {
            const activeForm = document.getElementById(activeFormId);
            activeForm.classList.remove('d-none');
            activeForm.querySelectorAll('input, textarea, select').forEach(input => {
                input.disabled = false;
                const name = input.name;
                 if (name.includes('[question]') || name.includes('[correct]') || name.includes('[term]') || name.includes('[def]') || name.includes('[sentences]') || name.includes('[sentence]') || name.includes('[word]')) {
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
        } else if (taskType.includes('para ihop')) {
            if (existingQuestions && existingQuestions.length > 0) { existingQuestions.forEach(q => addPairField(q)); } else { addPairField(); }
        } else if (taskType.includes('textluckor')) {
            if (existingQuestions && existingQuestions.gaps && existingQuestions.gaps.length > 0) { 
                existingQuestions.gaps.forEach(q => addGapField(q)); 
            } else { addGapField(); }
        }
    };

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
