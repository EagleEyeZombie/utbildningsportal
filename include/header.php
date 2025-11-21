<?php
require_once "include/class_user.php";
require_once "include/class_task.php";
require_once "include/config.php";
require_once "include/functions.php";

?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utbildningsportal</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <!-- Custom JS -->
    <script src="js/script.js"></script>
    
    <!-- Fix för favicon -->
    <link rel="icon" href="data:,">
</head>
<body class="<?php echo (isset($_SESSION['role_level']) && $_SESSION['role_level'] >= 5) ? 'admin-mode' : 'student-page-background'; ?>">

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"><i class="bi bi-book-half"></i> KunskapsÄventyret</a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
        
        <li class="nav-item">
          <a class="nav-link" href="index.php">Start</a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- OM INLOGGAD -->
            
            <?php if (isset($_SESSION['role_level']) && $_SESSION['role_level'] >= 5): ?>
                <!-- MENY FÖR ADMIN (10) OCH LÄRARE (5) -->
                
                <li class="nav-item">
                    <!-- Både Admin och Lärare får länken "Lägg till användare" -->
                    <!-- Behörighetskollen för VILKA roller de får skapa görs inne på register.php -->
                    <a class="nav-link" href="register.php"><i class="bi bi-person-plus"></i> Lägg till användare</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="admin_dashboard.php"><i class="bi bi-shield-lock"></i> Adminpanel</a>
                </li>
            
            <?php else: ?>
                <!-- MENY FÖR ELEV (1) -->
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="bi bi-map"></i> Min Karta</a>
                </li>
            <?php endif; ?>

            <!-- GEMENSAMT FÖR ALLA INLOGGADE -->
            <li class="nav-item ms-2">
                <span class="navbar-text text-light me-2">
                    <small>Inloggad som:</small> <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
                </span>
            </li>

            <li class="nav-item">
                <a class="nav-link btn btn-outline-danger btn-sm text-danger ms-2" href="logout.php" style="border: 1px solid #dc3545; padding: 5px 10px;">Logga ut</a>
            </li>

        <?php else: ?>
            <!-- OM INTE INLOGGAD -->
            <li class="nav-item">
                <a class="nav-link" href="login.php">Logga in</a>
            </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>
