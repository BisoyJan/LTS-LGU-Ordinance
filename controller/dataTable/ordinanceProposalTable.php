<?php
require '../../database/database.php';
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = getConnection();

$output = array();
$columns = array("id", "proposal", "proposal_date", "details", "file_name");

$search_value = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : "";


$sql = "SELECT op.id, op.proposal, op.proposal_date, op.details, op.file_name, op.file_path, 
        op.file_type, op.file_size, op.created_at, 
        c.name as committee_name, 
        u.username as created_by
        FROM ordinance_proposals op
        LEFT JOIN committees c ON op.committee_id = c.id
        LEFT JOIN users u ON op.user_id = u.id";
$totalQuery = mysqli_query($conn, $sql);
$total_all_rows = mysqli_num_rows($totalQuery);

// Filtering
$whereClause = "";
if (!empty($search_value)) {
    $search_value = mysqli_real_escape_string($conn, $search_value);
    $whereClause = " WHERE proposal LIKE '%$search_value%' 
                     OR details LIKE '%$search_value%' 
                     OR file_name LIKE '%$search_value%'";
}

// Sorting
$orderClause = " ORDER BY id DESC"; // Default order
if (isset($_POST['order']) && isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
    $column_index = intval($_POST['order'][0]['column']);
    $order_direction = ($_POST['order'][0]['dir'] === 'asc') ? 'DESC' : 'ASC';
    if (isset($columns[$column_index])) {
        $orderClause = " ORDER BY " . $columns[$column_index] . " " . $order_direction;
    }
}

// Get total filtered count
if (!empty($whereClause)) {
    $filtered_query = "SELECT COUNT(*) as total FROM ordinance_proposals" . $whereClause;
    $filtered_result = mysqli_query($conn, $filtered_query);
    $filtered_row = mysqli_fetch_assoc($filtered_result);
    $count_filtered_rows = $filtered_row['total'];
} else {
    $count_filtered_rows = $total_all_rows;
}

// Pagination
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) && $_POST['length'] != -1 ? intval($_POST['length']) : 10;
$limitClause = " LIMIT $start, $length";

// Execute Query
$final_query = $sql . $whereClause . $orderClause . $limitClause;
$query = mysqli_query($conn, $final_query);

if (!$query) {
    echo json_encode(['error' => 'Database query failed: ' . mysqli_error($conn)]);
    exit;
}

$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $formatted_date = date('M d, Y', strtotime($row['proposal_date']));
    $googleDocsUrl = !empty($row['file_path']) ?
        "https://docs.google.com/document/d/" . $row['file_path'] . "/preview" : '';

    $file_html = '<div class="file-attachment">';
    if (!empty($row['file_name'])) {
        $file_html .= '<i class="fas fa-file-word text-primary me-1"></i>' .
            '<a href="' . $googleDocsUrl . '" target="_blank">' .
            htmlspecialchars($row['file_name']) . '</a>';
    } else {
        $file_html .= '<span class="text-muted">No file</span>';
    }
    $file_html .= '</div>';

    $actions = '
        <button class="viewButton btn btn-primary btn-sm" data-id="' . $row["id"] . '"><i class="fas fa-eye"></i></button>
        <button class="editButton btn btn-success btn-sm ms-1" data-id="' . $row["id"] . '"><i class="fas fa-edit"></i></button>
        <button class="deleteButton btn btn-danger btn-sm ms-1" data-id="' . $row["id"] . '"><i class="fas fa-trash"></i></button>
    ';

    $data[] = array(
        $row['id'],
        $row['proposal'] . '<br><small class="text-muted">By: ' . $row['created_by'] .
        '<br>Committee: ' . $row['committee_name'] . '</small>',
        $formatted_date,
        $row['details'],
        $file_html,
        $actions
    );
}

// Function to format file size
function formatFileSize($bytes)
{
    if ($bytes < 1024) {
        return $bytes . " B";
    } elseif ($bytes < 1048576) {
        return round($bytes / 1024, 2) . " KB";
    } elseif ($bytes < 1073741824) {
        return round($bytes / 1048576, 2) . " MB";
    } else {
        return round($bytes / 1073741824, 2) . " GB";
    }
}

// Function to get file icon based on file type
function getFileIcon($fileType)
{
    if ($fileType === 'doc' || $fileType === 'docx') {
        return '<i class="fas fa-file-word text-primary"></i>';
    }
    return '<i class="fas fa-file text-secondary"></i>';
}

// Function to truncate text
function truncateText($text, $limit)
{
    $words = explode(' ', $text);
    if (count($words) > $limit) {
        return implode(' ', array_slice($words, 0, $limit)) . '...';
    }
    return $text;
}

// Output JSON response
$output = array(
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    'recordsTotal' => $total_all_rows,
    'recordsFiltered' => $count_filtered_rows,
    'data' => $data,
);

echo json_encode($output);
