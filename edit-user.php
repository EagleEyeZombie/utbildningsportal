<?php
require_once "include/header.php";

// --- SÄKERHETSVAKT ---
if (!isset($_SESSION['user_id']) || $_SESSION['role_level'] < 5) {
    header("Location: login.php");
    exit;
}

// 1. HÄMTA DATA FÖR DROPDOWNS
$allUserRoles = $pdo->query("SELECT * FROM roles")->fetchAll();
$allProgressSpeeds = $pdo->query("SELECT * FROM progress_speeds ORDER BY ps_id ASC")->fetchAll();
$allClasses = $school_obj->getAllClasses(); // <--- NYTT: Hämta klasser

// 2. HÄMTA ANVÄNDAREN SOM SKA REDIGERAS
if (isset($_GET['uid'])) {
    $userId = $_GET['uid'];
    $userInfoResult = $user_obj->selectUserInfo($userId);
    
    if ($userInfoResult['success']) {
        $currentUserInfo = $userInfoResult['data'];
    } else {
        die("Användaren hittades inte.");
    }
} else {
    header("Location: admin_dashboard.php");
    exit;
}

// 3. HANTERA FORMULÄR
if (isset($_POST['update-submit'])) {
    
    $uname  = cleanInput($_POST["uname"]); 
    $ufname = cleanInput($_POST["ufname"]);
    $ulname = cleanInput($_POST["ulname"]);
    $umail  = trim($_POST["umail"]);
    $upass  = $_POST["upass"];
    $upassrpt = $_POST["upassrpt"];
    $urole  = cleanInput($_POST["urole"]);
    $uspeed = isset($_POST["uspeed"]) ? cleanInput($_POST["uspeed"]) : 1;
    $uclass = !empty($_POST['uclass']) ? cleanInput($_POST['uclass']) : null; // <--- NYTT: Hämta klass

    $result = $user_obj->checkUserRegisterInfo($uname, $umail, $upass, $upassrpt, "edit", $userId);

    if (!$result['success']) {
        echo "<div class='container mt-3'><div class='alert alert-danger'>" . $result['error'] . "</div></div>";
    } 
    else {
        // Kör update (skickar med uclass)
        $result = $user_obj->editUser($userId, $uname, $ufname, $ulname, $umail, $upass, $urole, $uspeed, $uclass);
        
        if (!$result['success']) {
            echo "<div class='container mt-3'><div class='alert alert-danger'>Error: " . $result['error'] . "</div></div>";
        } 
        else {
            $userInfoResult = $user_obj->selectUserInfo($userId);
            $currentUserInfo = $userInfoResult['data'];
            echo "<div class='container mt-3'><div class='alert alert-success'>Användaren uppdaterad! <a href='user-management.php'>Tillbaka till listan</a></div></div>";
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Redigera användare</h2>
                    <form action="delete-user.php" method="POST" class="d-inline" onsubmit="return confirm('Är du säker?');">
                        <?= csrfInput() ?>
                        <input type="hidden" name="uid" value="<?= $userId ?>">
                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Ta bort</button>
                    </form>
                </div>
                
                <form action="" method="POST">
                    
                    <div class="mb-3">
                        <label for="uname" class="form-label">Användarnamn:</label>
                        <input type="text" id="uname" name="uname" class="form-control" value="<?= htmlspecialchars($currentUserInfo['u_name']) ?>" required>
                        <div class="form-text">Du kan ändra användarnamnet (måste vara unikt).</div>
                    </div>

                    <div class="mb-3">
                        <label for="ufname" class="form-label">Förnamn:</label>
                        <input type="text" id="ufname" name="ufname" class="form-control" value="<?= htmlspecialchars($currentUserInfo['u_fname']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="ulname" class="form-label">Efternamn:</label>
                        <input type="text" id="ulname" name="ulname" class="form-control" value="<?= htmlspecialchars($currentUserInfo['u_lname']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="umail" class="form-label">E-post:</label>
                        <input type="email" id="umail" name="umail" class="form-control" value="<?= htmlspecialchars($currentUserInfo['u_email']) ?>" required>
                    </div>

                    <div class="alert alert-secondary mt-4">
                        <small><i class="bi bi-info-circle"></i> Lämna lösenordsfälten tomma om du inte vill byta lösenord.</small>
                    </div>

                    <div class="mb-3">
                        <label for="upass" class="form-label">Nytt Lösenord:</label>
                        <input type="password" id="upass" name="upass" class="form-control" placeholder="Nytt lösenord...">
                    </div>

                    <div class="mb-3">
                        <label for="upassrpt" class="form-label">Upprepa Lösenord:</label>
                        <input type="password" id="upassrpt" name="upassrpt" class="form-control" placeholder="Upprepa nytt lösenord...">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="urole" class="form-label">Roll:</label>
                            <select id="urole" name="urole" class="form-select" required>
                                <?php foreach($allUserRoles as $role): ?>
                                    <option value="<?= $role['r_id'] ?>" <?= ($role['r_id'] == $currentUserInfo['u_role_fk']) ? 'selected' : '' ?>>
                                        <?= $role['r_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="uclass" class="form-label">Klass (Valfritt):</label>
                            <select id="uclass" name="uclass" class="form-select">
                                <option value="">-- Ingen klass --</option>
                                <?php foreach ($allClasses as $c): ?>
                                    <option value="<?= $c['c_id'] ?>" <?= ($c['c_id'] == $currentUserInfo['u_class_fk']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['c_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                         <label for="uspeed" class="form-label">XP-Bonus (Takt):</label>
                         <select id="uspeed" name="uspeed" class="form-select">
                             <?php foreach($allProgressSpeeds as $speed): ?>
                                 <option value="<?= $speed['ps_id'] ?>" <?= ($speed['ps_id'] == $currentUserInfo['u_progress_speed_fk']) ? 'selected' : '' ?>>
                                     <?= $speed['ps_name'] ?> (<?= $speed['ps_multiplier'] ?>x)
                                 </option>
                             <?php endforeach; ?>
                         </select>
                         <div class="form-text small">Individanpassning.</div>
                    </div>

                    <div class="d-grid mt-3 gap-2">
                        <button type="submit" name="update-submit" class="btn btn-primary btn-lg">Spara ändringar</button>
                        <a href="user-management.php" class="btn btn-outline-secondary">Avbryt</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once "include/footer.php"; ?>
