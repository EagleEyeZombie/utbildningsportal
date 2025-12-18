<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// // Flöde D. Steg 2.1: Hämta ID från URL
$taskId = isset($_GET['id']) ? $_GET['id'] : 0;
$task = $task_obj->getTaskById($taskId);
// ... (Om ingen uppgift hittas, kasta ut användaren) ...

if (!$task) {
    header("Location: dashboard.php");
    exit;
}

// --- SÄKERHET: KOLLA OM LÅST ---
// Flöde D. Steg 2.2: SÄKERHET - Är uppgiften låst?
$unlockedLevel = $task_obj->getUnlockedLevel($_SESSION['user_id'], $task['t_type_fk'], $task['t_genre_fk']);
$isLocked = ($task['tl_level'] > $unlockedLevel);

// Flöde D. Steg 3: Avkoda JSON-data
$questions = json_decode($task['t_questions'], true);
$taskTypeName = strtolower($task['type_name']); 

// Räkna steg
$totalSteps = count($questions) + 1;
if (strpos($taskTypeName, 'sortering') !== false || strpos($taskTypeName, 'para ihop') !== false || strpos($taskTypeName, 'textluckor') !== false) {
    $totalSteps = 2; // Dessa typer har bara en "sida" med uppgifter
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <?php if ($isLocked): ?>
                <div class="card shadow-lg text-center border-secondary" style="border-width: 3px; background-color: #f8d7da;">
                    <div class="card-body p-5 rounded">
                        <i class="bi bi-lock-fill display-1 text-secondary mb-4"></i>
                        <h1 class="text-danger mb-3" style="font-family: 'Cinzel Decorative';">Detta kapitel är låst!</h1>
                        <p class="lead mb-4 text-dark">Du har inte låst upp denna nivå än.</p>
                        <a href="dashboard.php" class="btn btn-primary btn-lg">Tillbaka till Kartan</a>
                    </div>
                </div>
            
            <?php else: ?>
                <div class="progress mb-4" style="height: 25px;">
                    <div id="progressBar" class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">Start</div>
                </div>

                <form action="task_submit.php" method="POST" id="quizForm">
                    <input type="hidden" name="task_id" value="<?= $task['t_id'] ?>">
                    <?= csrfInput() ?>

                    <div class="step-card" id="step-0">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-5">
                                <span class="badge bg-secondary mb-2"><?= htmlspecialchars($task['type_name']) ?></span>
                                <h1 class="card-title mb-4"><?= htmlspecialchars($task['t_name']) ?></h1>
                                <div class="task-instruction-panel mb-4">
                                    <p class="lead" style="white-space: pre-wrap;"><?= htmlspecialchars($task['t_text']) ?></p>
                                </div>
                                <div class="d-grid gap-3">
                                    <button type="button" class="btn btn-primary btn-lg" onclick="nextStep()">Jag har läst texten och är redo <i class="bi bi-arrow-right"></i></button>
                                    <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i> Jag är inte redo (Gå tillbaka)</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php 
                    $qCount = 0;

                    // Flöde D. Steg 4: Dynamisk Rendering (Välj rätt gränssnitt)

                    // Flöde D. Steg 4.A. Fall A: Textluckor (Drag & Drop)
                    // === TEXTLUCKOR ===
                    if (strpos($taskTypeName, 'textluckor') !== false) {
                        // ... renderar lucktext-gränssnittet ...
                        $qCount++;
                        $gaps = $questions['gaps'];
                        $distractors = isset($questions['distractors']) ? $questions['distractors'] : [];
                        
                        $wordBank = $distractors;
                        foreach ($gaps as $g) {
                            $wordBank[] = $g['word'];
                        }
                        shuffle($wordBank);
                        ?>
                        <div class="step-card d-none" id="step-<?= $qCount ?>">
                            <div class="card shadow border-0">
                                <div class="card-header bg-primary text-white py-3">
                                    <h4 class="m-0">Fyll i luckorna</h4>
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="mb-4">Dra rätt ord från listan till luckorna i meningarna.</h5>
                                    
                                    <div class="p-3 bg-light border rounded mb-4">
                                        <h6 class="text-muted text-center mb-2">ORDBANK</h6>
                                        <div id="word-bank" class="d-flex flex-wrap justify-content-center gap-2 sortable-list" style="min-height: 50px;">
                                            <?php foreach ($wordBank as $word): ?>
                                                <div class="badge bg-warning text-dark p-2 fs-6 sortable-item" style="cursor: grab; user-select: none;">
                                                    <?= htmlspecialchars($word) ?>
                                                    <input type="hidden" value="<?= htmlspecialchars($word) ?>">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="list-group">
                                        <?php foreach ($gaps as $index => $gap): ?>
                                            <div class="list-group-item p-3 mb-2 border-0">
                                                <p class="lead mb-2">
                                                    <?php 
                                                    $parts = explode('___', $gap['sentence']);
                                                    echo htmlspecialchars($parts[0]);
                                                    ?>
                                                    <span id="gap-<?= $index ?>" class="gap-zone d-inline-block align-middle bg-white border border-2 border-secondary rounded sortable-list" style="width: 120px; height: 38px; vertical-align: middle; padding: 2px;">
                                                        </span>
                                                    <?php if (isset($parts[1])) echo htmlspecialchars($parts[1]); ?>
                                                </p>
                                                <input type="hidden" name="answers[<?= $index ?>]" id="input-gap-<?= $index ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="d-flex flex-column flex-md-row justify-content-between mt-4 gap-2">
                                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep()"><i class="bi bi-arrow-left"></i> Tillbaka</button>
                                        <button type="button" class="btn btn-success btn-lg" onclick="prepareSubmission()">Slutför och Rätta <i class="bi bi-check-lg"></i></button>
                                        <button type="submit" id="real-submit" style="display:none;"></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    
                    
                    // === PARA IHOP ===
                    elseif (strpos($taskTypeName, 'para ihop') !== false) {
                        
                        $qCount++;
                        $pairs = $questions; 
                        $shuffledTerms = $pairs;
                        shuffle($shuffledTerms);
                        ?>
                        <div class="step-card d-none" id="step-<?= $qCount ?>">
                            <div class="card shadow border-0">
                                <div class="card-header bg-primary text-white py-3"><h4 class="m-0">Para Ihop</h4></div>
                                <div class="card-body p-4">
                                    <h5 class="mb-4">Dra orden till vänster (använd handtaget) så de matchar beskrivningen.</h5>
                                    <div class="row g-0">
                                        <div class="col-6 pe-1"> <h6 class="text-center text-muted small mb-1">ORD</h6>
                                            <div id="sortable-terms" class="list-group">
                                                <?php foreach ($shuffledTerms as $index => $pair): ?>
                                                    <div class="list-group-item list-group-item-action p-3 border rounded mb-2 sortable-item d-flex align-items-center">
                                                        <i class="bi bi-grip-vertical me-3 handle fs-4 text-muted" style="cursor: grab;"></i>
                                                        <div class="flex-grow-1 text-center">
                                                            <strong><?= htmlspecialchars($pair['term']) ?></strong>
                                                        </div>
                                                        <input type="hidden" name="answers[<?= $index ?>]" value="<?= htmlspecialchars($pair['term']) ?>">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="col-6 ps-1"> <h6 class="text-center text-muted small mb-1">BETYDELSE</h6>
                                            <div class="list-group">
                                                <?php foreach ($pairs as $pair): ?>
                                                    <div class="list-group-item p-3 border rounded mb-2 bg-light text-center d-flex align-items-center justify-content-center" style="height: 66px;">
                                                        <?= htmlspecialchars($pair['def']) ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-column flex-md-row justify-content-between mt-4 gap-2">
                                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep()"><i class="bi bi-arrow-left"></i> Tillbaka</button>
                                        <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Är du säker på att du är klar?');">Slutför och Rätta <i class="bi bi-check-lg"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }

                    // Flöde D. Steg4.B. Fall B: Sortering
                    // === SORTERING ===
                    elseif (strpos($taskTypeName, 'sortering') !== false) {
                        // ... renderar sorterings-listan ...
                        $qCount++;
                        $correctOrder = $questions['s']; 
                        $shuffledOrder = $correctOrder; 
                        shuffle($shuffledOrder); 
                        ?>
                        <div class="step-card d-none" id="step-<?= $qCount ?>">
                            <div class="card shadow border-0">
                                <div class="card-header bg-primary text-white py-3"><h4 class="m-0">Sorteringsövning</h4></div>
                                <div class="card-body p-4">
                                    <h5 class="mb-4">Sortera meningarna i rätt ordning (använd handtaget).</h5>
                                    <div id="sortable-list" class="list-group mb-4">
                                        <?php foreach ($shuffledOrder as $index => $sentence): ?>
                                            <div class="list-group-item list-group-item-action p-3 border rounded mb-2 sortable-item d-flex align-items-center">
                                                <i class="bi bi-grip-vertical me-3 handle fs-4 text-muted" style="cursor: grab;"></i>
                                                <span><?= htmlspecialchars($sentence) ?></span>
                                                <input type="hidden" name="answers[<?= $index ?>]" value="<?= htmlspecialchars($sentence) ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep()"><i class="bi bi-arrow-left"></i> Tillbaka</button>
                                        <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Är du säker på att du är klar?');">Slutför och Rätta <i class="bi bi-check-lg"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    } 
                    
                    // === FLERVAL / SANT-FALSKT ===
                    else {
                        foreach ($questions as $index => $q): 
                            $qCount++;
                        ?>
                            <div class="step-card d-none" id="step-<?= $qCount ?>">
                                <div class="card shadow border-0">
                                    <div class="card-header bg-primary text-white py-3"><h4 class="m-0">Fråga <?= $qCount ?> av <?= count($questions) ?></h4></div>
                                    <div class="card-body p-4">
                                        <?php $questionText = isset($q['q']) ? $q['q'] : (isset($q['statement']) ? $q['statement'] : 'Fråga saknas'); ?>
                                        <?php if (strpos($taskTypeName, 'flerval') !== false): ?>
                                            <h5 class="mb-4"><?= htmlspecialchars($questionText) ?></h5>
                                            <div class="list-group mb-4">
                                                <?php
                                                $options = [];
                                                $options[] = ['text' => $q['a'], 'value' => $q['a']];
                                                if (!empty($q['w1'])) $options[] = ['text' => $q['w1'], 'value' => $q['w1']];
                                                if (!empty($q['w2'])) $options[] = ['text' => $q['w2'], 'value' => $q['w2']];
                                                if (!empty($q['w3'])) $options[] = ['text' => $q['w3'], 'value' => $q['w3']];
                                                shuffle($options);
                                                ?>
                                                <?php foreach ($options as $opt): ?>
                                                    <label class="list-group-item list-group-item-action p-3 border rounded mb-2"><input class="form-check-input me-2" type="radio" name="answers[<?= $qCount ?>]" value="<?= htmlspecialchars($opt['value']) ?>" required onclick="enableNextBtn(<?= $qCount ?>)"><span><?= htmlspecialchars($opt['text']) ?></span></label>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php elseif (strpos($taskTypeName, 'sant/falskt') !== false): ?>
                                            <h5 class="mb-4">Påstående:</h5>
                                            <p class="lead mb-4 p-3 bg-light border rounded"><?= htmlspecialchars($questionText) ?></p>
                                            <div class="list-group mb-4">
                                                <label class="list-group-item list-group-item-action p-3 border rounded mb-2"><input class="form-check-input me-2" type="radio" name="answers[<?= $qCount ?>]" value="Sant" required onclick="enableNextBtn(<?= $qCount ?>)"><span><i class="bi bi-check-circle-fill text-success"></i> Sant</span></label>
                                                <label class="list-group-item list-group-item-action p-3 border rounded mb-2"><input class="form-check-input me-2" type="radio" name="answers[<?= $qCount ?>]" value="Falskt" required onclick="enableNextBtn(<?= $qCount ?>)"><span><i class="bi bi-x-circle-fill text-danger"></i> Falskt</span></label>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                            <button type="button" class="btn btn-outline-secondary" onclick="prevStep()"><i class="bi bi-arrow-left"></i> Tillbaka</button>
                                            <?php if ($qCount < count($questions)): ?>
                                                <button type="button" class="btn btn-primary btn-lg next-btn" id="btn-next-<?= $qCount ?>" onclick="nextStep()" disabled>Nästa Fråga <i class="bi bi-arrow-right"></i></button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-success btn-lg next-btn" id="btn-next-<?= $qCount ?>" disabled>Slutför och Rätta <i class="bi bi-check-lg"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; 
                    } ?>

                </form>
                
                <script>
                    // ... (Samma script som förut) ...
                    let currentStep = 0;
                    const totalSteps = <?= $totalSteps ?>; 
                    const taskType = "<?= $taskTypeName ?>";

                    function updateProgress() {
                        let percent = 0;
                        if (currentStep > 0) { percent = (currentStep / (totalSteps - 1)) * 100; } 
                        else { percent = 5; }
                        const bar = document.getElementById('progressBar');
                        bar.style.width = percent + '%';
                        
                        if(currentStep === 0) {
                            bar.innerText = "Läser texten...";
                            bar.className = "progress-bar bg-info progress-bar-striped";
                        } else {
                            bar.innerText = (['sortering', 'para ihop', 'textluckor'].some(t => taskType.includes(t))) ? "Lös uppgiften" : `Fråga ${currentStep} av ${totalSteps-1}`;
                            bar.className = "progress-bar bg-success progress-bar-striped progress-bar-animated";
                        }
                    }

                    function nextStep() {
                        document.getElementById('step-' + currentStep).classList.add('d-none');
                        currentStep++;
                        document.getElementById('step-' + currentStep).classList.remove('d-none');
                        updateProgress();
                    }

                    function prevStep() {
                        document.getElementById('step-' + currentStep).classList.add('d-none');
                        currentStep--;
                        document.getElementById('step-' + currentStep).classList.remove('d-none');
                        updateProgress();
                    }

                    function enableNextBtn(stepId) {
                        const btn = document.getElementById('btn-next-' + stepId);
                        if(btn) { btn.disabled = false; }
                    }
                    
                    function prepareSubmission() {
                        if (!confirm('Är du säker på att du är klar?')) {
                            return;
                        }
                        if (taskType.includes('textluckor')) {
                            const gaps = document.querySelectorAll('.gap-zone');
                            gaps.forEach((gap, index) => {
                                const input = document.getElementById('input-gap-' + index);
                                const droppedBadge = gap.querySelector('.sortable-item input');
                                if (droppedBadge) {
                                    input.value = droppedBadge.value;
                                } else {
                                    input.value = "";
                                }
                            });
                        }
                        document.getElementById('real-submit').click();
                    }

                    updateProgress();

                    if (['sortering', 'para ihop'].some(t => taskType.includes(t))) {
                        const list = document.getElementById(taskType.includes('para ihop') ? 'sortable-terms' : 'sortable-list');
                        if(list) {
                            Sortable.create(list, {
                                animation: 150, 
                                ghostClass: 'sortable-ghost',
                                handle: '.handle',
                                onEnd: function (evt) {
                                    const items = list.querySelectorAll('.sortable-item');
                                    items.forEach((item, index) => {
                                        const input = item.querySelector('input[type="hidden"]');
                                        input.name = `answers[${index}]`; 
                                    });
                                }
                            });
                        }
                    }
                    
                    if (taskType.includes('textluckor')) {
                        const bank = document.getElementById('word-bank');
                        const gaps = document.querySelectorAll('.gap-zone');
                        
                        Sortable.create(bank, {
                            group: 'shared',
                            animation: 150,
                            sort: true
                        });
                        
                        gaps.forEach(gap => {
                            Sortable.create(gap, {
                                group: 'shared',
                                animation: 150,
                                max: 1, 
                                put: function (to) {
                                    return to.el.children.length < 1;
                                }
                            });
                        });
                    }
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once "include/footer.php"; ?>
