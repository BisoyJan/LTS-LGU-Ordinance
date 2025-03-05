<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if (isset($_SESSION['toast'])) {
    $message = $_SESSION['toast']['message'];
    $type = $_SESSION['toast']['type'];
    unset($_SESSION['toast']); // Clear the session message
    echo "showToast('$message', '$type');";
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

    <!-- <link rel="stylesheet" href="../../assets/css/datatables.css">

    <script src="../../assets/js/datatables.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/moment-2.29.4/dt-2.2.2/datatables.min.css" rel="stylesheet"
        integrity="sha384-/z1ZDMqmsYaq/NXh/ETpYUT4UDsfsPzi8Pezq/UyJYIvmAF7g5QBXJbuCIIMxPGl" crossorigin="anonymous">

    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/moment-2.29.4/dt-2.2.2/datatables.min.js"
        integrity="sha384-NuKovNwZ/4OQ6larI9ZRGjjGQZCUJAkvHYHiUGgz3EMMM4Is9tRCgTC12V9ci5Sp"
        crossorigin="anonymous"></script>


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
