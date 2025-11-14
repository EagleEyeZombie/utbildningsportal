<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    header("Location: login.php");
    exit;
}
// ---------------------

// --- NY FILTERLOGIK ---
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all'; // 'all' är standard
$currentUserId = $_SESSION['user_id'];
$pageTitle = "Hantera Alla Uppgifter"; // Standardtitel

if ($filter === 'my') {
    // Hämta bara mina uppgifter
    $allTasks = $task_obj->getTasksByTeacher($currentUserId);
    $pageTitle = "Mina Skapade Uppgifter";
} else {
    // Hämta alla uppgifter
    $allTasks = $task_obj->getAllTasks();
}
// ---------------------
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $pageTitle ?></h1>
        <a href="admin_create_task.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Skapa ny uppgift
        </a>
    </div>

    <div class="mb-3">
        <a href="admin_tasks.php?filter=all" class="btn <?php echo ($filter === 'all') ? 'btn-primary' : 'btn-outline-primary'; ?>">
            Visa Alla
        </a>
        <a href="admin_tasks.php?filter=my" class="btn <?php echo ($filter === 'my') ? 'btn-primary' : 'btn-outline-primary'; ?>">
            Visa Bara Mina
        </a>
    </div>
    <div class="card shadow">
        <div class="card-body">
            <?php if (count($allTasks) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Titel</th>
                                <th>Typ</th>
                                <th>Nivå</th>
                                <th>Skapad av</th>
                                <th>Åtgärd</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allTasks as $task): ?>
                                <tr>
                                    <td><?= htmlspecialchars($task['t_id']) ?></td>
                                    <td><strong><?= htmlspecialchars($task['t_name']) ?></strong></td>
                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($task['type_name']) ?></span></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($task['level_name']) ?></span></td>
                                    
                                    <td>
                                        <?= htmlspecialchars($task['teacher_name']) ?>
                                        <?php if ($task['t_teacher_fk'] == $currentUserId): ?>
                                            <span class="badge bg-success">Du</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">Redigera</a>
                                        <a href="#" class="btn btn-sm btn-danger">Ta bort</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <p class="lead text-muted">
                        <?php if ($filter === 'my'): ?>
                            Du har inte skapat några uppgifter än.
                        <?php else: ?>
                            Inga uppgifter skapade än.
                        <?php endif; ?>
                    </p>
                    <a href="admin_create_task.php" class="btn btn-primary">Skapa den första uppgiften</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="admin_dashboard.php" class="btn btn-outline-secondary">&laquo; Tillbaka till Adminpanel</a>
    </div>
</div>

<?php
require_once "include/footer.php";
?>
