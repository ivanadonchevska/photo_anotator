<?php
// Configuration for database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_photo";

// Get the form data
$photoId = $_POST["photo_id"];
$coordinates = $_POST["coordinates"];
$additionalData = $_POST["additional_data"];

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert the annotation into the database
$sql = "INSERT INTO annotations (photo_id, coordinates, additional_column) VALUES ('$photoId', '$coordinates', '$additionalData')";

if ($conn->query($sql) === TRUE) {
    $response = array(
        "status" => "success",
        "message" => "Annotation added successfully."
    );
} else {
    $response = array(
        "status" => "error",
        "message" => "Failed to add annotation: " . $conn->error
    );
}

// Close the database connection
$conn->close();

// Return the response as JSON
header("Content-type: application/json");
echo json_encode($response);
?>
