<?php
require_once "include/header.php";

// ---------------------------------------------------------
// 1. SESSIONSHANTERING & OMDIRIGERING (CONTROLLER)
// ---------------------------------------------------------
// Här kollar vi om användaren redan är inloggad.
// Det är dålig UX att visa inloggningsformuläret för någon som redan är inne.
if (isset($_SESSION['user_id'])) {
    
    // RBAC (Role Based Access Control) Omdirigering:
    // Om användaren har behörighetsnivå 5 eller högre (Lärare/Admin),
    // skickar vi dem direkt till Admin-panelen.
    if ($_SESSION['role_level'] >= 5) {
        header("Location: admin_dashboard.php");
    } 
    // Annars (Elev), skickar vi dem till elevens Dashboard (Spelplanen).
    else {
        header("Location: dashboard.php");
    }
    exit; // Avbryt exekveringen direkt efter en header-redirect.
}

$errorMsg = "";
// SuccessMsg behövs knappt här längre då ingen kommer från register.php automatiskt,
// men vi låter den vara kvar ifall man manuellt omdirigerar.
$successMsg = ""; 

// Visar meddelande om man precis skapat konto och blivit omdirigerad hit.
if (isset($_GET['signup']) && $_GET['signup'] == 'success') {
    $successMsg = "Ditt konto är nu skapat! Logga in nedan.";
}

// ---------------------------------------------------------
// 2. HANTERA INLOGGNINGSFORMULÄRET (POST-REQUEST)
// Detta block körs när användaren trycker på "Logga in".
// ---------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login-submit'])) {
    
    // --- SÄKERHET: CSRF-TOKEN ---
    // Kontrollera om token finns, annars använd tom sträng för att undvika felmeddelande
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    
    // verifyCsrfToken() (från functions.php) kollar att formuläret
    // verkligen skickades från vår server och inte från en annan sida.
    if (!verifyCsrfToken($token)) {
        die("Ogiltig CSRF-token. Försök ladda om sidan.");
    }

    // --- SÄKERHET: SANITERING ---
    // cleanInput() tar bort skadlig kod (XSS-skydd).
    // Vi tillåter inloggning med BÅDE e-post och användarnamn, så vi tvättar inputen som text.
    $email = cleanInput($_POST['email']);
    
    // Lösenordet tvättas INTE här eftersom specialtecken är tillåtna.
    // Det hanteras säkert via password_verify() senare.
    $password = $_POST['password']; 

    // --- LOGIK: AUTENTISERING (MODELL) ---
    // Vi anropar User-klassens metod loginUser().
    // Denna metod kollar mot databasen och verifierar lösenordshashen.
    $loginResult = $user_obj->loginUser($email, $password);

    if ($loginResult['success']) {
        // Om inloggningen lyckades:
        // Skicka användaren till rätt startsida beroende på roll.
        if ($loginResult['role_level'] >= 5) {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        // Om fel (fel lösenord/användare): Visa felmeddelande.
        $errorMsg = $loginResult['error'];
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">Logga in</h2>
                    
                    <?php if (!empty($successMsg)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $successMsg; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errorMsg)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $errorMsg; ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        
                        <?php echo csrfInput(); ?>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-post eller Användarnamn</label>
                            <input type="text" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Lösenord</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="login-submit" class="btn btn-primary">Logga in</button>
                        </div>
                    </form>
                    
                    </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once "include/footer.php";
?>
