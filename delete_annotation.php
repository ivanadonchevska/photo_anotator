<?php
// Configuration for database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_photo";

// Get the annotation ID from the POST parameters
$annotationId = $_POST["annotation_id"];

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete the annotation from the database
$sql = "DELETE FROM annotations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $annotationId);
$stmt->execute();

// Check if the deletion was successful
if ($stmt->affected_rows > 0) {
    $response = array("status" => "success");
} else {
    $response = array("status" => "error", "message" => "Failed to delete the annotation.");
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();

// Return the response as JSON
header("Content-Type: application/json");
echo json_encode($response);
?>
