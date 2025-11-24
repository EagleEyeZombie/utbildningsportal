<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$taskId = isset($_GET['id']) ? $_GET['id'] : 0;
$task = $task_obj->getTaskById($taskId);

if (!$task) {
    header("Location: dashboard.php");
    exit;
}

// --- SÄKERHET: KOLLA OM LÅST ---
$unlockedLevel = $task_obj->getUnlockedLevel($_SESSION['user_id'], $task['t_type_fk'], $task['t_genre_fk']);
$isLocked = ($task['tl_level'] > $unlockedLevel);

// Vi hämtar data oavsett, men vi visar olika saker beroende på $isLocked
$questions = json_decode($task['t_questions'], true);
$taskTypeName = strtolower($task['type_name']); 
$totalSteps = (strpos($taskTypeName, 'sortering') !== false) ? 2 : count($questions) + 1; 
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <?php if ($isLocked): ?>
                <!-- --- LÅST SKÄRM (NYTT) --- -->
                <div class="card shadow-lg text-center border-warning" style="border-width: 3px;">
                    <div class="card-body p-5 bg-light rounded">
                        <i class="bi bi-lock-fill display-1 text-secondary mb-4"></i>
                        <h1 class="text-danger mb-3" style="font-family: 'Cinzel Decorative';">Detta kapitel är låst!</h1>
                        <p class="lead mb-4">
                            Du försöker nå <strong><?= htmlspecialchars($task['t_name']) ?></strong> (Nivå <?= $task['tl_level'] ?>), 
                            men du har bara låst upp till Nivå <?= $unlockedLevel ?>.
                        </p>
                        <p class="text-muted mb-4">Du måste slutföra de tidigare kapitlen i sagan för att fortsätta.</p>
                        <a href="dashboard.php" class="btn btn-primary btn-lg">Tillbaka till Äventyret</a>
                    </div>
                </div>
            
            <?php else: ?>
                <!-- --- ORDINARIE QUIZ-VY (Om man får vara här) --- -->
            
                <div class="progress mb-4" style="height: 25px;">
                    <div id="progressBar" class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">Start</div>
                </div>

                <form action="task_submit.php" method="POST" id="quizForm">
                    <input type="hidden" name="task_id" value="<?= $task['t_id'] ?>">
                    <?= csrfInput() ?>

                    <!-- STEG 1: LÄSTEXTEN -->
                    <div class="step-card" id="step-0">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-5">
                                <span class="badge bg-secondary mb-2"><?= htmlspecialchars($task['type_name']) ?></span>
                                <h1 class="card-title mb-4"><?= htmlspecialchars($task['t_name']) ?></h1>
                                
                                <div class="task-instruction-panel mb-4">
                                    <p class="lead" style="white-space: pre-wrap;"><?= htmlspecialchars($task['t_text']) ?></p>
                                </div>

                                <div class="d-grid gap-3">
                                    <button type="button" class="btn btn-primary btn-lg" onclick="nextStep()">
                                        Jag har läst texten och är redo <i class="bi bi-arrow-right"></i>
                                    </button>
                                    <a href="dashboard.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-lg"></i> Jag är inte redo (Gå tillbaka)
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEG 2: FRÅGORNA -->
                    <?php 
                    $qCount = 0;
                    if (strpos($taskTypeName, 'sortering') !== false) {
                        // Sortering
                        $qCount++;
                        $correctOrder = $questions['s']; 
                        $shuffledOrder = $correctOrder; 
                        shuffle($shuffledOrder); 
                        ?>
                        <div class="step-card d-none" id="step-<?= $qCount ?>">
                            <div class="card shadow border-0">
                                <div class="card-header bg-primary text-white py-3">
                                    <h4 class="m-0">Sorteringsövning</h4>
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="mb-4">Instruktion: Dra och släpp meningarna så de hamnar i rätt händelseordning.</h5>
                                    <div id="sortable-list" class="list-group mb-4">
                                        <?php foreach ($shuffledOrder as $index => $sentence): ?>
                                            <div class="list-group-item list-group-item-action p-3 border rounded mb-2 sortable-item">
                                                <i class="bi bi-grip-vertical me-2"></i> 
                                                <span><?= htmlspecialchars($sentence) ?></span>
                                                <input type="hidden" name="answers[<?= $index ?>]" value="<?= htmlspecialchars($sentence) ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep()"><i class="bi bi-arrow-left"></i> Tillbaka</button>
                                        <button type="submit" class="btn btn-success btn-lg">Slutför och Rätta <i class="bi bi-check-lg"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    } else {
                        // Flerval / SantFalskt
                        foreach ($questions as $index => $q): 
                            $qCount++;
                        ?>
                            <div class="step-card d-none" id="step-<?= $qCount ?>">
                                <div class="card shadow border-0">
                                    <div class="card-header bg-primary text-white py-3">
                                        <h4 class="m-0">Fråga <?= $qCount ?> av <?= count($questions) ?></h4>
                                    </div>
                                    <div class="card-body p-4">
                                        <?php 
                                            $questionText = isset($q['q']) ? $q['q'] : (isset($q['statement']) ? $q['statement'] : 'Fråga saknas');
                                        ?>
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
                                                    <label class="list-group-item list-group-item-action p-3 border rounded mb-2">
                                                        <input class="form-check-input me-2" type="radio" name="answers[<?= $qCount ?>]" value="<?= htmlspecialchars($opt['value']) ?>" required onclick="enableNextBtn(<?= $qCount ?>)">
                                                        <span><?= htmlspecialchars($opt['text']) ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php elseif (strpos($taskTypeName, 'sant/falskt') !== false): ?>
                                            <h5 class="mb-4">Påstående:</h5>
                                            <p class="lead mb-4 p-3 bg-light border rounded"><?= htmlspecialchars($questionText) ?></p>
                                            <div class="list-group mb-4">
                                                <label class="list-group-item list-group-item-action p-3 border rounded mb-2">
                                                    <input class="form-check-input me-2" type="radio" name="answers[<?= $qCount ?>]" value="Sant" required onclick="enableNextBtn(<?= $qCount ?>)">
                                                    <span><i class="bi bi-check-circle-fill text-success"></i> Sant</span>
                                                </label>
                                                <label class="list-group-item list-group-item-action p-3 border rounded mb-2">
                                                    <input class="form-check-input me-2" type="radio" name="answers[<?= $qCount ?>]" value="Falskt" required onclick="enableNextBtn(<?= $qCount ?>)">
                                                    <span><i class="bi bi-x-circle-fill text-danger"></i> Falskt</span>
                                                </label>
                                            </div>
                                        <?php endif; ?>
                                        <div class="d-flex justify-content-between">
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
                            bar.innerText = (taskType.includes('sortering')) ? "Sortera meningarna" : `Fråga ${currentStep} av ${totalSteps-1}`;
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

                    updateProgress();

                    if (taskType.includes('sortering')) {
                        const list = document.getElementById('sortable-list');
                        if(list) {
                            Sortable.create(list, {
                                animation: 150, 
                                ghostClass: 'sortable-ghost', 
                                onEnd: function (evt) {
                                    const items = list.querySelectorAll('.sortable-item');
                                    items.forEach((item, index) => {
                                        const input = item.querySelector('input[type="hidden"]');
                                        input.name = `answers[${index}]`; 
                                    });
                                }
                            });
                            document.querySelectorAll('.sortable-item').forEach(item => {
                                item.style.cursor = 'grab';
                            });
                        }
                    }
                </script>
            
            <?php endif; // Slut på if($isLocked) ?>

        </div>
    </div>
</div>

<?php require_once "include/footer.php"; ?>
