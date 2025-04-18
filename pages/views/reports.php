<?php
include '../includes/main/header.php';

require_once('../../scripts/role_authenticator.php');
restrictAccess('legislator');

include '../includes/main/navigation.php';
?>

<div class="container-fluid">
    <div class="container mt-4">
        <h1 class="mb-4">Reports</h1>
        <p>Welcome to the Reports page. Here you can view and generate various reports.</p>
        <!-- Add report generation and display logic here -->
    </div>
</div>

<?php include '../includes/main/footer.php'; ?>

