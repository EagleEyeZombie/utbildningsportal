<?php
require_once "include/header.php";

// Hämta alla roller till dropdown-listan
try {
    $roleStmt = $pdo->query("SELECT * FROM roles ORDER BY r_level ASC");
    $allRoles = $roleStmt->fetchAll();
} catch (PDOException $e) {
    die("Kunde inte hämta roller: " . $e->getMessage());
}

$errorMsg = "";
$successMsg = "";

// Hantera formuläret
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register-submit'])) {

    // CSRF-check
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        die("Ogiltig CSRF-token. Försök ladda om sidan.");
    }

    // Hämta och rensa data
    $uname = cleanInput($_POST['uname']);
    $ufname = cleanInput($_POST['ufname']);
    $ulname = cleanInput($_POST['ulname']);
    $umail = cleanInput($_POST['umail']);
    $upass = $_POST['upass'];
    $upassrpt = $_POST['upassrpt'];
    $urole = cleanInput($_POST['urole']); // ID från dropdown

    // 1. Validera data (använder din metod i class_user.php)
    // Notera: Vi skickar 'create' som condition.
    $checkResult = $user_obj->checkUserRegisterInfo($uname, $umail, $upass, $upassrpt, "create");

    if (!$checkResult['success']) {
        $errorMsg = $checkResult['error'];
    } else {
        // 2. Skapa användaren
        // OBS: Din createUser-metod i class_user.php måste ta emot dessa parametrar
        $createResult = $user_obj->createUser($uname, $ufname, $ulname, $umail, $upass, $urole);

        if ($createResult['success']) {
            $successMsg = "Kontot är skapat! Du kan nu <a href='login.php'>logga in</a>.";
        } else {
            $errorMsg = $createResult['error'];
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">Skapa konto</h2>

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

                        <div class="mb-3">
                            <label for="uname" class="form-label">Användarnamn</label>
                            <input type="text" class="form-control" id="uname" name="uname" required>
                        </div>

                        <div class="mb-3">
                            <label for="umail" class="form-label">E-post</label>
                            <input type="email" class="form-control" id="umail" name="umail" required>
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

                        <div class="mb-3">
                            <label for="urole" class="form-label">Roll (Endast för test)</label>
                            <select class="form-select" id="urole" name="urole">
                                <?php foreach ($allRoles as $role): ?>
                                    <option value="<?= $role['r_id'] ?>"><?= $role['r_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">I en skarp version väljs "Elev" automatiskt.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="register-submit" class="btn btn-success">Registrera</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Har du redan ett konto? <a href="login.php">Logga in här</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "include/footer.php"; ?>
