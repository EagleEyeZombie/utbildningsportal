<?php

class Task {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * NY: Hämtar alla klasser (för dropdowns)
     */
    public function getAllClasses() {
        try {
            $stmt = $this->pdo->query("SELECT c_id, c_name FROM classes ORDER BY c_name ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * NY: Hämtar alla uppgiftstyper (för dropdowns)
     */
    public function getAllTypes() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM task_types");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * NY: Hämtar alla nivåer (för dropdowns)
     */
    public function getAllLevels() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM task_levels ORDER BY tl_level ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * UPPDATERAD: Tar nu emot $classId och $t_xp
     */
    public function createTask($name, $typeId, $levelId, $teacherId, $classId, $text, $questionsJson, $t_xp) {
        try {
            $sql = "INSERT INTO tasks (t_name, t_type_fk, t_level_fk, t_teacher_fk, t_class_fk, t_text, t_questions, t_xp) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute([$name, $typeId, $levelId, $teacherId, $classId, $text, $questionsJson, $t_xp])) {
                return ['success' => true];
            } else {
                return ['success' => false, 'error' => 'Kunde inte spara uppgiften.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Databasfel: ' . $e->getMessage()];
        }
    }

    /**
     * UPPDATERAD: Tar nu emot $classId och $t_xp
     */
    public function updateTask($taskId, $name, $typeId, $levelId, $classId, $text, $questionsJson, $t_xp) {
        try {
            $sql = "UPDATE tasks SET 
                        t_name = ?, 
                        t_type_fk = ?, 
                        t_level_fk = ?, 
                        t_class_fk = ?, 
                        t_text = ?, 
                        t_questions = ?, 
                        t_xp = ? 
                    WHERE t_id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute([$name, $typeId, $levelId, $classId, $text, $questionsJson, $t_xp, $taskId])) {
                return ['success' => true];
            } else {
                return ['success' => false, 'error' => 'Kunde inte uppdatera uppgiften.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Databasfel: ' . $e->getMessage()];
        }
    }

    /**
     * NY: Tar bort en uppgift
     */
    public function deleteTask($taskId) {
        try {
            $sql = "DELETE FROM tasks WHERE t_id = ?";
            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute([$taskId])) {
                return ['success' => true];
            } else {
                return ['success' => false, 'error' => 'Kunde inte ta bort uppgiften.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Databasfel: ' . $e->getMessage()];
        }
    }

    
    /**
     * UPPDATERAD: Hämtar nu även class_name
     */
    public function getAllTasks() {
        try {
            $sql = "SELECT tasks.*, 
                           users.u_name AS teacher_name, 
                           task_types.tt_name AS type_name, 
                           task_levels.tl_name AS level_name,
                           classes.c_name AS class_name
                    FROM tasks
                    LEFT JOIN users ON tasks.t_teacher_fk = users.u_id
                    LEFT JOIN task_types ON tasks.t_type_fk = task_types.tt_id
                    LEFT JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id
                    LEFT JOIN classes ON tasks.t_class_fk = classes.c_id
                    ORDER BY tasks.t_id DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * UPPDATERAD: Hämtar nu även class_name
     */
    public function getTaskById($id) {
        try {
            $sql = "SELECT tasks.*, 
                           users.u_name AS teacher_name, 
                           task_types.tt_name AS type_name, 
                           task_levels.tl_name AS level_name,
                           classes.c_name AS class_name
                    FROM tasks
                    LEFT JOIN users ON tasks.t_teacher_fk = users.u_id
                    LEFT JOIN task_types ON tasks.t_type_fk = task_types.tt_id
                    LEFT JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id
                    LEFT JOIN classes ON tasks.t_class_fk = classes.c_id
                    WHERE tasks.t_id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Spara eller uppdatera ett resultat för en elev
     */
    public function saveTaskResult($studentId, $taskId, $score, $completed) {
        try {
            $stmt = $this->pdo->prepare("SELECT st_id FROM student_tasks WHERE st_s_id_fk = ? AND st_t_id_fk = ?");
            $stmt->execute([$studentId, $taskId]);
            $existing = $stmt->fetch();

            if ($existing) {
                $sql = "UPDATE student_tasks SET st_score = ?, st_completed = ? WHERE st_id = ?";
                $updateStmt = $this->pdo->prepare($sql);
                return $updateStmt->execute([$score, $completed, $existing['st_id']]);
            } else {
                $sql = "INSERT INTO student_tasks (st_s_id_fk, st_t_id_fk, st_score, st_completed) VALUES (?, ?, ?, ?)";
                $insertStmt = $this->pdo->prepare($sql);
                return $insertStmt->execute([$studentId, $taskId, $score, $completed]);
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * UPPDATERAD: Hämtar nu även class_name
     */
    public function getTasksForStudent($studentId, $typeId = null) {
        try {
            $sql = "SELECT tasks.*, 
                           users.u_name AS teacher_name, 
                           task_types.tt_name AS type_name, 
                           task_levels.tl_name AS level_name,
                           student_tasks.st_score,
                           student_tasks.st_completed,
                           classes.c_name AS class_name
                    FROM tasks
                    LEFT JOIN users ON tasks.t_teacher_fk = users.u_id
                    LEFT JOIN task_types ON tasks.t_type_fk = task_types.tt_id
                    LEFT JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id
                    LEFT JOIN student_tasks ON tasks.t_id = student_tasks.st_t_id_fk AND student_tasks.st_s_id_fk = ?
                    LEFT JOIN classes ON tasks.t_class_fk = classes.c_id
                    ";
            
            $params = [$studentId]; 

            if ($typeId !== null && is_numeric($typeId)) {
                $sql .= " WHERE tasks.t_type_fk = ?";
                $params[] = $typeId; 
            }
            
            $sql .= " ORDER BY tasks.t_id DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * NY: Hämtar alla uppgifter skapade av en specifik lärare
     */
    public function getTasksByTeacher($teacherId) {
        try {
            $sql = "SELECT tasks.*, 
                           users.u_name AS teacher_name, 
                           task_types.tt_name AS type_name, 
                           task_levels.tl_name AS level_name,
                           classes.c_name AS class_name
                    FROM tasks
                    LEFT JOIN users ON tasks.t_teacher_fk = users.u_id
                    LEFT JOIN task_types ON tasks.t_type_fk = task_types.tt_id
                    LEFT JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id
                    LEFT JOIN classes ON tasks.t_class_fk = classes.c_id
                    WHERE tasks.t_teacher_fk = ?
                    ORDER BY tasks.t_id DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$teacherId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * UPPDATERAD: Kan nu även filtrera på $classId
     */
    public function getTasksFiltered($teacherId = null, $typeId = null, $levelId = null, $classId = null) {
        try {
            $sql = "SELECT tasks.*, 
                           users.u_name AS teacher_name, 
                           task_types.tt_name AS type_name, 
                           task_levels.tl_name AS level_name,
                           classes.c_name AS class_name
                    FROM tasks
                    LEFT JOIN users ON tasks.t_teacher_fk = users.u_id
                    LEFT JOIN task_types ON tasks.t_type_fk = task_types.tt_id
                    LEFT JOIN task_levels ON tasks.t_level_fk = task_levels.tl_id
                    LEFT JOIN classes ON tasks.t_class_fk = classes.c_id
                    ";
            
            $whereConditions = [];
            $params = [];

            if ($teacherId !== null) {
                $whereConditions[] = "tasks.t_teacher_fk = ?";
                $params[] = $teacherId;
            }
            if ($typeId !== null) {
                $whereConditions[] = "tasks.t_type_fk = ?";
                $params[] = $typeId;
            }
            if ($levelId !== null) {
                $whereConditions[] = "tasks.t_level_fk = ?";
                $params[] = $levelId;
            }
            if ($classId !== null) {
                $whereConditions[] = "tasks.t_class_fk = ?";
                $params[] = $classId;
            }

            if (count($whereConditions) > 0) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $sql .= " ORDER BY tasks.t_id DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
