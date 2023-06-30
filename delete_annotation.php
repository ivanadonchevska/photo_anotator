<?php
// Configuration for database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_photo";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the selected annotations from the AJAX request
$data = json_decode(file_get_contents('php://input'), true);
$selectedAnnotations = $data['selectedAnnotations'];

// Delete the selected annotations from the database
$deleteQuery = "DELETE FROM annotations WHERE id IN (" . implode(",", $selectedAnnotations) . ")";
$statement = $conn->prepare($deleteQuery);
$statement->execute();

// Check if the deletion was successful
if ($statement->affected_rows > 0) {
    // Annotation deleted successfully
    $response = array("status" => "success");
} else {
    // Failed to delete the annotation
    $response = array("status" => "error", "message" => "Failed to delete the annotation.");
}

// Return the response as JSON
header("Content-Type: application/json");
echo json_encode($response);

$statement->close();
$conn->close();
?>