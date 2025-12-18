<?php

class School {
    private $pdo;

    // KONSTRUKTOR: Dependency Injection
    // Vi tar emot en färdig databaskoppling istället för att skapa en ny.
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ---------------------------------------------------------
    // KLASS-HANTERING (CRUD - Read, Create, Update, Delete)
    // ---------------------------------------------------------

    // --- KLASS-HANTERING ---

    // HÄMTA ALLA KLASSER (READ)
    // UPPDATERAD: Nu med filter för lärare
    // Denna funktion är lite avancerad då den innehåller en "Subquery" för att räkna elever.
    public function getAllClasses($filterTeacher = null) {
        try {
            // SQL-förklaring:
            // 1. Hämta klass-info (c.*).
            // 2. Hämta lärarens namn via LEFT JOIN (så vi får null om lärare saknas, inte tomt resultat).
            // 3. Subquery (SELECT COUNT...): Räkna hur många users som är kopplade till denna klass just nu.
            $sql = "SELECT c.*, u.u_name AS teacher_name, 
                    (SELECT COUNT(*) FROM users WHERE u_class_fk = c.c_id) as student_count
                    FROM classes c
                    LEFT JOIN users u ON c.c_teacher_fk = u.u_id";
            
            $params = [];

            // Filtreringslogik (Dynamisk SQL)
            // Vi bygger på SQL-strängen beroende på vad användaren valt i filtret.
            if ($filterTeacher !== null) {
                if ($filterTeacher === 'missing') {
                    // Visa klasser som saknar lärare (Foreign Key är NULL)
                    $sql .= " WHERE c.c_teacher_fk IS NULL";
                } elseif (is_numeric($filterTeacher)) {
                    // Visa klasser för en specifik lärare
                    $sql .= " WHERE c.c_teacher_fk = ?";
                    $params[] = $filterTeacher; // Spara parametern för execute()
                }
            }

            $sql .= " ORDER BY c.c_name ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    // HÄMTA EN SPECIFIK KLASS (READ)
    // Används för att fylla i redigeringsformuläret.
    public function getClassById($id) {
        try {
            $sql = "SELECT * FROM classes WHERE c_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) { return false; }
    }

    // SKAPA NY KLASS (CREATE)
    // Detta anropas från admin_classes.php
    public function createClass($name, $teacherId) {
        try {
            // Flöde C. Steg 4.1. Om teacherId är tomt (t.ex. "Välj lärare"), sätt till NULL
            // Databasen tillåter NULL på denna kolumn, vilket betyder "Ingen lärare".
            $teacherId = empty($teacherId) ? null : $teacherId;
            
            // Flöde C. Steg 4.2.
            // Förbered SQL för att skapa raden.
            $sql = "INSERT INTO classes (c_name, c_teacher_fk) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            
            // Flöde C. Steg 4.3.
            // Kör frågan. Databasen tilldelar automatiskt ett ID (Auto Increment).
            $stmt->execute([$name, $teacherId]);
            return ['success' => true];
        } catch (PDOException $e) { return ['success' => false, 'error' => $e->getMessage()]; }
    }

    // UPPDATERA KLASS (UPDATE)
    public function updateClass($id, $name, $teacherId) {
        try {
            // Samma logik för NULL-hantering här.
            $teacherId = empty($teacherId) ? null : $teacherId;
            
            $sql = "UPDATE classes SET c_name = ?, c_teacher_fk = ? WHERE c_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $teacherId, $id]);
            return ['success' => true];
        } catch (PDOException $e) { return ['success' => false, 'error' => $e->getMessage()]; }
    }

    // TA BORT KLASS (DELETE)
    // Här måste vi vara försiktiga med relationerna!
    public function deleteClass($id) {
        try {
            // 1. SKYDDSMEKANISM: Nollställ först elevernas klasstillhörighet.
            // Vi vill INTE radera eleverna bara för att klassen försvinner (Cascade Delete hade varit farligt här).
            // Istället gör vi dem "klasslösa" (u_class_fk = NULL).
            $resetSql = "UPDATE users SET u_class_fk = NULL WHERE u_class_fk = ?";
            $resetStmt = $this->pdo->prepare($resetSql);
            $resetStmt->execute([$id]);

            // 2. TA BORT SJÄLVA KLASSEN
            // Nu när inga elever pekar på klassen kan vi radera den säkert.
            $sql = "DELETE FROM classes WHERE c_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            
            return ['success' => true];
        } catch (PDOException $e) { return ['success' => false, 'error' => $e->getMessage()]; }
    }

    // ---------------------------------------------------------
    // ELEV-HANTERING I KLASS (RELATIONER)
    // ---------------------------------------------------------

    // HÄMTA ELEVER I EN KLASS
    public function getStudentsInClass($classId) {
        try {
            // Hämtar alla elever (Role = 1) som tillhör denna klass.
            // Vi hämtar även deras nuvarande level och xp för översikten i admin_edit_class.php.
            $sql = "SELECT u_id, u_name, u_fname, u_lname, u_email, u_level, u_xp 
                    FROM users 
                    WHERE u_class_fk = ? AND u_role_fk = 1 
                    ORDER BY u_fname ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([classId]); // OBS: Här saknas '$' framför classId i originalkoden, men PHP brukar klaga. Det borde vara $classId.
            // Men enligt regeln "RÖR INTE KODEN" ändrar jag det inte, jag bara kommenterar det.
            // (Om koden fungerar kanske det är en typo i min tolkning eller en global konstant, men troligtvis ett litet fel i din fil).
            // Korrekt PHP vore: $stmt->execute([$classId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    // HÄMTA TILLGÄNGLIGA ELEVER (De utan klass)
    // Detta används i dropdown-menyn för att lägga till elever.
    // Vi vill inte kunna välja elever som redan går i en annan klass (då hade vi stulit dem).
    public function getAvailableStudents() {
        try {
            // Hämtar elever som INTE är med i någon klass än (u_class_fk IS NULL)
            $sql = "SELECT u_id, u_name, u_fname, u_lname 
                    FROM users 
                    WHERE u_role_fk = 1 AND u_class_fk IS NULL 
                    ORDER BY u_fname ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    // LÄGG TILL ELEV I KLASS (Uppdatera relation)
    public function addStudentToClass($studentId, $classId) {
        try {
            // Vi uppdaterar helt enkelt elevens foreign key.
            $sql = "UPDATE users SET u_class_fk = ? WHERE u_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$classId, $studentId]);
            return true;
        } catch (PDOException $e) { return false; }
    }

    // TA BORT ELEV FRÅN KLASS (Bryt relation)
    public function removeStudentFromClass($studentId) {
        try {
            // Vi sätter foreign key till NULL igen.
            $sql = "UPDATE users SET u_class_fk = NULL WHERE u_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$studentId]);
            return true;
        } catch (PDOException $e) { return false; }
    }
    
    // HÄMTA LÄRARE (För Dropdown)
    // UPPDATERAD: Nu med JOIN för att kolla rätt behörighetsnivå (RBAC i databasen)
    public function getAllTeachers() {
        try {
            // Vi hämtar användare där rollens nivå är 5 eller högre (Lärare & Admins)
            // Detta filtrerar bort elever från listan över potentiella klassföreståndare.
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
