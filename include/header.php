<?php
require_once "include/class_user.php";
require_once "include/config.php";
require_once "include/functions.php";

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Teori SQL</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="css/style.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script defer src="js/script.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        
        <li class="nav-item">
          <a class="nav-link" href="index.php">Start</a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (isset($_SESSION['role_level']) && $_SESSION['role_level'] >= 5): ?>
                <li class="nav-item">
                    <a class="nav-link" href="admin_dashboard.php">Adminpanel</a>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Min Sida</a>
            </li>

            <li class="nav-item">
                <a class="nav-link btn btn-outline-danger text-danger ms-2" href="logout.php">Logga ut</a>
            </li>

        <?php else: ?>
            <li class="nav-item">
                <a class="nav-link" href="login.php">Logga in</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="register.php">Registrera</a>
            </li>
        <?php endif; ?>

      </ul>
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>
