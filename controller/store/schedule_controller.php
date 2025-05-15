<?php
session_start();
require_once '../../database/database.php';
header('Content-Type: application/json');

$conn = getConnection();

if (isset($_POST['add_schedule'])) {
    $proposal_id = isset($_POST['proposal_id']) ? intval($_POST['proposal_id']) : 0;

    if ($_SESSION['role'] === 'committee') {
        $hearing_date = isset($_POST['hearing_date']) ? $_POST['hearing_date'] : '';
        $hearing_time = isset($_POST['hearing_time']) ? $_POST['hearing_time'] : '';
    } else {
        $reading_date = isset($_POST['reading_date']) ? $_POST['reading_date'] : '';
        $reading_time = isset($_POST['reading_time']) ? $_POST['reading_time'] : '';
    }

    $session_type = isset($_POST['session_type']) ? $_POST['session_type'] : 'Regular';
    $reading_result = isset($_POST['reading_result']) ? $_POST['reading_result'] : null;
    $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
    $hearing_status = isset($_POST['hearing_status']) ? $_POST['hearing_status'] : null;

    if ($_SESSION['role'] === 'committee') {
        if (!$proposal_id || !$hearing_date || !$hearing_time || !$session_type) {
            echo json_encode([
                'status' => 'error',
                'message' => 'All fields are required.'
            ]);
            exit;
        }
        $stmt = $conn->prepare("SELECT id FROM schedule WHERE proposal_id = ? AND hearing_date = ? AND hearing_time = ?");
        $stmt->bind_param("iss", $proposal_id, $hearing_date, $hearing_time);

    } else {
        if (!$proposal_id || !$reading_date || !$reading_time || !$session_type) {
            echo json_encode([
                'status' => 'error',
                'message' => 'All fields are required.'
            ]);
            exit;
        }
        $stmt = $conn->prepare("SELECT id FROM schedule WHERE proposal_id = ? AND reading_date = ? AND reading_time = ?");
        $stmt->bind_param("iss", $proposal_id, $reading_date, $reading_time);
    }

    // Check for duplicate schedule
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

    if ($_SESSION['role'] === 'committee') {
        $stmt = $conn->prepare("INSERT INTO schedule (proposal_id, hearing_date, hearing_time, session_type,  remarks, hearing_status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $proposal_id, $hearing_date, $hearing_time, $session_type, $remarks, $hearing_status);
    } else {
        $stmt = $conn->prepare("INSERT INTO schedule (proposal_id, reading_date, reading_time, session_type, reading_status, remarks) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $proposal_id, $reading_date, $reading_time, $session_type, $reading_result, $remarks);
    }

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

    if ($_SESSION['role'] === 'committee') {
        $hearing_date = isset($_POST['hearing_date']) ? $_POST['hearing_date'] : '';
        $hearing_time = isset($_POST['hearing_time']) ? $_POST['hearing_time'] : '';
    } else {
        $reading_date = isset($_POST['reading_date']) ? $_POST['reading_date'] : '';
        $reading_time = isset($_POST['reading_time']) ? $_POST['reading_time'] : '';
    }

    $session_type = isset($_POST['session_type']) ? $_POST['session_type'] : 'Regular';
    $reading_status = isset($_POST['reading_status']) ? $_POST['reading_status'] : null;

    $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
    $hearing_status = isset($_POST['hearing_status']) ? $_POST['hearing_status'] : null;

    // Dump and die for debugging
    // var_dump([
    //     'schedule_id' => $schedule_id,
    //     'current_status' => $current_status,
    //     'hearing_date' => isset($hearing_date) ? $hearing_date : null,
    //     'hearing_time' => isset($hearing_time) ? $hearing_time : null,
    //     'reading_date' => isset($reading_date) ? $reading_date : null,
    //     'reading_time' => isset($reading_time) ? $reading_time : null,
    //     'session_type' => $session_type,
    //     'reading_status' => $reading_status,
    //     'remarks' => $remarks,
    //     'hearing_status' => $hearing_status,
    //     'role' => $_SESSION['role']
    // ]);
    // die();

    // Debugging output removed


    if ($_SESSION['role'] === 'committee') {
        if (!$schedule_id || !$hearing_date || !$hearing_time || !$session_type || !$hearing_status) {
            echo json_encode([
                'status' => 'error',
                'message' => 'All fields are required.'
            ]);
            exit;
        }
    } else {
        if (!$schedule_id || !$reading_date || !$reading_time || !$session_type || !$reading_status) {
            echo json_encode([
                'status' => 'error',
                'message' => 'All fields are required. '
            ]);
            exit;
        }
    }

    if ($_SESSION['role'] === 'committee') {
        // Update schedule
        $stmt = $conn->prepare("UPDATE schedule s JOIN ordinance_proposals p ON s.proposal_id = p.id
    SET s.hearing_date=?, s.hearing_time=?, s.session_type=?, s.reading_status=?, s.remarks=?, s.hearing_status=?
    WHERE s.id=?");
        $stmt->bind_param("ssssssi", $hearing_date, $hearing_time, $session_type, $reading_status, $remarks, $hearing_status, $schedule_id);
    } else {
        // Update schedule
        $stmt = $conn->prepare("UPDATE schedule s JOIN ordinance_proposals p ON s.proposal_id = p.id
    SET s.reading_date=?, s.reading_time=?, s.session_type=?, s.reading_status=?, s.remarks=?
    WHERE s.id=?");
        $stmt->bind_param("sssssi", $reading_date, $reading_time, $session_type, $reading_status, $remarks, $schedule_id);
    }

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
