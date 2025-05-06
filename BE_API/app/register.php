<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


// Assuming you're using MySQLi
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database_name";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the raw POST data from Vue.js
$data = json_decode(file_get_contents("php://input"), true);

// Validate data (optional)
if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all fields.']);
    exit();
}

// Hash the password
$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

// Insert the user into the database
$sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $data['name'], $data['email'], $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'User registered successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Registration failed.']);
}

$stmt->close();
$conn->close();
