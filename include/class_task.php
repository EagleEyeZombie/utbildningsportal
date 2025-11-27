<?php

class Task {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // --- HÄMTA GRUNDDATA ---
    public function getAllGenres() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM genres ORDER BY g_name ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }
    public function getAllClasses() {
        try {
            $stmt = $this->pdo->query("SELECT c_id, c_name FROM classes ORDER BY c_name ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }
    public function getAllTypes() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM task_types");
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }
    public function getAllLevels() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM task_levels ORDER BY tl_level ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    // --- CRUD ---
    public function createTask($name, $typeId, $levelId, $teacherId, $classId, $genreId, $text, $questionsJson, $t_xp) {
        try {
            $sql = "INSERT INTO tasks (t_name, t_type_fk, t_level_fk, t_teacher_fk, t_class_fk, t_genre_fk, t_text, t_questions, t_xp) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$name, $typeId, $levelId, $teacherId, $classId, $genreId, $text, $questionsJson, $t_xp])) {
                return ['success' => true];
            }
            return ['success' => false, 'error' => 'Kunde inte spara uppgiften.'];
        } catch (PDOException $e) { return ['success' => false, 'error' => $e->getMessage()]; }
    }

    public function updateTask($taskId, $name, $typeId, $levelId, $classId, $genreId, $text, $questionsJson, $t_xp) {
        try {
            $sql = "UPDATE tasks SET t_name=?, t_type_fk=?, t_level_fk=?, t_class_fk=?, t_genre_fk=?, t_text=?, t_questions=?, t_xp=? WHERE t_id=?";
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$name, $typeId, $levelId, $classId, $genreId, $text, $questionsJson, $t_xp, $taskId])) {
                return ['success' => true];
            }
            return ['success' => false, 'error' => 'Kunde inte uppdatera uppgiften.'];
        } catch (PDOException $e) { return ['success' => false, 'error' => $e->getMessage()]; }
    }

    public function deleteTask($taskId) {
        try {
            $sql = "DELETE FROM tasks WHERE t_id = ?";
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$taskId])) { return ['success' => true]; }
            return ['success' => false, 'error' => 'Kunde inte ta bort uppgiften.'];
        } catch (PDOException $e) { return ['success' => false, 'error' => $e->getMessage()]; }
    }

    // --- HÄMTA UPPGIFTER ---
    public function getAllTasks() {
        try {
            $sql = "SELECT tasks.*, users.u_name AS teacher_name, task_types.tt_name AS type_name, 
                           task_levels.tl_name AS level_name, task_levels.tl_level, 
                           classes.c_name AS class_name, genres.g_name AS genre_name
                    FROM tasks
                    LEFT JOIN users ON tasks.t_teacher_fk = users.u_id
                    LEFT JOIN task_types ON tasks.t_type_fk = task_types.tt_id
                    LEFT JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id
                    LEFT JOIN classes ON tasks.t_class_fk = classes.c_id
                    LEFT JOIN genres ON tasks.t_genre_fk = genres.g_id
                    ORDER BY tasks.t_id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }
    
    public function getTaskById($id) {
        try {
            $sql = "SELECT tasks.*, users.u_name AS teacher_name, task_types.tt_name AS type_name, 
                           task_levels.tl_name AS level_name, task_levels.tl_level, 
                           classes.c_name AS class_name, genres.g_name AS genre_name
                    FROM tasks
                    LEFT JOIN users ON tasks.t_teacher_fk = users.u_id
                    LEFT JOIN task_types ON tasks.t_type_fk = task_types.tt_id
                    LEFT JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id
                    LEFT JOIN classes ON tasks.t_class_fk = classes.c_id
                    LEFT JOIN genres ON tasks.t_genre_fk = genres.g_id
                    WHERE tasks.t_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return false; }
    }

    public function getTasksForStudent($studentId, $typeId = null, $genreId = null) {
        try {
            $sql = "SELECT tasks.*, users.u_name AS teacher_name, task_types.tt_name AS type_name, 
                           task_levels.tl_name AS level_name, task_levels.tl_level, 
                           student_tasks.st_score, student_tasks.st_completed,
                           classes.c_name AS class_name, genres.g_name AS genre_name
                    FROM tasks
                    LEFT JOIN users ON tasks.t_teacher_fk = users.u_id
                    LEFT JOIN task_types ON tasks.t_type_fk = task_types.tt_id
                    LEFT JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id
                    LEFT JOIN student_tasks ON tasks.t_id = student_tasks.st_t_id_fk AND student_tasks.st_s_id_fk = ?
                    LEFT JOIN classes ON tasks.t_class_fk = classes.c_id
                    LEFT JOIN genres ON tasks.t_genre_fk = genres.g_id";
            
            $params = [$studentId]; 
            $whereConditions = [];

            if ($typeId !== null && is_numeric($typeId)) {
                $whereConditions[] = "tasks.t_type_fk = ?";
                $params[] = $typeId; 
            }
            if ($genreId !== null && is_numeric($genreId)) {
                $whereConditions[] = "tasks.t_genre_fk = ?";
                $params[] = $genreId; 
            }

            if (count($whereConditions) > 0) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $sql .= " ORDER BY task_levels.tl_level ASC, tasks.t_id ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    public function getTasksFiltered($teacherId = null, $typeId = null, $levelId = null, $classId = null, $genreId = null) {
        try {
            $sql = "SELECT tasks.*, users.u_name AS teacher_name, task_types.tt_name AS type_name, 
                           task_levels.tl_name AS level_name, task_levels.tl_level, 
                           classes.c_name AS class_name, genres.g_name AS genre_name
                    FROM tasks
                    LEFT JOIN users ON tasks.t_teacher_fk = users.u_id
                    LEFT JOIN task_types ON tasks.t_type_fk = task_types.tt_id
                    LEFT JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id
                    LEFT JOIN classes ON tasks.t_class_fk = classes.c_id
                    LEFT JOIN genres ON tasks.t_genre_fk = genres.g_id";
            
            $whereConditions = [];
            $params = [];

            if ($teacherId !== null) { $whereConditions[] = "tasks.t_teacher_fk = ?"; $params[] = $teacherId; }
            if ($typeId !== null) { $whereConditions[] = "tasks.t_type_fk = ?"; $params[] = $typeId; }
            if ($levelId !== null) { $whereConditions[] = "tasks.t_level_fk = ?"; $params[] = $levelId; }
            if ($classId !== null) { $whereConditions[] = "tasks.t_class_fk = ?"; $params[] = $classId; }
            if ($genreId !== null) { $whereConditions[] = "tasks.t_genre_fk = ?"; $params[] = $genreId; }

            if (count($whereConditions) > 0) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $sql .= " ORDER BY tasks.t_id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    // --- PROGRESSION & RESULTAT ---

    // UPPDATERAD: Nu tar den även emot genreId!
    public function getUnlockedLevel($studentId, $typeId, $genreId = null) {
        try {
            $sql = "SELECT MAX(task_levels.tl_level) 
                    FROM student_tasks 
                    JOIN tasks ON student_tasks.st_t_id_fk = tasks.t_id
                    JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id
                    WHERE student_tasks.st_s_id_fk = ? 
                    AND tasks.t_type_fk = ?
                    AND student_tasks.st_completed = 1";
            
            $params = [$studentId, $typeId];

            // NYTT: Filtrera på genre också om det finns
            if ($genreId !== null) {
                $sql .= " AND tasks.t_genre_fk = ?";
                $params[] = $genreId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $maxLevel = $stmt->fetchColumn();
            return ($maxLevel) ? $maxLevel + 1 : 1;
        } catch (PDOException $e) { return 1; }
    }

    public function saveTaskResult($studentId, $taskId, $score, $completed) {
         try {
            $stmt = $this->pdo->prepare("SELECT st_id FROM student_tasks WHERE st_s_id_fk = ? AND st_t_id_fk = ?");
            $stmt->execute([$studentId, $taskId]);
            $existing = $stmt->fetch();

            if ($existing) {
                $sql = "UPDATE student_tasks SET st_score = ?, st_completed = ? WHERE st_id = ?";
                $updateStmt = $this->pdo->prepare($sql);
                $updateStmt->execute([$score, $completed, $existing['st_id']]);
            } else {
                $sql = "INSERT INTO student_tasks (st_s_id_fk, st_t_id_fk, st_score, st_completed) VALUES (?, ?, ?, ?)";
                $insertStmt = $this->pdo->prepare($sql);
                $insertStmt->execute([$studentId, $taskId, $score, $completed]);
            }
            return true; 
        } catch (PDOException $e) { return false; }
    }

    // --- GAMIFICATION (BADGES) ---

    // --- GAMIFICATION (BADGES) - NY UPPGRADERING ---

    public function checkAchievements($studentId, $currentXp) {
        $newBadges = [];

        try {
            // 1. Hämta alla badges som studenten REDAN har (för att slippa dubbletter)
            $stmt = $this->pdo->prepare("SELECT sa_achievement_fk FROM student_achievements WHERE sa_student_fk = ?");
            $stmt->execute([$studentId]);
            $myBadges = $stmt->fetchAll(PDO::FETCH_COLUMN); // En enkel lista med ID:n

            // ---------------------------------------------------
            // KATEGORI 1: XP-BASERADE BADGES (Bokmal, Mästare etc)
            // ---------------------------------------------------
            // Vi hämtar alla badges som kräver XP, men som inte är "special-badges" (ID < 90000)
            $stmt = $this->pdo->prepare("SELECT * FROM achievements WHERE a_xp_required <= ? AND a_xp_required < 90000");
            $stmt->execute([$currentXp]);
            $xpBadges = $stmt->fetchAll();

            foreach ($xpBadges as $badge) {
                if (!in_array($badge['a_id'], $myBadges)) {
                    $this->awardBadge($studentId, $badge['a_id']);
                    $newBadges[] = $badge;
                    $myBadges[] = $badge['a_id']; // Lägg till i listan så vi vet att vi har den
                }
            }

            // ---------------------------------------------------
            // KATEGORI 2: SPELSÄTT (Klara Nivå 10 i en viss typ)
            // ---------------------------------------------------
            // Mappning: Namn i DB => Typ-ID (Kolla i task_types tabellen om ID stämmer!)
            $typeMapping = [
                'Quizmästaren' => 1, // Flervalsfrågor
                'Ordningsvakten' => 2, // Sortering
                'Pusselbiten' => 3, // Para ihop
                'Sanningssägaren' => 4, // Sant/Falskt
                'Ordgeniet' => 5  // Textluckor
            ];

            foreach ($typeMapping as $badgeName => $typeId) {
                // Kolla om badge redan finns
                $badgeId = $this->getBadgeIdByName($badgeName);
                if ($badgeId && !in_array($badgeId, $myBadges)) {
                    // Har studenten klarat en uppgift på nivå 10 av denna typ?
                    if ($this->hasCompletedTaskAtLevel($studentId, 10, 'type', $typeId)) {
                        $this->awardBadge($studentId, $badgeId);
                        $newBadges[] = ['a_name' => $badgeName]; // För display
                    }
                }
            }

            // ---------------------------------------------------
            // KATEGORI 3: GENRES (Klara Nivå 10 i en viss genre)
            // ---------------------------------------------------
            // Mappning: Namn i DB => Genre-ID
            $genreMapping = [
                'Drakryttaren' => 1, // Fantasy
                'Astronauten' => 2, // Sci-Fi
                'Detektiven' => 3, // Deckare
                'Spökjägaren' => 4, // Skräck
                'Professorn' => 5  // Fakta
            ];

            foreach ($genreMapping as $badgeName => $genreId) {
                $badgeId = $this->getBadgeIdByName($badgeName);
                if ($badgeId && !in_array($badgeId, $myBadges)) {
                    if ($this->hasCompletedTaskAtLevel($studentId, 10, 'genre', $genreId)) {
                        $this->awardBadge($studentId, $badgeId);
                        $newBadges[] = ['a_name' => $badgeName];
                    }
                }
            }

            // ---------------------------------------------------
            // KATEGORI 4: MÄNGDTRÄNING (Klara X antal på Nivå Y)
            // ---------------------------------------------------
            // Format: BadgeNamn => [Nivå, Antal]
            $grindMapping = [
                'Nyfiken Start' => [1, 5],
                'Uppvärmd' => [1, 10],
                'På God Väg' => [5, 5],
                'Erfaren' => [5, 10],
                'Eliten' => [10, 5],
                'Omöjlig' => [10, 10]
            ];

            foreach ($grindMapping as $badgeName => $req) {
                $level = $req[0];
                $countReq = $req[1];
                
                $badgeId = $this->getBadgeIdByName($badgeName);
                if ($badgeId && !in_array($badgeId, $myBadges)) {
                    // Räkna antal klarade uppgifter på denna nivå
                    $count = $this->countCompletedTasksAtLevel($studentId, $level);
                    if ($count >= $countReq) {
                        $this->awardBadge($studentId, $badgeId);
                        $newBadges[] = ['a_name' => $badgeName];
                    }
                }
            }

            return $newBadges;

        } catch (PDOException $e) { return []; }
    }

    // --- HJÄLPFUNKTIONER FÖR BADGES ---

    private function awardBadge($studentId, $badgeId) {
        $stmt = $this->pdo->prepare("INSERT INTO student_achievements (sa_student_fk, sa_achievement_fk) VALUES (?, ?)");
        $stmt->execute([$studentId, $badgeId]);
    }

    private function getBadgeIdByName($name) {
        $stmt = $this->pdo->prepare("SELECT a_id FROM achievements WHERE a_name = ?");
        $stmt->execute([$name]);
        return $stmt->fetchColumn();
    }

    private function hasCompletedTaskAtLevel($studentId, $level, $filterType, $filterId) {
        // Kollar om det finns MINST EN klarad uppgift (st_completed=1) på given nivå och typ/genre
        $sql = "SELECT COUNT(*) FROM student_tasks st
                JOIN tasks t ON st.st_t_id_fk = t.t_id
                JOIN task_levels tl ON t.t_level_fk = tl.tl_id
                WHERE st.st_s_id_fk = ? AND st.st_completed = 1 AND tl.tl_level = ?";
        
        if ($filterType == 'type') $sql .= " AND t.t_type_fk = ?";
        if ($filterType == 'genre') $sql .= " AND t.t_genre_fk = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$studentId, $level, $filterId]);
        return $stmt->fetchColumn() > 0;
    }

    private function countCompletedTasksAtLevel($studentId, $level) {
        $sql = "SELECT COUNT(*) FROM student_tasks st
                JOIN tasks t ON st.st_t_id_fk = t.t_id
                JOIN task_levels tl ON t.t_level_fk = tl.tl_id
                WHERE st.st_s_id_fk = ? AND st.st_completed = 1 AND tl.tl_level = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$studentId, $level]);
        return $stmt->fetchColumn();
    }

    public function getStudentBadges($studentId) {
        try {
            $sql = "SELECT a.*, sa.sa_date_earned 
                    FROM achievements a
                    JOIN student_achievements sa ON a.a_id = sa.sa_achievement_fk
                    WHERE sa.sa_student_fk = ?
                    ORDER BY sa.sa_date_earned DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$studentId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    // --- NYTT: RÄKNA FRAMSTEG FÖR SPECIAL-BADGES ---
    public function getSpecialBadgeProgress($studentId) {
        $progressData = [];

        // 1. KATEGORI: SPELSÄTT (Mål: Nivå 10)
        $typeMapping = [
            'Quizmästaren' => 1, 
            'Ordningsvakten' => 2, 
            'Pusselbiten' => 3, 
            'Sanningssägaren' => 4, 
            'Ordgeniet' => 5
        ];

        foreach ($typeMapping as $badgeName => $typeId) {
            // Hämta högsta klarade nivå för denna typ
            $sql = "SELECT MAX(tl.tl_level) FROM student_tasks st
                    JOIN tasks t ON st.st_t_id_fk = t.t_id
                    JOIN task_levels tl ON t.t_level_fk = tl.tl_id
                    WHERE st.st_s_id_fk = ? AND st.st_completed = 1 AND t.t_type_fk = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$studentId, $typeId]);
            $currentLevel = $stmt->fetchColumn() ?: 0; // 0 om inget klarat

            $progressData[$badgeName] = [
                'current' => $currentLevel,
                'target' => 10,
                'label' => 'Nivå'
            ];
        }

        // 2. KATEGORI: GENRES (Mål: Nivå 10)
        $genreMapping = [
            'Drakryttaren' => 1, 
            'Astronauten' => 2, 
            'Detektiven' => 3, 
            'Spökjägaren' => 4, 
            'Professorn' => 5
        ];

        foreach ($genreMapping as $badgeName => $genreId) {
            $sql = "SELECT MAX(tl.tl_level) FROM student_tasks st
                    JOIN tasks t ON st.st_t_id_fk = t.t_id
                    JOIN task_levels tl ON t.t_level_fk = tl.tl_id
                    WHERE st.st_s_id_fk = ? AND st.st_completed = 1 AND t.t_genre_fk = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$studentId, $genreId]);
            $currentLevel = $stmt->fetchColumn() ?: 0;

            $progressData[$badgeName] = [
                'current' => $currentLevel,
                'target' => 10,
                'label' => 'Nivå'
            ];
        }

        // 3. KATEGORI: MÄNGD (Mål: Antal uppgifter)
        $grindMapping = [
            'Nyfiken Start' => [1, 5],
            'Uppvärmd' => [1, 10],
            'På God Väg' => [5, 5],
            'Erfaren' => [5, 10],
            'Eliten' => [10, 5],
            'Omöjlig' => [10, 10]
        ];

        foreach ($grindMapping as $badgeName => $req) {
            $level = $req[0];
            $targetCount = $req[1];

            $sql = "SELECT COUNT(*) FROM student_tasks st
                    JOIN tasks t ON st.st_t_id_fk = t.t_id
                    JOIN task_levels tl ON t.t_level_fk = tl.tl_id
                    WHERE st.st_s_id_fk = ? AND st.st_completed = 1 AND tl.tl_level = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$studentId, $level]);
            $count = $stmt->fetchColumn();

            // Tak på count så det inte ser ut som 15/10
            if ($count > $targetCount) $count = $targetCount;

            $progressData[$badgeName] = [
                'current' => $count,
                'target' => $targetCount,
                'label' => 'Uppdrag'
            ];
        }

        return $progressData;
    }

// --- HÄMTA RELEVANTA UPPGIFTER (SMART VERSION) ---
    public function getRecentAndNextTasks($studentId, $limit = 8) {
        try {
            $sql = "
            (
                -- 1. SENAST KLARADE (Prioritet 1: Visa vad jag precis åstadkommit)
                SELECT t.*, u.u_name AS teacher_name, tt.tt_name AS type_name, 
                       tl.tl_name AS level_name, tl.tl_level, 
                       st.st_score, st.st_completed,
                       c.c_name AS class_name, g.g_name AS genre_name,
                       1 AS sort_priority 
                FROM student_tasks st
                JOIN tasks t ON st.st_t_id_fk = t.t_id
                LEFT JOIN users u ON t.t_teacher_fk = u.u_id
                LEFT JOIN task_types tt ON t.t_type_fk = tt.tt_id
                LEFT JOIN task_levels tl ON t.t_level_fk = tl.tl_id
                LEFT JOIN classes c ON t.t_class_fk = c.c_id
                LEFT JOIN genres g ON t.t_genre_fk = g.g_id
                WHERE st.st_s_id_fk = ? AND st.st_completed = 1
                ORDER BY st.st_id DESC
                LIMIT 2
            )
            UNION
            (
                -- 2. NÄSTA KAPITEL (Prioritet 2: Det direkta nästa steget i dina äventyr)
                -- Här räknar vi ut din Max-level för varje genre/typ och letar efter Level + 1
                SELECT t.*, u.u_name AS teacher_name, tt.tt_name AS type_name, 
                       tl.tl_name AS level_name, tl.tl_level, 
                       NULL as st_score, 0 as st_completed,
                       c.c_name AS class_name, g.g_name AS genre_name,
                       2 AS sort_priority 
                FROM tasks t
                JOIN task_levels tl ON t.t_level_fk = tl.tl_id
                JOIN (
                    -- Subquery: Hitta nästa nivå för varje Typ+Genre eleven spelat
                    SELECT t2.t_type_fk, t2.t_genre_fk, MAX(tl2.tl_level) + 1 as next_target_level
                    FROM student_tasks st2
                    JOIN tasks t2 ON st2.st_t_id_fk = t2.t_id
                    JOIN task_levels tl2 ON t2.t_level_fk = tl2.tl_id
                    WHERE st2.st_s_id_fk = ? AND st2.st_completed = 1
                    GROUP BY t2.t_type_fk, t2.t_genre_fk
                ) as progress ON t.t_type_fk = progress.t_type_fk 
                              AND t.t_genre_fk = progress.t_genre_fk 
                              AND tl.tl_level = progress.next_target_level
                LEFT JOIN users u ON t.t_teacher_fk = u.u_id
                LEFT JOIN task_types tt ON t.t_type_fk = tt.tt_id
                LEFT JOIN classes c ON t.t_class_fk = c.c_id
                LEFT JOIN genres g ON t.t_genre_fk = g.g_id
                
                -- Dubbelkolla att vi inte redan gjort den (om databasen har dubbletter/fel)
                WHERE t.t_id NOT IN (SELECT st_t_id_fk FROM student_tasks WHERE st_s_id_fk = ? AND st_completed = 1)
            )
            UNION
            (
                -- 3. NYA ÄVENTYR (Prioritet 3: Fyll ut listan med Nivå 1-uppgifter om det finns plats)
                SELECT t.*, u.u_name AS teacher_name, tt.tt_name AS type_name, 
                       tl.tl_name AS level_name, tl.tl_level, 
                       NULL as st_score, 0 as st_completed,
                       c.c_name AS class_name, g.g_name AS genre_name,
                       3 AS sort_priority 
                FROM tasks t
                JOIN task_levels tl ON t.t_level_fk = tl.tl_id
                LEFT JOIN users u ON t.t_teacher_fk = u.u_id
                LEFT JOIN task_types tt ON t.t_type_fk = tt.tt_id
                LEFT JOIN classes c ON t.t_class_fk = c.c_id
                LEFT JOIN genres g ON t.t_genre_fk = g.g_id
                
                -- Bara nivå 1, och bara sånt vi inte gjort
                WHERE tl.tl_level = 1
                AND t.t_id NOT IN (SELECT st_t_id_fk FROM student_tasks WHERE st_s_id_fk = ?)
                LIMIT ?
            )
            -- Sortera så att Klarade (1) kommer sist, Nästa steg (2) kommer först
            ORDER BY sort_priority ASC, tl_level ASC
            LIMIT ?";

            $stmt = $this->pdo->prepare($sql);
            
            // Parametrar:
            // 1. studentId (för "Senast klarade")
            // 2. studentId (för "Nästa kapitel" uträkning)
            // 3. studentId (för "Nästa kapitel" dubbelkoll)
            // 4. studentId (för "Nya äventyr" dubbelkoll)
            // 5. limit (för "Nya äventyr" limit)
            // 6. limit (Total limit)
            
            $stmt->execute([$studentId, $studentId, $studentId, $studentId, $limit, $limit]);
            
            return $stmt->fetchAll();

        } catch (PDOException $e) { return []; }
    }
}
?>
