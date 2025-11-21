<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 10) { // Endast Admin (Level 10)
    header("Location: index.php");
    exit;
}

// --- PAGINERING & SORTERING ---
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20; // Default 20
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'u_name';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Beräkna offset
$offset = ($page - 1) * $limit;

// Hämta totalt antal användare
$totalUsers = $user_obj->getTotalUsers();
$totalPages = ceil($totalUsers / $limit);

// Hämta användare för aktuell sida
$userList = $user_obj->getUsersPaginated($limit, $offset, $sort, $order);

// Funktion för sorteringslänk
function sortLink($col, $label, $currentSort, $currentOrder, $currentLimit) {
    $newOrder = ($currentSort === $col && $currentOrder === 'ASC') ? 'DESC' : 'ASC';
    $icon = '';
    if ($currentSort === $col) {
        $icon = ($currentOrder === 'ASC') ? ' <i class="bi bi-arrow-up"></i>' : ' <i class="bi bi-arrow-down"></i>';
    }
    return "<a href='?sort=$col&order=$newOrder&limit=$currentLimit' style='color: #0d6efd; text-decoration: none; font-weight: bold;'>$label $icon</a>";
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 style="font-family: 'Cinzel Decorative', serif; color: var(--success-green);">Hantera Användare</h1>
        <a href="register.php" class="btn btn-success"><i class="bi bi-person-plus"></i> Skapa ny användare</a>
    </div>

    <!-- INSTÄLLNINGAR (Antal per sida) -->
    <div class="mb-3 d-flex justify-content-end">
        <form action="" method="GET" class="d-flex align-items-center">
            <label class="me-2" style="color: #333;">Visa:</label>
            <select name="limit" class="form-select form-select-sm me-2" onchange="this.form.submit()" style="width: auto;">
                <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                <option value="40" <?= $limit == 40 ? 'selected' : '' ?>>40</option>
                <option value="80" <?= $limit == 80 ? 'selected' : '' ?>>80</option>
            </select>
            <span style="color: #333;">per sida</span>
            <!-- Behåll sortering när man byter antal -->
            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
            <input type="hidden" name="order" value="<?= htmlspecialchars($order) ?>">
        </form>
    </div>

    <!-- ANVÄNDARLISTA -->
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;"><?= sortLink('u_name', 'Användarnamn', $sort, $order, $limit) ?></th>
                            <th style="width: 15%;"><?= sortLink('u_fname', 'Förnamn', $sort, $order, $limit) ?></th>
                            <th style="width: 15%;"><?= sortLink('u_lname', 'Efternamn', $sort, $order, $limit) ?></th>
                            <th style="width: 25%;"><?= sortLink('u_email', 'E-post', $sort, $order, $limit) ?></th>
                            <th style="width: 10%;"><?= sortLink('r_name', 'Roll', $sort, $order, $limit) ?></th>
                            <th style="width: 20%;" class="text-end" style="color: #333;">Åtgärd</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($userList) > 0): ?>
                            <?php foreach ($userList as $row): ?>
                                <?php 
                                    // Välj färg baserat på roll
                                    $roleClass = 'bg-secondary'; // Default grå
                                    switch($row['r_name']) {
                                        case 'Admin':
                                            $roleClass = 'bg-danger border border-danger-subtle'; // Röd
                                            break;
                                        case 'Lärare':
                                            $roleClass = 'bg-primary border border-primary-subtle'; // Blå
                                            break;
                                        case 'Elev':
                                            $roleClass = 'bg-success border border-success-subtle'; // Grön
                                            break;
                                    }
                                ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['u_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['u_fname']) ?></td>
                                    <td><?= htmlspecialchars($row['u_lname']) ?></td>
                                    <td><?= htmlspecialchars($row['u_email']) ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?= $roleClass ?>"><?= htmlspecialchars($row['r_name']) ?></span>
                                    </td>
                                    <td class="text-end">
                                        <a href="edit-user.php?uid=<?= $row['u_id'] ?>" class="btn btn-sm btn-outline-success me-1">
                                            <i class="bi bi-pencil"></i> Redigera
                                        </a>
                                        <a href="delete-user.php?uid=<?= $row['u_id'] ?>" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Radera
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4">Inga användare hittades.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PAGINERING -->
    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <!-- Föregående -->
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>&sort=<?= $sort ?>&order=<?= $order ?>">Föregående</a>
                </li>

                <!-- Sidor -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>&sort=<?= $sort ?>&order=<?= $order ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Nästa -->
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>&sort=<?= $sort ?>&order=<?= $order ?>">Nästa</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

</div>

<?php require_once "include/footer.php"; ?>
