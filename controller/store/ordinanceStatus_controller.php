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
            // Update the proposal status in ordinance_proposals table
            $update_proposal = "UPDATE ordinance_proposals SET status = ? WHERE id = ?";
            $stmt = $conn->prepare($update_proposal);
            $stmt->bind_param("si", $action_type, $proposal_id);
            $stmt->execute();

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
