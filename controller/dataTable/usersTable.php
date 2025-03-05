<?php
require '../../database/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$output = array();
$sql = "SELECT id, username, email, role, created_at FROM users";

$totalQuery = mysqli_query($conn, $sql);
$total_all_rows = mysqli_num_rows($totalQuery);

$columns = array(
    0 => 'id',
    1 => 'username',
    2 => 'email',
    3 => 'role'
);

if (isset($_POST['search']['value'])) {
    $search_value = $_POST['search']['value'];
    $sql .= " WHERE username LIKE '%" . $search_value . "%'";
    $sql .= " OR email LIKE '%" . $search_value . "%'";
    $sql .= " OR role LIKE '%" . $search_value . "%'";

}

if (isset($_POST['order'])) {
    $column_name = $_POST['order'][0]['column'];
    $order = $_POST['order'][0]['dir'];
    $sql .= " ORDER BY " . $columns[$column_name] . " " . $order . "";
} else {
    $sql .= " ORDER BY id desc";
}

if ($_POST['length'] != -1) {
    $start = $_POST['start'];
    $length = $_POST['length'];
    $sql .= " LIMIT  " . $start . ", " . $length;
}

$query = mysqli_query($conn, $sql);
$count_rows = mysqli_num_rows($query);
$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $sub_array = array();
    $sub_array[] = $row['id'];
    $sub_array[] = $row['username'];
    $sub_array[] = $row["email"];
    $sub_array[] = $row['role'];
    $sub_array[] = '<button class="studentEditButton btn btn-success ms-1" value="' . $row["id"] . '" onclick="formIDChangeEdit()" type="button" data-bs-toggle="modal" data-bs-target="#StudentModal">Update</button>
    <button class="studentDeleteButton btn btn-danger " value="' . $row["id"] . '" type="button" data-bs-toggle="modal" data-bs-target="#StudentDeleteModal">Delete</button>';
    $data[] = $sub_array;
}

$output = array(
    'draw' => intval($_POST['draw']),
    'recordsTotal' => $count_rows,
    'recordsFiltered' => $total_all_rows,
    'data' => $data,
);
echo json_encode($output);

