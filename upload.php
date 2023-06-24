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

// Handle the photo upload
if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
    $targetDir = "photos/";
    $targetFile = $targetDir . basename($_FILES["photo"]["name"]);

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
        // Save the photo path in the database
        $sql = "INSERT INTO photos (path) VALUES ('$targetFile')";
        if ($conn->query($sql) === true) {
            // Send the photo path back to index.html using JSON response
            $response = array("status" => "success", "photo_path" => $targetFile);
            echo json_encode($response);
        } else {
            $response = array("status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error);
            echo json_encode($response);
        }
    } else {
        $response = array("status" => "error", "message" => "Sorry, there was an error uploading your photo.");
        echo json_encode($response);
    }
} else {
    $response = array("status" => "error", "message" => "Please select a photo to upload.");
    echo json_encode($response);
}

// Close the database connection
$conn->close();
?>