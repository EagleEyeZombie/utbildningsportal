<?php

class User {
    
    public $username;
    public $role_level;
    public $pdo;
    
    // KONSTRUKTOR: Dependency Injection
    // Vi skickar in databaskopplingen ($pdo) när vi skapar objektet.
    // Detta gör klassen oberoende av hur databasen ansluts (bra för testning).
    public function __construct($pdo, $username = 'Guest', $role_level = 0) {
        $this->username = $username;
        $this->role_level = $role_level;
        $this->pdo = $pdo;
    }
    
    /**
     * Validerar registrerings- och uppdateringsdata.
     * Denna funktion agerar "Vaktpost" innan vi ens pratar med databasen för att ändra något.
     */
    public function checkUserRegisterInfo($uname, $umail, $upass, $upassrpt, $condition, $currentUserId = null) {
        
        if ($condition === "create" || $condition === "edit") {   
            
            // Regel: Användarnamnslängd
            if (strlen($uname) < 3 || strlen($uname) > 20) {
                return ['success' => false, 'error' => 'Användarnamnet måste vara mellan 3 och 20 tecken långt.'];
            }

            // UNIKHETSKONTROLL (Användarnamn)
            // Vi använder Prepared Statements för att säkert kolla om namnet redan finns.
            // LOWER() gör kollen skiftlägesokänslig (Admin == admin).
            $stmt = $this->pdo->prepare("SELECT u_id FROM users WHERE LOWER(u_name) = LOWER(?)");
            $stmt->execute([strtolower($uname)]);
            
            if ($stmt->rowCount() > 0) {
                $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
                // Logik: Om vi skapar nytt (create) är alla träffar dåliga.
                // Om vi redigerar (edit) är det OK att hitta sig själv, men ingen annan.
                // Om skapa NY eller om REDIGERA och ID inte matchar mitt eget
                if ($condition === "create" || ($currentUserId !== null && $existingUser['u_id'] != $currentUserId)) {
                    return ['success' => false, 'error' => 'Användarnamnet är redan upptaget.'];
                }
            }

            // UNIKHETSKONTROLL (E-post)
            // Samma logik som för användarnamn.
            $stmt = $this->pdo->prepare("SELECT u_id FROM users WHERE LOWER(u_email) = LOWER(?)");
            $stmt->execute([strtolower($umail)]);
            
            if ($stmt->rowCount() > 0) {
                $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($condition === "create" || ($currentUserId !== null && $existingUser['u_id'] != $currentUserId)) {
                    return ['success' => false, 'error' => 'E-postadressen används redan.'];
                }
            }
        }

        // Validera e-postformat med inbyggd PHP-funktion
        if (!filter_var($umail, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Ogiltigt e-postformat.'];
        }

        // LÖSENORDSKRAV (Säkerhetspolicy)
        // Endast relevant vid 'create' eller om man fyllt i nytt lösenord vid 'edit'.
        if ($condition !== "edit" || !empty($upass)) {
            if ($upass !== $upassrpt) {
                return ['success' => false, 'error' => 'Lösenorden matchar inte.'];
            }
            if (strlen($upass) < 6) {
                return ['success' => false, 'error' => 'Lösenordet måste vara minst 6 tecken långt.'];
            }
            // Regex för att tvinga fram komplexitet (Stora bokstäver och specialtecken)
            if (!preg_match('/[A-Z]/', $upass)) {
                return ['success' => false, 'error' => 'Lösenordet måste innehålla minst en stor bokstav.'];
            }
            if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $upass)) {
                return ['success' => false, 'error' => 'Lösenordet måste innehålla minst ett specialtecken.'];
            }
        }

        return ['success' => true];
    }
    
    /**
     * Skapar en ny användare i databasen.
     */
    // Flöde A. Steg 4
    // Denna funktion tar emot validerad data och sparar den permanent.
    public function createUser($uname, $ufname, $ulname, $umail, $upass, $urole, $progressSpeed = 1, $classId = null){
        try {
            // SÄKERHET: HASHING
            // Vi använder password_hash() som automatiskt genererar ett säkert "salt" och använder BCRYPT.
            // Detta gör att inte ens databasadministratören kan se användarnas lösenord.
            $hashedPassword = password_hash($upass, PASSWORD_DEFAULT);
            
            // SÄKERHET: TRANSAKTIONER
            // Vi startar en transaktion. Allt måste lyckas, annars sparas inget.
            $this->pdo->beginTransaction();

            if (empty($classId)) $classId = null;

            // Flöde A. Steg 5.1
            // Prepared Statement (INSERT)
            $stmt = $this->pdo->prepare("INSERT INTO users (u_name, u_fname, u_lname, u_email, u_password, u_isactive, u_role_fk, u_progress_speed_fk, u_class_fk, u_created) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            // Flöde A. Steg 5.2
            // Exekvering med den hashade versionen av lösenordet.
            $stmt->execute([$uname, $ufname, $ulname, $umail, $hashedPassword, 1, $urole, $progressSpeed, $classId]);

            // Flöde A. Steg 5.3
            // Om vi kom hit utan fel, spara ändringarna permanent.
            $this->pdo->commit();
            return ['success' => true];

        } catch (Exception $e) {
            // Om något gick fel, ångra allt (Rollback).
            $this->pdo->rollBack();
            return ['success' => false, 'error' => 'Databasfel: ' . $e->getMessage()];
        }
    }
    
    /**
     * Uppdaterar en befintlig användare (Inklusive Klass och Användarnamn).
     * Används av admin (edit_user.php).
     */
    public function editUser($userId, $uname, $ufname, $ulname, $umail, $upass, $urole, $progressSpeed, $classId = null) {
        try {
            $this->pdo->beginTransaction();

            if (empty($classId)) $classId = null;

            // Dynamisk SQL-byggnad: Vi uppdaterar alltid bas-infon.
            // Grundfråga: Uppdatera allt inklusive u_name och u_class_fk
            $query = "UPDATE users SET u_name = ?, u_fname = ?, u_lname = ?, u_email = ?, u_role_fk = ?, u_progress_speed_fk = ?, u_class_fk = ?";
            $params = [$uname, $ufname, $ulname, $umail, $urole, $progressSpeed, $classId];

            // Om lösenord ska uppdateras (fältet var inte tomt), lägg till det i frågan
            // Annars rör vi inte det gamla lösenordet.
            if (!empty($upass)) {
                $hashedPassword = password_hash($upass, PASSWORD_DEFAULT);
                $query .= ", u_password = ?";
                $params[] = $hashedPassword;
            }

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
    
    // Hämtar en enskild användare (för Edit-formulär)
    public function selectUserInfo($userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE u_id = ?");
            $stmt->execute([$userId]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Fallback för gamla användare som saknar speed
                if (!isset($user['u_progress_speed_fk']) || empty($user['u_progress_speed_fk'])) {
                    $user['u_progress_speed_fk'] = 1;
                }
                return ['success' => true, 'data' => $user];
            } else {
                return ['success' => false, 'error' => 'Ingen användare med ID ' . $userId . ' hittades.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Databasfel: ' . $e->getMessage()];
        }
    }

    // INLOGGNING (Autentisering)
    public function loginUser($input, $password) {
        try {
            // Vi tillåter inloggning med antingen E-post ELLER Användarnamn.
            // Vi hämtar även r_level direkt via JOIN för att sätta behörighet i sessionen.
            $stmt = $this->pdo->prepare("
                SELECT users.*, roles.r_level 
                FROM users 
                INNER JOIN roles ON users.u_role_fk = roles.r_id 
                WHERE users.u_email = ? OR users.u_name = ?
            ");
            
            $stmt->execute([$input, $input]); 
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // SÄKERHET: password_verify
            // Jämför det inskrivna lösenordet med den lagrade hashen.
            if ($user && password_verify($password, $user['u_password'])) {
                
                // SÄKERHET: Session Fixation Protection
                // Vi genererar ett nytt sessions-ID vid inloggning.
                // Detta förhindrar att en angripare kan stjäla en session genom att lura offret att använda en känd länk.
                session_regenerate_id(true);
                
                // Spara viktig data i sessionen (för snabb åtkomst på alla sidor)
                $_SESSION['user_id'] = $user['u_id'];
                $_SESSION['username'] = $user['u_name'];
                $_SESSION['role_level'] = $user['r_level']; // Används för RBAC (t.ex. Admin-access)
                $_SESSION['user_xp'] = $user['u_xp'];
                $_SESSION['user_level'] = $user['u_level'];
                $_SESSION['user_theme'] = $user['u_theme'] ?? 'default';
                
                // Uppdatera "Senast inloggad"-stämpeln
                $updateStmt = $this->pdo->prepare("UPDATE users SET u_lastlogin = NOW() WHERE u_id = ?");
                $updateStmt->execute([$user['u_id']]);

                return ['success' => true, 'role_level' => $user['r_level']];

            } else {
                // Vi ger inte detaljer om VAD som var fel (Email eller Lösenord) för att inte hjälpa hackers.
                return ['success' => false, 'error' => 'Felaktigt användarnamn/e-post eller lösenord.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Databasfel: ' . $e->getMessage()];
        }
    }
    
    // Enkel sökning (används kanske på andra ställen)
    public function searchUsers($userName){
        try {
            $stmt = $this->pdo->prepare("
                SELECT u_name, u_fname, u_lname, u_email, r_name 
                FROM users 
                INNER JOIN roles ON users.u_role_fk = roles.r_id
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

    /**
     * Hämtar användare med Sök, Filter, Sortering och Paginering.
     * UPPDATERAD: Hämtar nu även KLASSNAMN (c_name).
     * Används av user-management.php (Adminvy).
     */
    public function getUsersFiltered($search, $roleId, $sortCol, $sortDir, $limit, $offset) {
        try {
            // Whitelisting för sortering (Säkerhet mot SQL injection i ORDER BY)
            $allowedSorts = ['u_name', 'u_fname', 'u_lname', 'u_email', 'r_name', 'u_created', 'c_name'];
            if (!in_array($sortCol, $allowedSorts)) $sortCol = 'u_name';
            $sortDir = strtoupper($sortDir) === 'DESC' ? 'DESC' : 'ASC';

            // Komplex SQL med JOINs för att få ut läsbara namn på Roll, Hastighet och Klass.
            $sql = "SELECT users.*, roles.r_name, progress_speeds.ps_name, classes.c_name 
                    FROM users 
                    LEFT JOIN roles ON users.u_role_fk = roles.r_id 
                    LEFT JOIN progress_speeds ON users.u_progress_speed_fk = progress_speeds.ps_id
                    LEFT JOIN classes ON users.u_class_fk = classes.c_id
                    WHERE 1=1";
            
            $params = [];

            // Lägg till sökfilter om det finns
            if (!empty($search)) {
                $sql .= " AND (u_name LIKE ? OR u_fname LIKE ? OR u_lname LIKE ? OR u_email LIKE ?)";
                $term = "%$search%";
                $params[] = $term; $params[] = $term; $params[] = $term; $params[] = $term;
            }

            // Lägg till rollfilter om det finns
            if (!empty($roleId) && $roleId !== 'all') {
                $sql .= " AND u_role_fk = ?";
                $params[] = $roleId;
            }

            // Lägg till sortering och paginering (LIMIT/OFFSET)
            $sql .= " ORDER BY $sortCol $sortDir LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();

        } catch (Exception $e) { return []; }
    }

    // Hjälpfunktion för paginering: Räkna totalt antal träffar med samma filter
    public function getUsersCountFiltered($search, $roleId) {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE 1=1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (u_name LIKE ? OR u_fname LIKE ? OR u_lname LIKE ? OR u_email LIKE ?)";
                $term = "%$search%";
                $params[] = $term; $params[] = $term; $params[] = $term; $params[] = $term;
            }

            if (!empty($roleId) && $roleId !== 'all') {
                $sql .= " AND u_role_fk = ?";
                $params[] = $roleId;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();

        } catch (Exception $e) { return 0; }
    }

    public function getUsersPaginated($limit, $offset) {
        return $this->getUsersFiltered('', 'all', 'u_name', 'ASC', $limit, $offset);
    }

    public function getTotalUsers() {
        return $this->getUsersCountFiltered('', 'all');
    }
    
    // För startsidan (Register-sidan)
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
        } catch (Exception $e) { return []; }
    }

    // --- LEVEL & XP SYSTEM (Flöde B - Gamification) ---
    
    // Flöde B. Steg 2.1: Hämta nuvarande XP och eventuell "Multiplier"
    // Denna funktion anropas när en uppgift är rättad.
    public function addXpAndCheckLevelup($userId, $baseXpAmount) {
        try {
            // Hämta elevens data OCH deras hastighets-bonus (ps_multiplier)
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
            $multiplier = $user['ps_multiplier'] ?? 1.0;

            // Flöde B. Steg 2.2: Beräkna ny total XP
            // Här appliceras individanpassningen (t.ex. 1.5x XP för de som behöver pepp).
            $xpWithBonus = floor($baseXpAmount * $multiplier);
            $newXp = $currentXp + $xpWithBonus;

            // Flöde B. Steg 2.3: Hämta nivå-regler från DATABASEN (Inte hårdkodat!)
            // Detta är "Data Driven Design". Vi kan ändra nivå-kraven i databasen utan att ändra koden.
            $stmtConfig = $this->pdo->query("SELECT lc_level, lc_xp_required FROM level_config ORDER BY lc_level ASC");
            $levelConfig = $stmtConfig->fetchAll(PDO::FETCH_KEY_PAIR);

            // Flöde B. Steg 2.4: Loopa igenom reglerna för att hitta rätt nivå
            // Vi kollar vilken nivå den nya XP-summan motsvarar.
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
                $calculatedLevel = $currentLevel; 
            }

            // Säkerställ att man inte tappar nivåer av misstag
            $finalLevel = max($currentLevel, $calculatedLevel);
            $leveledUp = ($finalLevel > $currentLevel);

            // Uppdatera användaren med ny XP och eventuell ny nivå
            $update = $this->pdo->prepare("UPDATE users SET u_xp = ?, u_level = ? WHERE u_id = ?");
            $update->execute([$newXp, $finalLevel, $userId]);

            // Uppdatera sessionen direkt så användaren ser resultatet (reaktivitet)
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
                $_SESSION['user_xp'] = $newXp;
                $_SESSION['user_level'] = $finalLevel;
            }

            // Returnera data så vi kan visa "Level Up!"-popup på frontend
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

    // --- TEMAHANTERING ---
    public function updateUserTheme($userId, $themeName) {
        try {
            // Whitelist för säkerhet (så man inte kan injicera CSS-filnamn)
            $allowedThemes = ['fantasy', 'pink', 'retro', 'cyberpunk', 'pixel', 'nature', 'ocean', 'rainbow'];
            
            if (!in_array($themeName, $allowedThemes)) {
                return ['success' => false, 'error' => 'Ogiltigt tema.'];
            }

            $stmt = $this->pdo->prepare("UPDATE users SET u_theme = ? WHERE u_id = ?");
            $stmt->execute([$themeName, $userId]);
            
            $_SESSION['user_theme'] = $themeName;
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Kunde inte byta tema.'];
        }
    }

    // --- LEVEL PROGRESS (Visualisering) ---
    // Räknar ut hur många procent av nuvarande nivå användaren klarat.
    // Används för progress-baren på Dashboarden.
    public function getLevelProgress($currentXp) {
        try {
            $stmt = $this->pdo->query("SELECT lc_level, lc_xp_required FROM level_config ORDER BY lc_level ASC");
            $levels = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            $currentLevelStart = 0;
            $nextLevelTarget = 100; // Standard
            $found = false;

            // Hitta intervallet: [StartXP för nuvarande nivå] ---DU ÄR HÄR--- [MålXP för nästa nivå]
            foreach ($levels as $lvl => $req) {
                if ($currentXp >= $req) {
                    $currentLevelStart = $req;
                } else {
                    $nextLevelTarget = $req;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // Max level uppnådd
                return ['percent' => 100, 'current' => $currentXp, 'target' => $currentXp, 'needed' => 0, 'is_max' => true];
            }

            $levelRange = $nextLevelTarget - $currentLevelStart;
            $xpGainedInLevel = $currentXp - $currentLevelStart;
            
            $percent = 0;
            if ($levelRange > 0) {
                $percent = round(($xpGainedInLevel / $levelRange) * 100);
            }

            return [
                'percent' => $percent,
                'current' => $currentXp,
                'target' => $nextLevelTarget,
                'needed' => $nextLevelTarget - $currentXp,
                'is_max' => false
            ];

        } catch (Exception $e) {
            return ['percent' => 0, 'current' => 0, 'target' => 100, 'needed' => 100, 'is_max' => false];
        }
    }
}
?>
