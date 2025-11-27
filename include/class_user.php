<?php

class User {
    
    public $username;
    public $role_level;
    public $pdo;
    
    public function __construct($pdo, $username = 'Guest', $role_level = 0) {
        $this->username = $username;
        $this->role_level = $role_level;
        $this->pdo = $pdo;
    }
    
    /**
     * Validerar registrerings- och uppdateringsdata.
     * Returnerar en array med 'success' (true/false) och ev. 'error' (meddelande på svenska).
     */
    public function checkUserRegisterInfo($uname, $umail, $upass, $upassrpt, $condition, $currentUserId = null) {
        
        // Steg 1-3 gäller främst vid skapande (create) eller om man byter namn/email vid edit
        if ($condition === "create" || $condition === "edit") {   
            
            // Steg 1: Validera användarnamnets längd
            if (strlen($uname) < 3 || strlen($uname) > 20) {
                return ['success' => false, 'error' => 'Användarnamnet måste vara mellan 3 och 20 tecken långt.'];
            }

            // Steg 2: Kolla om användarnamnet redan är upptaget
            // (Vi kollar detta främst vid 'create'. Vid 'edit' brukar man inte byta användarnamn, men om man gör det bör man kolla här)
            if ($condition === "create") {
                $stmt = $this->pdo->prepare("SELECT u_id FROM users WHERE LOWER(u_name) = LOWER(?)");
                $stmt->execute([strtolower($uname)]);
                if ($stmt->rowCount() > 0) {
                    return ['success' => false, 'error' => 'Användarnamnet är redan upptaget.'];
                }
            }

            // Steg 3: Kolla om e-postadressen redan finns
            $stmt = $this->pdo->prepare("SELECT u_id FROM users WHERE LOWER(u_email) = LOWER(?)");
            $stmt->execute([strtolower($umail)]);
            
            // Om vi hittar e-posten i databasen...
            if ($stmt->rowCount() > 0) {
                $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Om vi skapar ny (create): Det är alltid fel om mailen finns.
                // Om vi redigerar (edit): Det är bara fel om mailen tillhör NÅGON ANNAN än oss själva.
                if ($condition === "create" || ($currentUserId !== null && $existingUser['u_id'] != $currentUserId)) {
                    return ['success' => false, 'error' => 'E-postadressen används redan.'];
                }
            }
        }

        // Steg 4: Validera e-postformat
        if (!filter_var($umail, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Ogiltigt e-postformat.'];
        }

        // Steg 5 & 6: Lösenordskollar (Görs vid "create" ELLER om man fyllt i lösenordsfältet vid "edit")
        if ($condition !== "edit" || !empty($upass)) {
            
            // Kolla om lösenorden matchar
            if ($upass !== $upassrpt) {
                return ['success' => false, 'error' => 'Lösenorden matchar inte.'];
            }

            // Validera lösenordsstyrka
            if (strlen($upass) < 6) {
                return ['success' => false, 'error' => 'Lösenordet måste vara minst 6 tecken långt.'];
            }
            if (!preg_match('/[A-Z]/', $upass)) {
                return ['success' => false, 'error' => 'Lösenordet måste innehålla minst en stor bokstav.'];
            }
            if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $upass)) {
                return ['success' => false, 'error' => 'Lösenordet måste innehålla minst ett specialtecken.'];
            }
        }

        // ✅ Alla kontroller godkända
        return ['success' => true];
    }
    
    /**
     * Skapar en ny användare i databasen.
     * UPPDATERAD: Tar nu emot $progressSpeed (XP-multiplikator ID)
     */
    public function createUser($uname, $ufname, $ulname, $umail, $upass, $urole, $progressSpeed = 1, $classId = null){
        try {
            // Hasha lösenordet säkert
            $hashedPassword = password_hash($upass, PASSWORD_DEFAULT);

            // Starta transaktion
            $this->pdo->beginTransaction();

            // Hantera tom sträng som NULL för klass
            if (empty($classId)) $classId = null;

            // Sätt in användare i databasen (inklusive u_progress_speed_fk)
            $stmt = $this->pdo->prepare("INSERT INTO users (u_name, u_fname, u_lname, u_email, u_password, u_isactive, u_role_fk, u_progress_speed_fk, u_class_fk, u_created) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            // Vi skickar med $progressSpeed här
            $stmt->execute([$uname, $ufname, $ulname, $umail, $hashedPassword, 1, $urole, $progressSpeed, $classId]);

            // Bekräfta transaktion
            $this->pdo->commit();

            return ['success' => true];

        } catch (Exception $e) {
            // Ångra om något gick fel
            $this->pdo->rollBack();
            return ['success' => false, 'error' => 'Databasfel: ' . $e->getMessage()];
        }
    }
    
    /**
     * Uppdaterar en befintlig användare.
     * UPPDATERAD: Tar nu emot $progressSpeed
     */
    public function editUser($userId, $uname, $ufname, $ulname, $umail, $upass, $urole, $progressSpeed) {
        try {
            $this->pdo->beginTransaction();

            // Grundfråga för uppdatering - Nu med u_progress_speed_fk
            $query = "UPDATE users SET u_fname = ?, u_lname = ?, u_email = ?, u_role_fk = ?, u_progress_speed_fk = ?";
            $params = [$ufname, $ulname, $umail, $urole, $progressSpeed];

            // Om lösenord angavs, uppdatera det också
            if (!empty($upass)) {
                $hashedPassword = password_hash($upass, PASSWORD_DEFAULT);
                $query .= ", u_password = ?";
                $params[] = $hashedPassword;
            }

            // Lägg till WHERE-villkor och kör
            $query .= " WHERE u_id = ?";
            $params[] = $userId;

            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);

            $this->pdo->commit();

            return ['success' => true];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'error' => 'Databasfel: ' . $e->getMessage()];
        }
    }
    
    /**
     * Hämtar information om en specifik användare.
     * UPPDATERAD: Hämtar nu även u_progress_speed_fk
     */
    public function selectUserInfo($userId) {
        try {
            // Vi la till u_progress_speed_fk i listan
            $stmt = $this->pdo->prepare("SELECT u_name, u_fname, u_lname, u_email, u_role_fk, u_progress_speed_fk FROM users WHERE u_id = ?");
            $stmt->execute([$userId]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                return ['success' => true, 'data' => $user];
            } else {
                return ['success' => false, 'error' => 'Användaren hittades inte.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Databasfel: ' . $e->getMessage()];
        }
    }

    /**
     * Loggar in användaren.
     * Hanterar inloggning via både e-post och användarnamn.
     */
    public function loginUser($input, $password) {
        try {
            // Sök efter användare baserat på e-post ELLER användarnamn
            $stmt = $this->pdo->prepare("
                SELECT users.*, roles.r_level 
                FROM users 
                INNER JOIN roles ON users.u_role_fk = roles.r_id 
                WHERE users.u_email = ? OR users.u_name = ?
            ");
            
            $stmt->execute([$input, $input]); 
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifiera lösenordet
            if ($user && password_verify($password, $user['u_password'])) {
                
                // Förhindra session fixation
                session_regenerate_id(true);
                
                // Spara data i sessionen
                $_SESSION['user_id'] = $user['u_id'];
                $_SESSION['username'] = $user['u_name'];
                $_SESSION['role_level'] = $user['r_level'];
                $_SESSION['user_xp'] = $user['u_xp'];
                $_SESSION['user_level'] = $user['u_level'];
                
                // Uppdatera datum för senaste inloggning
                $updateStmt = $this->pdo->prepare("UPDATE users SET u_lastlogin = NOW() WHERE u_id = ?");
                $updateStmt->execute([$user['u_id']]);

                return ['success' => true, 'role_level' => $user['r_level']];

            } else {
                return ['success' => false, 'error' => 'Felaktigt användarnamn/e-post eller lösenord.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Databasfel: ' . $e->getMessage()];
        }
    }
    
    /**
     * Söker efter användare (för Admin-panelen - Enkel sökning).
     */
    public function searchUsers($userName){
        try {
            $stmt = $this->pdo->prepare("
                SELECT u_name, u_fname, u_lname, u_email, r_name 
                FROM users 
                INNER JOIN roles 
                ON users.u_role_fk = roles.r_id
                WHERE u_name LIKE ?");
            $stmt->execute(["%" . $userName . "%"]);

            $userList = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($userList) {
                return ['success' => true, 'data' => $userList];
            } else {
                return ['success' => false, 'error' => 'Inga användare hittades.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Databasfel: ' . $e->getMessage()];
        }
    }

    // --- METODER FÖR PAGINERING & SORTERING (Admin) ---

    /**
     * Hämtar en sida med användare, sorterad och begränsad.
     */
    public function getUsersPaginated($limit, $offset, $sortColumn = 'u_name', $sortOrder = 'ASC') {
        try {
            // Tillåtna kolumner för sortering (Säkerhetsåtgärd mot SQL-injection)
            $allowedColumns = ['u_name', 'u_fname', 'u_lname', 'u_email', 'r_name'];
            if (!in_array($sortColumn, $allowedColumns)) {
                $sortColumn = 'u_name';
            }
            $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

            $sql = "SELECT users.u_id, users.u_name, users.u_fname, users.u_lname, users.u_email, roles.r_name 
                    FROM users 
                    INNER JOIN roles ON users.u_role_fk = roles.r_id 
                    ORDER BY $sortColumn $sortOrder 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Räknar totalt antal användare (för paginering).
     */
    public function getTotalUsers() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }

    // --- NY METOD FÖR REGISTER.PHP ---
    // ÄNDRAT: Standardgräns ökad till 20
    public function getRecentUsers($limit = 20) {
        try {
            $sql = "SELECT u_name, u_fname, u_lname, u_email, r_name, u_created 
                    FROM users 
                    INNER JOIN roles ON users.u_role_fk = roles.r_id
                    ORDER BY u_id DESC 
                    LIMIT :limit";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // --- LEVEL & XP SYSTEM (NY FUNKTION) ---
    // Lägg denna längst ner i class_user.php, innan sista }

    public function addXpAndCheckLevelup($userId, $baseXpAmount) {
        try {
            // 1. Hämta användarens data + deras XP-multiplikator
            // Vi hämtar ps_multiplier via JOIN. Om ingen finns blir det NULL (hanteras nedan)
            $sql = "SELECT u.u_xp, u.u_level, ps.ps_multiplier 
                    FROM users u
                    LEFT JOIN progress_speeds ps ON u.u_progress_speed_fk = ps.ps_id
                    WHERE u.u_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user) return false;

            $currentXp = $user['u_xp'];
            $currentLevel = $user['u_level'];
            
            // Standardvärde 1.0 om inget är valt
            $multiplier = $user['ps_multiplier'] ?? 1.0;

            // 2. Beräkna ny XP med multiplikator
            // Vi avrundar till heltal (floor)
            $xpWithBonus = floor($baseXpAmount * $multiplier);
            $newXp = $currentXp + $xpWithBonus;

            // 3. Hämta nivå-gränser från databasen (level_config)
            // Vi antar att tabellen finns. Om den är tom, fallback till nuvarande level.
            $stmtConfig = $this->pdo->query("SELECT lc_level, lc_xp_required FROM level_config ORDER BY lc_level ASC");
            $levelConfig = $stmtConfig->fetchAll(PDO::FETCH_KEY_PAIR); // [1 => 0, 2 => 100, ...]

            // 4. Räkna ut vilken nivå användaren ska ha
            $calculatedLevel = 1;
            if ($levelConfig) {
                foreach ($levelConfig as $lvl => $reqXp) {
                    if ($newXp >= $reqXp) {
                        $calculatedLevel = $lvl;
                    } else {
                        break; 
                    }
                }
            } else {
                // Fallback om tabellen är tom eller saknas
                $calculatedLevel = $currentLevel; 
            }

            // 5. Uppdatera databasen
            // Vi uppdaterar leveln BARA om den har ökat
            $finalLevel = max($currentLevel, $calculatedLevel);
            $leveledUp = ($finalLevel > $currentLevel);

            $update = $this->pdo->prepare("UPDATE users SET u_xp = ?, u_level = ? WHERE u_id = ?");
            $update->execute([$newXp, $finalLevel, $userId]);

            // Uppdatera sessionen direkt
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
                $_SESSION['user_xp'] = $newXp;
                $_SESSION['user_level'] = $finalLevel;
            }

            return [
                'new_xp' => $newXp,
                'gained_xp' => $xpWithBonus,
                'new_level' => $finalLevel,
                'leveled_up' => $leveledUp
            ];

        } catch (Exception $e) {
            return false;
        }
    }
}
?>
