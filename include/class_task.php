<?php

class Task {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Hämta alla uppgifter (Denna har du redan)
    public function getAllTasks() {
        try {
            $sql = "SELECT tasks.*, 
                           users.u_name AS teacher_name, 
                           task_types.tt_name AS type_name, 
                           task_levels.tl_name AS level_name
                    FROM tasks
                    LEFT JOIN users ON tasks.t_teacher_fk = users.u_id
                    LEFT JOIN task_types ON tasks.t_type_fk = task_types.tt_id
                    LEFT JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id
                    ORDER BY tasks.t_id DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // NY: Hämta alla uppgiftstyper
    public function getAllTypes() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM task_types");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // NY: Hämta alla nivåer
    public function getAllLevels() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM task_levels ORDER BY tl_level ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // NY: Skapa en ny uppgift
    // ÄNDRAD: Tar nu emot $t_xp
    public function createTask($name, $typeId, $levelId, $teacherId, $text, $questionsJson, $t_xp) {
        try {
            // ÄNDRAD: Lade till t_xp i SQL
            $sql = "INSERT INTO tasks (t_name, t_type_fk, t_level_fk, t_teacher_fk, t_text, t_questions, t_xp) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            
            // ÄNDRAD: Lade till $t_xp i execute
            if ($stmt->execute([$name, $typeId, $levelId, $teacherId, $text, $questionsJson, $t_xp])) {
                return ['success' => true];
            } else {
                return ['success' => false, 'error' => 'Kunde inte spara uppgiften.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Databasfel: ' . $e->getMessage()];
        }
    }
    // NY: Hämta en specifik uppgift via ID
    public function getTaskById($id) {
        try {
            $sql = "SELECT tasks.*, 
                           users.u_name AS teacher_name, 
                           task_types.tt_name AS type_name, 
                           task_levels.tl_name AS level_name
                    FROM tasks
                    LEFT JOIN users ON tasks.t_teacher_fk = users.u_id
                    LEFT JOIN task_types ON tasks.t_type_fk = task_types.tt_id
                    LEFT JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id
                    WHERE tasks.t_id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    // NY: Spara eller uppdatera ett resultat för en elev
    public function saveTaskResult($studentId, $taskId, $score, $completed) {
        try {
            // 1. Kolla om det redan finns ett resultat
            $stmt = $this->pdo->prepare("SELECT st_id FROM student_tasks WHERE st_s_id_fk = ? AND st_t_id_fk = ?");
            $stmt->execute([$studentId, $taskId]);
            $existing = $stmt->fetch();

            if ($existing) {
                // 2. Uppdatera befintligt resultat
                // Vi uppdaterar alltid till det senaste försöket (du kan ändra logiken om du vill spara 'bästa' resultatet)
                $sql = "UPDATE student_tasks SET st_score = ?, st_completed = ? WHERE st_id = ?";
                $updateStmt = $this->pdo->prepare($sql);
                return $updateStmt->execute([$score, $completed, $existing['st_id']]);
            } else {
                // 3. Skapa nytt resultat
                $sql = "INSERT INTO student_tasks (st_s_id_fk, st_t_id_fk, st_score, st_completed) VALUES (?, ?, ?, ?)";
                $insertStmt = $this->pdo->prepare($sql);
                return $insertStmt->execute([$studentId, $taskId, $score, $completed]);
            }
        } catch (PDOException $e) {
            return false;
        }
    }
    // NY: Hämta alla uppgifter + elevens resultat (för Dashboard)
    public function getTasksForStudent($studentId) {
        try {
            // Vi använder LEFT JOIN på student_tasks för att få med resultatet OM det finns
            $sql = "SELECT tasks.*, 
                           users.u_name AS teacher_name, 
                           task_types.tt_name AS type_name, 
                           task_levels.tl_name AS level_name,
                           student_tasks.st_score,
                           student_tasks.st_completed
                    FROM tasks
                    LEFT JOIN users ON tasks.t_teacher_fk = users.u_id
                    LEFT JOIN task_types ON tasks.t_type_fk = task_types.tt_id
                    LEFT JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id
                    LEFT JOIN student_tasks ON tasks.t_id = student_tasks.st_t_id_fk AND student_tasks.st_s_id_fk = ?
                    ORDER BY tasks.t_id DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$studentId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
