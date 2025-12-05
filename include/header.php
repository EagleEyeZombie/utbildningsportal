<?php
require_once "include/class_user.php";
require_once "include/class_task.php";
require_once "include/class_school.php";
require_once "include/config.php";
require_once "include/functions.php";

// --- HANTERA TEMABYTE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_theme']) && isset($_SESSION['user_id'])) {
    if (verifyCsrfToken($_POST['csrf_token'])) {
        $newTheme = $_POST['set_theme'];
        $user_obj->updateUserTheme($_SESSION['user_id'], $newTheme);
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// --- LOGIK FÖR SIDTITEL (NYTT) ---
$current_page = basename($_SERVER['PHP_SELF']);
$site_name = "KunskapsÄventyret";
$page_title = "Välkommen"; // Standard

switch ($current_page) {
    case 'index.php': $page_title = "Start"; break;
    case 'login.php': $page_title = "Logga in"; break;
    case 'dashboard.php': $page_title = "Mina Äventyr"; break;
    case 'task_view.php': $page_title = "Uppdrag"; break;
    case 'task_submit.php': $page_title = "Resultat"; break;
    case 'badges.php': $page_title = "Utmärkelser"; break;
    
    // Admin
    case 'admin_dashboard.php': $page_title = "Adminpanel"; break;
    case 'user-management.php': $page_title = "Hantera Användare"; break;
    case 'admin_tasks.php': $page_title = "Hantera Uppgifter"; break;
    case 'admin_create_task.php': $page_title = "Skapa Uppgift"; break;
    case 'admin_edit_task.php': $page_title = "Redigera Uppgift"; break;
    case 'admin_classes.php': $page_title = "Hantera Klasser"; break;
    case 'edit_class.php': $page_title = "Redigera Klass"; break;
    case 'register.php': $page_title = "Lägg till Användare"; break;
    case '403.php': $page_title = "Åtkomst Nekad"; break;
}

// --- LOGIK FÖR TEMA (BAKGRUND & DESIGN) ---
$game_pages = ['dashboard.php', 'task_view.php', 'badges.php']; 

$body_class = 'student-page-background'; 
if (isset($_SESSION['role_level']) && $_SESSION['role_level'] >= 5 && !in_array($current_page, $game_pages)) {
    $body_class = 'admin-mode';
}

// Hämta tema
$userTheme = 'fantasy'; 
if (isset($_SESSION['user_id'])) {
    try {
        if (isset($_SESSION['user_theme'])) {
            $userTheme = $_SESSION['user_theme'];
        } else {
            $stmt = $pdo->prepare("SELECT u_theme FROM users WHERE u_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $row = $stmt->fetch();
            if ($row && !empty($row['u_theme'])) {
                $userTheme = $row['u_theme'];
                $_SESSION['user_theme'] = $userTheme;
            }
        }
    } catch (Exception $e) {}
}

$themeClass = 'theme-' . $userTheme;
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= $page_title ?> | <?= $site_name ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Creepster&family=Orbitron:wght@400;700&family=Pacifico&family=Lobster&family=Press+Start+2P&family=Merriweather:wght@400;700&family=Montserrat:wght@400;700&family=Chewy&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="css/style.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
</head>
<body class="<?php echo $body_class . ' ' . $themeClass; ?>">

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"><i class="bi bi-book-half"></i> KunskapsÄventyret</a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
        
        <?php if (isset($_SESSION['user_id'])): ?>
            
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php" style="color: var(--accent-gold) !important;"><i class="bi bi-joystick"></i> Mina Äventyr</a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="themeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-palette"></i> Utseende
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="themeDropdown" style="min-width: 200px;">
                    <li><h6 class="dropdown-header">Välj tema</h6></li>
                    
                    <li>
                        <form method="POST" class="d-block w-100">
                            <?= csrfInput() ?>
                            <input type="hidden" name="set_theme" value="fantasy">
                            <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between">
                                <span style="color: #ffd700;"><i class="bi bi-gem"></i> Fantasy</span>
                                <?php if($userTheme == 'fantasy') echo '<i class="bi bi-check-lg text-success"></i>'; ?>
                            </button>
                        </form>
                    </li>

                    <li>
                        <form method="POST" class="d-block w-100">
                            <?= csrfInput() ?>
                            <input type="hidden" name="set_theme" value="pink">
                            <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between">
                                <span style="color: #ff1493;"><i class="bi bi-heart-fill"></i> Pink</span>
                                <?php if($userTheme == 'pink') echo '<i class="bi bi-check-lg text-success"></i>'; ?>
                            </button>
                        </form>
                    </li>

                    <li>
                        <form method="POST" class="d-block w-100">
                            <?= csrfInput() ?>
                            <input type="hidden" name="set_theme" value="retro">
                            <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between">
                                <span style="color: #000; font-weight: bold;"><i class="bi bi-music-note-beamed"></i> Retro</span>
                                <?php if($userTheme == 'retro') echo '<i class="bi bi-check-lg text-success"></i>'; ?>
                            </button>
                        </form>
                    </li>

                    <li>
                        <form method="POST" class="d-block w-100">
                            <?= csrfInput() ?>
                            <input type="hidden" name="set_theme" value="cyberpunk">
                            <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between">
                                <span style="color: #0d6efd;"><i class="bi bi-cpu-fill"></i> Cyberpunk</span>
                                <?php if($userTheme == 'cyberpunk') echo '<i class="bi bi-check-lg text-success"></i>'; ?>
                            </button>
                        </form>
                    </li>

                    <li>
                        <form method="POST" class="d-block w-100">
                            <?= csrfInput() ?>
                            <input type="hidden" name="set_theme" value="pixel">
                            <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between">
                                <span style="color: #4a8a36; font-family: 'Press Start 2P', cursive; font-size: 0.6rem;"><i class="bi bi-grid-3x3"></i> Pixel</span>
                                <?php if($userTheme == 'pixel') echo '<i class="bi bi-check-lg text-success"></i>'; ?>
                            </button>
                        </form>
                    </li>

                    <li>
                        <form method="POST" class="d-block w-100">
                            <?= csrfInput() ?>
                            <input type="hidden" name="set_theme" value="nature">
                            <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between">
                                <span style="color: #2e8b57;"><i class="bi bi-tree-fill"></i> Nature</span>
                                <?php if($userTheme == 'nature') echo '<i class="bi bi-check-lg text-success"></i>'; ?>
                            </button>
                        </form>
                    </li>

                    <li>
                        <form method="POST" class="d-block w-100">
                            <?= csrfInput() ?>
                            <input type="hidden" name="set_theme" value="ocean">
                            <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between">
                                <span style="color: #0077be;"><i class="bi bi-water"></i> Ocean</span>
                                <?php if($userTheme == 'ocean') echo '<i class="bi bi-check-lg text-success"></i>'; ?>
                            </button>
                        </form>
                    </li>

                    <li>
                        <form method="POST" class="d-block w-100">
                            <?= csrfInput() ?>
                            <input type="hidden" name="set_theme" value="rainbow">
                            <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between">
                                <span style="background: linear-gradient(to right, red, orange, green, blue, violet); -webkit-background-clip: text; color: transparent; font-weight: bold;"><i class="bi bi-palette-fill" style="color: purple;"></i> Rainbow</span>
                                <?php if($userTheme == 'rainbow') echo '<i class="bi bi-check-lg text-success"></i>'; ?>
                            </button>
                        </form>
                    </li>

                </ul>
            </li>

            <?php if (isset($_SESSION['role_level']) && $_SESSION['role_level'] >= 5): ?>
                <li class="nav-item">
                    <a class="nav-link" href="admin_dashboard.php"><i class="bi bi-shield-lock"></i> Adminpanel</a>
                </li>
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
