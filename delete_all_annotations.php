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

// Check if the photo_id is provided
if (isset($_POST["photo_id"])) {
    $photoId = $_POST["photo_id"];

    // Delete all annotations for the specified photo
    $deleteAnnotationsSql = "DELETE FROM annotations WHERE photo_id = $photoId";
    if ($conn->query($deleteAnnotationsSql) === TRUE) {
        // Success message
        $response = array(
            "status" => "success",
            "message" => "All annotations deleted successfully"
        );
        echo json_encode($response);
    } else {
        // Error message
        $response = array(
            "status" => "error",
            "message" => "Failed to delete annotations: " . $conn->error
        );
        echo json_encode($response);
    }
} else {
    // Error message if photo_id is not provided
    $response = array(
        "status" => "error",
        "message" => "Invalid request. Please provide a photo_id"
    );
    echo json_encode($response);
}

// Close the database connection
$conn->close();
?>
