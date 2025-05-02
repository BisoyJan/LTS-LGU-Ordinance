<?php
require_once '../../database/database.php';
header('Content-Type: application/json');

$conn = getConnection();

$sql = "SELECT s.id, s.proposal_id, s.hearing_date, s.hearing_time, s.session_type, s.reading_status, s.remarks, p.proposal, s.hearing_status, p.file_path
        FROM schedule s
        JOIN ordinance_proposals p ON s.proposal_id = p.id";

$result = $conn->query($sql);

function proposalColor($proposal_id)
{
    $colors = [
        "#007bff",
        "#28a745",
        "#dc3545",
        "#ffc107",
        "#17a2b8",
        "#6f42c1",
        "#fd7e14",
        "#20c997",
        "#6610f2",
        "#e83e8c",
        "#007bff"
    ];
    return $colors[$proposal_id % count($colors)];
}

// Helper to format time as h:i AM/PM
function formatTimeAMPM($time)
{
    if (!$time)
        return '';
    $t = explode(':', $time);
    $hour = intval($t[0]);
    $minute = isset($t[1]) ? $t[1] : '00';
    $ampm = $hour >= 12 ? 'PM' : 'AM';
    $hour12 = $hour % 12;
    if ($hour12 === 0)
        $hour12 = 12;
    return sprintf('%d:%02d %s', $hour12, $minute, $ampm);
}

$events = [];
while ($row = $result->fetch_assoc()) {
    // FullCalendar expects 'start' to be a full ISO string for timed events
    $start = $row['hearing_date'];
    // Use raw time (HH:MM:SS) for start, not formatted AM/PM
    if (!empty($row['hearing_time'])) {
        $start .= 'T' . $row['hearing_time'];
    }
    $events[] = [
        'id' => $row['id'],
        'title' => $row['proposal'],
        'start' => $start,
        // Use raw time for eventDataTransform, formatted for modal
        'hearing_time' => $row['hearing_time'],
        'hearing_time_formatted' => formatTimeAMPM($row['hearing_time']),
        'remarks' => $row['remarks'],
        'proposal_id' => $row['proposal_id'],
        'session_type' => $row['session_type'],
        'reading_status' => $row['reading_status'],
        'hearing_status' => $row['hearing_status'],
        'color' => proposalColor($row['proposal_id']),
        'file_id' => $row['file_path'] // <-- Add file_id for document viewing
    ];
}

echo json_encode($events);
