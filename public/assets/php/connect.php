<?php
$servername = "localhost";
$username = "root";
$password = "loc.1005";
$database = "cgv";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
  die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Set charset
$conn->set_charset("utf8mb4");
?>