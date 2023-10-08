<?php

class account
{
    private $id;

    /* The name of the logged in account (or NULL if there is no logged in account) */
    private $name;

    public function __construct()
    {
        /* Initialize the $id and $name variables to NULL */
        $this->id = NULL;
        $this->name = NULL;
    }

    public function __destruct()
    {

    }

    /* Add a new account to the system and return its ID (the UserID column of the users table) */
    /**
     * @throws Exception
     */
    public function addAccount(string $name, string $passwd): int
    {
        /* Global $pdo object */
        global $pdo;

        /* Trim the strings to remove extra spaces */
        $name = trim($name);
        $passwd = trim($passwd);

        /* Check if the username is valid. If not, throw an exception */
        if (!$this->isNameValid($name)) {
            throw new Exception('Invalid user name');
        }

        /* Check if the password is valid. If not, throw an exception */
        if (!$this->isPasswdValid($passwd)) {
            throw new Exception('Invalid password');
        }

        /* Check if an account having the same name already exists. If it does, throw an exception */
        if (!is_null($this->getIdFromName($name))) {
            throw new Exception('User name not available');
        }

        /* Finally, add the new account */

        /* Insert query template */
        $query = 'INSERT INTO honeypot.users (Username, Password) VALUES (:name, :passwd)';

        /* Password hash */
        $hash = password_hash($passwd, PASSWORD_BCRYPT, ['cost' => 14]);

        /* Values array for PDO */
        $values = array(':name' => $name, ':passwd' => $hash);

        /* Execute the query */
        try {
            $res = $pdo->prepare($query);
            $res->execute($values);
        } catch (PDOException) {
            /* If there is a PDO exception, throw a standard exception */
            throw new Exception('Database query error');
        }

        /* Return the new ID */
        return $pdo->lastInsertId();
    }

    /* A sanitization check for the account username */
    public function isNameValid(string $name): bool
    {
        /* Initialize the return variable */
        $valid = TRUE;

        /* Example check: the length must be between 8 and 16 chars */
        $len = mb_strlen($name);

        if (($len < 1) || ($len > 50)) {
            $valid = FALSE;
        }

        /* You can add more checks here */
        $whiteList = 'abcdefghijklmnopqrstuvwxyz123456789';
        $name = strtolower($name);
        for ($i = 0; $i < mb_strlen($name); $i++) {
            $char = mb_substr($name, $i, 1);

            if (mb_strpos($whiteList, $char) === FALSE) {
                $valid = FALSE;
            }
        }

        return $valid;
    }

    /* A sanitization check for the account password */
    public function isPasswdValid(string $passwd): bool
    {
        /* Initialize the return variable */
        $valid = TRUE;

        /* Example check: the length must be between 8 and 16 chars */
        $len = mb_strlen($passwd);

        if (($len < 2) || ($len > 50)) {
            $valid = FALSE;
        }

        /* You can add more checks here */

        return $valid;
    }

    /* Returns the account id having $name as name, or NULL if it's not found */
    /**
     * @throws Exception
     */
    public function getIdFromName(string $name): ?int
    {
        /* Global $pdo object */
        global $pdo;

        /* Since this method is public, we check $name again here */
        if (!$this->isNameValid($name)) {
            throw new Exception('Invalid user name');
        }

        /* Initialize the return value. If no account is found, return NULL */
        $id = NULL;

        /* Search the ID on the database */
        $query = 'SELECT UserID FROM honeypot.users WHERE (Username = :name)';
        $values = array(':name' => $name);

        try {
            $res = $pdo->prepare($query);
            $res->execute($values);
        } catch (PDOException) {
            /* If there is a PDO exception, throw a standard exception */
            throw new Exception('Database query error');
        }

        $row = $res->fetch(PDO::FETCH_ASSOC);

        /* There is a result: get its ID */
        if (is_array($row)) {
            $id = intval($row['UserID']);
        }

        return $id;
    }

    /**
     * @throws Exception
     */
    public function login(string $name, string $passwd): bool
    {
        /* Global $pdo object */
        global $pdo;

        /* Trim the strings to remove extra spaces */
        $name = trim($name);
        $passwd = trim($passwd);

        /* Check if the username is valid. If not, return FALSE meaning the authentication failed */
        if (!$this->isNameValid($name)) {
            return FALSE;
        }

        /* Check if the password is valid. If not, return FALSE meaning the authentication failed */
        if (!$this->isPasswdValid($passwd)) {
            return FALSE;
        }

        /* Look for the account in the db. Note: the account must be enabled (account_enabled = 1) */
        $query = 'SELECT * FROM honeypot.users WHERE (Username = :name) AND (enabled = 1)';

        /* Values array for PDO */
        $values = array(':name' => $name);

        /* Execute the query */
        try {
            $res = $pdo->prepare($query);
            $res->execute($values);
        } catch (PDOException) {
            /* If there is a PDO exception, throw a standard exception */
            throw new Exception('Database query error');
        }

        $row = $res->fetch(PDO::FETCH_ASSOC);

        /* If there is a result, we must check if the password matches using password_verify() */
        if (is_array($row)) {
            if (password_verify($passwd, $row['Password'])) {
                /* Authentication succeeded. Set the class properties (id and name) */
                $this->id = intval($row['UserID']);
                $this->name = $name;

                /* Register the current Sessions on the database */
                try {
                    $this->registerLoginSession();
                } catch (Exception) {
                    throw new Exception('Session error');
                }

                /* Finally, Return TRUE */
                return TRUE;
            }
        }

        /* If we are here, it means the authentication failed: return FALSE */
        return FALSE;
    }

    /* Saves the current Session ID with the account ID */
    /**
     * @throws Exception
     */
    private function registerLoginSession(): void
    {
        /* Global $pdo object */
        global $pdo;

        /* Check that a Session has been started */
        if (session_status() == PHP_SESSION_ACTIVE) {
            /* 	Use a REPLACE statement to:
                - insert a new row with the session id, if it doesn't exist, or...
                - update the row having the session id, if it does exist.
            */
            $query = 'REPLACE INTO honeypot.user_sessions (SessionID, UserID, LoginTime) VALUES (:sid, :accountId, NOW())';
            $values = array(':sid' => session_id(), ':accountId' => $this->id);

            /* Execute the query */
            try {
                $res = $pdo->prepare($query);
                $res->execute($values);
            } catch (PDOException) {
                /* If there is a PDO exception, throw a standard exception */
                throw new Exception('Database query error');
            }
            $getSessions = 'SELECT COUNT(*) from honeypot.user_sessions where UserID=:id';
            $values = array(':id' => $this->id);
            $stmt = $pdo->prepare($getSessions);
            $stmt->execute($values);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (is_array($row)) {
                $count = intval($row['COUNT(*)']);
                if ($count > 1) {
                    $deleteOldSession = 'DELETE from honeypot.user_sessions where UserID =:id order by LoginTime limit 1';
                    $stmt = $pdo->prepare($deleteOldSession);
                    $stmt->execute($values);
                }
            }
        }
    }

    /* Login using Sessions */
    /**
     * @throws Exception
     */
    public function sessionLogin(): bool
    {
        /* Global $pdo object */
        global $pdo;

        /* Check that the Session has been started */
        if (session_status() == PHP_SESSION_ACTIVE) {
            /*
                Query template to look for the current session ID on the account_sessions table.
                The query also make sure the Session is not older than 7 days
            */
            $query = 'SELECT * FROM honeypot.user_sessions, honeypot.users WHERE (user_sessions.SessionID = :sid) ' .
                'AND (user_sessions.LoginTime >= (NOW() - INTERVAL 7 DAY)) AND (user_sessions.UserID = users.UserID) ' .
                'AND (users.enabled = 1)';

            /* Values array for PDO */
            $values = array(':sid' => session_id());

            /* Execute the query */
            try {
                $res = $pdo->prepare($query);
                $res->execute($values);
            } catch (PDOException) {
                /* If there is a PDO exception, throw a standard exception */
                throw new Exception('Database query error');
            }

            $row = $res->fetch(PDO::FETCH_ASSOC);

            if (is_array($row)) {
                /* Authentication succeeded. Set the class properties (id and name) and return TRUE*/
                $this->id = intval($row['UserID']);
                $this->name = $row['Username'];
                $query = 'UPDATE honeypot.user_sessions SET LoginTime = NOW() WHERE UserID = :id';
                $values = array(':id' => $this->id);
                try {
                    $res = $pdo->prepare($query);
                    $res->execute($values);
                } catch (PDOException) {
                    /* If there is a PDO exception, throw a standard exception */
                    throw new Exception('Database query error lul');
                }
                return TRUE;
            }
        }

        /* If we are here, the authentication failed */
        return FALSE;
    }

    /* Logout the current user */
    /**
     * @throws Exception
     */
    public function logout(): void
    {
        /* Global $pdo object */
        global $pdo;

        /* If there is no logged-in user, do nothing */
        if (is_null($this->id)) {
            return;
        }

        /* Reset the account-related properties */
        $this->id = NULL;
        $this->name = NULL;

        /* If there is an open Session, remove it from the account_sessions table */
        if (session_status() == PHP_SESSION_ACTIVE) {
            /* Delete query */
            $query = 'DELETE FROM honeypot.user_sessions WHERE (SessionID = :sid)';

            /* Values array for PDO */
            $values = array(':sid' => session_id());

            /* Execute the query */
            try {
                $res = $pdo->prepare($query);
                $res->execute($values);
            } catch (PDOException) {
                /* If there is a PDO exception, throw a standard exception */
                throw new Exception('Database query error');
            }
        }
    }

    /**
     * @throws Exception
     */
    public function solvedChallenge(int $chNo): void
    {
        /* Global $pdo object */
        global $pdo;

        $id = $this->id;

        /* Edit query template */
        $query = 'UPDATE honeypot.users SET challenge' . $chNo . ' = 1 WHERE UserID = :id';

        /* Values array for PDO */
        $values = array(':id' => $id);

        /* Execute the query */
        try {
            $res = $pdo->prepare($query);
            $res->execute($values);
        } catch (PDOException) {
            /* If there is a PDO exception, throw a standard exception */
            throw new Exception('Database query error');
        }
    }

    /**
     * @throws Exception
     */
    public function getSolvedChallenges(): array
    {
        /* Global $pdo object */
        global $pdo;
        $id = $this->id;
        /* Initialize the return value. If no account is found, return NULL */
        $solved = array();

        /* Search the ID on the database */
        $query = 'SELECT challenge1, challenge2, challenge3, challenge4, challenge5 FROM honeypot.users WHERE (UserID = :id)';
        $values = array(':id' => $id);

        try {
            $res = $pdo->prepare($query);
            $res->execute($values);
        } catch (PDOException) {
            /* If there is a PDO exception, throw a standard exception */
            throw new Exception('Database query error');
        }

        $row = $res->fetch(PDO::FETCH_ASSOC);

        /* There is a result: get its ID */
        if (is_array($row)) {
            array_push($solved, intval($row['challenge1']),
                intval($row['challenge2']),
                intval($row['challenge3']),
                intval($row['challenge4']),
                intval($row['challenge5']));

        }

        return $solved;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }
}