<?php
session_start();
require '../../database/database.php';

header('Content-Type: application/json');
$conn = getConnection();

if (isset($_POST['check_status'])) {
    $proposal_id = mysqli_real_escape_string($conn, $_POST['proposal_id']);

    // Check if proposal exists first
    $check_proposal = "SELECT id FROM ordinance_proposals WHERE id = ?";
    $stmt = $conn->prepare($check_proposal);
    $stmt->bind_param("i", $proposal_id);
    $stmt->execute();
    $proposal_result = $stmt->get_result();

    if ($proposal_result->num_rows === 0) {
        echo json_encode([
            'exists' => false,
            'status' => 'error',
            'message' => 'Proposal does not exist'
        ]);
        exit;
    }

    // Check if status exists
    $status_query = "SELECT os.*, u.username 
                    FROM ordinance_status os
                    LEFT JOIN users u ON os.user_id = u.id
                    WHERE os.proposal_id = ?";
    $stmt = $conn->prepare($status_query);
    $stmt->bind_param("i", $proposal_id);
    $stmt->execute();
    $status_result = $stmt->get_result();

    if ($status_result->num_rows > 0) {
        $status_data = $status_result->fetch_assoc();
        echo json_encode([
            'exists' => true,
            'status' => 'warning',
            'message' => 'Status already exists for this proposal',
            'data' => [
                'action_type' => $status_data['action_type'],
                'remarks' => $status_data['remarks'],
                'added_by' => $status_data['username'],
                'added_on' => $status_data['created_at']
            ]
        ]);
    } else {
        echo json_encode([
            'exists' => false,
            'status' => 'success',
            'message' => 'No status exists for this proposal'
        ]);
    }
    exit;
}

if (isset($_POST['add_status'])) {
    try {
        $proposal_id = mysqli_real_escape_string($conn, $_POST['proposal_id']);

        // First verify if the proposal exists
        $check_proposal = "SELECT id FROM ordinance_proposals WHERE id = ?";
        $stmt = $conn->prepare($check_proposal);
        $stmt->bind_param("i", $proposal_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Cannot add status: The selected proposal does not exist or may have been deleted.'
            ]);
            exit;
        }

        // Continue with existing status check
        $check_query = "SELECT COUNT(*) as count FROM ordinance_status WHERE proposal_id = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("i", $proposal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($data['count'] > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'A status already exists for this proposal.'
            ]);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
        $action_type = mysqli_real_escape_string($conn, $_POST['action_type']);

        // Insert new status
        $query = "INSERT INTO ordinance_status (proposal_id, user_id, remarks, action_type) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiss", $proposal_id, $user_id, $remarks, $action_type);

        if ($stmt->execute()) {

            echo json_encode([
                'status' => 'success',
                'message' => 'Status updated successfully'
            ]);
        } else {
            throw new Exception("Unable to update the status. Please try again.");
        }
    } catch (Exception $e) {
        $error_message = "An error occurred while updating the status.";
        if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            $error_message = "Cannot add status: The proposal reference is invalid.";
        }
        echo json_encode([
            'status' => 'error',
            'message' => $error_message
        ]);
    }
    exit;
}

// Fetch Status History
if (isset($_POST['fetch_Status'])) {
    try {
        $id = mysqli_real_escape_string($conn, $_POST['id']);

        // Updated query to use user_id instead of added_by
        $query = "SELECT os.*, u.username as added_by 
                 FROM ordinance_status os
                 LEFT JOIN users u ON os.user_id = u.id
                 WHERE os.proposal_id = ?
                 ORDER BY os.action_date DESC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            throw new Exception("Query execution failed");
        }

        $result = $stmt->get_result();
        $status_history = [];

        while ($row = $result->fetch_assoc()) {
            $status_history[] = [
                'action_type' => $row['action_type'],
                'remarks' => $row['remarks'],
                'action_date' => $row['action_date'],
                'added_by' => $row['username'] ?? 'Unknown User'
            ];
        }

        // Updated file query to include creation info
        $file_query = "SELECT 
            op.file_path, 
            op.file_name, 
            op.created_at,
            creator.username as creator
        FROM ordinance_proposals op
        LEFT JOIN users creator ON op.user_id = creator.id
        WHERE op.id = ?";

        $file_stmt = $conn->prepare($file_query);
        $file_stmt->bind_param("i", $id);
        $file_stmt->execute();
        $file_result = $file_stmt->get_result();
        $file_data = $file_result->fetch_assoc();

        $drive_history = null;
        if ($file_data && !empty($file_data['file_path'])) {
            $fileId = $file_data['file_path'];
            $drive_history = [
                'view_url' => "https://docs.google.com/document/d/{$fileId}/edit",
                'file_name' => $file_data['file_name'],
                'created_at' => $file_data['created_at'],
                'creator' => $file_data['creator'],
                'revision_url' => "https://docs.google.com/document/d/{$fileId}/edit#versions"
            ];
        }

        echo json_encode([
            'status' => 'success',
            'data' => $status_history,
            'drive_history' => $drive_history
        ]);

    } catch (Exception $e) {
        error_log("Status History Error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch status history: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Update Status
if (isset($_POST['update_status'])) {
    try {
        $proposal_id = mysqli_real_escape_string($conn, $_POST['proposal_id']);
        $user_id = $_SESSION['user_id'];
        $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
        $action_type = mysqli_real_escape_string($conn, $_POST['action_type']);

        $query = "INSERT INTO ordinance_status (proposal_id, user_id, remarks, action_type) 
                 VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiss", $proposal_id, $user_id, $remarks, $action_type);

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Status updated successfully'
            ]);
        } else {
            throw new Exception("Failed to update status");
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// Delete Status
if (isset($_POST['delete_status'])) {
    try {
        $status_id = mysqli_real_escape_string($conn, $_POST['status_id']);

        $query = "DELETE FROM ordinance_status WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $status_id);

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Status deleted successfully'
            ]);
        } else {
            throw new Exception("Failed to delete status");
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// Get Latest Status
if (isset($_POST['get_latest_status'])) {
    try {
        $id = mysqli_real_escape_string($conn, $_POST['id']);

        $query = "SELECT os.*, u.username 
                 FROM ordinance_status os
                 LEFT JOIN users u ON os.user_id = u.id
                 WHERE os.proposal_id = ?
                 ORDER BY os.action_date DESC
                 LIMIT 1";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'action_type' => $row['action_type'],
                    'remarks' => $row['remarks'],
                    'action_date' => $row['action_date'],
                    'added_by' => $row['username'],
                    'proposal_id' => $row['proposal_id']
                ]
            ]);
        } else {
            echo json_encode([
                'status' => 'success',  // Changed to success with empty data
                'data' => [
                    'action_type' => '',
                    'remarks' => '',
                    'proposal_id' => $id
                ]
            ]);
        }
    } catch (Exception $e) {
        error_log("Get Latest Status Error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch latest status'
        ]);
    }
    exit;
}
