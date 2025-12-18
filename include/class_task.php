<?php

class Task {
    private $pdo;

    // KONSTRUKTOR: Dependency Injection
    // Vi tar emot databaskopplingen utifrån. Detta gör klassen oberoende av
    // hur uppkopplingen skapades (bra för testning och underhåll).
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ---------------------------------------------------------
    // HÄMTA METADATA (För formulär och listor)
    // ---------------------------------------------------------
    // Dessa metoder hämtar grunddata som behövs för dropdown-menyer i admin-panelen.

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

    // ---------------------------------------------------------
    // CRUD - CREATE, UPDATE, DELETE (Flöde C - Admin)
    // ---------------------------------------------------------

    // SKAPA UPPGIFT (CREATE)
    // Tar emot all data från admin_create_task.php och sparar i databasen.
    public function createTask($name, $typeId, $levelId, $teacherId, $classId, $genreId, $text, $questionsJson, $t_xp) {
        try {
            // ÄNDRAT: Lade till t_created i SQL-frågan
            // Vi använder databasens inbyggda funktion NOW() för att sätta tidsstämpeln.
            // Frågetecknen (?) är platshållare för Prepared Statements (Säkerhet mot SQL Injection).
            $sql = "INSERT INTO tasks (t_name, t_type_fk, t_level_fk, t_teacher_fk, t_class_fk, t_genre_fk, t_text, t_questions, t_xp, t_created) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            
            // Execute kör frågan med datan i en separat kanal, vilket omöjliggör injektioner.
            if ($stmt->execute([$name, $typeId, $levelId, $teacherId, $classId, $genreId, $text, $questionsJson, $t_xp])) {
                return ['success' => true];
            }
            return ['success' => false, 'error' => 'Kunde inte spara uppgiften.'];
        } catch (PDOException $e) { return ['success' => false, 'error' => $e->getMessage()]; }
    }

    // UPPDATERA UPPGIFT (UPDATE)
    // Uppdaterar en befintlig rad. Observera WHERE t_id = ? på slutet för att inte skriva över allt.
    public function updateTask($taskId, $name, $typeId, $levelId, $classId, $genreId, $text, $questionsJson, $t_xp, $teacherId) { // <--- $teacherId tillagd
    try {
        // Lade till t_teacher_fk=? i SQL
        $sql = "UPDATE tasks SET t_name=?, t_type_fk=?, t_level_fk=?, t_class_fk=?, t_genre_fk=?, t_text=?, t_questions=?, t_xp=?, t_teacher_fk=? WHERE t_id=?";
        $stmt = $this->pdo->prepare($sql);
        // Lade till $teacherId i execute-arrayen (näst sist)
        // Ordningen i arrayen måste matcha ordningen på frågetecknen exakt.
        if ($stmt->execute([$name, $typeId, $levelId, $classId, $genreId, $text, $questionsJson, $t_xp, $teacherId, $taskId])) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Kunde inte uppdatera uppgiften.'];
    } catch (PDOException $e) { return ['success' => false, 'error' => $e->getMessage()]; }
}

    // TA BORT UPPGIFT (DELETE)
    // Raderar en uppgift. Om du har Foreign Key constraints inställda på CASCADE i databasen,
    // kommer även alla elevresultat kopplade till denna uppgift att försvinna automatiskt.
    public function deleteTask($taskId) {
        try {
            $sql = "DELETE FROM tasks WHERE t_id = ?";
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$taskId])) { return ['success' => true]; }
            return ['success' => false, 'error' => 'Kunde inte ta bort uppgiften.'];
        } catch (PDOException $e) { return ['success' => false, 'error' => $e->getMessage()]; }
    }

    // ---------------------------------------------------------
    // HÄMTA UPPGIFTER & SÖKNING (Flöde C & D)
    // ---------------------------------------------------------

    // HÄMTA ALLT (Enkel lista)
    // Används sällan i produktion förutom vid felsökning, då den saknar filter.
    public function getAllTasks() {
        // Enkel hämtning utan filter (kan användas som fallback)
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
                    ORDER BY tasks.t_created DESC"; // Sortera på datum default
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }
    
    // HÄMTA EN (För Task View / Edit)
    // Används i både admin_edit_task.php (för att fylla formuläret) och task_view.php (för att visa uppgiften).
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

    // HÄMTA FÖR ELEV (Dashboard - Sökresultat)
    // Denna funktion används när eleven filtrerar på dashboarden.
    // Den JOINar med `student_tasks` för att vi ska kunna se om eleven redan klarat uppgiften (st_completed).
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

            // Bygger dynamisk WHERE-sats baserat på filter
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

    // --- ADMIN: AVANCERAD FILTRERING & PAGINERING (Flöde C) ---
    // Denna funktion driver listan i admin_tasks.php.
    // UPPDATERAD: Hanterar nu 'missing' för lärare (raderade användare)
    public function getTasksFiltered($teacherId, $typeId, $levelId, $classId, $genreId, $sortCol, $sortDir, $limit, $offset) {
        try {
            // Whitelisting för sortering för att förhindra SQL Injection i ORDER BY
            $allowedSorts = ['t_id', 't_name', 'type_name', 'genre_name', 'level_name', 't_xp', 'teacher_name', 't_created'];
            if (!in_array($sortCol, $allowedSorts)) $sortCol = 't_created'; 
            $sortDir = strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';

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

            // NY LOGIK HÄR: Filter för lärare
            if ($teacherId !== null) { 
                if ($teacherId === 'missing') {
                    // Hämta uppgifter där lärare är NULL (raderad)
                    $whereConditions[] = "tasks.t_teacher_fk IS NULL";
                } else {
                    // Hämta specifik lärare
                    $whereConditions[] = "tasks.t_teacher_fk = ?"; 
                    $params[] = $teacherId; 
                }
            }
            
            if ($typeId !== null) { $whereConditions[] = "tasks.t_type_fk = ?"; $params[] = $typeId; }
            if ($levelId !== null) { $whereConditions[] = "tasks.t_level_fk = ?"; $params[] = $levelId; }
            if ($classId !== null) { $whereConditions[] = "tasks.t_class_fk = ?"; $params[] = $classId; }
            if ($genreId !== null) { $whereConditions[] = "tasks.t_genre_fk = ?"; $params[] = $genreId; }

            if (count($whereConditions) > 0) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            // Paginering: LIMIT och OFFSET bestämmer vilken sida vi visar
            $sql .= " ORDER BY $sortCol $sortDir LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }
    
    // --- ADMIN: RÄKNA TOTALT ANTAL (För Paginering) ---
    // Måste ha samma filterlogik som getTasksFiltered för att räkna rätt sidor.
    public function getTasksCountFiltered($teacherId, $typeId, $levelId, $classId, $genreId) {
        try {
            $sql = "SELECT COUNT(*) FROM tasks";
            $whereConditions = [];
            $params = [];

            // SAMMA LOGIK HÄR:
            if ($teacherId !== null) { 
                if ($teacherId === 'missing') {
                    $whereConditions[] = "t_teacher_fk IS NULL";
                } else {
                    $whereConditions[] = "t_teacher_fk = ?"; 
                    $params[] = $teacherId; 
                }
            }
            
            if ($typeId !== null) { $whereConditions[] = "t_type_fk = ?"; $params[] = $typeId; }
            if ($levelId !== null) { $whereConditions[] = "t_level_fk = ?"; $params[] = $levelId; }
            if ($classId !== null) { $whereConditions[] = "t_class_fk = ?"; $params[] = $classId; }
            if ($genreId !== null) { $whereConditions[] = "t_genre_fk = ?"; $params[] = $genreId; }

            if (count($whereConditions) > 0) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) { return 0; }
    }

    // ---------------------------------------------------------
    // REKOMMENDATIONSMOTOR (Flöde D - Dashboard)
    // ---------------------------------------------------------
    // Denna funktion använder UNION för att kombinera tre olika listor till en:
    // 1. Senast klarade (för att förbättra resultat).
    // 2. Nästa logiska steg (Progression i en serie).
    // 3. Nya äventyr (Level 1-uppgifter).
    // --- REKOMMENDATIONER (Dashboard) - KVAR SOM FÖRUT ---
    public function getRecentAndNextTasks($studentId, $limit = 8) {
        try {
            $sql = "
            (
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
                SELECT t.*, u.u_name AS teacher_name, tt.tt_name AS type_name, 
                       tl.tl_name AS level_name, tl.tl_level, 
                       NULL as st_score, 0 as st_completed,
                       c.c_name AS class_name, g.g_name AS genre_name,
                       2 AS sort_priority 
                FROM tasks t
                JOIN task_levels tl ON t.t_level_fk = tl.tl_id
                JOIN (
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
                WHERE t.t_id NOT IN (SELECT st_t_id_fk FROM student_tasks WHERE st_s_id_fk = ? AND st_completed = 1)
            )
            UNION
            (
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
                WHERE tl.tl_level = 1
                AND t.t_id NOT IN (SELECT st_t_id_fk FROM student_tasks WHERE st_s_id_fk = ?)
                LIMIT ?
            )
            ORDER BY sort_priority ASC, tl_level ASC
            LIMIT ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$studentId, $studentId, $studentId, $studentId, $limit, $limit]);
            
            return $stmt->fetchAll();

        } catch (PDOException $e) { return []; }
    }

    // ---------------------------------------------------------
    // PROGRESSION & RESULTAT (Flöde B & D)
    // ---------------------------------------------------------

    // LÅS UPP NIVÅER (Flöde D - Säkerhet)
    // Beräknar vilken nivå eleven har rätt att se baserat på vad de klarat tidigare.
    // Returnerar MaxLevel + 1.
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

    // SPARA RESULTAT (Flöde B)
    // Sparar resultatet i `student_tasks`. Om raden finns uppdateras den (UPDATE), annars skapas den (INSERT).
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

    // ---------------------------------------------------------
    // GAMIFICATION (Flöde B - Badges)
    // ---------------------------------------------------------

    // Hämta elevens badges
    public function getStudentBadges($studentId) {
        try {
            $sql = "SELECT achievements.*, student_achievements.sa_date_earned 
                    FROM achievements
                    JOIN student_achievements ON achievements.a_id = student_achievements.sa_achievement_fk
                    WHERE student_achievements.sa_student_fk = ?
                    ORDER BY student_achievements.sa_date_earned DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$studentId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    // KOLLA ACHIEVEMENTS (Motorn för badges)
    // Denna körs varje gång en uppgift lämnas in.
    // Den kollar om elevens nya XP eller antal uppgifter uppfyller kraven för nya badges.
    public function checkAchievements($studentId, $currentXp) {
        $newBadges = [];
        try {
            // Flöde B. Steg 3.1: Hämta elevens nuvarande badges (för att inte ge dubbletter)
            $stmt = $this->pdo->prepare("SELECT sa_achievement_fk FROM student_achievements WHERE sa_student_fk = ?");
            $stmt->execute([$studentId]);
            $myBadges = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Flöde B. Steg 3.2: Kolla XP-baserade badges (Databasstyrd logik)
            // Vi hämtar alla badges som kräver mindre eller lika mycket XP som eleven har.
            $stmt = $this->pdo->prepare("SELECT * FROM achievements WHERE a_xp_required <= ? AND a_xp_required < 90000");
            $stmt->execute([$currentXp]);
            $xpBadges = $stmt->fetchAll();

            foreach ($xpBadges as $badge) {
                if (!in_array($badge['a_id'], $myBadges)) {
                    $this->awardBadge($studentId, $badge['a_id']);
                    $newBadges[] = $badge;
                    $myBadges[] = $badge['a_id'];
                }
            }

            // KOD-BASERADE BADGES (Specialregler)
            // Vissa badges är svåra att definiera bara med SQL-regler, så vi kollar dem med kod här.
            
            // Typ-badges (T.ex. "Klara 10 Quiz")
            $typeMapping = [
                'Quizmästaren' => 1, 'Ordningsvakten' => 2, 'Pusselbiten' => 3, 
                'Sanningssägaren' => 4, 'Ordgeniet' => 5
            ];
            foreach ($typeMapping as $badgeName => $typeId) {
                $badge = $this->getBadgeByName($badgeName); 
                if ($badge && !in_array($badge['a_id'], $myBadges)) {
                    if ($this->hasCompletedTaskAtLevel($studentId, 10, 'type', $typeId)) {
                        $this->awardBadge($studentId, $badge['a_id']);
                        $newBadges[] = $badge; 
                        $myBadges[] = $badge['a_id'];
                    }
                }
            }

            // Genre-badges (T.ex. "Klara 10 Fantasy-uppgifter")
            $genreMapping = [
                'Drakryttaren' => 1, 'Astronauten' => 2, 'Detektiven' => 3, 
                'Spökjägaren' => 4, 'Professorn' => 5
            ];
            foreach ($genreMapping as $badgeName => $genreId) {
                $badge = $this->getBadgeByName($badgeName);
                if ($badge && !in_array($badge['a_id'], $myBadges)) {
                    if ($this->hasCompletedTaskAtLevel($studentId, 10, 'genre', $genreId)) {
                        $this->awardBadge($studentId, $badge['a_id']);
                        $newBadges[] = $badge;
                        $myBadges[] = $badge['a_id'];
                    }
                }
            }

            // Flöde B. Steg 3.3: Kolla "Grind"-badges (Antal klarade uppgifter totalt)
            $grindMapping = [
                'Nyfiken Start' => [1, 5], 'Uppvärmd' => [1, 10], 'På God Väg' => [5, 5],
                'Erfaren' => [5, 10], 'Eliten' => [10, 5], 'Omöjlig' => [10, 10]
            ];
            foreach ($grindMapping as $badgeName => $req) {
                // Här anropas hjälpfunktionen countCompletedTasksAtLevel
                $badge = $this->getBadgeByName($badgeName);
                if ($badge && !in_array($badge['a_id'], $myBadges)) {
                    if ($this->countCompletedTasksAtLevel($studentId, $req[0]) >= $req[1]) {
                        $this->awardBadge($studentId, $badge['a_id']);
                        $newBadges[] = $badge;
                        $myBadges[] = $badge['a_id'];
                    }
                }
            }

            return $newBadges;
        } catch (PDOException $e) { return []; }
    }

    // --- HJÄLPFUNKTIONER FÖR BADGES ---
    
    // Delar ut en badge (INSERT i student_achievements)
    private function awardBadge($studentId, $badgeId) {
        $stmt = $this->pdo->prepare("INSERT INTO student_achievements (sa_student_fk, sa_achievement_fk) VALUES (?, ?)");
        $stmt->execute([$studentId, $badgeId]);
    }

    private function getBadgeByName($name) {
        $stmt = $this->pdo->prepare("SELECT * FROM achievements WHERE a_name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Kollar om eleven har klarat en viss nivå av en viss typ/genre
    private function hasCompletedTaskAtLevel($studentId, $level, $filterType, $filterId) {
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

    // Räknar totalt antal klarade uppgifter på en nivå
    private function countCompletedTasksAtLevel($studentId, $level) {
        $sql = "SELECT COUNT(*) FROM student_tasks st
                JOIN tasks t ON st.st_t_id_fk = t.t_id
                JOIN task_levels tl ON t.t_level_fk = tl.tl_id
                WHERE st.st_s_id_fk = ? AND st.st_completed = 1 AND tl.tl_level = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$studentId, $level]);
        return $stmt->fetchColumn();
    }

    // --- FRAMSTEG FÖR SPECIAL-BADGES (Visualisering) ---
    // Denna funktion används av badges.php för att visa "3/10 klarade" på progressbaren.
    public function getSpecialBadgeProgress($studentId) {
        $progressData = [];

        // 1. SPELSÄTT
        $typeMapping = ['Quizmästaren' => 1, 'Ordningsvakten' => 2, 'Pusselbiten' => 3, 'Sanningssägaren' => 4, 'Ordgeniet' => 5];
        foreach ($typeMapping as $badgeName => $typeId) {
            $sql = "SELECT MAX(tl.tl_level) FROM student_tasks st JOIN tasks t ON st.st_t_id_fk = t.t_id JOIN task_levels tl ON t.t_level_fk = tl.tl_id WHERE st.st_s_id_fk = ? AND st.st_completed = 1 AND t.t_type_fk = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$studentId, $typeId]);
            $progressData[$badgeName] = ['current' => $stmt->fetchColumn() ?: 0, 'target' => 10, 'label' => 'Nivå'];
        }

        // 2. GENRES
        $genreMapping = ['Drakryttaren' => 1, 'Astronauten' => 2, 'Detektiven' => 3, 'Spökjägaren' => 4, 'Professorn' => 5];
        foreach ($genreMapping as $badgeName => $genreId) {
            $sql = "SELECT MAX(tl.tl_level) FROM student_tasks st JOIN tasks t ON st.st_t_id_fk = t.t_id JOIN task_levels tl ON t.t_level_fk = tl.tl_id WHERE st.st_s_id_fk = ? AND st.st_completed = 1 AND t.t_genre_fk = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$studentId, $genreId]);
            $progressData[$badgeName] = ['current' => $stmt->fetchColumn() ?: 0, 'target' => 10, 'label' => 'Nivå'];
        }

        // 3. MÄNGD
        $grindMapping = ['Nyfiken Start' => [1, 5], 'Uppvärmd' => [1, 10], 'På God Väg' => [5, 5], 'Erfaren' => [5, 10], 'Eliten' => [10, 5], 'Omöjlig' => [10, 10]];
        foreach ($grindMapping as $badgeName => $req) {
            $sql = "SELECT COUNT(*) FROM student_tasks st JOIN tasks t ON st.st_t_id_fk = t.t_id JOIN task_levels tl ON t.t_level_fk = tl.tl_id WHERE st.st_s_id_fk = ? AND st.st_completed = 1 AND tl.tl_level = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$studentId, $req[0]]);
            $count = $stmt->fetchColumn();
            if ($count > $req[1]) $count = $req[1];
            $progressData[$badgeName] = ['current' => $count, 'target' => $req[1], 'label' => 'Uppdrag'];
        }

        return $progressData;
    }
}
?>
