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
     */
    public function checkUserRegisterInfo($uname, $umail, $upass, $upassrpt, $condition, $currentUserId = null) {
        
        if ($condition === "create" || $condition === "edit") {   
            
            if (strlen($uname) < 3 || strlen($uname) > 20) {
                return ['success' => false, 'error' => 'Användarnamnet måste vara mellan 3 och 20 tecken långt.'];
            }

            if ($condition === "create") {
                $stmt = $this->pdo->prepare("SELECT u_id FROM users WHERE LOWER(u_name) = LOWER(?)");
                $stmt->execute([strtolower($uname)]);
                if ($stmt->rowCount() > 0) {
                    return ['success' => false, 'error' => 'Användarnamnet är redan upptaget.'];
                }
            }

            $stmt = $this->pdo->prepare("SELECT u_id FROM users WHERE LOWER(u_email) = LOWER(?)");
            $stmt->execute([strtolower($umail)]);
            
            if ($stmt->rowCount() > 0) {
                $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($condition === "create" || ($currentUserId !== null && $existingUser['u_id'] != $currentUserId)) {
                    return ['success' => false, 'error' => 'E-postadressen används redan.'];
                }
            }
        }

        if (!filter_var($umail, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Ogiltigt e-postformat.'];
        }

        if ($condition !== "edit" || !empty($upass)) {
            if ($upass !== $upassrpt) {
                return ['success' => false, 'error' => 'Lösenorden matchar inte.'];
            }
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

        return ['success' => true];
    }
    
    /**
     * Skapar en ny användare i databasen.
     */
    public function createUser($uname, $ufname, $ulname, $umail, $upass, $urole, $progressSpeed = 1, $classId = null){
        try {
            $hashedPassword = password_hash($upass, PASSWORD_DEFAULT);
            $this->pdo->beginTransaction();

            if (empty($classId)) $classId = null;

            $stmt = $this->pdo->prepare("INSERT INTO users (u_name, u_fname, u_lname, u_email, u_password, u_isactive, u_role_fk, u_progress_speed_fk, u_class_fk, u_created) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            $stmt->execute([$uname, $ufname, $ulname, $umail, $hashedPassword, 1, $urole, $progressSpeed, $classId]);

            $this->pdo->commit();
            return ['success' => true];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'error' => 'Databasfel: ' . $e->getMessage()];
        }
    }
    
    /**
     * Uppdaterar en befintlig användare.
     */
    public function editUser($userId, $uname, $ufname, $ulname, $umail, $upass, $urole, $progressSpeed) {
        try {
            $this->pdo->beginTransaction();

            $query = "UPDATE users SET u_fname = ?, u_lname = ?, u_email = ?, u_role_fk = ?, u_progress_speed_fk = ?";
            $params = [$ufname, $ulname, $umail, $urole, $progressSpeed];

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
    
    /**
     * Hämtar information om en specifik användare.
     */
    public function selectUserInfo($userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE u_id = ?");
            $stmt->execute([$userId]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Fallback för gamla användare
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

    /**
     * Loggar in användaren.
     */
    public function loginUser($input, $password) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT users.*, roles.r_level 
                FROM users 
                INNER JOIN roles ON users.u_role_fk = roles.r_id 
                WHERE users.u_email = ? OR users.u_name = ?
            ");
            
            $stmt->execute([$input, $input]); 
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['u_password'])) {
                
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['u_id'];
                $_SESSION['username'] = $user['u_name'];
                $_SESSION['role_level'] = $user['r_level'];
                $_SESSION['user_xp'] = $user['u_xp'];
                $_SESSION['user_level'] = $user['u_level'];
                // Ladda in temat vid inloggning också
                $_SESSION['user_theme'] = $user['u_theme'] ?? 'default';
                
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
     * Enkel sökning
     */
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

    // --- AVANCERAD HÄMTNING MED FILTER ---

    public function getUsersFiltered($search, $roleId, $sortCol, $sortDir, $limit, $offset) {
        try {
            $allowedSorts = ['u_name', 'u_fname', 'u_lname', 'u_email', 'r_name', 'u_created'];
            if (!in_array($sortCol, $allowedSorts)) $sortCol = 'u_name';
            $sortDir = strtoupper($sortDir) === 'DESC' ? 'DESC' : 'ASC';

            $sql = "SELECT users.*, roles.r_name, progress_speeds.ps_name 
                    FROM users 
                    LEFT JOIN roles ON users.u_role_fk = roles.r_id 
                    LEFT JOIN progress_speeds ON users.u_progress_speed_fk = progress_speeds.ps_id
                    WHERE 1=1";
            
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

            $sql .= " ORDER BY $sortCol $sortDir LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();

        } catch (Exception $e) { return []; }
    }

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

    // --- LEVEL & XP SYSTEM ---

    public function addXpAndCheckLevelup($userId, $baseXpAmount) {
        try {
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

            $xpWithBonus = floor($baseXpAmount * $multiplier);
            $newXp = $currentXp + $xpWithBonus;

            $stmtConfig = $this->pdo->query("SELECT lc_level, lc_xp_required FROM level_config ORDER BY lc_level ASC");
            $levelConfig = $stmtConfig->fetchAll(PDO::FETCH_KEY_PAIR);

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

            $finalLevel = max($currentLevel, $calculatedLevel);
            $leveledUp = ($finalLevel > $currentLevel);

            $update = $this->pdo->prepare("UPDATE users SET u_xp = ?, u_level = ? WHERE u_id = ?");
            $update->execute([$newXp, $finalLevel, $userId]);

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

    // --- TEMAHANTERING ---
    public function updateUserTheme($userId, $themeName) {
        try {
            // ÄNDRAT: minecraft -> pixel
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

    // --- HÄMTA LEVEL PROGRESS (För Dashboard) ---
    public function getLevelProgress($currentXp) {
        try {
            // Hämta alla nivågränser
            $stmt = $this->pdo->query("SELECT lc_level, lc_xp_required FROM level_config ORDER BY lc_level ASC");
            $levels = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [1=>0, 2=>100, 3=>300...]

            $currentLevelStart = 0;
            $nextLevelTarget = 100; // Standard om db är tom
            $found = false;

            // Hitta var vi befinner oss i stegen
            foreach ($levels as $lvl => $req) {
                if ($currentXp >= $req) {
                    $currentLevelStart = $req;
                } else {
                    $nextLevelTarget = $req;
                    $found = true;
                    break;
                }
            }

            // Om vi nått max level (inga högre krav hittades)
            if (!$found) {
                return [
                    'percent' => 100,
                    'current' => $currentXp,
                    'target' => $currentXp,
                    'needed' => 0,
                    'is_max' => true
                ];
            }

            // Beräkna procent för stapeln
            // Ex: Level 2 (100xp) till Level 3 (300xp). Range = 200.
            // Har 150 xp. Har tagit 50 av 200 på denna nivå. = 25%
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
