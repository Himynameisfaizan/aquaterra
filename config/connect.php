<?php
if (session_status() === PHP_SESSION_NONE) {
    // session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Database Configuration
$local = true; 

if ($local) {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $dbName = 'bhagirath';
    $site = "http://localhost/office_php_project/bhagirath/";
} else {
    $host = 'localhost';
    $username = 'u799879276_bhagirath_db';
    $password = 'Bhagi@rath1';
    $dbName = 'u799879276_bhagirath_db';
    $site = 'https://bhagirathenterprises.co.in/';
}

// Make `$site` global
global $site;

// Create Database Connection
$conn = new mysqli($host, $username, $password, $dbName);

// Check Connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Optional: Set Character Encoding to UTF-8
$conn->set_charset("utf8");

?>