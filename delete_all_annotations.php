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

// Delete all annotations from the database
$deleteQuery = "DELETE FROM annotations";
$statement = $conn->prepare($deleteQuery);
$statement->execute();

// Check if the deletion was successful
if ($statement->affected_rows > 0) {
    // All annotations deleted successfully
    $response = array("status" => "success");
} else {
    // Failed to delete all annotations
    $response = array("status" => "error", "message" => "Failed to delete all annotations.");
}

// Return the response as JSON
header("Content-Type: application/json");
echo json_encode($response);

$statement->close();
$conn->close();
?>
