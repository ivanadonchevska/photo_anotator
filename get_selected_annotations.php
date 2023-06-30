<?php
// Database connection configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "db_photo";

// Establish database connection
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the selected annotations from the database
$selectedAnnotationsQuery = "SELECT * FROM annotations WHERE id IN (" . implode(",", $selectedAnnotations) . ")";
$selectedAnnotationsStmt = $conn->prepare($selectedAnnotationsQuery);
$selectedAnnotationsStmt->execute();
$selectedAnnotationsStmt->store_result(); // Store the result set
$selectedAnnotationsStmt->bind_result($annotationId, $annotationText, $xCoordinate, $yCoordinate);

// Fetch the selected annotations into an array
$selectedAnnotations = array();
while ($selectedAnnotationsStmt->fetch()) {
    $selectedAnnotations[] = array(
        'id' => $annotationId,
        'annotation_text' => $annotationText,
        'x_coordinate' => $xCoordinate,
        'y_coordinate' => $yCoordinate
    );
}

// Close the selected annotations statement
$selectedAnnotationsStmt->close();

// Display the selected annotations as markers on the photo
foreach ($selectedAnnotations as $annotation) {
    // Code to display the annotation marker
}

$conn->close();
