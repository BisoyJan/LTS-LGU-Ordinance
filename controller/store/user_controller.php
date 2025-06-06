<?php
require '../../database/database.php';
require_once '../../utils/password.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = getConnection();

if (isset($_POST['fetch_User'])) {
    $user_id = mysqli_real_escape_string($conn, $_POST['id']);

    $sql = "SELECT id, username, name, email, role, committee_id FROM users WHERE id = '$user_id'";

    $query_run = mysqli_query($conn, $sql);

    try {
        $data = $query_run->fetch_assoc();
        $res = [
            'status' => $data ? 'success' : 'warning',
            'data' => $data ?? [],
            'message' => $data ? '' : 'User ID not found'
        ];
    } catch (mysqli_sql_exception $e) {
        $res = [
            'status' => 'error',
            'message' => 'Error fetching user: ' . $e->getMessage()
        ];
    }

    echo json_encode($res);
    return;
}

if (isset($_POST['create_User'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = PasswordUtil::hashPassword($_POST['password']); // Hash before escaping
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $committee_id = null;
    if ($role === 'legislator' || $role === 'committee') {
        $committee_id = isset($_POST['committee_id']) ? intval($_POST['committee_id']) : null;
    }

    if ($committee_id) {
        $sql = "INSERT INTO users (username, name, email, password, role, committee_id) VALUES ('$username', '$name', '$email', '$password', '$role', $committee_id)";
    } else {
        $sql = "INSERT INTO users (username, name, email, password, role) VALUES ('$username', '$name', '$email', '$password', '$role')";
    }

    try {
        $conn->query($sql);
        $res = [
            'status' => 'success',
            'message' => 'User created successfully'
        ];

    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $res = [
                'status' => 'warning',
                'message' => 'Username/Email already exists'
            ];
        } else {
            $res = [
                'status' => 'error',
                'message' => 'Error creating user: ',
                'error' => $e->getMessage()
            ];
        }
    }

    echo json_encode($res);
    return;
}

if (isset($_POST['update_User'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $committee_id = null;
    if ($role === 'legislator' || $role === 'committee') {
        $committee_id = isset($_POST['committee_id']) ? intval($_POST['committee_id']) : null;
    }

    $sql = "UPDATE users SET username = '$username', name = '$name', email = '$email', role = '$role'";
    if ($committee_id) {
        $sql .= ", committee_id = $committee_id";
    } else {
        $sql .= ", committee_id = NULL";
    }

    // Add password update only if a new password is provided
    if (!empty($_POST['password'])) {
        $password = PasswordUtil::hashPassword($_POST['password']);
        $sql .= ", password = '$password'";
    }

    $sql .= " WHERE id = '$id'";

    try {
        $conn->query($sql);
        $res = [
            'status' => 'success',
            'message' => 'User updated successfully'
        ];
    } catch (mysqli_sql_exception $e) {
        $res = [
            'status' => 'error',
            'message' => 'Error updating user: ' . $e->getMessage()
        ];
    }

    echo json_encode($res);
    return;
}

if (isset($_POST['delete_User'])) {
    $id = mysqli_real_escape_string($conn, $_POST['deleteID']);

    $sql = "DELETE FROM users WHERE id = '$id'";

    try {
        $conn->query($sql);
        $res = [
            'status' => 'success',
            'message' => 'User deleted successfully'
        ];
    } catch (mysqli_sql_exception $e) {
        $res = [
            'status' => 'error',
            'message' => 'Error deleting user: ' . $e->getMessage()
        ];
    }

    echo json_encode($res);
    return;
}
