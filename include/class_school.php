<?php

class School {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // --- KLASS-HANTERING ---

    // --- KLASS-HANTERING ---

    // UPPDATERAD: Nu med filter för lärare
    public function getAllClasses($filterTeacher = null) {
        try {
            $sql = "SELECT c.*, u.u_name AS teacher_name, 
                    (SELECT COUNT(*) FROM users WHERE u_class_fk = c.c_id) as student_count
                    FROM classes c
                    LEFT JOIN users u ON c.c_teacher_fk = u.u_id";
            
            $params = [];

            // Filtreringslogik
            if ($filterTeacher !== null) {
                if ($filterTeacher === 'missing') {
                    // Visa klasser som saknar lärare
                    $sql .= " WHERE c.c_teacher_fk IS NULL";
                } elseif (is_numeric($filterTeacher)) {
                    // Visa klasser för en specifik lärare
                    $sql .= " WHERE c.c_teacher_fk = ?";
                    $params[] = $filterTeacher;
                }
            }

            $sql .= " ORDER BY c.c_name ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    public function getClassById($id) {
        try {
            $sql = "SELECT * FROM classes WHERE c_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) { return false; }
    }

    public function createClass($name, $teacherId) {
        try {
            // Flöde C. Steg 4.1. Om teacherId är tomt (t.ex. "Välj lärare"), sätt till NULL
            $teacherId = empty($teacherId) ? null : $teacherId;
            
            // Flöde C. Steg 4.2.
            $sql = "INSERT INTO classes (c_name, c_teacher_fk) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            // Flöde C. Steg 4.3.
            $stmt->execute([$name, $teacherId]);
            return ['success' => true];
        } catch (PDOException $e) { return ['success' => false, 'error' => $e->getMessage()]; }
    }

    public function updateClass($id, $name, $teacherId) {
        try {
            $teacherId = empty($teacherId) ? null : $teacherId;
            
            $sql = "UPDATE classes SET c_name = ?, c_teacher_fk = ? WHERE c_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $teacherId, $id]);
            return ['success' => true];
        } catch (PDOException $e) { return ['success' => false, 'error' => $e->getMessage()]; }
    }

    public function deleteClass($id) {
        try {
            // 1. Nollställ först elevernas klasstillhörighet (så de inte raderas, bara blir klasslösa)
            $resetSql = "UPDATE users SET u_class_fk = NULL WHERE u_class_fk = ?";
            $resetStmt = $this->pdo->prepare($resetSql);
            $resetStmt->execute([$id]);

            // 2. Ta bort klassen
            $sql = "DELETE FROM classes WHERE c_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            
            return ['success' => true];
        } catch (PDOException $e) { return ['success' => false, 'error' => $e->getMessage()]; }
    }

    // --- ELEV-HANTERING I KLASS ---

    public function getStudentsInClass($classId) {
        try {
            // Hämtar alla elever (Role = 1) som tillhör denna klass
            // Vi hämtar även deras nuvarande level och xp för översikt
            $sql = "SELECT u_id, u_name, u_fname, u_lname, u_email, u_level, u_xp 
                    FROM users 
                    WHERE u_class_fk = ? AND u_role_fk = 1 
                    ORDER BY u_fname ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$classId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    public function getAvailableStudents() {
        try {
            // Hämtar elever som INTE är med i någon klass än
            $sql = "SELECT u_id, u_name, u_fname, u_lname 
                    FROM users 
                    WHERE u_role_fk = 1 AND u_class_fk IS NULL 
                    ORDER BY u_fname ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    public function addStudentToClass($studentId, $classId) {
        try {
            $sql = "UPDATE users SET u_class_fk = ? WHERE u_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$classId, $studentId]);
            return true;
        } catch (PDOException $e) { return false; }
    }

    public function removeStudentFromClass($studentId) {
        try {
            $sql = "UPDATE users SET u_class_fk = NULL WHERE u_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$studentId]);
            return true;
        } catch (PDOException $e) { return false; }
    }
    
// Hämta alla lärare (för dropdown)
    // UPPDATERAD: Nu med JOIN för att kolla rätt behörighetsnivå
    public function getAllTeachers() {
        try {
            // Vi hämtar användare där rollens nivå är 5 eller högre
            $sql = "SELECT users.u_id, users.u_name 
                    FROM users 
                    JOIN roles ON users.u_role_fk = roles.r_id 
                    WHERE roles.r_level >= 5 
                    ORDER BY users.u_name ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }
}
?>
