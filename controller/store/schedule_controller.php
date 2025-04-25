<?php
require_once '../../database/database.php';
header('Content-Type: application/json');

$conn = getConnection();

if (isset($_POST['add_schedule'])) {
    $proposal_id = isset($_POST['proposal_id']) ? intval($_POST['proposal_id']) : 0;
    $hearing_date = isset($_POST['hearing_date']) ? $_POST['hearing_date'] : '';
    $hearing_time = isset($_POST['hearing_time']) ? $_POST['hearing_time'] : '';
    $session_type = isset($_POST['session_type']) ? $_POST['session_type'] : 'Regular';
    $reading_result = isset($_POST['reading_result']) ? $_POST['reading_result'] : null;
    $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';

    if (!$proposal_id || !$hearing_date || !$hearing_time || !$session_type || !$reading_result) {
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required.'
        ]);
        exit;
    }

    // Check for duplicate schedule
    $stmt = $conn->prepare("SELECT id FROM schedule WHERE proposal_id = ? AND hearing_date = ? AND hearing_time = ?");
    $stmt->bind_param("iss", $proposal_id, $hearing_date, $hearing_time);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'This proposal is already scheduled for the selected date and time.'
        ]);
        $stmt->close();
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO schedule (proposal_id, hearing_date, hearing_time, session_type, reading_result, remarks) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $proposal_id, $hearing_date, $hearing_time, $session_type, $reading_result, $remarks);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Schedule added successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add schedule: ' . $conn->error
        ]);
    }
    $stmt->close();
    exit;
}

// Edit schedule
if (isset($_POST['edit_schedule'])) {
    $schedule_id = isset($_POST['schedule_id']) ? intval($_POST['schedule_id']) : 0;
    $current_status = isset($_POST['current_status']) ? trim($_POST['current_status']) : '';
    $hearing_date = isset($_POST['hearing_date']) ? $_POST['hearing_date'] : '';
    $hearing_time = isset($_POST['hearing_time']) ? $_POST['hearing_time'] : '';
    $session_type = isset($_POST['session_type']) ? $_POST['session_type'] : 'Regular';
    $reading_result = isset($_POST['reading_result']) ? $_POST['reading_result'] : null;
    $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';

    if (!$schedule_id || !$hearing_date || !$hearing_time || !$session_type || !$reading_result) {
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required.'
        ]);
        exit;
    }

    // Update schedule
    $stmt = $conn->prepare("UPDATE schedule s JOIN ordinance_proposals p ON s.proposal_id = p.id
        SET s.hearing_date=?, s.hearing_time=?, s.session_type=?, s.reading_result=?, s.remarks=?, p.current_status=?
        WHERE s.id=?");
    $stmt->bind_param("ssssssi", $hearing_date, $hearing_time, $session_type, $reading_result, $remarks, $current_status, $schedule_id);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Schedule updated successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update schedule: ' . $conn->error
        ]);
    }
    $stmt->close();
    exit;
}

// Delete schedule
if (isset($_POST['delete_schedule'])) {
    $schedule_id = isset($_POST['schedule_id']) ? intval($_POST['schedule_id']) : 0;
    if (!$schedule_id) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid schedule ID.'
        ]);
        exit;
    }
    $stmt = $conn->prepare("DELETE FROM schedule WHERE id=?");
    $stmt->bind_param("i", $schedule_id);
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Schedule deleted successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to delete schedule: ' . $conn->error
        ]);
    }
    $stmt->close();
    exit;
}
