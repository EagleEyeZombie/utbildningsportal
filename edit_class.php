<?php
require_once "include/header.php";

// ---------------------------------------------------------
// SÄKERHET & BEHÖRIGHET (RBAC)
// ---------------------------------------------------------
// Vi kontrollerar att användaren är inloggad och är Admin eller Lärare (Nivå 5+).
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    header("Location: login.php");
    exit;
}

// ---------------------------------------------------------
// 1. VALIDERING AV ID (INGÅNGSDATA)
// ---------------------------------------------------------
// Vi måste veta VILKEN klass vi ska redigera. ID:t kommer via URL:en (GET).
// T.ex. edit_class.php?id=5
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Om ID saknas eller är ogiltigt, skicka tillbaka till listan.
    header("Location: admin_classes.php");
    exit;
}

$classId = $_GET['id'];

// Hämta klassens nuvarande data (Namn, Lärare) från databasen.
$class = $school_obj->getClassById($classId);

// Om klassen inte finns i databasen (t.ex. raderad nyss), avbryt.
if (!$class) {
    die("Klassen hittades inte.");
}

$msg = "";

// ---------------------------------------------------------
// 2. HANTERA FORMULÄR: UPPDATERA KLASSINFO
// ---------------------------------------------------------
// Detta körs när användaren ändrar klassnamn eller ansvarig lärare.
if (isset($_POST['update_class']) && verifyCsrfToken($_POST['csrf_token'])) {
    
    // Sanitering av indata
    $cName = cleanInput($_POST['c_name']);
    $cTeacher = cleanInput($_POST['c_teacher']);
    
    // Anropa modellen för att köra UPDATE SQL.
    // Detta ändrar raden i tabellen `classes`.
    $res = $school_obj->updateClass($classId, $cName, $cTeacher);
    
    if ($res['success']) {
        $msg = "<div class='alert alert-success'>Klassinfo uppdaterad!</div>";
        // VIKTIGT: Hämta klassobjektet på nytt så att formuläret visar de nya värdena direkt.
        $class = $school_obj->getClassById($classId); 
    } else {
        $msg = "<div class='alert alert-danger'>Fel: {$res['error']}</div>";
    }
}

// ---------------------------------------------------------
// 3. HANTERA FORMULÄR: LÄGG TILL ELEV (RELATION)
// ---------------------------------------------------------
// Här kopplar vi en "lös" elev till denna klass.
if (isset($_POST['add_student']) && verifyCsrfToken($_POST['csrf_token'])) {
    $studentId = $_POST['student_id'];
    
    // Anropa modellen. I bakgrunden körs en SQL UPDATE på `users`-tabellen:
    // "UPDATE users SET u_class_fk = $classId WHERE u_id = $studentId"
    if ($school_obj->addStudentToClass($studentId, $classId)) {
        $msg = "<div class='alert alert-success'>Elev tillagd!</div>";
    }
}

// ---------------------------------------------------------
// 4. HANTERA LÄNK: TA BORT ELEV (RELATION)
// ---------------------------------------------------------
// Här kopplar vi loss en elev från klassen (gör dem klasslösa igen).
if (isset($_GET['remove_student'])) {
    $studentId = $_GET['remove_student'];
    
    // Anropa modellen. SQL: "UPDATE users SET u_class_fk = NULL ..."
    // Vi raderar alltså inte eleven, vi bara nollställer relationen.
    if ($school_obj->removeStudentFromClass($studentId)) {
        $msg = "<div class='alert alert-warning'>Elev borttagen från klassen.</div>";
    }
}

// ---------------------------------------------------------
// 5. FÖRBERED DATA FÖR VYN
// ---------------------------------------------------------
// Innan vi ritar ut HTML, hämta färska listor från databasen.

// Hämta alla elever som JUST NU går i denna klass (för höger-tabellen).
$classStudents = $school_obj->getStudentsInClass($classId);

// Hämta alla elever som SAKNAR klass (för dropdown-listan "Lägg till").
// Detta är smart UX: Vi visar bara elever som faktiskt kan läggas till.
$availableStudents = $school_obj->getAvailableStudents();

// Hämta alla lärare (för dropdown-listan "Ansvarig lärare").
$allTeachers = $school_obj->getAllTeachers();

?>

<div class="container mt-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Redigera Klass: <?= htmlspecialchars($class['c_name']) ?></h1>
        
        <a href="admin_classes.php" class="btn btn-outline-dark fw-bold">
            <i class="bi bi-arrow-left"></i> Tillbaka
        </a>
    </div>

    <?= $msg ?>

    <div class="row">
        <div class="col-md-4">
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-dark fw-bold">Inställningar</div>
                <div class="card-body">
                    <form action="" method="POST">
                        <?= csrfInput() ?>
                        <div class="mb-3">
                            <label for="c_name" class="form-label">Namn <small class="text-muted">(T.ex. 8A, Grupp Röd)</small></label>
                            <input type="text" id="c_name" name="c_name" class="form-control" value="<?= htmlspecialchars($class['c_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="c_teacher" class="form-label">Lärare</label>
                            <select id="c_teacher" name="c_teacher" class="form-select">
                                <option value="">-- Ingen --</option>
                                <?php foreach ($allTeachers as $t): ?>
                                    <option value="<?= $t['u_id'] ?>" <?= ($t['u_id'] == $class['c_teacher_fk']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t['u_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="update_class" class="btn btn-primary w-100">Spara ändringar</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-dark fw-bold">Lägg till elev</div>
                <div class="card-body">
                    <form action="" method="POST">
                        <?= csrfInput() ?>
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Välj elev (utan klass)</label>
                            <select id="student_id" name="student_id" class="form-select" required>
                                <option value="" disabled selected>-- Välj elev --</option>
                                <?php foreach ($availableStudents as $s): ?>
                                    <option value="<?= $s['u_id'] ?>">
                                        <?= htmlspecialchars($s['u_fname'] . ' ' . $s['u_lname']) ?> (<?= $s['u_name'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="add_student" class="btn btn-success w-100">Lägg till i klassen</button>
                    </form>
                    <div class="text-muted small mt-2">Endast elever som inte redan tillhör en klass visas här.</div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Elever i klassen (<?= count($classStudents) ?>)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Namn</th>
                                    <th>Användarnamn</th>
                                    <th>Nivå / XP</th>
                                    <th class="text-end">Åtgärd</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($classStudents) > 0): ?>
                                    <?php foreach ($classStudents as $student): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($student['u_fname'] . ' ' . $student['u_lname']) ?></td>
                                            <td><?= htmlspecialchars($student['u_name']) ?></td>
                                            <td><span class="badge bg-secondary">Lvl <?= $student['u_level'] ?></span> <small class="text-muted"><?= $student['u_xp'] ?> XP</small></td>
                                            
                                            <td class="text-end">
                                                <a href="edit_class.php?id=<?= $classId ?>&remove_student=<?= $student['u_id'] ?>" class="btn btn-sm btn-outline-danger" title="Ta bort från klass">
                                                    <i class="bi bi-person-dash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">Inga elever i klassen än.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "include/footer.php"; ?>
