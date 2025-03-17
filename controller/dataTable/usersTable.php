<?php
require '../../database/database.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = getConnection();

$output = array();
$columns = array("id", "username", "email", "role");
$search_value = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : "";

// Base Query
$sql = "SELECT id, username, email, role, created_at FROM users";
$totalQuery = mysqli_query($conn, $sql);
$total_all_rows = mysqli_num_rows($totalQuery);

// Filtering
$whereClause = "";
if (!empty($search_value)) {
    $search_value = mysqli_real_escape_string($conn, $search_value);
    $whereClause = " WHERE username LIKE '%$search_value%' OR email LIKE '%$search_value%' OR role LIKE '%$search_value%'";
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
    $filtered_query = "SELECT COUNT(*) as total FROM users" . $whereClause;
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
    $data[] = [
        htmlspecialchars($row['id']),
        htmlspecialchars($row['username']),
        htmlspecialchars($row["email"]),
        htmlspecialchars($row['role']),
        '<button class="editButton btn btn-success ms-1" data-id="' . $row["id"] . '" onclick="formIDChangeEdit()" type="button" data-bs-toggle="modal" data-bs-target="#userModal">Update</button>
        <button class="deleteButton btn btn-danger" data-id="' . $row["id"] . '" type="button" data-bs-toggle="modal" data-bs-target="#userDeleteModal">Delete</button>'
    ];
}

// Output JSON response
$output = array(
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    'recordsTotal' => $total_all_rows,
    'recordsFiltered' => $count_filtered_rows,
    'data' => $data,
);

echo json_encode($output);
