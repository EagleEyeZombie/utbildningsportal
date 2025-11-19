<?php
require_once "include/header.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    header("Location: login.php");
    exit;
}

// Hämta data för filter
$allTypes = $task_obj->getAllTypes();
$allLevels = $task_obj->getAllLevels();
$allGenres = $task_obj->getAllGenres(); // <-- NY
$stmt = $pdo->query("SELECT u_id, u_name FROM users WHERE u_role_fk >= 5 ORDER BY u_name");
$allTeachers = $stmt->fetchAll();

// --- FILTERLOGIK ---
$currentUserId = $_SESSION['user_id'];
$filterTeacher = (isset($_GET['teacher']) && $_GET['teacher'] !== 'all') ? (int)$_GET['teacher'] : null;
$filterType = (isset($_GET['type']) && $_GET['type'] !== 'all') ? (int)$_GET['type'] : null;
$filterLevel = (isset($_GET['level']) && $_GET['level'] !== 'all') ? (int)$_GET['level'] : null;
$filterGenre = (isset($_GET['genre']) && $_GET['genre'] !== 'all') ? (int)$_GET['genre'] : null; // <-- NY

// Hämta data (med alla filter)
$allTasks = $task_obj->getTasksFiltered($filterTeacher, $filterType, $filterLevel, null, $filterGenre);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Hantera Uppgifter</h1>
        <a href="admin_create_task.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Skapa ny uppgift
        </a>
    </div>

    <!-- FILTERFORMULÄR -->
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
                
                <!-- NYTT GENRE-FÄLT -->
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
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrera</button>
                </div>
                <div class="col-md-1">
                    <a href="admin_tasks.php" class="btn btn-outline-secondary w-100" title="Nollställ">X</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <?php if (count($allTasks) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Titel</th>
                                <th>Typ</th>
                                <th>Genre</th> <!-- NY -->
                                <th>Nivå</th>
                                <th>Skapare</th>
                                <th>Åtgärd</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allTasks as $task): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($task['t_name']) ?></strong></td>
                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($task['type_name']) ?></span></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($task['genre_name']) ?></span></td> <!-- NY -->
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($task['level_name']) ?></span></td>
                                    <td>
                                        <?= htmlspecialchars($task['teacher_name']) ?>
                                        <?php if ($task['t_teacher_fk'] == $currentUserId): ?>
                                            <span class="badge bg-success">Du</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="admin_edit_task.php?id=<?= $task['t_id'] ?>" class="btn btn-sm btn-primary">Redigera</a>
                                        <a href="delete_task.php?id=<?= $task['t_id'] ?>" class="btn btn-sm btn-danger">Ta bort</a>
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
