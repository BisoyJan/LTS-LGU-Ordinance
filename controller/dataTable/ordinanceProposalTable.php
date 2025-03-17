<?php
require '../../database/database.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = getConnection();

$output = array();
$columns = array("id", "proposal", "proposal_date", "details", "status", "file_name");
$search_value = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : "";

// Base Query
$sql = "SELECT id, proposal, proposal_date, details, status, file_name, file_path, file_type, file_size, created_at FROM ordinance_proposals";
$totalQuery = mysqli_query($conn, $sql);
$total_all_rows = mysqli_num_rows($totalQuery);

// Filtering
$whereClause = "";
if (!empty($search_value)) {
    $search_value = mysqli_real_escape_string($conn, $search_value);
    $whereClause = " WHERE proposal LIKE '%$search_value%' OR details LIKE '%$search_value%' OR status LIKE '%$search_value%' OR file_name LIKE '%$search_value%'";
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

    // File attachment display
    $file_html = '';
    if (!empty($row['file_name'])) {
        $file_size_formatted = formatFileSize($row['file_size']);
        $file_icon = getFileIcon($row['file_type']);
        $file_html = "<div class='file-attachment'>
                        <span class='file-icon'>{$file_icon}</span>
                        <a href='{$row['file_path']}' target='_blank' title='Download {$row['file_name']}'>
                            {$row['file_name']} ({$file_size_formatted})
                        </a>
                      </div>";
    } else {
        $file_html = "<span class='text-muted'>No file attached</span>";
    }

    $data[] = [
        htmlspecialchars($row['id']),
        htmlspecialchars($row['proposal']),
        htmlspecialchars($formatted_date),
        truncateText(htmlspecialchars($row["details"]), 6),  // Truncate details to 20 words
        '<span class="badge bg-' . getStatusColor($row['status']) . '">' . htmlspecialchars($row['status']) . '</span>',
        '<div class="file-attachment">
            <span class="file-icon">' . getFileIcon($row['file_type']) . '</span>
            <a href="' . htmlspecialchars($row['file_path']) . '" target="_blank" class="file-link" data-path="' . htmlspecialchars($row['file_path']) . '">
                ' . htmlspecialchars($row['file_name']) . ' (' . formatFileSize($row['file_size']) . ')
            </a>
        </div>',
        '<button class="viewButton btn btn-primary btn-sm" data-id="' . $row["id"] . '" type="button" data-bs-toggle="modal" data-bs-target="#viewProposalModal"><i class="fas fa-eye"></i></button>
        <button class="editButton btn btn-success btn-sm ms-1" data-id="' . $row["id"] . '" onclick="formIDChangeEdit()" type="button" data-bs-toggle="modal" data-bs-target="#proposalModal"><i class="fas fa-edit"></i></button>
        <button class="viewFileButton btn btn-info btn-sm ms-1" data-id="' . $row["id"] . '" ' . (empty($row['file_path']) ? 'disabled' : '') . ' onclick="viewFile(\'' . $row['file_path'] . '\')" type="button"><i class="fas fa-file-alt"></i></button>
        <button class="deleteButton btn btn-danger btn-sm" data-id="' . $row["id"] . '" type="button" data-bs-toggle="modal" data-bs-target="#proposalDeleteModal"><i class="fas fa-trash"></i></button>'
    ];
}

// Function to get Bootstrap color class based on status
function getStatusColor($status)
{
    switch ($status) {
        case 'Draft':
            return 'secondary';
        case 'Under Review':
            return 'info';
        case 'Pending Approval':
            return 'warning';
        case 'Initial Planning':
            return 'primary';
        case 'Public Comment Period':
            return 'dark';
        case 'Approved':
            return 'success';
        case 'Rejected':
            return 'danger';
        case 'Implemented':
            return 'success';
        default:
            return 'secondary';
    }
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
    switch ($fileType) {
        case 'pdf':
            return '<i class="fas fa-file-pdf text-danger"></i>';
        case 'doc':
        case 'docx':
            return '<i class="fas fa-file-word text-primary"></i>';
        case 'txt':
            return '<i class="fas fa-file-alt text-secondary"></i>';
        default:
            return '<i class="fas fa-file text-secondary"></i>';
    }
}

// Function to truncate text
function truncateText($text, $limit)
{
    $words = explode(' ', $text);
    if (count($words) > $limit) {
        return implode(' ', array_slice($words, 0, $limit)) . '.....';
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
