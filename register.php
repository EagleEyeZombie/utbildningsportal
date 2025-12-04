<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id'])) {
    // Inte inloggad alls -> Gå till Login
    header("Location: login.php");
    exit;
}

if ($_SESSION['role_level'] < 5) {
    // Inloggad men fel behörighet (t.ex. Elev försöker nå Admin) -> Gå till 403
    header("Location: 403.php");
    exit;
}

// 1. Hämta data för dropdowns
try {
    $roleStmt = $pdo->query("SELECT * FROM roles ORDER BY r_level ASC");
    $allRoles = $roleStmt->fetchAll();

    $speedStmt = $pdo->query("SELECT * FROM progress_speeds ORDER BY ps_id ASC");
    $allProgressSpeeds = $speedStmt->fetchAll();

    // Hämta klasser via school_obj
    $allClasses = $school_obj->getAllClasses();

} catch (PDOException $e) {
    die("Kunde inte hämta data: " . $e->getMessage());
}

$errorMsg = "";
$successMsg = "";

// 2. Hantera formulär
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register-submit'])) {
    
// Kontrollera om token finns, annars använd tom sträng för att undvika felmeddelande
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    
    if (!verifyCsrfToken($token)) {
        die("Ogiltig CSRF-token. Försök ladda om sidan.");
    }

    $uname = cleanInput($_POST['uname']);
    $ufname = cleanInput($_POST['ufname']);
    $ulname = cleanInput($_POST['ulname']);
    $umail = cleanInput($_POST['umail']);
    $upass = $_POST['upass'];
    $upassrpt = $_POST['upassrpt'];
    $urole = cleanInput($_POST['urole']);
    
    // Hämta XP-hastighet (Standard: 1)
    $uspeed = isset($_POST['uspeed']) ? cleanInput($_POST['uspeed']) : 1;
    
    // Hämta Klass (Kan vara tom)
    $uclass = !empty($_POST['uclass']) ? cleanInput($_POST['uclass']) : null;

    $checkResult = $user_obj->checkUserRegisterInfo($uname, $umail, $upass, $upassrpt, "create");

    if (!$checkResult['success']) {
        $errorMsg = $checkResult['error'];
    } else {
        // Skicka med både hastighet och klass till create-funktionen
        $createResult = $user_obj->createUser($uname, $ufname, $ulname, $umail, $upass, $urole, $uspeed, $uclass);

        if ($createResult['success']) {
            $successMsg = "Användaren <strong>$uname</strong> har skapats!";
        } else {
            $errorMsg = $createResult['error'];
        }
    }
}

// 3. Hämta de 20 senaste användarna (för listan i botten)
$recentUsers = $user_obj->getRecentUsers(20);

?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <div class="card shadow mb-5">
                <div class="card-header">
                    <h3 class="mb-0"><i class="bi bi-person-plus-fill"></i> Lägg till ny användare</h3>
                </div>
                <div class="card-body p-4">

                    <?php if ($errorMsg): ?>
                        <div class="alert alert-danger"><?= $errorMsg ?></div>
                    <?php endif; ?>
                    
                    <?php if ($successMsg): ?>
                        <div class="alert alert-success"><?= $successMsg ?></div>
                    <?php endif; ?>

                    <form action="register.php" method="POST">
                        <?php echo csrfInput(); ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ufname" class="form-label">Förnamn</label>
                                <input type="text" class="form-control" id="ufname" name="ufname" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ulname" class="form-label">Efternamn</label>
                                <input type="text" class="form-control" id="ulname" name="ulname" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="uname" class="form-label">Användarnamn</label>
                                <input type="text" class="form-control" id="uname" name="uname" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="umail" class="form-label">E-post</label>
                                <input type="email" class="form-control" id="umail" name="umail" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="upass" class="form-label">Lösenord</label>
                                <input type="password" class="form-control" id="upass" name="upass" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="upassrpt" class="form-label">Upprepa lösenord</label>
                                <input type="password" class="form-control" id="upassrpt" name="upassrpt" required>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded border mb-3">
                            <h6 class="mb-3 text-muted">Konto-inställningar</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="urole" class="form-label fw-bold">Roll</label>
                                    <select class="form-select" id="urole" name="urole">
                                        <?php foreach ($allRoles as $role): ?>
                                            <option value="<?= $role['r_id'] ?>" <?= ($role['r_id'] == 1) ? 'selected' : '' ?>><?= $role['r_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="uclass" class="form-label fw-bold">Klass (Valfritt)</label>
                                    <select class="form-select" id="uclass" name="uclass">
                                        <option value="">-- Ingen klass --</option>
                                        <?php foreach ($allClasses as $c): ?>
                                            <option value="<?= $c['c_id'] ?>"><?= htmlspecialchars($c['c_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="uspeed" class="form-label fw-bold">XP-Takt</label>
                                    <select class="form-select" id="uspeed" name="uspeed">
                                        <?php foreach ($allProgressSpeeds as $speed): ?>
                                            <option value="<?= $speed['ps_id'] ?>">
                                                <?= $speed['ps_name'] ?> (<?= $speed['ps_multiplier'] ?>x)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="user-management.php" class="btn btn-outline-dark fw-bold me-2">Tillbaka till Användarlistan</a>
                            <button type="submit" name="register-submit" class="btn btn-success px-4">Skapa Användare</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (!empty($recentUsers)): ?>
            <div class="card shadow">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-clock-history"></i> Senast tillagda (20 st)</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Namn</th>
                                    <th>Användarnamn</th>
                                    <th>E-post</th>
                                    <th>Roll</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentUsers as $user): ?>
                                    <?php 
                                        $roleClass = 'bg-secondary'; 
                                        switch($user['r_name']) {
                                            case 'Admin': $roleClass = 'bg-danger border border-danger-subtle'; break;
                                            case 'Lärare': $roleClass = 'bg-primary border border-primary-subtle'; break;
                                            case 'Elev': $roleClass = 'bg-success border border-success-subtle'; break;
                                        }
                                    ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($user['u_fname'] . ' ' . $user['u_lname']) ?></strong></td>
                                        <td><?= htmlspecialchars($user['u_name']) ?></td>
                                        <td><?= htmlspecialchars($user['u_email']) ?></td>
                                        <td><span class="badge rounded-pill <?= $roleClass ?>"><?= htmlspecialchars($user['r_name']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once "include/footer.php"; ?>
