<?php
// Configuration for database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_photo";

// Check if the photo ID is provided
if (isset($_POST["photo_id"])) {
    $photoId = $_POST["photo_id"];

    // Create a connection to the database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare a statement to delete the photo and associated annotations
    $deletePhotoSql = "DELETE FROM photos WHERE id = ?";
    $deleteAnnotationsSql = "DELETE FROM annotations WHERE photo_id = ?";

    // Use prepared statements to prevent SQL injection
    $deletePhotoStmt = $conn->prepare($deletePhotoSql);
    $deleteAnnotationsStmt = $conn->prepare($deleteAnnotationsSql);

    if ($deletePhotoStmt && $deleteAnnotationsStmt) {
        // Bind the photo ID parameter to the prepared statement
        $deletePhotoStmt->bind_param("i", $photoId);
        $deleteAnnotationsStmt->bind_param("i", $photoId);

        // Execute the prepared statements
        $deletePhotoResult = $deletePhotoStmt->execute();
        $deleteAnnotationsResult = $deleteAnnotationsStmt->execute();

        if ($deletePhotoResult && $deleteAnnotationsResult) {
            // Return success status
            $response = array("status" => "success");
        } else {
            // Return error message
            $response = array("status" => "error", "message" => "Failed to delete the photo.");
        }

        // Close the prepared statements
        $deletePhotoStmt->close();
        $deleteAnnotationsStmt->close();
    } else {
        // Return error message
        $response = array("status" => "error", "message" => "Failed to prepare the delete statements.");
    }

    // Close the database connection
    $conn->close();
} else {
    // Return error message
    $response = array("status" => "error", "message" => "Photo ID not provided.");
}

// Return the response as JSON
header("Content-Type: application/json");
echo json_encode($response);
?>