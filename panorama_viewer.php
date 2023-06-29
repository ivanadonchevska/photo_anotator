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
$stmt->store_result(); // Store the result set
$stmt->bind_result($photoPath);

if ($stmt->fetch()) {
    // Construct the full path to the photo
    $fullPath = $photoPath;

    // Retrieve the annotations associated with the photo from the database
    $annotationsQuery = "SELECT * FROM annotations";
    $annotationsStmt = $conn->prepare($annotationsQuery);
    $annotationsStmt->execute();
    $annotationsStmt->store_result(); // Store the result set
    $annotationsStmt->bind_result($annotationId, $annotationText, $xCoordinate, $yCoordinate);

    // Fetch the annotations into an array
    $annotations = array();
    while ($annotationsStmt->fetch()) {
        $annotations[] = array(
            'id' => $annotationId,
            'annotation_text' => $annotationText,
            'x_coordinate' => $xCoordinate,
            'y_coordinate' => $yCoordinate
        );
    }

    // Handle form submission to select annotations
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve the selected annotations from the form submission
        $selectedAnnotations = $_POST['selected_annotations'];
    } else {
        // Set default selected annotations (if any)
        $selectedAnnotations = array();
    }

    // Close the annotations statement
    $annotationsStmt->close();

    // Display the photo as a panorama viewer using Photo Sphere Viewer
    echo '
    <html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core/index.min.css">
    </head>
    <body>
        <!-- Annotation selection form -->
        <form method="POST">
            <label for="selected_annotations">Select Annotations:</label><br>
            <select name="selected_annotations[]" multiple>
            ';
    foreach ($annotations as $annotation) {
        $selected = in_array($annotation['id'], $selectedAnnotations) ? 'selected' : '';
        echo '<option value="' . $annotation['id'] . '" ' . $selected . '>' . $annotation['annotation_text'] . '</option>';
    }
    echo '
            </select><br>
            <input type="submit" value="Submit">
        </form>
    
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
    echo "Photo not found.";
}

$stmt->close();
$conn->close();
?>
