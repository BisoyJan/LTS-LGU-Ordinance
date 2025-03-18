<?php
require '../../database/database.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = getConnection();

$output = array();
$columns = array(
    "op.id",
    "op.proposal",
    "op.proposal_date",
    "os.action_type"
);

$search_value = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : "";

// Base Query with JOIN to get latest status
$sql = "SELECT op.id, op.proposal, op.proposal_date, op.details, op.file_path,
        os.action_type, os.remarks, os.action_date
        FROM ordinance_proposals op
        LEFT JOIN (
            SELECT proposal_id, action_type, remarks, action_date
            FROM ordinance_status os1
            WHERE action_date = (
                SELECT MAX(action_date)
                FROM ordinance_status os2
                WHERE os1.proposal_id = os2.proposal_id
            )
        ) os ON op.id = os.proposal_id";

$totalQuery = mysqli_query($conn, $sql);
$total_all_rows = mysqli_num_rows($totalQuery);

// Filtering
$whereClause = "";
if (!empty($search_value)) {
    $search_value = mysqli_real_escape_string($conn, $search_value);
    $whereClause = " WHERE op.proposal LIKE '%$search_value%' 
                     OR os.action_type LIKE '%$search_value%'";
}

// Sorting
$orderClause = " ORDER BY op.id DESC";
if (isset($_POST['order']) && isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
    $column_index = intval($_POST['order'][0]['column']);
    $order_direction = ($_POST['order'][0]['dir'] === 'asc') ? 'DESC' : 'ASC';
    if (isset($columns[$column_index])) {
        $orderClause = " ORDER BY " . $columns[$column_index] . " " . $order_direction;
    }
}

// Get filtered count
if (!empty($whereClause)) {
    $filtered_query = "SELECT COUNT(*) as total FROM (" . $sql . $whereClause . ") as filtered";
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

// Final Query
$final_query = $sql . $whereClause . $orderClause . $limitClause;
$query = mysqli_query($conn, $final_query);

if (!$query) {
    echo json_encode(['error' => 'Database query failed: ' . mysqli_error($conn)]);
    exit;
}

$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $formatted_date = date('M d, Y', strtotime($row['proposal_date']));
    $status = $row['action_type'] ?: 'Not Started';
    $status_badge = getStatusBadge($status);

    // Create Google Drive edit URL
    $driveEditUrl = '';
    if (!empty($row['file_path'])) {
        $driveFileId = $row['file_path'];
        $driveEditUrl = "https://docs.google.com/document/d/" . $driveFileId . "/edit";
    }

    $data[] = [
        htmlspecialchars($row['id']),
        htmlspecialchars($row['proposal']),
        htmlspecialchars($formatted_date),
        $status_badge,
        '<button class="viewButton btn btn-primary btn-sm" data-id="' . $row["id"] . '" type="button" data-bs-toggle="modal" data-bs-target="#viewStatusModal"><i class="fas fa-eye"></i></button>
         <button class="updateStatusButton btn btn-warning btn-sm ms-1" data-id="' . $row["id"] . '" type="button" data-bs-toggle="modal" data-bs-target="#updateStatusModal"><i class="fas fa-pen"></i></button>' .
        (!empty($driveEditUrl) ? ' <a href="' . $driveEditUrl . '" target="_blank" class="btn btn-info btn-sm ms-1" title="Edit in Google Drive"><i class="fas fa-file-edit"></i></a>' : '')
    ];
}

function getStatusBadge($status)
{
    $badgeClass = '';
    switch ($status) {
        case 'Draft':
            $badgeClass = 'secondary';
            break;
        case 'Under Review':
            $badgeClass = 'info';
            break;
        case 'Pending Approval':
            $badgeClass = 'warning';
            break;
        case 'Initial Planning':
            $badgeClass = 'primary';
            break;
        case 'Public Comment Period':
            $badgeClass = 'info';
            break;
        case 'Approved':
            $badgeClass = 'success';
            break;
        case 'Rejected':
            $badgeClass = 'danger';
            break;
        case 'Implemented':
            $badgeClass = 'dark';
            break;
        default:
            $badgeClass = 'secondary';
    }
    return '<span class="badge bg-' . $badgeClass . '">' . htmlspecialchars($status) . '</span>';
}

$output = array(
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    'recordsTotal' => $total_all_rows,
    'recordsFiltered' => $count_filtered_rows,
    'data' => $data
);

echo json_encode($output);

