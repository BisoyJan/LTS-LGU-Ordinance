<?php
ob_start(); // Start output buffering
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Store toast message in a variable to output after HTML
$toastScript = '';
if (isset($_SESSION['toast'])) {
    $message = $_SESSION['toast']['message'];
    $type = $_SESSION['toast']['type'];
    unset($_SESSION['toast']);
    $toastScript = "<script>showToast('$message', '$type');</script>";
}

// Check if the requested file exists
$page = basename($_SERVER['PHP_SELF']);
$validPages = ['dashboard.php', 'user.php', 'committee.php', 'ordinanceProposal.php', 'ordinanceStatus.php', 'schedule.php', 'report.php', 'setting.php'];

if (!in_array($page, $validPages)) {
    header("Location: ../views/error/404.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LGU Ordinance System</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/moment-2.29.4/dt-2.2.2/datatables.min.css" rel="stylesheet"
        integrity="sha384-/z1ZDMqmsYaq/NXh/ETpYUT4UDsfsPzi8Pezq/UyJYIvmAF7g5QBXJbuCIIMxPGl" crossorigin="anonymous">

    <!-- Add SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/moment-2.29.4/dt-2.2.2/datatables.min.js"
        integrity="sha384-NuKovNwZ/4OQ6larI9ZRGjjGQZCUJAkvHYHiUGgz3EMMM4Is9tRCgTC12V9ci5Sp"
        crossorigin="anonymous"></script>
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        // Function to show toast message dynamically
        function showToast(message, type) {
            let toastElement = $('#toastMessage');
            let toastBody = $('#toastBody');
            let toastTitle = $('#toastTitle');
            let toastIcon = $('#toastIcon');
            let toastTime = $('#toastTime');

            // Define title, icon, and border color based on type
            let title, iconSrc, borderColor;
            if (type === 'success') {
                title = "Success";
                iconSrc = "https://cdn-icons-png.flaticon.com/512/845/845646.png"; // Green Check Icon
                borderColor = "border-success shadow-success";
            } else if (type === 'warning') {
                title = "Warning";
                iconSrc = "https://cdn-icons-png.flaticon.com/512/564/564619.png"; // Yellow Warning Icon
                borderColor = "border-warning shadow-warning";
            } else {
                title = "Error";
                iconSrc = "https://cdn-icons-png.flaticon.com/512/463/463612.png"; // Red Error Icon
                borderColor = "border-danger shadow-danger";
            }

            // Update toast content
            toastTitle.text(title);
            toastIcon.attr("src", iconSrc);
            toastBody.html(message);

            // Store timestamp for updating time
            let toastTimestamp = new Date();
            toastTime.attr("data-time", toastTimestamp.getTime());
            toastTime.text("Just now");

            // Remove previous border classes and apply new one
            toastElement.removeClass("border-success border-danger border-warning shadow-success shadow-danger shadow-warning")
                .addClass(borderColor);

            // Show toast
            let toast = new bootstrap.Toast(toastElement[0]);
            toast.show();

            // Start updating the toast time dynamically
            updateToastTime();
        }

        // Function to update the toast time dynamically every minute
        function updateToastTime() {
            setInterval(function () {
                let toastTimeElement = $('#toastTime');
                let toastTimestamp = parseInt(toastTimeElement.attr("data-time"));
                let now = new Date().getTime();
                let diffInMinutes = Math.floor((now - toastTimestamp) / 60000);

                if (diffInMinutes === 0) {
                    toastTimeElement.text("Just now");
                } else if (diffInMinutes === 1) {
                    toastTimeElement.text("1 min ago");
                } else {
                    toastTimeElement.text(diffInMinutes + " mins ago");
                }
            }, 60000);
        }

    </script>

    <style>
        body {
            overflow-x: hidden;
            padding-top: 50px;
            /* Add padding to account for fixed navbar */
        }

        .sidebar {
            height: 100vh;
            background: #ffffff;
            color: #333;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 1000;
            /* Lower than topbar */
            position: fixed;
            top: 0px;
            /* Start below the topbar */
            left: 0;
            width: 250px;
            overflow-y: auto;
            bottom: 0;
        }

        .sidebar .nav-link {
            color: #333;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 5px 15px;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            background: #f8f9fa;
            color: #007bff;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: #e9ecef;
            color: #007bff;
            font-weight: bold;
        }

        .topbar {
            background: #ffffff;
            color: #333;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            height: 50px;
            z-index: 999;
            /* Higher than sidebar */
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
        }

        .logo-container {
            border-bottom: 1px solid #eee;
            padding: 15px;
        }

        .logo-container img {
            width: 80px;
            height: auto;
        }

        .main-content {
            transition: all 0.3s;
            padding: 20px;
            background-color: #f9f9f9;
            min-height: calc(100vh - 70px);
            margin-left: 250px;
            /* Same as sidebar width */
        }

        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            font-size: 2.5rem;
            color: #007bff;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
                /* Hide sidebar by default on mobile */
                box-shadow: none;
            }

            .sidebar.active {
                margin-left: 0;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            }

            .main-content {
                margin-left: 0 !important;
                width: 100%;
            }
        }

        .toggle-btn {
            cursor: pointer;
            font-size: 1.5rem;
            color: #333;
        }
    </style>
</head>

<body>
    <?php
    if (!empty($toastScript)) {
        echo $toastScript;
    }
    ?>

