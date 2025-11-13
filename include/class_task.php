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
    public function createTask($name, $typeId, $levelId, $teacherId, $text, $questionsJson) {
        try {
            $sql = "INSERT INTO tasks (t_name, t_type_fk, t_level_fk, t_teacher_fk, t_text, t_questions) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute([$name, $typeId, $levelId, $teacherId, $text, $questionsJson])) {
                return ['success' => true];
            } else {
                return ['success' => false, 'error' => 'Kunde inte spara uppgiften.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Databasfel: ' . $e->getMessage()];
        }
    }
}
?>
