<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    header("Location: login.php");
    exit;
}
// ---------------------

// Hämta alla uppgifter med hjälp av vår nya klass
$allTasks = $task_obj->getAllTasks();
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Hantera Uppgifter</h1>
        <a href="admin_create_task.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Skapa ny uppgift
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
                                    <td><?= htmlspecialchars($task['teacher_name']) ?></td>
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
                    <p class="lead text-muted">Inga uppgifter skapade än.</p>
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
