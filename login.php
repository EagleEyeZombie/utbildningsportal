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

// 2. Hantera formuläret
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login-submit'])) {
    
    // CSRF-check (Säkerhet 5/5)
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        die("Ogiltig CSRF-token. Försök ladda om sidan.");
    }

    $email = cleanInput($_POST['email']);
    $password = $_POST['password']; // Lösenord ska inte rensas med cleanInput, de kan innehålla specialtecken

    // Anropa metoden i class_user.php
    $loginResult = $user_obj->loginUser($email, $password);

    if ($loginResult['success']) {
        // Lyckad inloggning! Omdirigera baserat på roll.
        if ($loginResult['role_level'] >= 5) {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        // Misslyckad inloggning
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
                    
                    <?php if (!empty($errorMsg)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $errorMsg; ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <?php echo csrfInput(); ?>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-post</label>
                            <input type="email" class="form-control" id="email" name="email" required>
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
