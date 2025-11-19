<?php
require_once "include/header.php";

// 1. Omdirigera om man redan är inloggad
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role_level'] >= 5) {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit;
}

$errorMsg = "";
$successMsg = ""; // Variabel för framgångsmeddelande

// Kollar om vi kom hit från en lyckad registrering
if (isset($_GET['signup']) && $_GET['signup'] == 'success') {
    $successMsg = "Ditt konto är nu skapat! Logga in nedan.";
}

// 2. Hantera formuläret
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login-submit'])) {
    
    // CSRF-check (Säkerhet 5/5)
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        die("Ogiltig CSRF-token. Försök ladda om sidan.");
    }

    $email = cleanInput($_POST['email']);
    $password = $_POST['password']; 

    // Anropa metoden i class_user.php
    $loginResult = $user_obj->loginUser($email, $password);

    if ($loginResult['success']) {
        if ($loginResult['role_level'] >= 5) {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
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
                    
                    <!-- VISA SUCCESS MEDDELANDE -->
                    <?php if (!empty($successMsg)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $successMsg; ?>
                        </div>
                    <?php endif; ?>

                    <!-- VISA FELMEDDELANDE -->
                    <?php if (!empty($errorMsg)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $errorMsg; ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <!-- CSRF Token -->
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

                    <div class="text-center mt-3">
                        <p>Saknar du konto? <a href="register.php">Registrera dig här</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once "include/footer.php";
?>
