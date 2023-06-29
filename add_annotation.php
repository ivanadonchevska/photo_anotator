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

// Retrieve the form data from the AJAX request
$annotationText = $_POST['annotation-text'];
$xCoordinate = $_POST['x-coordinate'];
$yCoordinate = $_POST['y-coordinate'];

// Insert the annotation into the annotations table
$insertAnnotationQuery = "INSERT INTO annotations (annotation_text, x_coordinate, y_coordinate) VALUES (?, ?, ?)";
$statement = $conn->prepare($insertAnnotationQuery);
$statement->bind_param("sdd", $annotationText, $xCoordinate, $yCoordinate);
$statement->execute();

// Check if the insertion was successful
if ($statement->affected_rows > 0) {
    // Annotation added successfully
    $response = array("status" => "success");
} else {
    // Failed to add the annotation
    $response = array("status" => "error", "message" => "Failed to add the annotation.");
}

// Send the response as JSON
header("Content-Type: application/json");
echo json_encode($response);

// Close the database connection
$conn->close();
?>