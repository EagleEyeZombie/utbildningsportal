<?php

class Task {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Hämtar alla uppgifter tillsammans med namn på lärare, typ och nivå.
     */
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
            return []; // Returnera tom array vid fel
        }
    }
}
?>
