<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "Suvarna@17";
$database = "patient";
$port = 3307;

// Create a MySQL connection
$conn = new mysqli($host, $username, $password, $database, $port);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set content type to JSON
header("Content-Type: application/json");

// Get JSON input data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (isset($data['noteId'], $data['noteTitle'], $data['noteContent'], $data['createdAt'])) {
    $noteId = $data['noteId'];
    $noteTitle = $data['noteTitle'];
    $noteContent = $data['noteContent'];
    $createdAt = $data['createdAt'];

    // Prepare SQL statement to insert the data
    $stmt = $conn->prepare("INSERT INTO notes (noteId, noteTitle, noteContent, createdAt) VALUES (?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE noteTitle = VALUES(noteTitle), noteContent = VALUES(noteContent), createdAt = VALUES(createdAt)");
    $stmt->bind_param("isss", $noteId, $noteTitle, $noteContent, $createdAt);

    // Execute statement
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Note synced successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to sync note."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
}

// Close the connection
$conn->close();
?>
