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
	
	public function checkUserRegisterInfo($uname, $umail, $upass, $upassrpt, $condition, $currentUserId = null) {
   //Steps 1-3 happens only for user creation, not user edit
	if ($condition === "create") {   
    // Step 1: Username Length Validation
    if (strlen($uname) < 3 || strlen($uname) > 20) {
        return ['success' => false, 'error' => 'Username must be between 3 and 20 characters long.'];
    }

    // Step 2: Check if username already exists (only during create, unless it's the same username)
    
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE LOWER(u_name) = LOWER(?)");
        $stmt->execute([strtolower($uname)]);
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'error' => 'Username already exists.'];
        }


    // Step 3: Check if email exists and validate email format
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE LOWER(u_email) = LOWER(?)");
        $stmt->execute([strtolower($umail)]);
        if ($stmt->rowCount() > 0 && ($currentUserId === null || $stmt->fetch()['u_id'] !== $currentUserId)) {
            return ['success' => false, 'error' => 'Email already exists.'];
        }
    }

    // Step 4: Check if email is valid
    if (!filter_var($umail, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Invalid email format.'];
    }


	if($condition !== "edit" || $upass !== ""){
		// Step 5: Check if passwords match
		if ($upass !== $upassrpt) {
			return ['success' => false, 'error' => 'Passwords do not match.'];
		}

		// Step 6: Validate password strength
	   if (strlen($upass) < 6) {
			return ['success' => false, 'error' => 'Password must be at least 6 characters long.'];
		}
		if (!preg_match('/[A-Z]/', $upass)) {
			return ['success' => false, 'error' => 'Password must contain at least one uppercase letter.'];
		}
		if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $upass)) {
			return ['success' => false, 'error' => 'Password must contain at least one special character.'];
		}
	}

    // ✅ All checks passed
    return ['success' => true];
}
	
	public function createUser($uname, $ufname, $ulname, $umail, $upass, $urole){
		try {
			// Hash the password securely
			$hashedPassword = password_hash($upass, PASSWORD_DEFAULT);

			// Begin transaction
			$this->pdo->beginTransaction();

			// Insert user into database
			$stmt = $this->pdo->prepare("INSERT INTO users (u_name, u_fname, u_lname, u_email, u_password, u_isactive, u_role_fk) 
										 VALUES (?, ?, ?, ?, ?, ?, ?)");
			$stmt->execute([$uname, $ufname, $ulname, $umail, $hashedPassword, 1, $urole]);

			// Commit transaction
			$this->pdo->commit();

			return ['success' => true];

		} 
		catch (Exception $e) {
			// Rollback if something goes wrong
			$this->pdo->rollBack();
			return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
		}
	}
	
	public function editUser($userId, $uname, $ufname, $ulname, $umail, $upass, $urole) {
    try {
        // Begin transaction
        $this->pdo->beginTransaction();

        // Prepare the base SQL query to update user info (excluding username)
        $query = "UPDATE users SET u_fname = ?, u_lname = ?, u_email = ?, u_role_fk = ?";

        // If password is provided (i.e., not empty), hash and update it
        if (!empty($upass)) {
            $hashedPassword = password_hash($upass, PASSWORD_DEFAULT);
            $query .= ", u_password = ?";
            $stmt = $this->pdo->prepare($query . " WHERE u_id = ?");
            $stmt->execute([$ufname, $ulname, $umail, $urole, $hashedPassword, $userId]);
        } else {
            // If no password change, exclude the password from the query
            $stmt = $this->pdo->prepare($query . " WHERE u_id = ?");
            $stmt->execute([$ufname, $ulname, $umail, $urole, $userId]);
        }

        // Commit transaction
        $this->pdo->commit();

        return ['success' => true];
    } catch (Exception $e) {
        // Rollback if something goes wrong
        $this->pdo->rollBack();
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}
	
	public function selectUserInfo($userId) {
    try {
        // Prepare the SQL statement
        $stmt = $this->pdo->prepare("SELECT u_name, u_fname, u_lname, u_email, u_role_fk FROM users WHERE u_id = ?");
        $stmt->execute([$userId]);

        // Fetch user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return ['success' => true, 'data' => $user];
        } else {
            return ['success' => false, 'error' => 'User not found.'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

public function loginUser($input, $password) {
        try {
            // ÄNDRING: Vi kollar nu om $input matchar u_email ELLER u_name
            $stmt = $this->pdo->prepare("
                SELECT users.*, roles.r_level 
                FROM users 
                INNER JOIN roles ON users.u_role_fk = roles.r_id 
                WHERE users.u_email = ? OR users.u_name = ?
            ");
            
            // Vi skickar in $input två gånger (en för email-kollen, en för namn-kollen)
            $stmt->execute([$input, $input]); 
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['u_password'])) {
                
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['u_id'];
                $_SESSION['username'] = $user['u_name'];
                $_SESSION['role_level'] = $user['r_level'];
                $_SESSION['user_xp'] = $user['u_xp'];
                $_SESSION['user_level'] = $user['u_level'];
                
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
	
	public function searchUsers($userName){
		
		try {
        // Prepare the SQL statement
        $stmt = $this->pdo->prepare("
			SELECT u_name, u_fname, u_lname, u_email, r_name 
			FROM users 
			INNER JOIN roles 
			ON users.u_role_fk = roles.r_id
			WHERE u_name LIKE ?");
		$stmt->execute(["%" . $userName . "%"]);

        // Fetch user data
        $userList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($userList) {
            return ['success' => true, 'data' => $userList];
        } else {
            return ['success' => false, 'error' => 'User not found.'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
		
	}
	
	
	/*public function displayUser() {
        echo "Username: {$this->username}, Role: {$this->role_level}";
		//print_r $this->pdo;
    }*/
	
}
