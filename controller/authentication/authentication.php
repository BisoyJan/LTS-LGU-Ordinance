<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../database/database.php';
require_once __DIR__ . '/../../utils/password.php';

class Authentication
{
    private $conn;

    public function __construct()
    {
        $this->conn = getConnection();
    }

    public function login($username, $password)
    {
        $query = "SELECT id, username, role, password FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Log login attempt
        error_log("Login attempt for username: " . $username);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Check if password is hashed
            if (strlen($user['password']) > 30) {
                // Password is hashed
                if (PasswordUtil::verifyPassword($password, $user['password'])) {
                    $this->setUserSession($user);
                    error_log("Login successful for user: " . $username . " with role: " . $user['role']);
                    return true;
                }
            } else {
                // Legacy password (not hashed)
                if ($password === $user['password']) {
                    // Hash the password and update it in the database
                    $hashedPassword = PasswordUtil::hashPassword($password);
                    $this->updatePassword($user['id'], $hashedPassword);
                    $this->setUserSession($user);
                    error_log("Login successful for user: " . $username . " with role: " . $user['role']);
                    return true;
                }
            }
            error_log("Invalid password for user: " . $username);
        } else {
            error_log("User not found: " . $username);
        }
        return false;
    }

    private function setUserSession($user)
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
    }

    private function updatePassword($userId, $hashedPassword)
    {
        $query = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $hashedPassword, $userId);
        $stmt->execute();
    }

    public function logout()
    {
        // Clear all session variables
        $_SESSION = array();

        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();

        // Redirect to login page
        header("Location: ../../index.php");
        exit();
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $auth = new Authentication();
    $auth->logout();
}
