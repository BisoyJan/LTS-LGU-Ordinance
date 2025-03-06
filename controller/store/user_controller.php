<?php

require '../../database/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);


if (isset($_POST['fetch_User'])) {
    $user_id = mysqli_real_escape_string($conn, $_POST['id']);

    $sql = "SELECT id, username, email, role FROM users WHERE id = '$user_id'";

    $query_run = mysqli_query($conn, $sql);

    try {
        $user_data = $query_run->fetch_assoc();
        $res = [
            'status' => $user_data ? 'success' : 'warning',
            'data' => $user_data ?? [],
            'message' => $user_data ? '' : 'User ID not found'
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
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";

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
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $sql = "UPDATE users SET username = '$username', email = '$email', role = '$role' WHERE id = '$id'";

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
