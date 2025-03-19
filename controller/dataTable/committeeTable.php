<?php
require '../../database/database.php';

$conn = getConnection();
$query = "SELECT id, name, description, created_at FROM committees ORDER BY name";
$result = mysqli_query($conn, $query);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $row['created_at'] = date('M d, Y', strtotime($row['created_at']));
    $data[] = $row;
}

echo json_encode([
    'data' => $data
]);
