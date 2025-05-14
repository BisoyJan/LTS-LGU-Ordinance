<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function restrictAccess($rolePermission)
{
    // Check if headers have already been sent
    if (!headers_sent()) {
        if (!isset($_SESSION['role'])) {
            header("Location: ../../controller/authentication/authentication.php?action=logout");
            exit();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../../controller/authentication/authentication.php?action=logout");
            exit();
        }
    } else {
        // Fallback for when headers are already sent
        echo '<script>window.location.href="../../controller/authentication/authentication.php?action=logout";</script>';
        exit();
    }

    $userRole = $_SESSION['role'];

    // Define role-based access logic
    $roleAccess = [
        'mayor' => ['dashboard.php', 'ordinanceProposal.php', 'ordinanceStatus.php'],
        'legislator' => ['dashboard.php', 'ordinanceProposal.php', 'ordinanceStatus.php', 'schedule.php', 'user.php'],
        'committee' => ['dashboard.php', 'ordinanceProposal.php', 'ordinanceStatus.php', 'schedule.php', 'reports.php', 'committee.php'],
        'secretary' => ['dashboard.php', 'ordinanceProposal.php', 'schedule.php', 'ordinanceStatus.php'],
        'admin' => ['dashboard.php', 'user.php', 'committee.php', 'ordinanceProposal.php', 'ordinanceStatus.php', 'schedule.php', 'reports.php', 'setting.php']
    ];

    // Get the current page name
    $currentPage = basename($_SERVER['PHP_SELF']);

    // Check if the user's role has access to the current page
    if (!in_array($currentPage, $roleAccess[$userRole] ?? [])) {
        if (!headers_sent()) {
            header("Location: ../../pages/views/error/403.php");
            exit();
        } else {
            echo '<script>window.location.href="../../pages/views/error/403.php";</script>';
            exit();
        }
    }
}
