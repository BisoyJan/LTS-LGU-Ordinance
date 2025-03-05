<?php
require '../../database/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

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
    $whereClause = " WHERE username LIKE '%$search_value%' OR role LIKE '%$search_value%'";
}

// Sorting
$orderClause = " ORDER BY id DESC"; // Default order
if (isset($_POST['order']) && isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
    $column_index = intval($_POST['order'][0]['column']);
    $order_direction = ($_POST['order'][0]['dir'] === 'asc') ? 'ASC' : 'DESC';
    if (isset($columns[$column_index])) {
        $orderClause = " ORDER BY " . $columns[$column_index] . " " . $order_direction;
    }
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
        '<button class="studentEditButton btn btn-success ms-1" value="' . htmlspecialchars($row["id"]) . '" onclick="formIDChangeEdit()" type="button" data-bs-toggle="modal" data-bs-target="#StudentModal">Update</button>
        <button class="studentDeleteButton btn btn-danger" value="' . htmlspecialchars($row["id"]) . '" type="button" data-bs-toggle="modal" data-bs-target="#StudentDeleteModal">Delete</button>'
    ];
}

$count_filtered_rows = mysqli_num_rows($query);

// Output JSON response
$output = array(
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    'recordsTotal' => $total_all_rows,
    'recordsFiltered' => $count_filtered_rows,
    'data' => $data,
);

echo json_encode($output);
