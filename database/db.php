<?php
session_start();
global $conn;
date_default_timezone_set('Asia/Manila');

$conn = mysqli_connect("localhost", "root", "", "lgu_ordinance_system") or die("Connection failed: " . mysqli_connect_error());

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
