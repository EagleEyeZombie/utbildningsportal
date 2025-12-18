<?php
require_once "include/header.php";

// ---------------------------------------------------------
// SÄKERHETSVAKT (RBAC) - FLÖDE C
// ---------------------------------------------------------
// 1. Autentisering: Är användaren inloggad?
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    // 2. Auktorisering: Har användaren rätt nivå? (5 = Lärare)
    header("Location: login.php");
    exit;
}

$errorMsg = "";
//Flöde C. Steg 5.1.1.
// Här initierar vi variabler för feedback till användaren (UI/UX).
$successMsg = "";

// ---------------------------------------------------------
// 1. HANTERA FORMULÄR: SKAPA KLASS (CREATE)
// ---------------------------------------------------------
// Vi lyssnar efter ett POST-anrop. Detta sker när knappen "Spara Klass" trycks ner.

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_class'])) {
    
    // Flöde C. Steg 3.1
    // SÄKERHET (CSRF): Vi verifierar att anropet kommer från vårt eget formulär.
    // Detta förhindrar att andra sidor postar data till vår server i smyg.
    if (verifyCsrfToken($_POST['csrf_token'])) {
        
        // Flöde C. Steg 3.2
        // SÄKERHET (Sanitering): Vi tvättar indatan för att förhindra XSS och SQL Injection.
        $cName = cleanInput($_POST['c_name']);
        $cTeacher = cleanInput($_POST['c_teacher']);
        
        // Flöde C. Steg 3.3
        // ANROPA MODELLEN: Vi skickar den tvättade datan till School-klassen som sköter SQL-frågan.
        $result = $school_obj->createClass($cName, $cTeacher);
        
        if ($result['success']) {
            // //Flöde C. Steg 5.1.2.
            // Feedback: Om allt gick bra visar vi ett grönt meddelande.
            $successMsg = "Klassen <strong>$cName</strong> har skapats!";
        } else {
            // Felhantering: Om databasen protesterar visar vi felet.
            $errorMsg = "Fel vid skapande: " . $result['error'];
        }
    } else {
        $errorMsg = "Ogiltig säkerhetstoken.";
    }
}

// ---------------------------------------------------------
// 2. HANTERA ÅTGÄRD: RADERA KLASS (DELETE)
// ---------------------------------------------------------
// Här lyssnar vi på URL-parametrar (GET). T.ex. admin_classes.php?delete=5

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    // SÄKERHET: is_numeric() säkerställer att ID:t faktiskt är en siffra.
    $delId = $_GET['delete'];
    
    // Anropa modellen för att utföra raderingen.
    $result = $school_obj->deleteClass($delId);
    
    if ($result['success']) {
        $successMsg = "Klassen har tagits bort.";
    } else {
        $errorMsg = "Kunde inte ta bort klassen.";
    }
}

// ---------------------------------------------------------
// 3. HÄMTA DATA & FILTRERING (READ)
// ---------------------------------------------------------

// --- FILTERLOGIK (NYTT) ---
// Vi kollar om användaren valt att filtrera listan på en specifik lärare.
$filterTeacher = (isset($_GET['teacher']) && $_GET['teacher'] !== 'all') ? $_GET['teacher'] : null;

// Flöde C. Steg 5.2. Hämta data
// Vi hämtar klasslistan från modellen. Om ett filter är valt skickas det med.
// Modellen anpassar då SQL-frågan (WHERE c_teacher_fk = ?).
$allClasses = $school_obj->getAllClasses($filterTeacher); // Skickar med filtret här

// Flöde C. Steg 1.1: Inmatning (Frontend & Förberedelse) Hämta lärare: Variabeln $allTeachers fylls genom funktionen $school_obj->getAllTeachers().
// Vi behöver listan på lärare BÅDE för dropdown-menyn i "Skapa klass" OCH för filtret.
$allTeachers = $school_obj->getAllTeachers();

?>

<div class="container mt-5">
    
    <div class="d-flex flex-column flex-md-row align-items-center mb-4 gap-3 text-center text-md-start">
        <a href="admin_dashboard.php" class="btn btn-outline-dark fw-bold">
            <i class="bi bi-arrow-left"></i> Tillbaka till Adminpanelen
        </a>
        <h1 class="m-0" style="font-family: 'Cinzel Decorative', serif; color: var(--accent-gold);">
            <i class="bi bi-people-fill d-none d-md-inline me-2"></i>Hantera Klasser
        </h1>
    </div>

    <?php if ($errorMsg): ?><div class="alert alert-danger"><?= $errorMsg ?></div><?php endif; ?>
    <?php if ($successMsg): ?><div class="alert alert-success"><?= $successMsg ?></div><?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-dark">
            <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Skapa ny klass</h5>
        </div>
        <div class="card-body">
            <form action="" method="POST" class="row g-3 align-items-end">
                <?= csrfInput() ?>
                
                <div class="col-12 col-md-5">
                    <label for="c_name" class="form-label">Klassnamn</label>
                    <input type="text" name="c_name" id="c_name" class="form-control" placeholder="T.ex. 8A, Grupp Röd..." required>
                </div>
                
                <div class="col-12 col-md-4">
                    <label for="c_teacher" class="form-label">Ansvarig Lärare</label>
                    <select name="c_teacher" id="c_teacher" class="form-select">
<option value="">-- Välj lärare (Valfritt) --</option>
                        <?php foreach ($allTeachers as $t): ?>
                            <option value="<?= $t['u_id'] ?>"><?= htmlspecialchars($t['u_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" name="create_class" class="btn btn-primary w-100">Spara Klass</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body bg-light">
            <form action="admin_classes.php" method="GET" class="row g-3 align-items-center">
                <div class="col-md-9">
                    <label for="filter_teacher" class="form-label fw-bold">Filtrera på lärare:</label>
                    <select name="teacher" id="filter_teacher" class="form-select" onchange="this.form.submit()">
                        <option value="all">Alla Klasser</option>
                        
                        <option value="missing" <?php echo ($filterTeacher === 'missing') ? 'selected' : ''; ?> style="color: red;">Klasser utan lärare (Raderad)</option>
                        
                        <option value="" disabled>---</option>
                        <?php foreach ($allTeachers as $t): ?>
                            <option value="<?= $t['u_id'] ?>" <?php echo ($filterTeacher == $t['u_id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($t['u_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <a href="admin_classes.php" class="btn btn-outline-dark w-100 mt-4">Rensa filter</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body p-0">
            <div class="">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Klassnamn</th>
                            <th>Lärare</th>
                            <th class="text-center">Antal Elever</th>
                            <th class="text-end">Åtgärd</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($allClasses) > 0): ?>
                            <?php foreach ($allClasses as $class): ?>
                                <tr>
                                    <td data-label="Klassnamn"><strong><?= htmlspecialchars($class['c_name']) ?></strong></td>
                                    
                                    <td data-label="Lärare">
                                        <?php if ($class['teacher_name']): ?>
                                            <span class="badge bg-info text-dark"><?= htmlspecialchars($class['teacher_name']) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Saknar lärare</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td data-label="Antal Elever" class="text-center">
                                        <span class="badge rounded-pill bg-secondary"><?= $class['student_count'] ?></span>
                                    </td>
                                    
                                    <td data-label="Åtgärd" class="text-end">
                                        <a href="edit_class.php?id=<?= $class['c_id'] ?>" class="btn btn-sm btn-primary me-1">
                                            Redigera / Elever
                                        </a>
                                        <a href="admin_classes.php?delete=<?= $class['c_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Är du säker? Eleverna i klassen kommer inte raderas, men de blir klasslösa.');">
                                            Ta bort
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted">Inga klasser hittades med valda filter.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php require_once "include/footer.php"; ?>
