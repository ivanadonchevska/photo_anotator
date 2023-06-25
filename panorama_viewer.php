<?php
$photoId = $_GET['id'];

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

// Retrieve the photo path from the database based on the photo ID
$stmt = $conn->prepare("SELECT path FROM photos WHERE id = ?");
$stmt->bind_param("i", $photoId);
$stmt->execute();
$stmt->bind_result($photoPath);

if ($stmt->fetch()) {
    // Construct the full path to the photo
    $fullPath = $photoPath;

    // Check if the photo file exists
    if (file_exists($fullPath)) {
        // Display the photo as a panorama viewer using Photo Sphere Viewer
        echo '
        <html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core/index.min.css">
        </head>
        <body>
            <div id="container" style="display: flex; justify-content: center; align-items: center;">
                <div id="viewer" style="width: 80vw; height: 80vh;"></div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/three/build/three.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core/index.min.js"></script>
            <script>
                const viewer = new PhotoSphereViewer.Viewer({
                    container: document.querySelector(\'#viewer\'),
                    panorama: \'' . $fullPath . '\',
                });
            </script>
        </body>
        </html>';
    } else {
        echo "Photo file not found.";
    }
} else {
    echo "Photo not found.";
}

$stmt->close();
$conn->close();
?>