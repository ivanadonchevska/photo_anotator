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

    // Use prepared statements to prevent SQL injection
    $deletePhotoSql = "DELETE FROM photos WHERE id = ?";
    $deletePhotoStmt = $conn->prepare($deletePhotoSql);

    if ($deletePhotoStmt) {
        // Bind the photo ID parameter to the prepared statement
        $deletePhotoStmt->bind_param("i", $photoId);

        // Execute the prepared statement to delete the photo
        $deletePhotoStmt->execute();

        // Check if the photo was deleted successfully
        if ($deletePhotoStmt->affected_rows > 0) {
            // Photo deleted successfully
            $response = array("status" => "success");
        } else {
            // Failed to delete the photo
            $response = array("status" => "error", "message" => "Failed to delete the photo.");
        }

        // Close the prepared statement for deleting the photo
        $deletePhotoStmt->close();
    } else {
        // Failed to prepare the statement for deleting the photo
        $response = array("status" => "error", "message" => "Failed to prepare the statement for deleting the photo.");
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