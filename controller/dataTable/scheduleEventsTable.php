<?php
require_once '../../database/database.php';
header('Content-Type: application/json');

$conn = getConnection();

$sql = "SELECT s.id, s.proposal_id, s.hearing_date, s.hearing_time, s.reading_date, s.reading_time, s.session_type, s.reading_status, s.remarks, p.proposal, s.hearing_status, p.file_path
        FROM schedule s
        JOIN ordinance_proposals p ON s.proposal_id = p.id";

$result = $conn->query($sql);

// Color for hearing and reading events
function eventColor($proposal_id, $type)
{
    // Use different base colors for hearing and reading
    $hearingColors = [
        "#007bff",
        "#28a745",
        "#dc3545",
        "#ffc107",
        "#17a2b8",
        "#6f42c1",
        "#fd7e14",
        "#20c997",
        "#6610f2",
        "#e83e8c"
    ];
    $readingColors = [
        "#6c757d",
        "#20c997",
        "#fd7e14",
        "#6610f2",
        "#e83e8c",
        "#007bff",
        "#28a745",
        "#dc3545",
        "#ffc107",
        "#17a2b8"
    ];
    if ($type === 'hearing') {
        return $hearingColors[$proposal_id % count($hearingColors)];
    } else {
        return $readingColors[$proposal_id % count($readingColors)];
    }
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
    // Hearing event
    if (!empty($row['hearing_date'])) {
        $start = $row['hearing_date'];
        if (!empty($row['hearing_time'])) {
            $start .= 'T' . $row['hearing_time'];
        }
        $events[] = [
            'id' => $row['id'] . '-hearing',
            'proposal_id' => $row['proposal_id'],
            'title' => '[Hearing] ' . $row['proposal'],
            'start' => $start,
            'type' => 'hearing',
            'hearing_time' => $row['hearing_time'],
            'hearing_time_formatted' => formatTimeAMPM($row['hearing_time']),
            'reading_time' => $row['reading_time'], // include for consistency
            'reading_time_formatted' => formatTimeAMPM($row['reading_time']),
            'remarks' => $row['remarks'],
            'session_type' => $row['session_type'],
            'hearing_status' => $row['hearing_status'],
            'reading_status' => $row['reading_status'],
            'color' => eventColor($row['proposal_id'], 'hearing'),
            'file_id' => $row['file_path']
        ];
    }
    // Reading event
    if (!empty($row['reading_date'])) {
        $start = $row['reading_date'];
        if (!empty($row['reading_time'])) {
            $start .= 'T' . $row['reading_time'];
        }
        $events[] = [
            'id' => $row['id'] . '-reading',
            'proposal_id' => $row['proposal_id'],
            'title' => '[Reading] ' . $row['proposal'],
            'start' => $start,
            'type' => 'reading',
            'reading_time' => $row['reading_time'],
            'reading_time_formatted' => formatTimeAMPM($row['reading_time']),
            'hearing_time' => $row['hearing_time'], // include for consistency
            'hearing_time_formatted' => formatTimeAMPM($row['hearing_time']),
            'remarks' => $row['remarks'],
            'session_type' => $row['session_type'],
            'hearing_status' => $row['hearing_status'],
            'reading_status' => $row['reading_status'],
            'color' => eventColor($row['proposal_id'], 'reading'),
            'file_id' => $row['file_path']
        ];
    }
}

echo json_encode($events);
