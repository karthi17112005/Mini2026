<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "campus_ministry_db";

// Database Connection (Compatible with both Old & New PHP/MySQL)
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>