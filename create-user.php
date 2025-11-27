<?php
require_once "include/header.php";

// Hämta roller och hastigheter för dropdowns
$allUserRoles = $pdo->query("SELECT * FROM roles")->fetchAll();
$allProgressSpeeds = $pdo->query("SELECT * FROM progress_speeds ORDER BY ps_id ASC")->fetchAll();

if(isset($_POST['register-submit'])){
	
	$uname = cleanInput($_POST["uname"]);
	$ufname = cleanInput($_POST["ufname"]);
	$ulname = cleanInput($_POST["ulname"]);
	$umail = trim($_POST["umail"]);
	$upass = $_POST["upass"];
	$upassrpt = $_POST["upassrpt"];
	$urole = cleanInput($_POST["urole"]);
    
    // NYTT: Hämta vald hastighet (sätt standard till 1 om inget valts)
    $uspeed = isset($_POST["uspeed"]) ? cleanInput($_POST["uspeed"]) : 1;
	
	$result = $user_obj->checkUserRegisterInfo($uname, $umail, $upass, $upassrpt, "create");

	if (!$result['success']) {
        // Visa felmeddelande snyggt (Bootstrap alert)
		echo "<div class='container mt-3'><div class='alert alert-danger'>" . $result['error'] . "</div></div>";
	} 
	else {
        // NYTT: Skicka med $uspeed till funktionen
		$result = $user_obj->createUser($uname, $ufname, $ulname, $umail, $upass, $urole, $uspeed);
		
        if (!$result['success']) {
			echo "<div class='container mt-3'><div class='alert alert-danger'>Error: " . $result['error'] . "</div></div>";
		} 
		else {
			echo "<div class='container mt-3'><div class='alert alert-success'>Användare skapad! <a href='admin_dashboard.php'>Tillbaka till adminpanelen</a></div></div>";
		}
	}
}
?>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg p-4">
                <h2 class="text-center mb-4">Lägg till användare</h2>
                <form action="" method="POST">
                    
                    <div class="mb-3">
                        <label for="uname" class="form-label">Användarnamn:</label>
                        <input type="text" id="uname" name="uname" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="ufname" class="form-label">Förnamn:</label>
                        <input type="text" id="ufname" name="ufname" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="ulname" class="form-label">Efternamn:</label>
                        <input type="text" id="ulname" name="ulname" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="umail" class="form-label">E-post:</label>
                        <input type="email" id="umail" name="umail" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="upass" class="form-label">Lösenord:</label>
                        <input type="password" id="upass" name="upass" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="upassrpt" class="form-label">Upprepa Lösenord:</label>
                        <input type="password" id="upassrpt" name="upassrpt" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="urole" class="form-label">Roll:</label>
                            <select id="urole" name="urole" class="form-select" required>
                                <?php foreach($allUserRoles as $role): ?>
                                    <option value="<?= $role['r_id'] ?>"><?= $role['r_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="uspeed" class="form-label">XP-Bonus (Takt):</label>
                            <select id="uspeed" name="uspeed" class="form-select">
                                <?php foreach($allProgressSpeeds as $speed): ?>
                                    <option value="<?= $speed['ps_id'] ?>">
                                        <?= $speed['ps_name'] ?> (<?= $speed['ps_multiplier'] ?>x)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text small">Används för individanpassning.</div>
                        </div>
                    </div>

                    <div class="d-grid mt-3">
                        <button type="submit" name="register-submit" class="btn btn-primary btn-lg">Skapa konto</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once "include/footer.php"; ?>
