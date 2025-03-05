<?php

require '../../database/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['create_User'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";

    if ($conn->query($sql) === TRUE) {
        $res = [
            'status' => 'success',
            'message' => 'User created successfully'
        ];
        echo json_encode($res);
        return;
    } else {

        $res = [
            'status' => 'error',
            'message' => 'Error creating user: ',
            'error' => $conn->error
        ];
        echo json_encode($res);
        return;
    }
}
