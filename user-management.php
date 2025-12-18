<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
// Här startar vi med en viktig behörighetskontroll (RBAC - Role Based Access Control).
// 1. isset($_SESSION['user_id']): Kollar om användaren överhuvudtaget är inloggad.
// 2. $_SESSION['role_level'] < 5: Kollar om användaren har tillräcklig behörighet.
//    Vi har bestämt att nivå 5 och uppåt är administratörer/lärare.
// Om något av detta falerar, skickar vi dem direkt till login-sidan med header().
// exit; är kritiskt för att stoppa resten av koden från att köras (så ingen info läcker).
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    header("Location: login.php");
    exit;
}

// 1. HÄMTA PARAMETRAR (Input Hantering)
// Här hämtar vi inställningar från URL:en (GET-request).
// Detta gör att vi kan dela en länk (t.ex. "sida 2, sökning 'Anders'") och få exakt samma vy.

// Ternary Operator (villkor ? sant : falskt) används för att sätta standardvärden.
// cleanInput() används på ALLT som kommer utifrån för att skydda mot XSS (Cross Site Scripting).
$search   = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
$role     = isset($_GET['role']) ? cleanInput($_GET['role']) : 'all'; // Standard: Visa alla roller
$limit    = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;        // Standard: 20 rader per sida
$page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;           // Standard: Sida 1
$sortCol  = isset($_GET['sort']) ? cleanInput($_GET['sort']) : 'u_name'; // Standard: Sortera på användarnamn
$sortDir  = isset($_GET['dir']) ? cleanInput($_GET['dir']) : 'ASC';      // Standard: Stigande ordning (A-Ö)

// Validering av limit: Förhindrar att någon manipulerar URL:en till ?limit=999999 och kraschar servern.
if (!in_array($limit, [20, 40, 80])) $limit = 20;

// Paginering-logik: Beräkna "Offset".
// Offset berättar för databasen hur många rader den ska hoppa över.
// Exempel: Sida 1 -> (1-1)*20 = 0 (Hoppa över 0, börja på rad 1).
// Exempel: Sida 2 -> (2-1)*20 = 20 (Hoppa över 20, börja på rad 21).
$offset = ($page - 1) * $limit;

// 2. HÄMTA DATA (Kommunikation med Modellen)
// Hämta alla roller för dropdown-filtret (Admin, Lärare, Elev).
$allRoles = $pdo->query("SELECT * FROM roles")->fetchAll();

// Hämta den faktiska listan med användare baserat på våra filter.
// Här skickar vi in offset och limit så att databasen bara hämtar de 20 rader vi faktiskt behöver se.
// Detta är "Server-side pagination" vilket är mycket snabbare än att hämta allt och filtrera med PHP.
$users = $user_obj->getUsersFiltered($search, $role, $sortCol, $sortDir, $limit, $offset);

// Hämta TOTALA antalet användare som matchar sökningen (utan limit).
// Detta behövs för att veta hur många sidknappar vi ska rita ut (1, 2, 3...).
$totalUsers = $user_obj->getUsersCountFiltered($search, $role);

// Beräkna antal sidor: ceil() avrundar uppåt.
// Exempel: 21 användare / 20 per sida = 1.05 -> Avrundas till 2 sidor.
$totalPages = ceil($totalUsers / $limit);

// Hjälpfunktion för att skapa sorterings-länkar i tabellrubrikerna.
// Denna funktion ser till att om vi redan sorterar på 'Namn ASC', så blir nästa klick 'Namn DESC'.
// Den ser också till att vi behåller nuvarande sökning ($search) och filter ($role) när vi sorterar.
function sortLink($displayText, $dbCol, $currentCol, $currentDir, $search, $role, $limit) {
    // Logik för att byta riktning (Toggle)
    $newDir = ($dbCol === $currentCol && $currentDir === 'ASC') ? 'DESC' : 'ASC';
    
    // Visa pil-ikon om denna kolumn är den aktiva sorteringen
    $icon = '';
    if ($dbCol === $currentCol) {
        $icon = ($currentDir === 'ASC') ? ' <i class="bi bi-caret-up-fill"></i>' : ' <i class="bi bi-caret-down-fill"></i>';
    }
    
    // Bygg URL:en
    $url = "?sort=$dbCol&dir=$newDir&search=$search&role=$role&limit=$limit&page=1";
    return "<a href='$url' class='text-dark text-decoration-none fw-bold'>$displayText $icon</a>";
}
?>

<div class="container mt-5 mb-5">
    
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex flex-column flex-md-row align-items-center gap-3 text-center text-md-start">
            <a href="admin_dashboard.php" class="btn btn-outline-dark fw-bold">
                <i class="bi bi-arrow-left"></i> Tillbaka till Adminpanelen
            </a>
            <h1 class="m-0" style="font-family: 'Cinzel Decorative', serif; color: var(--accent-gold);">
                <i class="bi bi-people d-none d-md-inline me-2"></i>Hantera Användare
            </h1>
        </div>
        <a href="register.php" class="btn btn-success col-12 col-lg-auto">
            <i class="bi bi-person-plus"></i> Skapa ny användare
        </a>
    </div>

    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 'success'): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle"></i> Användaren har tagits bort.</div>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] == 'self_delete'): ?>
        <div class="alert alert-danger">Du kan inte radera ditt eget konto!</div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body bg-light">
            <form action="user-management.php" method="GET" class="row g-2 align-items-center">
                
                <div class="col-12 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Sök namn..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>

                <div class="col-12 col-lg-3">
                    <select name="role" class="form-select" onchange="this.form.submit()">
                        <option value="all" <?= $role == 'all' ? 'selected' : '' ?>>Alla Roller</option>
                        <?php foreach($allRoles as $r): ?>
                            <option value="<?= $r['r_id'] ?>" <?= $role == $r['r_id'] ? 'selected' : '' ?>>
                                <?= $r['r_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-lg-2">
                    <select name="limit" class="form-select" onchange="this.form.submit()">
                        <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20 / sida</option>
                        <option value="40" <?= $limit == 40 ? 'selected' : '' ?>>40 / sida</option>
                        <option value="80" <?= $limit == 80 ? 'selected' : '' ?>>80 / sida</option>
                    </select>
                </div>

                <div class="col-12 col-lg-3 text-lg-end d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filtrera</button>
                    <a href="user-management.php" class="btn btn-outline-dark flex-grow-1 fw-bold">Rensa</a>
                </div>

                <input type="hidden" name="sort" value="<?= $sortCol ?>">
                <input type="hidden" name="dir" value="<?= $sortDir ?>">
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white border-0 py-3">
            <span class="text-muted">Visar <strong><?= count($users) ?></strong> av <strong><?= $totalUsers ?></strong> användare</span>
        </div>
        <div class="card-body p-0">
            <div class=""> 
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="d-none d-md-table-cell"><?= sortLink('Användarnamn', 'u_name', $sortCol, $sortDir, $search, $role, $limit) ?></th>
                            <th><?= sortLink('Namn', 'u_fname', $sortCol, $sortDir, $search, $role, $limit) ?></th>
                            
                            <th><?= sortLink('Klass', 'c_name', $sortCol, $sortDir, $search, $role, $limit) ?></th>
                            
                            <th class="d-none d-md-table-cell"><?= sortLink('E-post', 'u_email', $sortCol, $sortDir, $search, $role, $limit) ?></th>
                            <th><?= sortLink('Roll', 'r_name', $sortCol, $sortDir, $search, $role, $limit) ?></th>
                            <th class="d-none d-md-table-cell">XP / Takt</th>
                            <th class="text-end">Åtgärd</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $user): ?>
                                <?php 
                                    // Sätter olika färger på "Badges" beroende på roll för bättre UX
                                    $roleClass = 'bg-secondary';
                                    if ($user['r_name'] == 'Admin') $roleClass = 'bg-danger';
                                    if ($user['r_name'] == 'Lärare') $roleClass = 'bg-primary';
                                    if ($user['r_name'] == 'Elev') $roleClass = 'bg-success';
                                ?>
                                <tr>
                                    <td data-label="Användarnamn" class="fw-bold d-none d-md-table-cell"><?= htmlspecialchars($user['u_name']) ?></td>
                                    
                                    <td data-label="Namn"><?= htmlspecialchars($user['u_fname'] . ' ' . $user['u_lname']) ?></td>
                                    
                                    <td data-label="Klass">
                                        <?php if (!empty($user['c_name'])): ?>
                                            <span class="badge bg-info text-dark"><?= htmlspecialchars($user['c_name']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <td data-label="E-post" class="d-none d-md-table-cell"><?= htmlspecialchars($user['u_email']) ?></td>
                                    
                                    <td data-label="Roll"><span class="badge <?= $roleClass ?>"><?= htmlspecialchars($user['r_name']) ?></span></td>
                                    
                                    <td data-label="XP / Takt" class="d-none d-md-table-cell">
                                        <small class="text-muted">
                                            <?= $user['u_xp'] ?> XP 
                                            <?php if(isset($user['ps_name']) && $user['ps_name'] != 'Normal'): ?>
                                                <br><span class="text-warning fw-bold"><?= htmlspecialchars($user['ps_name']) ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    
                                    <td data-label="Åtgärd" class="text-end">
                                        <a href="edit-user.php?uid=<?= $user['u_id'] ?>" class="btn btn-sm btn-primary me-1">
                                            <i class="bi bi-pencil"></i> Redigera
                                        </a>
                                        
                                        <form action="delete-user.php" method="POST" class="d-inline" onsubmit="return confirm('Är du säker på att du vill ta bort denna användare?');">
                                            
                                            <?= csrfInput() ?>
                                            
                                            <input type="hidden" name="uid" value="<?= $user['u_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Radera
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">Inga användare hittades.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white d-flex justify-content-center py-3">
            <nav>
                <ul class="pagination mb-0 flex-wrap justify-content-center gap-1">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= $search ?>&role=<?= $role ?>&limit=<?= $limit ?>&sort=<?= $sortCol ?>&dir=<?= $sortDir ?>">Föregående</a>
                    </li>
                    
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= $search ?>&role=<?= $role ?>&limit=<?= $limit ?>&sort=<?= $sortCol ?>&dir=<?= $sortDir ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= $search ?>&role=<?= $role ?>&limit=<?= $limit ?>&sort=<?= $sortCol ?>&dir=<?= $sortDir ?>">Nästa</a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once "include/footer.php"; ?>
