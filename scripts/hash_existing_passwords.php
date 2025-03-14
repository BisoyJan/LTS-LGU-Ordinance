<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/password.php';

$conn = getConnection();
$query = "SELECT id, password FROM users";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    if (strlen($row['password']) <= 30) {  // Unhashed password
        $hashedPassword = PasswordUtil::hashPassword($row['password']);
        $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $hashedPassword, $row['id']);
        $stmt->execute();
        echo "Updated password for user ID: " . $row['id'] . "\n";
    }
}

echo "Password hashing complete!\n";
?>

