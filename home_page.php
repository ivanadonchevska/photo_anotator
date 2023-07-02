<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Handle logout
if (isset($_POST['logout'])) {
    // Destroy the session
    session_destroy();

    // Redirect the user to the login page
    header("Location: login.php");
    exit;
}
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
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Photo Uploader and Home Page</title>
        <link rel="stylesheet" type="text/css" href="./home_page1.css">
        <script>
        // Handle the form submission for deleting a photo
        function deletePhoto(photoId) {
            if (confirm("Are you sure you want to delete this photo?")) {
                fetch("delete.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: "photo_id=" + encodeURIComponent(photoId),
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        if (data.status === "success") {
                            // Remove the deleted photo from the page
                            var photoDiv = document.querySelector('input[name="photo_id"][value="' + photoId + '"]').parentNode;
                            photoDiv.parentNode.removeChild(photoDiv);
                            // Reload the page to reflect the changes
                            location.reload();
                        } else {
                            console.log(data.message);
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        }
        </script>
    </head>

    <body>
        <div class="logout-container">
            <form action="home_page.php" method="POST">
                <button type="submit" name="logout">Logout</button>
            </form>
        </div>
        
        <!-- Upload form -->
        <div class="upload-form">
            <h2>Upload a Photo</h2>
            <form id="upload-form" enctype="multipart/form-data">
                <label for="photo" class="file-label">
                    <span>Choose Photo</span>
                    <input type="file" name="photo" id="photo" accept="image/*" required>
                </label>
            <input class="btn-upload" type="submit" value="Upload">
            </form>
        </div>

        <br>
        <h1>Photo Feed</h1>

        <!-- Display uploaded photos -->
        <div id="photo-container">
            <?php
            // Retrieve the uploaded photos from the database
            $sql = "SELECT * FROM photos";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $photoId = $row["id"];
                    $photoPath = $row["path"];

                    // Display the photo as a link to the panorama viewer
                    echo '<div>';
                    echo '<a href="panorama_viewer.php?id=' . $photoId . '">';
                    echo '<img src="' . $photoPath . '" alt="Uploaded Photo" style="max-width: 500px;"><br>';
                    echo '</a>';
                    echo '</div>';

                    // Add the delete button
                    echo '<form class="delete-form" method="POST">';
                    echo '<input type="hidden" name="photo_id" value="' . $photoId . '">';
                    echo '<input type="button" value="Delete Photo" onclick="deletePhoto(' . $photoId . ')">';
                    echo '</form>';


                }
            } else {
                echo '<p>No uploaded photos found.</p>';
            }

            // Close the database connection
            $conn->close();
            ?>
        </div>
        <!-- Add Annotation Form -->
       
                            
        <!-- JavaScript code -->
        <script>
            // Handle the form submission
            document.getElementById("upload-form").addEventListener("submit", function (e) {
                e.preventDefault(); // Prevent the form from submitting

                var formData = new FormData(this);

                fetch("upload.php", {
                    method: "POST",
                    body: formData,
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        if (data.status === "success") {
                            // Reload the page to display the new photo
                            location.reload();
                        } else {
                            console.log(data.message);
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            });
        </script>
        
        <!-- JavaScript code -->
        
    </body>
</html>