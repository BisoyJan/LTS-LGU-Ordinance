<?php
require '../../database/database.php';

header('Content-Type: application/json');
$conn = getConnection();

if (isset($_POST['create_committee'])) {
    try {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        $query = "INSERT INTO committees (name, description) VALUES ('$name', '$description')";

        if ($conn->query($query)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Committee created successfully'
            ]);
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error creating committee: ' . $e->getMessage()
        ]);
    }
}

if (isset($_POST['fetch_committee'])) {
    try {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $query = "SELECT * FROM committees WHERE id = '$id'";
        $result = mysqli_query($conn, $query);

        if ($row = mysqli_fetch_assoc($result)) {
            echo json_encode([
                'status' => 'success',
                'data' => $row
            ]);
        } else {
            throw new Exception("Committee not found");
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

if (isset($_POST['update_committee'])) {
    try {
        $id = mysqli_real_escape_string($conn, $_POST['committeeId']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        $query = "UPDATE committees SET name = '$name', description = '$description' WHERE id = '$id'";

        if ($conn->query($query)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Committee updated successfully'
            ]);
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error updating committee: ' . $e->getMessage()
        ]);
    }
}

if (isset($_POST['delete_committee'])) {
    try {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $query = "DELETE FROM committees WHERE id = '$id'";

        if ($conn->query($query)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Committee deleted successfully'
            ]);
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error deleting committee: ' . $e->getMessage()
        ]);
    }
}
