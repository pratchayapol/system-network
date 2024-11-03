<?php
// update_status.php

// Get the data from the request
$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'];
$count_id = $data['count_id'];
$status = $data['status'];

// Database connection
$servername = "192.168.1.202:3341"; // Database host
$username = "root"; // Database username
$password = "adminpcn"; // Database password
$dbname = "system_network"; // Database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Prepare and bind the statement
$stmt = $conn->prepare("UPDATE count_net SET `status` = ? WHERE `user_id` = ? AND `count` = ?");
$stmt->bind_param("ssi", $status, $user_id, $count_id);

// Execute the statement and check for success
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
}

// Close the connection
$stmt->close();
$conn->close();
?>
