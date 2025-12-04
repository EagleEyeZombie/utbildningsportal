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

// HÄR LÄGGER VI TILL DEN SAKNADE VARIABELN:
$currentUserId = $_SESSION['user_id'];

// HÄMTA PARAMETRAR FRÅN URL
// ÄNDRAT: Tog bort (int) för teacher så vi kan ta emot 'missing'
$filterTeacher = (isset($_GET['teacher']) && $_GET['teacher'] !== 'all') ? $_GET['teacher'] : null;
$filterType = (isset($_GET['type']) && $_GET['type'] !== 'all') ? (int)$_GET['type'] : null;
$filterLevel = (isset($_GET['level']) && $_GET['level'] !== 'all') ? (int)$_GET['level'] : null;
$filterGenre = (isset($_GET['genre']) && $_GET['genre'] !== 'all') ? (int)$_GET['genre'] : null;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20; 
$sortCol = isset($_GET['sort']) ? cleanInput($_GET['sort']) : 't_created'; // Default: Skapad
$sortDir = isset($_GET['dir']) ? cleanInput($_GET['dir']) : 'DESC'; // Default: Nyast först

if (!in_array($limit, [20, 40, 80])) $limit = 20;
$offset = ($page - 1) * $limit;

// 2. HÄMTA DATA
$allTypes = $task_obj->getAllTypes();
$allLevels = $task_obj->getAllLevels();
$allGenres = $task_obj->getAllGenres();
$stmt = $pdo->query("SELECT u_id, u_name FROM users WHERE u_role_fk >= 5 ORDER BY u_name");
$allTeachers = $stmt->fetchAll();

// Hämta alla lärare (för att kunna byta ägare)
// FIX: Vi måste joina med roles för att kolla nivån (r_level), inte ID:t
$stmt = $pdo->query("SELECT users.u_id, users.u_name 
                     FROM users 
                     JOIN roles ON users.u_role_fk = roles.r_id 
                     WHERE roles.r_level >= 5 
                     ORDER BY users.u_name ASC");
$allTeachers = $stmt->fetchAll();

$allTasks = $task_obj->getTasksFiltered($filterTeacher, $filterType, $filterLevel, null, $filterGenre, $sortCol, $sortDir, $limit, $offset);
$totalTasks = $task_obj->getTasksCountFiltered($filterTeacher, $filterType, $filterLevel, null, $filterGenre);
$totalPages = ceil($totalTasks / $limit);

function sortLink($displayText, $dbCol, $currentCol, $currentDir, $teacher, $type, $level, $genre, $limit) {
    $newDir = ($dbCol === $currentCol && $currentDir === 'ASC') ? 'DESC' : 'ASC';
    $icon = '';
    if ($dbCol === $currentCol) {
        $icon = ($currentDir === 'ASC') ? ' <i class="bi bi-caret-up-fill"></i>' : ' <i class="bi bi-caret-down-fill"></i>';
    }
    $url = "?sort=$dbCol&dir=$newDir&page=1&limit=$limit";
    if ($teacher) $url .= "&teacher=$teacher";
    if ($type) $url .= "&type=$type";
    if ($level) $url .= "&level=$level";
    if ($genre) $url .= "&genre=$genre";
    
    return "<a href='$url' class='text-dark text-decoration-none fw-bold'>$displayText $icon</a>";
}
?>

<div class="container mt-5 mb-5">
    
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex flex-column flex-md-row align-items-center gap-3 text-center text-md-start">
            <a href="admin_dashboard.php" class="btn btn-outline-dark fw-bold">
                <i class="bi bi-arrow-left"></i> Tillbaka till Adminpanelen
            </a>
            <h1 class="m-0">
                <i class="bi bi-pencil-square d-none d-md-inline me-2"></i>Hantera Uppgifter
            </h1>
        </div>
        <a href="admin_create_task.php" class="btn btn-success col-12 col-lg-auto">
            <i class="bi bi-plus-circle"></i> Skapa ny uppgift
        </a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle"></i> Uppgiften har raderats.</div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body bg-light">
            <form action="admin_tasks.php" method="GET" class="row g-3 align-items-end">
                
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="teacher" class="form-label">Skapare</label>
                    <select name="teacher" id="teacher" class="form-select" onchange="this.form.submit()">
                        <option value="all">Alla Lärare</option>
                        <option value="<?= $_SESSION['user_id'] ?>" <?php echo ($filterTeacher == $_SESSION['user_id']) ? 'selected' : ''; ?>>Bara Mina</option>
                        <option value="" disabled>---</option>
                        <?php foreach ($allTeachers as $t): ?>
                            <option value="<?= $t['u_id'] ?>" <?php echo ($filterTeacher == $t['u_id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($t['u_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label for="type" class="form-label">Typ</label>
                    <select name="type" id="type" class="form-select" onchange="this.form.submit()">
                        <option value="all">Alla</option>
                        <?php foreach ($allTypes as $type): ?>
                            <option value="<?= $type['tt_id'] ?>" <?php echo ($filterType == $type['tt_id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($type['tt_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-6 col-md-3 col-lg-2">
                    <label for="genre" class="form-label">Genre</label>
                    <select name="genre" id="genre" class="form-select" onchange="this.form.submit()">
                        <option value="all">Alla</option>
                        <?php foreach ($allGenres as $g): ?>
                            <option value="<?= $g['g_id'] ?>" <?php echo ($filterGenre == $g['g_id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($g['g_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label for="level" class="form-label">Nivå</label>
                    <select name="level" id="level" class="form-select" onchange="this.form.submit()">
                        <option value="all">Alla</option>
                        <?php foreach ($allLevels as $level): ?>
                            <option value="<?= $level['tl_id'] ?>" <?php echo ($filterLevel == $level['tl_id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($level['tl_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label for="limit" class="form-label">Visa</label>
                    <select name="limit" id="limit" class="form-select" onchange="this.form.submit()">
                        <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20 / sida</option>
                        <option value="40" <?= $limit == 40 ? 'selected' : '' ?>>40 / sida</option>
                        <option value="80" <?= $limit == 80 ? 'selected' : '' ?>>80 / sida</option>
                    </select>
                </div>
                
                <div class="col-12 col-lg-1 text-lg-end d-flex gap-2">
                    <a href="admin_tasks.php" class="btn btn-outline-dark fw-bold w-100 d-flex align-items-center justify-content-center" title="Rensa">Rensa</a>
                </div>

                <input type="hidden" name="sort" value="<?= $sortCol ?>">
                <input type="hidden" name="dir" value="<?= $sortDir ?>">
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white border-0 py-3">
            <span class="text-muted">Visar <strong><?= count($allTasks) ?></strong> av <strong><?= $totalTasks ?></strong> uppgifter</span>
        </div>
        <div class="card-body p-0">
            <?php if (count($allTasks) > 0): ?>
                <div class="">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><?= sortLink('Skapad', 't_created', $sortCol, $sortDir, $filterTeacher, $filterType, $filterLevel, $filterGenre, $limit) ?></th>
                                <th><?= sortLink('Titel', 't_name', $sortCol, $sortDir, $filterTeacher, $filterType, $filterLevel, $filterGenre, $limit) ?></th>
                                <th><?= sortLink('Typ', 'type_name', $sortCol, $sortDir, $filterTeacher, $filterType, $filterLevel, $filterGenre, $limit) ?> / <?= sortLink('Genre', 'genre_name', $sortCol, $sortDir, $filterTeacher, $filterType, $filterLevel, $filterGenre, $limit) ?></th>
                                <th><?= sortLink('Nivå', 'level_name', $sortCol, $sortDir, $filterTeacher, $filterType, $filterLevel, $filterGenre, $limit) ?> / <?= sortLink('XP', 't_xp', $sortCol, $sortDir, $filterTeacher, $filterType, $filterLevel, $filterGenre, $limit) ?></th>
                                <th><?= sortLink('Skapare', 'teacher_name', $sortCol, $sortDir, $filterTeacher, $filterType, $filterLevel, $filterGenre, $limit) ?></th>
                                <th class="text-end">Åtgärd</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allTasks as $task): ?>
                                <tr>
                                    <td data-label="Skapad" class="text-muted small">
                                        <?= $task['t_created'] ? date('Y-m-d', strtotime($task['t_created'])) : '-' ?>
                                    </td>
                                    
                                    <td data-label="Titel"><strong><?= htmlspecialchars($task['t_name']) ?></strong></td>
                                    
                                    <td data-label="Typ / Genre">
                                        <span class="badge bg-secondary"><?= htmlspecialchars($task['type_name']) ?></span>
                                        <?php if(!empty($task['genre_name'])): ?>
                                            <span class="badge bg-dark border"><?= htmlspecialchars($task['genre_name']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td data-label="Nivå / XP">
                                        <span class="badge bg-primary">Lvl <?= $task['tl_level'] ?></span> 
                                        <small class="text-muted"><?= $task['t_xp'] ?> XP</small>
                                    </td>
                                    
                                    <td data-label="Skapare">
                                        <?= htmlspecialchars($task['teacher_name'] ?? 'Raderad lärare') ?>
                                        <?php if ($task['t_teacher_fk'] == $currentUserId): ?>
                                        <span class="badge bg-success">Du</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td data-label="Åtgärd" class="text-end">
                                        <a href="admin_edit_task.php?id=<?= $task['t_id'] ?>" class="btn btn-sm btn-primary me-1">
                                            Redigera
                                        </a>
                                        <form action="delete_task.php" method="POST" class="d-inline" onsubmit="return confirm('Är du säker? All statistik för denna uppgift kommer också försvinna.');">
                                            <?= csrfInput() ?>
                                            <input type="hidden" name="id" value="<?= $task['t_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Ta bort
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($totalPages > 1): ?>
                <div class="card-footer bg-white d-flex justify-content-center py-3">
                    <nav>
                        <ul class="pagination mb-0 flex-wrap justify-content-center gap-1">
                            <?php 
                                $baseUrl = "?sort=$sortCol&dir=$sortDir&limit=$limit";
                                if ($filterTeacher) $baseUrl .= "&teacher=$filterTeacher";
                                if ($filterType) $baseUrl .= "&type=$filterType";
                                if ($filterLevel) $baseUrl .= "&level=$filterLevel";
                                if ($filterGenre) $baseUrl .= "&genre=$filterGenre";
                            ?>
                            
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $page - 1 ?>">Föregående</a>
                            </li>
                            
                            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= $baseUrl ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $page + 1 ?>">Nästa</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-5">
                    <p class="lead text-muted">Inga uppgifter hittades med valda filter.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once "include/footer.php"; ?>
