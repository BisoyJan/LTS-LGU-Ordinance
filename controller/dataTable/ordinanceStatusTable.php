<?php
require '../../database/database.php';
session_start();

$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$conn = getConnection();

$output = array();
$columns = array(
    "op.id",
    "op.proposal",
    "op.proposal_date",
    "u.username",
    "c.name",  // Changed from c.committee_name to c.name
    "os.action_type"
);

$search_value = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : "";

// Modified base query with correct column names
$sql = "SELECT 
    op.id, 
    op.proposal, 
    op.proposal_date, 
    op.details, 
    op.file_path,
    COALESCE(u1.username, 'System') as created_by,
    COALESCE(u1.name, 'System') as author,
    COALESCE(c.name, 'Unassigned') as committee_name,
    os.action_type,
    os.remarks, 
    os.action_date,
    COALESCE(u2.username, 'System') as status_updated_by
FROM ordinance_proposals op
LEFT JOIN users u1 ON op.user_id = u1.id
LEFT JOIN committees c ON op.committee_id = c.id
LEFT JOIN (
    SELECT proposal_id, action_type, remarks, action_date, user_id
    FROM ordinance_status os1
    WHERE action_date = (
        SELECT MAX(action_date)
        FROM ordinance_status os2
        WHERE os1.proposal_id = os2.proposal_id
    )
) os ON op.id = os.proposal_id
LEFT JOIN users u2 ON os.user_id = u2.id";

$totalQuery = mysqli_query($conn, $sql);
if (!$totalQuery) {
    error_log("SQL Error in total query: " . mysqli_error($conn));
    echo json_encode(['error' => 'Database query failed: ' . mysqli_error($conn)]);
    exit;
}

$total_all_rows = mysqli_num_rows($totalQuery);

// Get user's committee_id if not admin/secretary
$userCommitteeId = null;
if ($userRole === 'legislator' || $userRole === 'committee') {
    $userCommitteeId = null;
    if ($userId) {
        $userCommitteeQuery = mysqli_query($conn, "SELECT committee_id FROM users WHERE id = $userId LIMIT 1");
        if ($userCommitteeQuery && $row = mysqli_fetch_assoc($userCommitteeQuery)) {
            $userCommitteeId = $row['committee_id'];
        }
    }
}

// Filtering
$whereClause = "";
if (!empty($search_value)) {
    $search_value = mysqli_real_escape_string($conn, $search_value);
    $whereClause .= " WHERE (op.proposal LIKE '%$search_value%' 
                     OR os.action_type LIKE '%$search_value%'
                     OR u1.username LIKE '%$search_value%'
                     OR c.name LIKE '%$search_value%')";
}

// Committee filter from session (for legislator/committee)
if (($userRole === 'legislator' || $userRole === 'committee') && $userCommitteeId) {
    $whereClause .= (empty($whereClause) ? " WHERE" : " AND") . " op.committee_id = $userCommitteeId";
}

// Committee filter from UI (for admin/secretary)
if (($userRole === 'admin' || $userRole === 'secretary') && isset($_GET['committee']) && !empty($_GET['committee'])) {
    $committee_id = intval($_GET['committee']);
    $whereClause .= (empty($whereClause) ? " WHERE" : " AND") . " op.committee_id = $committee_id";
}

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $whereClause .= (empty($whereClause) ? " WHERE" : " AND") . " os.action_type = '$status'";
}

if (!empty($_GET['fromDate']) && !empty($_GET['toDate'])) {
    $fromDate = mysqli_real_escape_string($conn, $_GET['fromDate']);
    $toDate = mysqli_real_escape_string($conn, $_GET['toDate']);
    $whereClause .= (empty($whereClause) ? " WHERE" : " AND") . " os.action_date BETWEEN '$fromDate' AND '$toDate'";
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

// Final Query with error logging
$final_query = $sql . $whereClause . $orderClause . $limitClause;
$query = mysqli_query($conn, $final_query);

if (!$query) {
    error_log("SQL Error in final query: " . mysqli_error($conn));
    echo json_encode(['error' => 'Database query failed: ' . mysqli_error($conn)]);
    exit;
}

$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $formatted_date = date('M d, Y', strtotime($row['proposal_date']));
    $status = $row['action_type'] ?: 'Not Started';
    $status_badge = getStatusBadge($status);

    $googleDocsUrl = !empty($row['file_path']) ?
        "https://docs.google.com/document/d/" . $row['file_path'] . "/edit" : '';

    $file_html = '<div class="file-attachment">';
    if (!empty($row['file_name'])) {
        $file_html .= '<i class="fas fa-file-word text-primary me-1"></i>' .
            '<a href="' . $googleDocsUrl . '" target="_blank">' .
            htmlspecialchars($row['file_name']) . '</a>';
    } else {
        $file_html .= '<span class="text-muted">No file</span>';
    }
    $file_html .= '</div>';

    // Enable viewButton only if status is Approved
    $isApproved = (strtolower($status) === 'approved');

    $actions = '
        <button class="viewButton btn btn-primary btn-sm" data-id="' . $row["id"] . '" type="button" data-bs-toggle="modal" data-bs-target="#viewStatusModal"' . ($isApproved ? '' : 'enabled') . '><i class="fas fa-eye"></i></button>
        <button class="updateStatusButton btn btn-warning btn-sm ms-1" data-id="' . $row["id"] . '" type="button"' . ($isApproved ? ' disabled' : '') . '><i class="fas fa-pen"></i></button>';

    // File edit button logic with mayor role check
    if (!empty($row['file_path'])) {
        if ($userRole === 'mayor') {
            // Only allow download/upload if status is "Pending Approval"
            if (strtolower($status) === 'pending approval') {
                $downloadUrl = "https://docs.google.com/uc?export=download&id=" . $row['file_path'];
                $actions .= ' <a href="' . $downloadUrl . '" target="_blank" class="btn btn-success btn-sm ms-1" title="Download Document"><i class="fas fa-download"></i></a>';
                $actions .= ' <button class="btn btn-warning btn-sm ms-1 uploadMayorFileBtn" data-id="' . $row["id"] . '" title="Upload Updated Proposal"><i class="fas fa-upload"></i></button>';
            }
            // Always disable file edit for mayor
            $actions .= ' <a href="javascript:void(0)" class="btn btn-info btn-sm ms-1 disabled" title="Mayors cannot edit documents"><i class="fas fa-file-edit"></i></a>';
        } else if (!empty($row['action_type']) && !$isApproved) {
            $actions .= ' <a href="' . $googleDocsUrl . '" target="_blank" class="btn btn-info btn-sm ms-1" title="View Document"><i class="fas fa-file-edit"></i></a>';
        } else {
            $actions .= ' <a href="javascript:void(0)" class="btn btn-info btn-sm ms-1 disabled" title="' . ($isApproved ? 'Disabled for Approved status' : 'Status update required before viewing') . '"><i class="fas fa-file-edit"></i></a>';
        }
    } else {
        $actions .= ' <a href="javascript:void(0)" class="btn btn-info btn-sm ms-1 disabled" title="No document available"><i class="fas fa-file-edit"></i></a>';
    }

    $data[] = array(
        htmlspecialchars($row['id']),
        htmlspecialchars($row['proposal']) .
        '<br><small class="text-muted">Author: ' . htmlspecialchars($row['author']) .
        '<br>Committee: ' . htmlspecialchars($row['committee_name']) . '</small>',
        $formatted_date,
        $status_badge,
        $actions
    );
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

