<?php
include '../includes/main/header.php';

require_once('../../scripts/role_authenticator.php');
restrictAccess('legislator');

include '../includes/main/navigation.php';
?>

<div class="container-fluid">
    Hello World Testing Testing
</div>

<?php include '../includes/main/footer.php'; ?>

