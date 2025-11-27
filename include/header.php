<?php
require_once "include/class_user.php";
require_once "include/class_task.php";
require_once "include/class_school.php";
require_once "include/config.php";
require_once "include/functions.php";

// --- LOGIK FÖR TEMA (BAKGRUND) ---
// Vi vill att dashboard.php och task_view.php ALLTID ska ha spel-bakgrunden,
// även om det är en admin som besöker dem.
$current_page = basename($_SERVER['PHP_SELF']);
$game_pages = ['dashboard.php', 'task_view.php', 'badges.php']; // Lade till badges.php här också

$body_class = 'student-page-background'; // Standard (Elev)

// Om man är admin OCH inte är på en spelsida, då kör vi admin-temat (vitt)
if (isset($_SESSION['role_level']) && $_SESSION['role_level'] >= 5 && !in_array($current_page, $game_pages)) {
    $body_class = 'admin-mode';
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utbildningsportal</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="css/style.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script src="js/script.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    <link rel="icon" href="data:,">
</head>
<body class="<?php echo $body_class; ?>">

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"><i class="bi bi-book-half"></i> KunskapsÄventyret</a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (isset($_SESSION['role_level'])): ?>
                
                <?php if ($_SESSION['role_level'] >= 10): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php" style="color: var(--accent-gold) !important;"><i class="bi bi-joystick"></i> Mina Äventyr</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php"><i class="bi bi-shield-lock"></i> Adminpanel</a>
                    </li>
                
                <?php elseif ($_SESSION['role_level'] == 5): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php" style="color: var(--accent-gold) !important;"><i class="bi bi-joystick"></i> Mina Äventyr</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php"><i class="bi bi-shield-lock"></i> Adminpanel</a>
                    </li>
                
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="bi bi-map"></i> Mina Äventyr</a>
                    </li>
                <?php endif; ?>
                
            <?php endif; ?>

            <li class="nav-item ms-2">
                <span class="navbar-text text-light me-2">
                    <small>Inloggad som:</small> <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
                </span>
            </li>

            <li class="nav-item">
                <a class="nav-link btn btn-outline-danger btn-sm text-danger ms-2" href="logout.php" style="border: 1px solid #dc3545; padding: 5px 10px;">Logga ut</a>
            </li>

        <?php else: ?>
            <li class="nav-item">
                <a class="nav-link" href="login.php">Logga in</a>
            </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>
