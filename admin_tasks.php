<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role_level'] < 5) {
    header("Location: 403.php");
    exit;
}

// Hämta data för filter
$allTypes = $task_obj->getAllTypes();
$allLevels = $task_obj->getAllLevels();
$allGenres = $task_obj->getAllGenres();
$stmt = $pdo->query("SELECT u_id, u_name FROM users WHERE u_role_fk >= 5 ORDER BY u_name");
$allTeachers = $stmt->fetchAll();

// --- FILTERLOGIK ---
$currentUserId = $_SESSION['user_id'];
$filterTeacher = (isset($_GET['teacher']) && $_GET['teacher'] !== 'all') ? (int)$_GET['teacher'] : null;
$filterType = (isset($_GET['type']) && $_GET['type'] !== 'all') ? (int)$_GET['type'] : null;
$filterLevel = (isset($_GET['level']) && $_GET['level'] !== 'all') ? (int)$_GET['level'] : null;
$filterGenre = (isset($_GET['genre']) && $_GET['genre'] !== 'all') ? (int)$_GET['genre'] : null;

// Hämta data (med alla filter)
$allTasks = $task_obj->getTasksFiltered($filterTeacher, $filterType, $filterLevel, null, $filterGenre);
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="admin_dashboard.php" class="btn btn-outline-dark me-3 fw-bold">
                <i class="bi bi-arrow-left"></i> Tillbaka till Adminpanelen
            </a>
            <h1 class="m-0"><i class="bi bi-pencil-square"></i> Hantera Uppgifter</h1>
        </div>
        <a href="admin_create_task.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Skapa ny uppgift
        </a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i> Uppgiften har raderats.
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body bg-light">
            <form action="admin_tasks.php" method="GET" class="row g-3 align-items-end">
                
                <div class="col-md-3">
                    <label for="teacher" class="form-label">Skapare</label>
                    <select name="teacher" id="teacher" class="form-select">
                        <option value="all">Alla Lärare</option>
                        <option value="<?= $currentUserId ?>" <?php echo ($filterTeacher == $currentUserId) ? 'selected' : ''; ?>>Bara Mina</option>
                        <option value="" disabled>---</option>
                        <?php foreach ($allTeachers as $teacher): ?>
                            <option value="<?= $teacher['u_id'] ?>" <?php echo ($filterTeacher == $teacher['u_id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($teacher['u_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="type" class="form-label">Typ</label>
                    <select name="type" id="type" class="form-select">
                        <option value="all">Alla</option>
                        <?php foreach ($allTypes as $type): ?>
                            <option value="<?= $type['tt_id'] ?>" <?php echo ($filterType == $type['tt_id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($type['tt_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="genre" class="form-label">Genre</label>
                    <select name="genre" id="genre" class="form-select">
                        <option value="all">Alla</option>
                        <?php foreach ($allGenres as $g): ?>
                            <option value="<?= $g['g_id'] ?>" <?php echo ($filterGenre == $g['g_id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($g['g_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="level" class="form-label">Nivå</label>
                    <select name="level" id="level" class="form-select">
                        <option value="all">Alla</option>
                        <?php foreach ($allLevels as $level): ?>
                            <option value="<?= $level['tl_id'] ?>" <?php echo ($filterLevel == $level['tl_id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($level['tl_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3 text-md-end">
                    <button type="submit" class="btn btn-primary w-100 mb-1">Filtrera</button>
                    <a href="admin_tasks.php" class="btn btn-outline-dark w-100 btn-sm fw-bold">Rensa filter</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body p-0">
            <?php if (count($allTasks) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Titel</th>
                                <th>Typ / Genre</th>
                                <th>Nivå / XP</th>
                                <th>Skapare</th>
                                <th class="text-end">Åtgärd</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allTasks as $task): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($task['t_name']) ?></strong></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($task['type_name']) ?></span>
                                        <?php if(!empty($task['genre_name'])): ?>
                                            <span class="badge bg-dark border"><?= htmlspecialchars($task['genre_name']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">Lvl <?= $task['tl_level'] ?></span> 
                                        <small class="text-muted"><?= $task['t_xp'] ?> XP</small>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($task['teacher_name']) ?>
                                        <?php if ($task['t_teacher_fk'] == $currentUserId): ?>
                                            <span class="badge bg-success">Du</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="admin_edit_task.php?id=<?= $task['t_id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i> Redigera
                                        </a>
                                        
                                        <form action="delete_task.php" method="POST" class="d-inline" onsubmit="return confirm('Är du säker? All statistik för denna uppgift kommer också försvinna.');">
                                            <?= csrfInput() ?>
                                            <input type="hidden" name="id" value="<?= $task['t_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i> Ta bort
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <p class="lead text-muted">Inga uppgifter hittades med valda filter.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once "include/footer.php"; ?>
