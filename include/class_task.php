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

    public function getUnlockedLevel($studentId, $typeId) {
        try {
            $sql = "SELECT MAX(task_levels.tl_level) 
                    FROM student_tasks 
                    JOIN tasks ON student_tasks.st_t_id_fk = tasks.t_id
                    JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id
                    WHERE student_tasks.st_s_id_fk = ? 
                    AND tasks.t_type_fk = ?
                    AND student_tasks.st_completed = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$studentId, $typeId]);
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

    public function checkAchievements($studentId, $currentXp) {
        try {
            // 1. Hämta badges som eleven KAN få men INTE har än
            $sql = "SELECT * FROM achievements 
                    WHERE a_xp_required <= ? 
                    AND a_id NOT IN (
                        SELECT sa_achievement_fk FROM student_achievements WHERE sa_student_fk = ?
                    )";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$currentXp, $studentId]);
            $newBadges = $stmt->fetchAll();

            // 2. Dela ut dem
            if ($newBadges) {
                foreach ($newBadges as $badge) {
                    $insert = $this->pdo->prepare("INSERT INTO student_achievements (sa_student_fk, sa_achievement_fk) VALUES (?, ?)");
                    $insert->execute([$studentId, $badge['a_id']]);
                }
                return $newBadges;
            }
            return [];
        } catch (PDOException $e) { return []; }
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
}
?>
