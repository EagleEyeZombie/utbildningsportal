<?php
require_once "include/header.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    header("Location: login.php");
    exit;
}

$types = $task_obj->getAllTypes();
$levels = $task_obj->getAllLevels();
$allClasses = $task_obj->getAllClasses();
$allGenres = $task_obj->getAllGenres();
$errorMsg = "";
$successMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create-task'])) {
    
    if (!verifyCsrfToken($_POST['csrf_token'])) { die("Ogiltig CSRF-token."); }

    $tName = cleanInput($_POST['t_name']);
    $tType = cleanInput($_POST['t_type']);
    $tLevel = cleanInput($_POST['t_level']);
    $tText = cleanInput($_POST['t_text']); 
    $teacherId = $_SESSION['user_id'];
    $tXp = cleanInput($_POST['t_xp']);
    $tClass = !empty($_POST['t_class']) ? cleanInput($_POST['t_class']) : null;
    $tGenre = !empty($_POST['t_genre']) ? cleanInput($_POST['t_genre']) : null;

    $questionsData = [];
    
    $typeNameQuery = $pdo->prepare("SELECT tt_name FROM task_types WHERE tt_id = ?");
    $typeNameQuery->execute([$tType]);
    $taskTypeName = strtolower($typeNameQuery->fetchColumn());
    
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
    // 5. TEXTLUCKOR (NYTT!)
    elseif (strpos($taskTypeName, 'textluckor') !== false) {
        $gaps = [];
        if (isset($_POST['questions_gaps'])) {
            foreach ($_POST['questions_gaps'] as $g) {
                if (!empty($g['sentence']) && !empty($g['word'])) {
                    $gaps[] = ['sentence' => cleanInput($g['sentence']), 'word' => cleanInput($g['word'])];
                }
            }
        }
        
        // Hantera falska ord (distractors)
        $distractors = [];
        if (!empty($_POST['gap_distractors'])) {
            $rawDistractors = explode(',', $_POST['gap_distractors']);
            foreach($rawDistractors as $d) {
                $d = trim($d);
                if(!empty($d)) $distractors[] = cleanInput($d);
            }
        }
        
        $questionsData = ['gaps' => $gaps, 'distractors' => $distractors];
    }

    $jsonQuestions = json_encode($questionsData, JSON_UNESCAPED_UNICODE);

    $result = $task_obj->createTask($tName, $tType, $tLevel, $teacherId, $tClass, $tGenre, $tText, $jsonQuestions, $tXp);

    if ($result['success']) {
        $successMsg = "Uppgiften skapad! <a href='admin_tasks.php'>Tillbaka till listan</a>";
    } else {
        $errorMsg = $result['error'];
    }
}
?>

<div class="container mt-5 mb-5">
    <h1>Skapa ny uppgift</h1>
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
                            <input type="text" name="t_name" class="form-control" required placeholder="T.ex. Verb och Substantiv">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Typ av uppgift</label>
                                <select name="t_type" id="taskTypeDropdown" class="form-select" required>
                                    <?php foreach ($types as $t): ?>
                                        <option value="<?= $t['tt_id'] ?>"><?= $t['tt_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Genre (Tema)</label>
                                <select name="t_genre" class="form-select" required>
                                    <?php foreach ($allGenres as $g): ?>
                                        <option value="<?= $g['g_id'] ?>"><?= $g['g_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Svårighetsgrad</label>
                                <select name="t_level" class="form-select" required>
                                    <?php foreach ($levels as $l): ?>
                                        <option value="<?= $l['tl_id'] ?>"><?= $l['tl_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Klass (Valfri)</label>
                                <select name="t_class" class="form-select">
                                    <option value="">Ingen specifik klass</option>
                                    <?php foreach ($allClasses as $class): ?>
                                        <option value="<?= $class['c_id'] ?>"><?= htmlspecialchars($class['c_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
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

                <!-- FORM: FLERVAL -->
                <div class="card shadow-sm task-form-section" id="form-flerval">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Frågor (Flerval)</span>
                        <button type="button" class="btn btn-sm btn-light" onclick="addQuestionField()">+ Lägg till fråga</button>
                    </div>
                    <div class="card-body" id="questions-container"></div>
                </div>

                <!-- FORM: SANT/FALSKT -->
                <div class="card shadow-sm task-form-section d-none" id="form-sant-falskt">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Frågor (Sant/Falskt)</span>
                        <button type="button" class="btn btn-sm btn-light" onclick="addTrueFalseField()">+ Lägg till påstående</button>
                    </div>
                    <div class="card-body" id="tf-questions-container"></div>
                </div>

                <!-- FORM: SORTERING -->
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

                <!-- FORM: PARA IHOP -->
                <div class="card shadow-sm task-form-section d-none" id="form-paraihop">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Para ihop (Term och Betydelse)</span>
                        <button type="button" class="btn btn-sm btn-light" onclick="addPairField()">+ Lägg till par</button>
                    </div>
                    <div class="card-body" id="pair-questions-container"></div>
                </div>

                <!-- FORM: TEXTLUCKOR (NYTT!) -->
                <div class="card shadow-sm task-form-section d-none" id="form-textluckor">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Textluckor</span>
                        <button type="button" class="btn btn-sm btn-light" onclick="addGapField()">+ Lägg till mening</button>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">Skriv meningen och använd <strong>___</strong> där luckan ska vara. Skriv det rätta ordet i rutan bredvid.</div>
                        <div id="gaps-container"></div>
                        
                        <div class="mt-4">
                            <label class="form-label fw-bold">Falska ord (Distractors)</label>
                            <input type="text" name="gap_distractors" class="form-control" placeholder="Separera med komma (t.ex. röd, blå, grön)">
                            <div class="form-text">Dessa ord kommer också finnas med i listan för att göra det svårare.</div>
                        </div>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" name="create-task" class="btn btn-success btn-lg">Spara Uppgift</button>
                </div>
            </div>
            
             <div class="col-md-4">
                <div class="alert alert-info">
                    <h5><i class="bi bi-info-circle"></i> Tips</h5>
                    <p>Välj rätt uppgiftstyp för att se rätt formulär.</p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // --- FLERVAL ---
    let questionCount = 0;
    function addQuestionField() {
        questionCount++;
        const container = document.getElementById('questions-container');
        const html = `
        <div class="border p-3 mb-3 rounded bg-light position-relative" id="q-row-${questionCount}">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
            <div class="mb-2"><label class="form-label fw-bold">Fråga ${questionCount}</label><input type="text" name="questions_mc[${questionCount}][question]" class="form-control" required placeholder="Fråga"></div>
            <div class="row g-2">
                <div class="col-md-6"><input type="text" name="questions_mc[${questionCount}][correct]" class="form-control border-success" required placeholder="Rätt svar"></div>
                <div class="col-md-6"><input type="text" name="questions_mc[${questionCount}][wrong1]" class="form-control border-danger" required placeholder="Fel svar 1"></div>
                <div class="col-md-6"><input type="text" name="questions_mc[${questionCount}][wrong2]" class="form-control border-danger" placeholder="Fel svar 2"></div>
                <div class="col-md-6"><input type="text" name="questions_mc[${questionCount}][wrong3]" class="form-control border-danger" placeholder="Fel svar 3"></div>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

    // --- SANT/FALSKT ---
    let tfQuestionCount = 0;
    function addTrueFalseField() {
        tfQuestionCount++;
        const container = document.getElementById('tf-questions-container');
        const html = `
        <div class="border p-3 mb-3 rounded bg-light position-relative" id="tf-q-row-${tfQuestionCount}">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
            <div class="mb-2"><label class="form-label fw-bold">Påstående ${tfQuestionCount}</label><input type="text" name="questions_tf[${tfQuestionCount}][question]" class="form-control" required placeholder="Påstående"></div>
            <div class="mb-2"><label class="form-label">Rätt svar</label><select name="questions_tf[${tfQuestionCount}][correct]" class="form-select"><option value="Sant">Sant</option><option value="Falskt">Falskt</option></select></div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

    // --- PARA IHOP ---
    let pairCount = 0;
    function addPairField() {
        pairCount++;
        const container = document.getElementById('pair-questions-container');
        const html = `
        <div class="border p-3 mb-3 rounded bg-light position-relative" id="pair-row-${pairCount}">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
            <div class="row">
                <div class="col-md-6"><label class="form-label fw-bold">Term</label><input type="text" name="questions_pair[${pairCount}][term]" class="form-control" required placeholder="T.ex. Sköldpadda"></div>
                <div class="col-md-6"><label class="form-label fw-bold">Betydelse</label><input type="text" name="questions_pair[${pairCount}][def]" class="form-control" required placeholder="T.ex. Djur"></div>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

    // --- TEXTLUCKOR (NYTT!) ---
    let gapCount = 0;
    function addGapField() {
        gapCount++;
        const container = document.getElementById('gaps-container');
        const html = `
        <div class="border p-3 mb-3 rounded bg-light position-relative" id="gap-row-${gapCount}">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.remove()"></button>
            <div class="row">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Mening med lucka</label>
                    <input type="text" name="questions_gaps[${gapCount}][sentence]" class="form-control" required placeholder="T.ex. Katten sitter på ___">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Rätt ord (Luckan)</label>
                    <input type="text" name="questions_gaps[${gapCount}][word]" class="form-control border-success" required placeholder="T.ex. mattan">
                </div>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

    // --- BYT FORMULÄR ---
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
        else if (selectedText.includes('textluckor')) { activeFormId = 'form-textluckor'; } // <-- NYTT

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

    function initForms() {
        updateForms(); 
        const selectedText = dropdown.options[dropdown.selectedIndex].text.toLowerCase();
        if (selectedText.includes('flerval')) { addQuestionField(); } 
        else if (selectedText.includes('sant/falskt')) { addTrueFalseField(); }
        else if (selectedText.includes('para ihop')) { addPairField(); }
        else if (selectedText.includes('textluckor')) { addGapField(); } // <-- NYTT
    }
    
    window.onload = initForms;
</script>

<?php require_once "include/footer.php"; ?>
